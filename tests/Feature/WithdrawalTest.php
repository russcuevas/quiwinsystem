<?php

namespace Tests\Feature;

use App\Models\PointTransaction;
use App\Models\User;
use App\Models\UserMail;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithdrawalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Juan Player',
            'email' => 'juan@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'status' => 'approved',
            'points' => 1000,
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Boss',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'approved',
            'points' => 9999,
            'is_active' => true,
        ]);
    }

    public function test_user_cannot_withdraw_if_balance_is_below_500()
    {
        $this->user->points = 499;
        $this->user->save();

        $response = $this->actingAs($this->user)->post('/user/withdraw', [
            'amount' => 500,
            'gcash_number' => '09123456789',
            'gcash_name' => 'Juan Dela Cruz',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(0, Withdrawal::count());
        $this->assertEquals(499, $this->user->fresh()->points);
    }

    public function test_user_cannot_withdraw_more_than_current_balance()
    {
        $this->user->points = 1000;
        $this->user->save();

        $response = $this->actingAs($this->user)->post('/user/withdraw', [
            'amount' => 1001,
            'gcash_number' => '09123456789',
            'gcash_name' => 'Juan Dela Cruz',
        ]);

        $response->assertSessionHasErrors('amount');
        $this->assertEquals(0, Withdrawal::count());
        $this->assertEquals(1000, $this->user->fresh()->points);
    }

    public function test_user_cannot_withdraw_with_invalid_gcash_number()
    {
        // Not starting with 09
        $response = $this->actingAs($this->user)->post('/user/withdraw', [
            'amount' => 500,
            'gcash_number' => '08123456789',
            'gcash_name' => 'Juan Dela Cruz',
        ]);
        $response->assertSessionHasErrors('gcash_number');

        // Less than 11 digits
        $response2 = $this->actingAs($this->user)->post('/user/withdraw', [
            'amount' => 500,
            'gcash_number' => '0912345',
            'gcash_name' => 'Juan Dela Cruz',
        ]);
        $response2->assertSessionHasErrors('gcash_number');

        $this->assertEquals(0, Withdrawal::count());
    }

    public function test_valid_withdrawal_submits_without_points_deduction()
    {
        $response = $this->actingAs($this->user)->post('/user/withdraw', [
            'amount' => 500,
            'gcash_number' => '09123456789',
            'gcash_name' => 'Juan Dela Cruz',
        ]);

        $response->assertSessionHas('success');

        // Check withdrawal was created
        $withdrawal = Withdrawal::first();
        $this->assertNotNull($withdrawal);
        $this->assertEquals(500, $withdrawal->amount);
        $this->assertEquals('09123456789', $withdrawal->gcash_number);
        $this->assertEquals('Juan Dela Cruz', $withdrawal->gcash_name);
        $this->assertEquals('pending', $withdrawal->status);

        // Crucial: Points are NOT deducted yet (wala pang bawas)
        $this->assertEquals(1000, $this->user->fresh()->points);
    }

    public function test_admin_approval_deducts_points_and_creates_mail()
    {
        // Submit request
        $this->actingAs($this->user)->post('/user/withdraw', [
            'amount' => 600,
            'gcash_number' => '09171234567',
            'gcash_name' => 'Juan Dela Cruz',
        ]);

        $withdrawal = Withdrawal::first();
        $this->assertEquals('pending', $withdrawal->status);
        $this->assertEquals(1000, $this->user->fresh()->points);

        // Admin approves
        $response = $this->actingAs($this->admin)->post("/admin/withdrawals/{$withdrawal->id}/approve");
        $response->assertSessionHas('success');

        // Check points are NOW deducted
        $this->assertEquals(400, $this->user->fresh()->points);

        // Check withdrawal status is approved
        $withdrawal->refresh();
        $this->assertEquals('approved', $withdrawal->status);
        $this->assertNotNull($withdrawal->approved_at);

        // Check PointTransaction is recorded
        $tx = PointTransaction::where('user_id', $this->user->id)->where('type', 'withdrawal')->first();
        $this->assertNotNull($tx);
        $this->assertEquals(-600, $tx->amount);
        $this->assertEquals(400, $tx->balance_after);

        // Check In-game Mail is sent with "Already sent by admin"
        $mail = UserMail::where('user_id', $this->user->id)->where('type', 'withdrawal_approved')->first();
        $this->assertNotNull($mail);
        $this->assertStringContainsString('Already sent by the admin', $mail->message);
    }

    public function test_admin_rejection_does_not_deduct_points()
    {
        // Submit request
        $this->actingAs($this->user)->post('/user/withdraw', [
            'amount' => 500,
            'gcash_number' => '09171234567',
            'gcash_name' => 'Juan Dela Cruz',
        ]);

        $withdrawal = Withdrawal::first();

        // Admin rejects
        $response = $this->actingAs($this->admin)->post("/admin/withdrawals/{$withdrawal->id}/reject", [
            'remarks' => 'Incorrect GCash Name provided',
        ]);

        $response->assertSessionHas('success');

        // Points must remain 1000
        $this->assertEquals(1000, $this->user->fresh()->points);

        $withdrawal->refresh();
        $this->assertEquals('rejected', $withdrawal->status);
        $this->assertEquals('Incorrect GCash Name provided', $withdrawal->admin_remarks);

        // Check UserMail notification
        $mail = UserMail::where('user_id', $this->user->id)->where('type', 'withdrawal_rejected')->first();
        $this->assertNotNull($mail);
        $this->assertStringContainsString('rejected', $mail->message);
    }
}
