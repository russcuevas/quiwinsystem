<?php

namespace Tests\Feature;

use App\Models\GameSession;
use App\Models\User;
use App\Services\OpenTdbService;
use Tests\TestCase;

class QuiwinGameTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\GameSetting::truncate();
    }

    public function test_user_registration_creates_pending_user_with_coupon_code()
    {
        $uniqueEmail = 'test_' . uniqid() . '@example.com';
        $response = $this->post(route('register.submit'), [
            'name' => 'Test Player',
            'email' => $uniqueEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', [
            'email' => $uniqueEmail,
            'status' => 'pending',
            'points' => 0,
        ]);

        $user = User::where('email', $uniqueEmail)->first();
        $this->assertNotNull($user->referral_code);
        $this->assertStringStartsWith('QUI-', $user->referral_code);
    }

    public function test_pending_user_cannot_login_until_admin_approves()
    {
        $uniqueEmail = 'pending_' . uniqid() . '@example.com';
        $user = User::create([
            'name' => 'Pending Player',
            'email' => $uniqueEmail,
            'password' => bcrypt('password123'),
            'role' => 'user',
            'status' => 'pending',
            'referral_code' => 'QUI-' . strtoupper(substr(md5(uniqid()), 0, 6)),
            'points' => 0,
            'is_active' => true,
        ]);

        // Attempt login while pending
        $loginRes = $this->post(route('login.submit'), [
            'email' => $uniqueEmail,
            'password' => 'password123',
        ]);

        $loginRes->assertSessionHasErrors('email');
        $this->assertGuest();

        // Admin approves player
        $admin = User::where('role', 'admin')->first() ?: User::create([
            'name' => 'Admin User',
            'email' => 'admin_' . uniqid() . '@quiwin.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => 'approved',
            'points' => 9999,
        ]);

        $approveRes = $this->actingAs($admin)->post(route('admin.users.approve', ['userId' => $user->id]));
        $approveRes->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('approved', $user->status);
        $this->assertEquals(200, $user->points);

        // Now login succeeds
        $this->post(route('logout'));
        $loginResApproved = $this->post(route('login.submit'), [
            'email' => $uniqueEmail,
            'password' => 'password123',
        ]);
        $loginResApproved->assertRedirect(route('user.home'));
    }

    public function test_referral_system_and_five_player_quest_reward()
    {
        $admin = User::where('role', 'admin')->first();

        // Referrer player
        $referrer = User::create([
            'name' => 'Top Inviter',
            'email' => 'inviter_' . uniqid() . '@quiwin.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'status' => 'approved',
            'referral_code' => 'QUI-MASTER',
            'points' => 200,
            'is_active' => true,
        ]);

        // Register 5 friends using QUI-MASTER
        $friendIds = [];
        for ($i = 1; $i <= 5; $i++) {
            $friendEmail = "friend{$i}_" . uniqid() . "@quiwin.com";
            $this->post(route('register.submit'), [
                'name' => "Friend {$i}",
                'email' => $friendEmail,
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'referral_code' => 'QUI-MASTER',
            ]);

            $friend = User::where('email', $friendEmail)->first();
            $this->assertEquals($referrer->id, $friend->referred_by);
            $friendIds[] = $friend->id;
        }

        // Admin approves first 4 friends -> quest not yet triggered
        for ($i = 0; $i < 4; $i++) {
            $this->actingAs($admin)->post(route('admin.users.approve', ['userId' => $friendIds[$i]]));
        }

        $referrer->refresh();
        $this->assertEquals(200, $referrer->points);
        $this->assertEquals(4, $referrer->approvedReferrals()->count());
        $this->assertFalse($referrer->quest_rewarded);

        // Admin approves 5th friend -> +1,000 PTS Quest Reward granted!
        $this->actingAs($admin)->post(route('admin.users.approve', ['userId' => $friendIds[4]]));

        $referrer->refresh();
        $this->assertEquals(5, $referrer->approvedReferrals()->count());
        $this->assertTrue($referrer->quest_rewarded);
        $this->assertEquals(1200, $referrer->points); // 200 initial + 1000 quest bonus!
    }

    public function test_game_start_deducts_50_points_and_creates_session()
    {
        $user = User::create([
            'name' => 'Start Player',
            'email' => 'start_' . uniqid() . '@quiwin.com',
            'password' => bcrypt('password123'),
            'points' => 200,
            'role' => 'user',
            'status' => 'approved',
            'referral_code' => 'QUI-' . strtoupper(substr(md5(uniqid()), 0, 6)),
        ]);

        $response = $this->actingAs($user)->post(route('game.start'));

        $this->assertEquals(150, $user->fresh()->points);
        $session = GameSession::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($session);
        $this->assertGreaterThanOrEqual(10, count($session->questions_data));
        $this->assertEquals(1, $session->current_round);
        $this->assertEquals(1, $session->current_question_index);
    }

    public function test_round_one_easy_scoring_and_streak()
    {
        $user = User::create([
            'name' => 'Scoring Player',
            'email' => 'score_' . uniqid() . '@quiwin.com',
            'password' => bcrypt('password123'),
            'points' => 200,
            'role' => 'user',
            'status' => 'approved',
            'referral_code' => 'QUI-' . strtoupper(substr(md5(uniqid()), 0, 6)),
        ]);

        $this->actingAs($user)->post(route('game.start'));
        $session = GameSession::where('user_id', $user->id)->latest()->first();
        
        $currentQ = $session->questions_data[0];
        $correctAnswer = $currentQ['correct_answer'];

        // Submit correct answer for Q1
        $response = $this->actingAs($user)->postJson(route('game.answer', ['sessionId' => $session->id]), [
            'answer' => $correctAnswer,
        ]);

        $response->assertJson([
            'success' => true,
            'is_correct' => true,
            'points_awarded' => 2, // Round 1 Easy correct = +2
            'current_streak' => 1,
        ]);

        // Submit wrong answer for Q2
        $responseWrong = $this->actingAs($user)->postJson(route('game.answer', ['sessionId' => $session->id]), [
            'answer' => 'Definitely Wrong Answer 12345',
        ]);

        $responseWrong->assertJson([
            'success' => true,
            'is_correct' => false,
            'points_awarded' => -3, // Round 1 Easy wrong = -3
            'current_streak' => 0, // Reset streak on error
        ]);
    }

    public function test_admin_access_control()
    {
        $admin = User::where('email', 'admin@quiwin.com')->first() ?: User::create([
            'name' => 'Admin User',
            'email' => 'admin@quiwin.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => 'approved',
            'is_active' => true,
        ]);
        $user = User::where('email', 'player@quiwin.com')->first() ?: User::create([
            'name' => 'Reg User',
            'email' => 'reg_' . uniqid() . '@quiwin.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'status' => 'approved',
            'referral_code' => 'QUI-' . strtoupper(substr(md5(uniqid()), 0, 6)),
        ]);

        // Regular user denied
        $resUser = $this->actingAs($user)->get(route('admin.dashboard'));
        $resUser->assertRedirect(route('user.home'));

        // Admin granted
        $resAdmin = $this->actingAs($admin)->get(route('admin.dashboard'));
        $resAdmin->assertStatus(200);
    }
}
