<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\PointTransaction;
use App\Models\Question;
use App\Models\User;
use App\Models\UserMail;
use App\Models\Withdrawal;
use App\Services\OpenTdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    protected OpenTdbService $openTdbService;

    public function __construct(OpenTdbService $openTdbService)
    {
        $this->openTdbService = $openTdbService;
    }

    public function dashboard()
    {
        $totalUsers = User::where('role', 'user')->count();
        $pendingUsersCount = User::where('role', 'user')->where('status', 'pending')->count();
        $totalApprovedUsers = User::where('role', 'user')->where('status', 'approved')->count();
        $totalGames = GameSession::count();
        $totalCompletedGames = GameSession::where('status', 'completed')->count();
        $totalPointsInCirculation = User::where('role', 'user')->sum('points');
        $totalQuestionsInDb = Question::count();

        // Pending users awaiting approval
        $pendingUsers = User::where('role', 'user')
            ->where('status', 'pending')
            ->with('referrer')
            ->latest()
            ->take(10)
            ->get();

        // Withdrawals stats & pending queue
        $totalWithdrawalsCount = Withdrawal::count();
        $pendingWithdrawalsCount = Withdrawal::where('status', 'pending')->count();
        $totalApprovedWithdrawalsAmount = Withdrawal::where('status', 'approved')->sum('amount');
        $pendingWithdrawals = Withdrawal::with('user')
            ->where('status', 'pending')
            ->latest()
            ->take(8)
            ->get();

        // Top players
        $topPlayers = User::where('role', 'user')
            ->where('status', 'approved')
            ->orderByDesc('points')
            ->take(8)
            ->get();

        // Recent game sessions
        $recentSessions = GameSession::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Recent point transactions
        $recentTransactions = PointTransaction::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Questions distribution
        $easyCount = Question::where('difficulty', 'easy')->count();
        $mediumCount = Question::where('difficulty', 'medium')->count();
        $hardCount = Question::where('difficulty', 'hard')->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'pendingUsersCount',
            'totalApprovedUsers',
            'pendingUsers',
            'totalWithdrawalsCount',
            'pendingWithdrawalsCount',
            'totalApprovedWithdrawalsAmount',
            'pendingWithdrawals',
            'totalGames',
            'totalCompletedGames',
            'totalPointsInCirculation',
            'totalQuestionsInDb',
            'topPlayers',
            'recentSessions',
            'recentTransactions',
            'easyCount',
            'mediumCount',
            'hardCount'
        ));
    }

    public function withdrawals(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'all');
        $query = Withdrawal::with('user');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('gcash_number', 'like', "%{$search}%")
                  ->orWhere('gcash_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $withdrawals = $query->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(15);

        $pendingCount = Withdrawal::where('status', 'pending')->count();
        $totalApprovedAmount = Withdrawal::where('status', 'approved')->sum('amount');
        $totalPendingAmount = Withdrawal::where('status', 'pending')->sum('amount');

        return view('admin.withdrawals', compact(
            'withdrawals',
            'search',
            'status',
            'pendingCount',
            'totalApprovedAmount',
            'totalPendingAmount'
        ));
    }

    public function approveWithdrawal(Request $request, $id)
    {
        $withdrawal = Withdrawal::with('user')->findOrFail($id);

        if ($withdrawal->status === 'approved') {
            return back()->with('info', "Withdrawal #{$withdrawal->id} is already approved.");
        }

        $user = $withdrawal->user;

        // Verify player has sufficient balance
        if ($user->points < $withdrawal->amount) {
            return back()->with('error', "Cannot approve: Player {$user->name} currently only has " . number_format($user->points) . " PTS, which is less than the requested ₱" . number_format($withdrawal->amount) . ".");
        }

        DB::transaction(function () use ($withdrawal, $user, $request) {
            // Deduct points from player in-game
            $user->points -= $withdrawal->amount;
            $user->save();

            // Record point transaction
            PointTransaction::create([
                'user_id' => $user->id,
                'game_session_id' => null,
                'type' => 'withdrawal',
                'amount' => -$withdrawal->amount,
                'balance_after' => $user->points,
                'description' => "GCash Withdrawal Approved & Sent: ₱" . number_format($withdrawal->amount) . " to {$withdrawal->gcash_number} ({$withdrawal->gcash_name})",
            ]);

            // Update withdrawal status
            $withdrawal->status = 'approved';
            $withdrawal->approved_at = now();
            if ($request->filled('remarks')) {
                $withdrawal->admin_remarks = $request->remarks;
            }
            $withdrawal->save();

            // Send In-Game Mail marked "Already sent by the admin"
            UserMail::create([
                'user_id' => $user->id,
                'title' => 'Withdrawal Sent! (Already sent by Admin) 💸',
                'message' => "Already sent by the admin! Your withdrawal request of ₱" . number_format($withdrawal->amount) . " has been approved and sent to GCash account: {$withdrawal->gcash_name} ({$withdrawal->gcash_number}). Your in-game balance has been updated (-" . number_format($withdrawal->amount) . " PTS).",
                'type' => 'withdrawal_approved',
                'is_read' => false,
            ]);
        });

        return back()->with('success', "Withdrawal #{$withdrawal->id} (₱" . number_format($withdrawal->amount) . " for {$user->name}) has been APPROVED and marked as 'Already sent by the admin'!");
    }

    public function rejectWithdrawal(Request $request, $id)
    {
        $withdrawal = Withdrawal::with('user')->findOrFail($id);

        if ($withdrawal->status === 'approved') {
            return back()->with('error', "Cannot reject an already approved withdrawal.");
        }

        $reason = $request->input('remarks', 'Withdrawal request was rejected by Admin.');

        $withdrawal->status = 'rejected';
        $withdrawal->admin_remarks = $reason;
        $withdrawal->save();

        // Notify user via in-game mail
        UserMail::create([
            'user_id' => $withdrawal->user_id,
            'title' => 'Withdrawal Request Rejected ❌',
            'message' => "Your withdrawal request for ₱" . number_format($withdrawal->amount) . " to GCash ({$withdrawal->gcash_number}) was rejected by Admin. Reason: {$reason}. No points were deducted.",
            'type' => 'withdrawal_rejected',
            'is_read' => false,
        ]);

        return back()->with('success', "Withdrawal #{$withdrawal->id} has been REJECTED.");
    }

    public function users(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'all');
        $query = User::where('role', 'user')->with(['referrer', 'approvedReferrals']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('referral_code', 'like', "%{$search}%");
            });
        }

        $users = $query->orderByDesc('id')->paginate(15);
        $pendingCount = User::where('role', 'user')->where('status', 'pending')->count();

        return view('admin.users', compact('users', 'search', 'status', 'pendingCount'));
    }

    public function approveUser($userId)
    {
        $user = User::findOrFail($userId);
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot approve admin accounts.');
        }

        if ($user->status === 'approved') {
            return back()->with('info', "Player {$user->name} is already approved.");
        }

        $welcomeBonus = (int) \App\Models\GameSetting::getVal('welcome_bonus', 200);

        // Ensure user has a referral code
        if (!$user->referral_code) {
            do {
                $uniqueCode = 'QUI-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
            } while (User::where('referral_code', $uniqueCode)->exists());
            $user->referral_code = $uniqueCode;
        }

        $user->status = 'approved';
        $user->points = $welcomeBonus;
        $user->save();

        // Award welcome bonus points transaction
        PointTransaction::create([
            'user_id' => $user->id,
            'game_session_id' => null,
            'type' => 'register_bonus',
            'amount' => $welcomeBonus,
            'balance_after' => $user->points,
            'description' => "Welcome registration bonus approved by Admin (+{$welcomeBonus} PTS) | Coupon Code: {$user->referral_code}",
        ]);

        $message = "Player {$user->name} has been APPROVED! Granted {$welcomeBonus} welcome points with Coupon Code {$user->referral_code}.";

        // Check if user was referred by another player
        if ($user->referred_by) {
            $referrer = User::find($user->referred_by);
            if ($referrer) {
                $approvedCount = $referrer->approvedReferrals()->count();

                // If referrer reaches 5 approved referrals and hasn't claimed quest bonus
                if ($approvedCount >= 5 && !$referrer->quest_rewarded) {
                    $questReward = 1000;
                    $referrer->points += $questReward;
                    $referrer->quest_rewarded = true;
                    $referrer->save();

                    PointTransaction::create([
                        'user_id' => $referrer->id,
                        'game_session_id' => null,
                        'type' => 'quest_reward',
                        'amount' => $questReward,
                        'balance_after' => $referrer->points,
                        'description' => "🎯 Referral Quest Milestone Completed (5/5 Friends Invited & Approved) (+{$questReward} PTS)",
                    ]);

                    $message .= " 🚀 Referrer {$referrer->name} achieved 5/5 Quest Milestone & awarded +1,000 PTS bonus!";
                }
            }
        }

        return back()->with('success', $message);
    }

    public function rejectUser($userId)
    {
        $user = User::findOrFail($userId);
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot reject admin accounts.');
        }

        $user->status = 'rejected';
        $user->save();

        return back()->with('success', "Player {$user->name}'s registration has been REJECTED.");
    }

    public function updateUserPoints(Request $request, $userId)
    {
        $request->validate([
            'amount' => 'required|integer',
            'reason' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($userId);
        $amount = (int) $request->amount;
        $reason = $request->reason ?: 'Admin adjustment';

        $user->points += $amount;
        $user->save();

        PointTransaction::create([
            'user_id' => $user->id,
            'game_session_id' => null,
            'type' => 'admin_adjustment',
            'amount' => $amount,
            'balance_after' => $user->points,
            'description' => "Admin Adjustment: {$reason} (" . ($amount >= 0 ? "+{$amount}" : "{$amount}") . " points)",
        ]);

        return back()->with('success', "Updated {$user->name}'s points balance to {$user->points}!");
    }

    public function toggleUserStatus($userId)
    {
        $user = User::findOrFail($userId);
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot deactivate admin accounts.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User account {$user->name} has been {$statusStr}.");
    }

    public function questions(Request $request)
    {
        $difficulty = $request->input('difficulty');
        $category = $request->input('category');
        $search = $request->input('search');

        $query = Question::query();

        if ($difficulty) {
            $query->where('difficulty', $difficulty);
        }
        if ($category) {
            $query->where('category', 'like', "%{$category}%");
        }
        if ($search) {
            $query->where('question_text', 'like', "%{$search}%");
        }

        $questions = $query->latest()->paginate(20);
        $categories = Question::select('category')->distinct()->pluck('category');

        return view('admin.questions', compact('questions', 'categories', 'difficulty', 'category', 'search'));
    }

    public function syncQuestions(Request $request)
    {
        $difficulty = $request->input('difficulty', 'all');

        $difficulties = ($difficulty === 'all') ? ['easy', 'medium', 'hard'] : [$difficulty];
        $totalSynced = 0;

        foreach ($difficulties as $diff) {
            $questions = $this->openTdbService->fetchQuestionsForDifficulty($diff, 15);
            $totalSynced += count($questions);
        }

        return back()->with('success', "Synced {$totalSynced} questions from OpenTDB API!");
    }

    public function settings()
    {
        $settings = \App\Models\GameSetting::getSettings();
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'easy_correct_points' => 'required|integer|min:1|max:100',
            'easy_wrong_penalty' => 'required|integer|min:0|max:100',
            'easy_timer_seconds' => 'required|integer|min:3|max:60',

            'medium_correct_points' => 'required|integer|min:1|max:100',
            'medium_wrong_penalty' => 'required|integer|min:0|max:100',
            'medium_timer_seconds' => 'required|integer|min:3|max:60',

            'hard_correct_points' => 'required|integer|min:1|max:100',
            'hard_wrong_penalty' => 'required|integer|min:0|max:100',
            'hard_timer_seconds' => 'required|integer|min:3|max:60',

            'entry_fee' => 'required|integer|min:0|max:1000',
            'welcome_bonus' => 'required|integer|min:0|max:5000',
            'streak_3_bonus' => 'required|integer|min:0|max:50',
            'streak_5_bonus' => 'required|integer|min:0|max:50',
            'streak_8_bonus' => 'required|integer|min:0|max:100',
        ]);

        foreach ($validated as $key => $val) {
            \App\Models\GameSetting::setVal($key, $val);
        }

        return back()->with('success', 'Game pointing system & economy rules have been updated successfully!');
    }

    public function resetSettings()
    {
        \App\Models\GameSetting::truncate();
        return back()->with('success', 'Game pointing system rules have been reset to factory defaults.');
    }
}
