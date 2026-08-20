<?php

namespace Tests\Feature;

use App\Models\GameSession;
use App\Models\User;
use App\Services\OpenTdbService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuiwinGameTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\GameSetting::truncate();
    }

    public function test_user_registration_awards_200_points()
    {
        $uniqueEmail = 'test_' . uniqid() . '@example.com';
        $response = $this->post(route('register.submit'), [
            'name' => 'Test Player',
            'email' => $uniqueEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('user.home'));
        $this->assertDatabaseHas('users', [
            'email' => $uniqueEmail,
            'points' => 200,
        ]);
    }

    public function test_game_start_deducts_50_points_and_creates_session()
    {
        $user = User::create([
            'name' => 'Start Player',
            'email' => 'start_' . uniqid() . '@quiwin.com',
            'password' => bcrypt('password123'),
            'points' => 200,
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->post(route('game.start'));

        $this->assertEquals(150, $user->fresh()->points);
        $session = GameSession::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($session);
        $this->assertEquals(30, count($session->questions_data));
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
        $admin = User::where('email', 'admin@quiwin.com')->first();
        $user = User::where('email', 'player@quiwin.com')->first() ?: User::create([
            'name' => 'Reg User',
            'email' => 'reg_' . uniqid() . '@quiwin.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
        ]);

        // Regular user denied
        $resUser = $this->actingAs($user)->get(route('admin.dashboard'));
        $resUser->assertRedirect(route('user.home'));

        // Admin granted
        $resAdmin = $this->actingAs($admin)->get(route('admin.dashboard'));
        $resAdmin->assertStatus(200);
    }

    public function test_admin_can_manipulate_pointing_system()
    {
        $admin = User::where('email', 'admin@quiwin.com')->first();

        // Update pointing system settings as admin
        $res = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'easy_correct_points' => 10,
            'easy_wrong_penalty' => 4,
            'easy_timer_seconds' => 7,
            'medium_correct_points' => 15,
            'medium_wrong_penalty' => 8,
            'medium_timer_seconds' => 6,
            'hard_correct_points' => 25,
            'hard_wrong_penalty' => 20,
            'hard_timer_seconds' => 5,
            'entry_fee' => 75,
            'welcome_bonus' => 300,
            'streak_3_bonus' => 3,
            'streak_5_bonus' => 6,
            'streak_8_bonus' => 12,
        ]);

        $res->assertSessionHas('success');

        // Check that GameSetting reflects the new custom pointing
        $this->assertEquals(10, \App\Models\GameSetting::getVal('easy_correct_points'));
        $this->assertEquals(4, \App\Models\GameSetting::getVal('easy_wrong_penalty'));
        $this->assertEquals(75, \App\Models\GameSetting::getVal('entry_fee'));
    }
}
