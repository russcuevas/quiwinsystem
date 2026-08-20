<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\PointTransaction;
use App\Models\Question;
use App\Models\User;
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
        $totalGames = GameSession::count();
        $totalCompletedGames = GameSession::where('status', 'completed')->count();
        $totalPointsInCirculation = User::where('role', 'user')->sum('points');
        $totalQuestionsInDb = Question::count();

        // Top players
        $topPlayers = User::where('role', 'user')
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

    public function users(Request $request)
    {
        $search = $request->input('search');
        $query = User::where('role', 'user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderByDesc('points')->paginate(15);

        return view('admin.users', compact('users', 'search'));
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
