<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\PointTransaction;
use App\Models\Question;
use App\Models\SessionAnswer;
use App\Models\User;
use App\Services\OpenTdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    protected OpenTdbService $openTdbService;

    public function __construct(OpenTdbService $openTdbService)
    {
        $this->openTdbService = $openTdbService;
    }

    /**
     * Start a new game session (Always fresh from scratch - no resuming permitted).
     */
    public function start(Request $request)
    {
        $user = Auth::user();

        // Abandon any prior uncompleted sessions so users cannot cheat or resume
        GameSession::where('user_id', $user->id)
            ->whereIn('status', ['active', 'bankrupt_paused'])
            ->update(['status' => 'abandoned']);

        $settings = \App\Models\GameSetting::getSettings();
        $entryFee = (int) ($settings['entry_fee'] ?? 50);

        // Check if user has at least entry_fee points to enter
        if ($user->points < $entryFee) {
            return redirect()->route('user.home')->with('error', "Insufficient points! A game entry requires {$entryFee} points. Please add points to play.");
        }

        // Deduct entry fee
        DB::beginTransaction();
        try {
            $user->points -= $entryFee;
            $user->save();

            // Fetch 30 unique questions for this user
            $questions = $this->openTdbService->getGameQuestions($user->id);

            $session = GameSession::create([
                'user_id' => $user->id,
                'start_points' => $user->points + $entryFee,
                'entry_fee' => $entryFee,
                'current_round' => 1,
                'current_question_index' => 1,
                'total_correct' => 0,
                'total_incorrect' => 0,
                'max_streak' => 0,
                'current_streak' => 0,
                'points_delta' => 0,
                'status' => 'active',
                'questions_data' => $questions,
                'answers_history' => [],
            ]);

            PointTransaction::create([
                'user_id' => $user->id,
                'game_session_id' => $session->id,
                'type' => 'game_entry',
                'amount' => -$entryFee,
                'balance_after' => $user->points,
                'description' => "Game Entry Fee (-{$entryFee} points) [Match #{$session->id}]",
            ]);

            DB::commit();

            return redirect()->route('game.play', ['sessionId' => $session->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('user.home')->with('error', 'Failed to initialize game: ' . $e->getMessage());
        }
    }

    /**
     * Show game arena page.
     */
    public function play($sessionId)
    {
        $user = Auth::user();
        $session = GameSession::where('id', $sessionId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($session->status === 'completed') {
            return redirect()->route('game.summary', ['sessionId' => $session->id]);
        }

        if ($session->status === 'abandoned') {
            return redirect()->route('user.home')->with('error', 'This match was cancelled/disqualified because you left the arena or switched tabs. Please start a new match.');
        }

        return view('game.play', compact('session', 'user'));
    }

    /**
     * API: Get current question and session state.
     */
    public function getState($sessionId)
    {
        $user = Auth::user();
        $session = GameSession::where('id', $sessionId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $currentIndex = $session->current_question_index;
        $questions = $session->questions_data ?? [];
        $currentQuestion = null;

        if ($currentIndex > 30 || (count($questions) > 0 && $currentIndex > count($questions))) {
            $session->status = 'completed';
            $session->save();
        } else {
            foreach ($questions as $q) {
                if ($q['index'] === $currentIndex) {
                    $currentQuestion = [
                        'index' => $q['index'],
                        'round' => $q['round'],
                        'difficulty' => $q['difficulty'],
                        'category' => $q['category'],
                        'question_text' => $q['question_text'],
                        'choices' => $q['choices'],
                    ];
                    break;
                }
            }
        }

        $settings = \App\Models\GameSetting::getSettings();
        $timerSeconds = ($session->current_round === 1)
            ? ($settings['easy_timer_seconds'] ?? 5)
            : (($session->current_round === 2) ? ($settings['medium_timer_seconds'] ?? 5) : ($settings['hard_timer_seconds'] ?? 5));

        return response()->json([
            'session_id' => $session->id,
            'status' => $session->status,
            'current_round' => $session->current_round,
            'current_question_index' => $session->current_question_index,
            'total_questions' => count($questions),
            'total_correct' => $session->total_correct,
            'total_incorrect' => $session->total_incorrect,
            'current_streak' => $session->current_streak,
            'max_streak' => $session->max_streak,
            'points_delta' => $session->points_delta,
            'user_points' => $user->points,
            'timer_seconds' => $timerSeconds,
            'settings' => $settings,
            'question' => $currentQuestion,
        ]);
    }

    /**
     * API: Submit answer for current question.
     */
    public function submitAnswer(Request $request, $sessionId)
    {
        $user = Auth::user();
        $session = GameSession::where('id', $sessionId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($session->status === 'completed') {
            return response()->json(['error' => 'Game is already completed.'], 400);
        }

        $userAnswer = $request->input('answer'); // null if timed out
        $currentIndex = $session->current_question_index;
        $questions = $session->questions_data ?? [];

        $targetQuestion = null;
        foreach ($questions as $q) {
            if ($q['index'] === $currentIndex) {
                $targetQuestion = $q;
                break;
            }
        }

        if (!$targetQuestion) {
            return response()->json(['error' => 'Question not found.'], 404);
        }

        $round = $targetQuestion['round'];
        $difficulty = $targetQuestion['difficulty'];
        $correctAnswer = $targetQuestion['correct_answer'];
        $isCorrect = ($userAnswer !== null && trim((string)$userAnswer) === trim((string)$correctAnswer));

        // Dynamic scoring settings
        $settings = \App\Models\GameSetting::getSettings();
        $basePoints = 0;
        $streakBonus = 0;

        if ($isCorrect) {
            if ($round === 1) {
                $basePoints = (int) ($settings['easy_correct_points'] ?? 2);
            } elseif ($round === 2) {
                $basePoints = (int) ($settings['medium_correct_points'] ?? 3);
            } else {
                $basePoints = (int) ($settings['hard_correct_points'] ?? 5);
            }

            $newStreak = $session->current_streak + 1;
            // Dynamic Streak Multiplier bonus
            if ($newStreak >= 8) {
                $streakBonus = (int) ($settings['streak_8_bonus'] ?? 5);
            } elseif ($newStreak >= 5) {
                $streakBonus = (int) ($settings['streak_5_bonus'] ?? 2);
            } elseif ($newStreak >= 3) {
                $streakBonus = (int) ($settings['streak_3_bonus'] ?? 1);
            }

            $pointsAwarded = $basePoints + $streakBonus;
            $session->total_correct += 1;
            $session->current_streak = $newStreak;
            if ($newStreak > $session->max_streak) {
                $session->max_streak = $newStreak;
            }
        } else {
            if ($round === 1) {
                $pointsAwarded = -abs((int) ($settings['easy_wrong_penalty'] ?? 3));
            } elseif ($round === 2) {
                $pointsAwarded = -abs((int) ($settings['medium_wrong_penalty'] ?? 5));
            } else {
                $pointsAwarded = -abs((int) ($settings['hard_wrong_penalty'] ?? 10));
            }

            $session->total_incorrect += 1;
            $session->current_streak = 0; // Reset streak on error
        }

        DB::beginTransaction();
        try {
            // Update user balance and points delta
            $session->points_delta += $pointsAwarded;
            $user->points += $pointsAwarded;
            $user->save();

            // Record session answer
            SessionAnswer::create([
                'game_session_id' => $session->id,
                'user_id' => $user->id,
                'question_id' => $targetQuestion['id'] ?? null,
                'question_index' => $currentIndex,
                'round' => $round,
                'difficulty' => $difficulty,
                'user_answer' => $userAnswer ?? '[Timed Out]',
                'is_correct' => $isCorrect,
                'points_awarded' => $pointsAwarded,
                'streak_at_answer' => $session->current_streak,
            ]);

            // Append to answer history in session
            $history = $session->answers_history ?? [];
            $history[] = [
                'index' => $currentIndex,
                'round' => $round,
                'difficulty' => $difficulty,
                'question_text' => $targetQuestion['question_text'],
                'user_answer' => $userAnswer,
                'correct_answer' => $correctAnswer,
                'is_correct' => $isCorrect,
                'points_awarded' => $pointsAwarded,
                'streak' => $session->current_streak,
            ];
            $session->answers_history = $history;

            // Check for bankruptcy (points <= 0)
            $isBankrupt = false;
            if ($user->points <= 0) {
                $isBankrupt = true;
                $session->status = 'bankrupt_paused';
            }

            // Check round transition or game completion
            $isRoundBreak = false;
            $nextRound = $round;

            if ((int)$currentIndex === 10) {
                $isRoundBreak = true;
                $session->current_round = 2;
            } elseif ((int)$currentIndex === 20) {
                $isRoundBreak = true;
                $session->current_round = 3;
            }

            $isCompleted = false;
            if ($currentIndex >= 30) {
                $isCompleted = true;
                $session->status = 'completed';

                // Record final match transaction summary
                PointTransaction::create([
                    'user_id' => $user->id,
                    'game_session_id' => $session->id,
                    'type' => 'game_reward',
                    'amount' => $session->points_delta,
                    'balance_after' => $user->points,
                    'description' => "Quiwin Match #{$session->id} Completed (Score: {$session->total_correct}/30, Net Delta: " . ($session->points_delta >= 0 ? "+{$session->points_delta}" : "{$session->points_delta}") . ")",
                ]);
            } else {
                $session->current_question_index += 1;
            }

            $session->save();
            DB::commit();

            $nextQuestion = null;
            if (!$isCompleted) {
                foreach ($questions as $q) {
                    if ($q['index'] === $session->current_question_index) {
                        $nextQuestion = [
                            'index' => $q['index'],
                            'round' => $q['round'],
                            'difficulty' => $q['difficulty'],
                            'category' => $q['category'],
                            'question_text' => $q['question_text'],
                            'choices' => $q['choices'],
                        ];
                        break;
                    }
                }
            }

            $timerSeconds = ($session->current_round === 1)
                ? ($settings['easy_timer_seconds'] ?? 5)
                : (($session->current_round === 2) ? ($settings['medium_timer_seconds'] ?? 5) : ($settings['hard_timer_seconds'] ?? 5));

            return response()->json([
                'success' => true,
                'is_correct' => $isCorrect,
                'correct_answer' => $correctAnswer,
                'user_answer' => $userAnswer,
                'base_points' => $basePoints,
                'streak_bonus' => $streakBonus,
                'points_awarded' => $pointsAwarded,
                'current_streak' => $session->current_streak,
                'points_delta' => $session->points_delta,
                'user_points' => $user->points,
                'timer_seconds' => $timerSeconds,
                'settings' => $settings,
                'is_bankrupt' => $isBankrupt,
                'is_round_break' => $isRoundBreak,
                'is_completed' => $isCompleted,
                'next_round' => $session->current_round,
                'next_question_index' => $session->current_question_index,
                'next_question' => $nextQuestion,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to record answer: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: Top up points inside the game during bankruptcy pause.
     */
    public function topUpInGame(Request $request, $sessionId)
    {
        $request->validate([
            'amount' => 'required|integer|min:20|max:10000',
        ]);

        $user = Auth::user();
        $session = GameSession::where('id', $sessionId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $amount = (int) $request->amount;

        DB::beginTransaction();
        try {
            $user->points += $amount;
            $user->save();

            if ($user->points > 0 && $session->status === 'bankrupt_paused') {
                $session->status = 'active';
                $session->save();
            }

            PointTransaction::create([
                'user_id' => $user->id,
                'game_session_id' => $session->id,
                'type' => 'top_up',
                'amount' => $amount,
                'balance_after' => $user->points,
                'description' => "Emergency In-Game Top-Up (+{$amount} points)",
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully added {$amount} points! You can now continue your game.",
                'new_points' => $user->points,
                'status' => $session->status,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Top-up failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Summary page after completing 30 questions.
     */
    public function summary($sessionId)
    {
        $user = Auth::user();
        $session = GameSession::where('id', $sessionId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return view('game.summary', compact('session', 'user'));
    }

    /**
     * Abandon/Disqualify an active game session (e.g. Tab switch or forfeit).
     */
    public function abandon(Request $request, $sessionId)
    {
        $user = Auth::user();
        $session = GameSession::where('id', $sessionId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $reason = $request->input('reason', 'forfeited');

        if ($session->status !== 'completed') {
            $session->status = 'abandoned';
            $session->save();
        }

        $message = ($reason === 'tab_switched')
            ? 'Match disqualified! Anti-cheat detected tab switching or loss of window focus.'
            : 'Game match was forfeited.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('user.home'),
            ]);
        }

        return redirect()->route('user.home')->with('error', $message);
    }
}
