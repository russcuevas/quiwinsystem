<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function home()
    {
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

        // Ensure player has a unique referral code
        if (!$user->referral_code) {
            do {
                $uniqueCode = 'QUI-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
            } while (User::where('referral_code', $uniqueCode)->exists());
            $user->referral_code = $uniqueCode;
            $user->save();
        }

        // Referral Quest Stats
        $approvedReferralsCount = $user->approvedReferrals()->count();
        $pendingReferralsCount = $user->pendingReferrals()->count();
        $referralQuestTarget = 5;
        $referralQuestProgress = min($approvedReferralsCount, $referralQuestTarget);
        $referralsList = $user->referrals()->latest()->take(6)->get();

        $entryFee = (int) \App\Models\GameSetting::getVal('entry_fee', 50);

        return view('user.home', compact(
            'user',
            'activeSession',
            'recentGames',
            'leaderboard',
            'totalGames',
            'accuracy',
            'bestStreak',
            'transactions',
            'entryFee',
            'approvedReferralsCount',
            'pendingReferralsCount',
            'referralQuestTarget',
            'referralQuestProgress',
            'referralsList'
        ));
    }

    public function topUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:10|max:10000',
        ]);

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
}
