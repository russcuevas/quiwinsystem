<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\PointTransaction;
use App\Models\User;
use App\Models\UserMail;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function home()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Abandon any lingering active session when user leaves to home
        GameSession::where('user_id', $user->id)
            ->whereIn('status', ['active', 'bankrupt_paused'])
            ->update(['status' => 'abandoned']);

        $activeSession = null;

        // Recent completed game sessions
        $recentGames = GameSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->latest()
            ->take(5)
            ->get();

        // Leaderboard (Top 10 players by points)
        $leaderboard = User::where('role', 'user')
            ->orderByDesc('points')
            ->take(10)
            ->get();

        // User stats
        $totalGames = GameSession::where('user_id', $user->id)->where('status', 'completed')->count();
        $totalCorrect = GameSession::where('user_id', $user->id)->where('status', 'completed')->sum('total_correct');
        $totalQuestionsAnswered = GameSession::where('user_id', $user->id)->where('status', 'completed')->sum('total_correct') +
                                  GameSession::where('user_id', $user->id)->where('status', 'completed')->sum('total_incorrect');
        $accuracy = $totalQuestionsAnswered > 0 ? round(($totalCorrect / $totalQuestionsAnswered) * 100, 1) : 0;
        $bestStreak = GameSession::where('user_id', $user->id)->max('max_streak') ?? 0;

        // Recent point transactions
        $transactions = PointTransaction::where('user_id', $user->id)
            ->latest()
            ->take(8)
            ->get();

        // User Withdrawals History
        $withdrawals = Withdrawal::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        // User In-Game Mails
        $mails = UserMail::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();
        $unreadMailsCount = UserMail::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        // Ensure player has a unique referral code
        if (!$user->referral_code) {
            do {
                $uniqueCode = 'QUI-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
            } while (User::where('referral_code', $uniqueCode)->exists());
            $user->referral_code = $uniqueCode;
            $user->save();
        }

        // 7-Day Weekly Daily Play Quest Stats
        $activeDailyStreak = $user->getActiveDailyStreak();
        $playedToday = $user->hasPlayedToday();
        $weeklyQuestTarget = 7;
        $weeklyQuestProgress = $activeDailyStreak % 7;
        if ($activeDailyStreak > 0 && $weeklyQuestProgress === 0) {
            $weeklyQuestProgress = 7;
        }
        $weeklyQuestReward = (int) \App\Models\GameSetting::getVal('weekly_quest_reward', 300);
        $weeklyQuestClaims = (int) ($user->weekly_quest_claims ?? 0);

        // Referral Quest Stats
        $approvedReferralsCount = $user->approvedReferrals()->count();
        $pendingReferralsCount = $user->pendingReferrals()->count();
        $referralQuestTarget = 5;
        $referralQuestProgress = min($approvedReferralsCount, $referralQuestTarget);
        $referralQuestReward = (int) \App\Models\GameSetting::getVal('referral_quest_reward', 1000);
        $referralsList = $user->referrals()->latest()->take(6)->get();

        $entryFee = (int) \App\Models\GameSetting::getVal('entry_fee', 50);

        // Rank Rewards Claimed Array & Config
        $claimedRankRewards = $user->claimed_rank_rewards ?? [];
        $rankRewardsConfig = self::getRankTiersConfig();
        $minMatchesForRankReward = 20;

        return view('user.home', compact(
            'user',
            'activeSession',
            'recentGames',
            'leaderboard',
            'totalGames',
            'accuracy',
            'bestStreak',
            'transactions',
            'withdrawals',
            'mails',
            'unreadMailsCount',
            'entryFee',
            'activeDailyStreak',
            'playedToday',
            'weeklyQuestTarget',
            'weeklyQuestProgress',
            'weeklyQuestReward',
            'weeklyQuestClaims',
            'approvedReferralsCount',
            'pendingReferralsCount',
            'referralQuestTarget',
            'referralQuestProgress',
            'referralQuestReward',
            'referralsList',
            'claimedRankRewards',
            'rankRewardsConfig',
            'minMatchesForRankReward'
        ));
    }

    public static function getRankTiersConfig(): array
    {
        return [
            'challenger' => [
                'name' => 'Challenger',
                'min_points' => 200,
                'reward_points' => 20,
                'min_matches' => 20,
                'icon' => 'fa-shield',
                'color' => '#38bdf8',
            ],
            'veteran' => [
                'name' => 'Veteran',
                'min_points' => 500,
                'reward_points' => 50,
                'min_matches' => 20,
                'icon' => 'fa-bolt',
                'color' => '#fbbf24',
            ],
            'master' => [
                'name' => 'Master',
                'min_points' => 1000,
                'reward_points' => 100,
                'min_matches' => 20,
                'icon' => 'fa-crown',
                'color' => '#c084fc',
            ],
            'grandmaster' => [
                'name' => 'Grandmaster',
                'min_points' => 2500,
                'reward_points' => 500,
                'min_matches' => 20,
                'icon' => 'fa-gem',
                'color' => '#06b6d4',
            ],
            'legend' => [
                'name' => 'Legend',
                'min_points' => 5000,
                'reward_points' => 1000,
                'min_matches' => 20,
                'icon' => 'fa-fire-flame-curved',
                'color' => '#f43f5e',
            ],
        ];
    }

    public function claimRankReward(Request $request)
    {
        $request->validate([
            'tier' => 'required|string|in:challenger,veteran,master,grandmaster,legend',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $tierKey = strtolower($request->tier);
        $tiersConfig = self::getRankTiersConfig();

        if (!isset($tiersConfig[$tierKey])) {
            $msg = 'Invalid rank tier selected.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $tierData = $tiersConfig[$tierKey];

        // 1. Check if already claimed
        if ($user->hasClaimedRankReward($tierKey)) {
            $msg = "You have already claimed the {$tierData['name']} milestone reward!";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        // 2. Anti-Abuse Check: Must have completed at least 20 matches (prevents top-up-and-claim exploit)
        $completedGames = GameSession::where('user_id', $user->id)->where('status', 'completed')->count();
        if ($completedGames < $tierData['min_matches']) {
            $msg = "Anti-Abuse Requirement: You must play at least {$tierData['min_matches']} matches before claiming rank rewards! (You have played {$completedGames}/{$tierData['min_matches']} matches).";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        // 3. Points Requirement: Must have reached the tier's minimum points
        if ($user->points < $tierData['min_points']) {
            $msg = "You have not reached {$tierData['name']} rank yet. You need at least " . number_format($tierData['min_points']) . " PTS. (Current: " . number_format($user->points) . " PTS).";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        // 4. Award Reward Points
        $reward = (int) $tierData['reward_points'];
        $user->points += $reward;

        // Save claimed tier in array
        $claimed = $user->claimed_rank_rewards ?? [];
        $claimed[] = $tierKey;
        $user->claimed_rank_rewards = array_values(array_unique($claimed));
        $user->save();

        // 5. Record Point Transaction Ledger
        PointTransaction::create([
            'user_id' => $user->id,
            'game_session_id' => null,
            'type' => 'rank_reward',
            'amount' => $reward,
            'balance_after' => $user->points,
            'description' => "Rank Milestone Reward: Reached {$tierData['name']} (+{$reward} PTS)",
        ]);

        // 6. Send In-Game Notification Mail
        UserMail::create([
            'user_id' => $user->id,
            'title' => "Rank Reward Claimed: {$tierData['name']} 🏆",
            'message' => "Congratulations! You have claimed +{$reward} bonus points for unlocking the {$tierData['name']} rank tier after completing {$completedGames} matches!",
            'type' => 'system',
            'is_read' => false,
        ]);

        $successMsg = "🎉 Congratulations! You claimed +{$reward} PTS for reaching {$tierData['name']} rank!";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'tier' => $tierKey,
                'reward_points' => $reward,
                'new_points' => $user->points,
                'claimed_rank_rewards' => $user->claimed_rank_rewards,
                'message' => $successMsg,
            ]);
        }

        return back()->with('success', $successMsg);
    }

    public function topUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:10|max:10000',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $amount = (int) $request->amount;
        $user->points += $amount;
        $user->save();

        PointTransaction::create([
            'user_id' => $user->id,
            'game_session_id' => null,
            'type' => 'top_up',
            'amount' => $amount,
            'balance_after' => $user->points,
            'description' => "Account Top-Up (+{$amount} points)",
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'new_points' => $user->points,
                'message' => "Successfully added {$amount} points! New balance: {$user->points} points.",
            ]);
        }

        return back()->with('success', "Successfully added {$amount} points to your account!");
    }

    public function withdraw(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Balance constraint: Cannot withdraw if balance is below 500
        if ($user->points < 500) {
            $msg = "You cannot withdraw. Minimum required balance to withdraw is 500 PTS (₱500). Your current balance is " . number_format($user->points) . " PTS.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        // 2. Form validation
        $request->validate([
            'amount' => [
                'required',
                'integer',
                'min:500',
                'max:' . $user->points,
            ],
            'gcash_number' => [
                'required',
                'string',
                'regex:/^09\d{9}$/',
            ],
            'gcash_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],
        ], [
            'amount.min' => 'The minimum withdrawal amount is 500 pesos (500 PTS).',
            'amount.max' => "You cannot withdraw more than your current balance of " . number_format($user->points) . " PTS.",
            'gcash_number.regex' => 'GCash number must start with 09 and contain exactly 11 digits (e.g. 09123456789).',
            'gcash_name.required' => 'Please provide the registered GCash account name.',
        ]);

        $amount = (int) $request->amount;

        // 3. Create Pending Withdrawal (NO deduction yet - wala pang bawas)
        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'payment_method' => 'gcash',
            'gcash_number' => $request->gcash_number,
            'gcash_name' => trim($request->gcash_name),
            'status' => 'pending',
        ]);

        // 4. Create confirmation notification in player's mail
        UserMail::create([
            'user_id' => $user->id,
            'title' => 'Withdrawal Request Submitted ⏳',
            'message' => "Your withdrawal request for ₱" . number_format($amount) . " to GCash ({$request->gcash_number} - {$request->gcash_name}) has been submitted to Admin. Note: Points will remain in your account and will only be deducted once approved and sent by Admin.",
            'type' => 'system',
            'is_read' => false,
        ]);

        $successMsg = "Withdrawal request of ₱" . number_format($amount) . " to GCash {$request->gcash_number} ({$request->gcash_name}) has been submitted! Your points will be deducted once approved by Admin.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'withdrawal' => $withdrawal,
            ]);
        }

        return back()->with('success', $successMsg);
    }

    public function markMailRead($mailId)
    {
        $mail = UserMail::where('user_id', Auth::id())->findOrFail($mailId);
        $mail->is_read = true;
        $mail->save();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Marked as read.');
    }

    public function markAllMailsRead()
    {
        UserMail::where('user_id', Auth::id())->where('is_read', false)->update(['is_read' => true]);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }
}
