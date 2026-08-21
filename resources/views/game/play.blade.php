@extends('layouts.app')

@section('title', 'Quiwin Arena - Match #' . $session->id)

@php
    $settings = $settings ?? \App\Models\GameSetting::getSettings();
    $easyCorrect = $settings['easy_correct_points'] ?? 2;
    $easyWrong = $settings['easy_wrong_penalty'] ?? 3;
    $medCorrect = $settings['medium_correct_points'] ?? 3;
    $medWrong = $settings['medium_wrong_penalty'] ?? 5;
    $hardCorrect = $settings['hard_correct_points'] ?? 5;
    $hardWrong = $settings['hard_wrong_penalty'] ?? 10;
@endphp

@section('content')
    <div style="max-width: 900px; margin: 0 auto; position: relative; padding-bottom: 3.5rem;">

        <!-- Top Arena Status Bar (Round, Points, Streak, Live Balance) -->
        <div class="glass-card arena-top-bar"
            style="padding: clamp(0.75rem, 2vw, 1.25rem) clamp(0.85rem, 2.5vw, 1.75rem); margin-bottom: 1rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.65rem; border: 1px solid rgba(255,255,255,0.12);">

            <!-- Round & Difficulty Badge -->
            <div style="display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap;">
                <div id="round-badge"
                    style="padding: 0.35rem 0.75rem; border-radius: 9999px; font-weight: 800; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399; white-space: nowrap;">
                    <i class="fa-solid fa-layer-group"></i> <span id="round-text">ROUND 1 &bull; EASY</span>
                </div>

                <div id="category-badge"
                    style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; color: #cbd5e1; font-weight: 600; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <i class="fa-solid fa-tag text-cyan-400"></i> <span id="category-text">Loading Category...</span>
                </div>
            </div>

            <!-- Right Side: Streak & Live Score / Points -->
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <!-- Streak Flame -->
                <div id="streak-container"
                    style="display: flex; align-items: center; gap: 0.35rem; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.35); padding: 0.35rem 0.65rem; border-radius: 9999px; font-weight: 800; font-size: 0.82rem; color: #fbbf24; transition: transform 0.2s ease;">
                    <i class="fa-solid fa-fire text-amber-400" id="streak-icon"></i>
                    <span id="streak-count">0</span> STREAK
                </div>

                <!-- Live Net Delta -->
                <div
                    style="display: flex; align-items: center; gap: 0.35rem; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); padding: 0.35rem 0.65rem; border-radius: 9999px; font-weight: 800; font-size: 0.82rem; color: #a5b4fc;">
                    Score: <span id="points-delta" style="font-weight: 900; margin-left: 0.15rem;">0</span> PTS
                </div>

                <!-- Player Wallet Balance -->
                <div class="points-badge" style="padding: 0.35rem 0.65rem; font-size: 0.82rem;">
                    <i class="fa-solid fa-coins"></i>
                    <span id="player-balance">{{ number_format($user->points) }}</span>
                </div>
            </div>

        </div>

        <!-- Progress Bar (1 to 30) -->
        <div style="margin-bottom: 1rem;">
            <div
                style="display: flex; justify-content: space-between; font-size: 0.78rem; font-weight: 700; color: #94a3b8; margin-bottom: 0.35rem;">
                <span>QUESTION <span id="question-index-text" style="color: #fff; font-size: 0.95rem;">1</span> OF 30</span>
                <span id="round-indicator-text" style="color: #34d399;">Round 1: +{{ $easyCorrect }} / -{{ $easyWrong }} PTS</span>
            </div>
            <div
                style="width: 100%; height: 7px; background: rgba(255,255,255,0.08); border-radius: 9999px; overflow: hidden; position: relative;">
                <div id="progress-bar-fill"
                    style="height: 100%; width: 3.33%; background: linear-gradient(90deg, #6366f1, #06b6d4); border-radius: 9999px; transition: width 0.4s ease;">
                </div>
            </div>
        </div>

        <!-- Main Question Arena Card -->
        <div id="main-arena-card" class="glass-card arena-card"
            style="padding: clamp(1.25rem, 3vw, 2.5rem); position: relative; border: 1px solid rgba(99, 102, 241, 0.3); box-shadow: 0 16px 48px rgba(0,0,0,0.5); transition: border-color 0.25s ease, box-shadow 0.25s ease;">

            <!-- Streak Broken Alert Banner (Hidden by default) -->
            <div id="streak-broken-pop"
                style="position: absolute; top: -16px; left: 50%; transform: translateX(-50%); z-index: 60; display: none; pointer-events: none; width: max-content; max-width: 90%;">
                <div
                    style="background: linear-gradient(135deg, #ef4444, #b91c1c); color: #fff; font-weight: 800; font-size: 0.85rem; padding: 0.35rem 1rem; border-radius: 9999px; box-shadow: 0 0 24px rgba(239, 68, 68, 0.7); border: 1px solid #fca5a5; display: flex; align-items: center; gap: 0.4rem; letter-spacing: 0.3px;">
                    <i class="fa-solid fa-heart-crack text-amber-300"></i>
                    <span id="streak-broken-text">STREAK BROKEN!</span>
                </div>
            </div>

            <!-- Countdown Timer Ring -->
            <div style="display: flex; justify-content: center; margin-bottom: 1.25rem;">
                <div class="timer-wrapper"
                    style="position: relative; width: 76px; height: 76px; display: flex; align-items: center; justify-content: center;">
                    <svg width="76" height="76" viewBox="0 0 84 84"
                        style="transform: rotate(-90deg); width: 100%; height: 100%;">
                        <circle cx="42" cy="42" r="36" stroke="rgba(255,255,255,0.1)" stroke-width="6"
                            fill="transparent" />
                        <circle id="timer-circle" cx="42" cy="42" r="36" stroke="#10b981" stroke-width="6"
                            fill="transparent" stroke-dasharray="226" stroke-dashoffset="0" stroke-linecap="round"
                            style="transition: stroke-dashoffset 0.1s linear, stroke 0.3s ease;" />
                    </svg>
                    <div id="timer-number"
                        style="position: absolute; font-size: 1.6rem; font-weight: 900; color: #fff; font-family: 'Space Grotesk', sans-serif;">
                        0
                    </div>
                </div>
            </div>

            <!-- Question Prompt -->
            <div
                style="min-height: 60px; display: flex; align-items: center; justify-content: center; text-align: center; margin-bottom: 1.5rem;">
                <h2 id="question-text"
                    style="font-size: clamp(1.05rem, 3.5vw, 1.35rem); font-weight: 700; color: #fff; line-height: 1.45;">
                    Fetching questions...
                </h2>
            </div>

            <!-- Streak Bonus Announcement Pop (Hidden by default) -->
            <div id="combo-pop"
                style="text-align: center; height: 28px; margin-bottom: 0.75rem; opacity: 0; transition: all 0.3s ease;">
                <span id="combo-text"
                    style="background: linear-gradient(135deg, #f59e0b, #ef4444); color: #fff; font-weight: 800; font-size: 0.85rem; padding: 0.25rem 0.75rem; border-radius: 9999px; box-shadow: 0 0 16px rgba(245, 158, 11, 0.5);">
                    🔥 3x COMBO BONUS (+1 PTS)!
                </span>
            </div>

            <!-- 4 Choices Grid -->
            <div id="choices-grid" class="choices-container"
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 0.75rem;">
                <!-- Choice buttons will be dynamically rendered here -->
            </div>

            <!-- Feedback Result Message under choices -->
            <div id="feedback-container"
                style="margin-top: 1.25rem; text-align: center; min-height: 28px; font-weight: 700; font-size: 1rem; opacity: 0; transition: opacity 0.2s ease;">
                <span id="feedback-text"></span>
            </div>

        </div>

        <!-- Bottom Actions: Forfeit / Sound Toggle -->
        <div
            style="display: flex; align-items: center; justify-content: space-between; margin-top: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <form action="{{ route('game.abandon', ['sessionId' => $session->id]) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to forfeit this match? Points earned or lost will be finalized.')">
                @csrf
                <button type="submit" class="btn btn-outline"
                    style="color: #94a3b8; font-size: 0.8rem; padding: 0.4rem 0.75rem; min-height: 36px;">
                    <i class="fa-solid fa-flag"></i> Forfeit Match
                </button>
            </form>

            <div style="display: flex; align-items: center; gap: 0.4rem; color: #64748b; font-size: 0.8rem;">
                <i class="fa-solid fa-volume-high text-cyan-400"></i> Audio FX Active
            </div>
        </div>

    </div>

    <!-- INTERMISSION / ROUND BREAK MODAL (After Q10 and Q20) -->
    <div id="roundBreakModal" class="modal-overlay">
        <div class="modal-card" style="text-align: center; max-width: 520px; border: 1px solid rgba(99, 102, 241, 0.3);">
            <div
                style="width: 64px; height: 64px; margin: 0 auto 1.25rem; background: linear-gradient(135deg, #6366f1, #06b6d4); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; box-shadow: 0 0 24px rgba(99, 102, 241, 0.5);">
                <i class="fa-solid fa-forward-step"></i>
            </div>

            <h2 id="break-title" style="font-size: 1.75rem; font-weight: 900; color: #fff; margin-bottom: 0.5rem;">Round 1
                Complete!</h2>
            <p id="break-subtitle" style="color: #cbd5e1; font-size: 0.95rem; margin-bottom: 1.5rem;">
                Take a breather. The difficulty is increasing for the next round!
            </p>

            <!-- Round Stats Card -->
            <div
                style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 1rem; padding: 1.25rem; margin-bottom: 1.5rem; display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;">
                <div>
                    <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Correct</div>
                    <div id="break-correct" style="font-size: 1.35rem; font-weight: 800; color: #34d399;">0</div>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Current Streak</div>
                    <div id="break-streak" style="font-size: 1.35rem; font-weight: 800; color: #fbbf24;">0 🔥</div>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Net Points</div>
                    <div id="break-points" style="font-size: 1.35rem; font-weight: 800; color: #818cf8;">0 PTS</div>
                </div>
            </div>

            <!-- Next Round Preview Banner -->
            <div id="next-round-banner"
                style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.35); border-radius: 0.85rem; padding: 0.85rem; margin-bottom: 1.5rem; text-align: left; font-size: 0.85rem; color: #fef3c7;">
                <strong>Next: Round 2 (Questions 11-20)</strong> &bull; Medium difficulty &bull; <span
                    style="color: #34d399;">+{{ $medCorrect }} PTS</span> / <span style="color: #fb7185;">-{{ $medWrong }} PTS</span>
            </div>

            <button type="button" class="btn btn-primary"
                style="width: 100%; padding: 0.9rem; font-size: 1.1rem; font-weight: 700; border-radius: 1rem;"
                onclick="proceedToNextRound()">
                <i class="fa-solid fa-play"></i> Start Next Round
            </button>
        </div>
    </div>

    <!-- BANKRUPTCY / OUT OF POINTS LIVE MODAL -->
    <div id="bankruptcyModal" class="modal-overlay">
        <div class="modal-card"
            style="text-align: center; max-width: 480px; border: 2px solid #ef4444; box-shadow: 0 0 40px rgba(239, 68, 68, 0.4);">
            <div
                style="width: 68px; height: 68px; margin: 0 auto 1.25rem; background: rgba(239, 68, 68, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 2rem; animation: pulse 1.5s infinite;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <h2 style="font-size: 1.6rem; font-weight: 900; color: #fff; margin-bottom: 0.4rem;">You Ran Out of Points!
            </h2>
            <p style="color: #fca5a5; font-size: 0.95rem; margin-bottom: 1.5rem; line-height: 1.4;">
                You cannot continue playing because your points balance has reached <strong>0 or below</strong>. Please add
                points to proceed with your match!
            </p>

            <!-- Quick selection -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.6rem; margin-bottom: 1.25rem;">
                <button type="button" class="btn btn-outline"
                    style="border-color: rgba(239, 68, 68, 0.4); font-size: 0.9rem; font-weight: 700; color: #fca5a5;"
                    onclick="setBankruptTopUp(100)">
                    +100 PTS
                </button>
                <button type="button" class="btn btn-outline"
                    style="border-color: rgba(239, 68, 68, 0.4); font-size: 0.9rem; font-weight: 700; color: #fca5a5;"
                    onclick="setBankruptTopUp(200)">
                    +200 PTS
                </button>
                <button type="button" class="btn btn-outline"
                    style="border-color: rgba(239, 68, 68, 0.4); font-size: 0.9rem; font-weight: 700; color: #fca5a5;"
                    onclick="setBankruptTopUp(500)">
                    +500 PTS
                </button>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label
                    style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem; text-align: left;">
                    Enter Points to Add:
                </label>
                <input type="number" id="bankruptPointsInput" value="100" min="20" max="10000"
                    class="form-input"
                    style="border-color: rgba(239, 68, 68, 0.5); font-size: 1.1rem; font-weight: 700; color: #fbbf24; text-align: center;">
            </div>

            <button type="button" id="bankruptConfirmBtn" class="btn btn-gold"
                style="width: 100%; padding: 0.9rem; font-size: 1.05rem;" onclick="submitBankruptTopUp()">
                <i class="fa-solid fa-coins"></i> Confirm & Resume Game
            </button>
        </div>
    </div>

    <!-- ANTI-CHEAT TAB SWITCH / DISQUALIFICATION MODAL -->
    <div id="antiCheatModal" class="modal-overlay">
        <div class="modal-card"
            style="text-align: center; max-width: 480px; border: 2px solid #ef4444; box-shadow: 0 0 50px rgba(239, 68, 68, 0.5);">
            <div
                style="width: 72px; height: 72px; margin: 0 auto 1.25rem; background: rgba(239, 68, 68, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 2.25rem; animation: pulse 1.2s infinite;">
                <i class="fa-solid fa-shield-virus"></i>
            </div>

            <h2 style="font-size: 1.65rem; font-weight: 900; color: #fff; margin-bottom: 0.5rem;">Anti-Cheat Violation!
            </h2>
            <p style="color: #fca5a5; font-size: 0.95rem; margin-bottom: 1.5rem; line-height: 1.5;">
                Tab switching or leaving the game window was detected. To prevent cheating, your match has been
                <strong>automatically cancelled</strong> and entry points forfeited.
            </p>

            <div
                style="background: rgba(255,255,255,0.05); border-radius: 0.85rem; padding: 0.85rem; font-size: 0.85rem; color: #cbd5e1; margin-bottom: 1.5rem;">
                Redirecting to Home Hub in <strong id="antiCheatCountdown" style="color: #fbbf24;">2</strong> seconds...
            </div>

            <a href="{{ route('user.home') }}" class="btn btn-danger"
                style="width: 100%; padding: 0.85rem; font-size: 1rem;">
                <i class="fa-solid fa-arrow-left"></i> Return to Home
            </a>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .choice-btn {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 1rem;
            padding: 1.15rem 1.25rem;
            color: #f1f5f9;
            font-size: 1rem;
            font-weight: 600;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            transition: transform 0.08s ease, background 0.12s ease, border-color 0.12s ease, box-shadow 0.12s ease, opacity 0.15s ease;
            width: 100%;
            outline: none;
            will-change: transform;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }

        .choice-btn:hover:not(:disabled) {
            background: rgba(99, 102, 241, 0.15);
            border-color: rgba(99, 102, 241, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.2);
        }

        .choice-btn:active:not(:disabled),
        .choice-btn.selected {
            transform: scale(0.97) !important;
            border-color: #818cf8 !important;
            background: rgba(99, 102, 241, 0.35) !important;
            box-shadow: 0 0 16px rgba(99, 102, 241, 0.4) !important;
        }

        .choice-btn:disabled {
            cursor: default;
        }

        .choice-btn.correct {
            background: rgba(16, 185, 129, 0.25) !important;
            border-color: #10b981 !important;
            color: #34d399 !important;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.4) !important;
        }

        .choice-btn.wrong {
            background: rgba(244, 63, 94, 0.25) !important;
            border-color: #f43f5e !important;
            color: #fb7185 !important;
            box-shadow: 0 0 20px rgba(244, 63, 94, 0.4) !important;
        }

        .choice-letter {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
            flex-shrink: 0;
            color: #a5b4fc;
        }

        .choice-btn.correct .choice-letter {
            background: #10b981;
            color: #fff;
        }

        .choice-btn.wrong .choice-letter {
            background: #f43f5e;
            color: #fff;
        }

        /* ========================================================
                       ULTRA-SMOOTH GPU-ACCELERATED GAMEPLAY ANIMATIONS
                       ======================================================== */

        /* 1. Correct Answer: Smooth Emerald Glow & Pop */
        @keyframes choiceCorrectPop {
            0% {
                transform: scale3d(1, 1, 1);
            }

            40% {
                transform: scale3d(1.035, 1.035, 1);
                box-shadow: 0 0 30px rgba(16, 185, 129, 0.6);
            }

            100% {
                transform: scale3d(1, 1, 1);
            }
        }

        .choice-btn.correct {
            animation: choiceCorrectPop 0.4s ease-out !important;
            background: rgba(16, 185, 129, 0.28) !important;
            border-color: #10b981 !important;
            color: #34d399 !important;
            box-shadow: 0 0 25px rgba(16, 185, 129, 0.45) !important;
        }

        @keyframes correctArenaPulse {
            0% {
                border-color: rgba(99, 102, 241, 0.3);
            }

            35% {
                border-color: #10b981;
                box-shadow: 0 0 45px rgba(16, 185, 129, 0.45), 0 16px 48px rgba(0, 0, 0, 0.5);
            }

            100% {
                border-color: rgba(99, 102, 241, 0.3);
            }
        }

        .arena-correct-pulse {
            animation: correctArenaPulse 0.6s ease-out;
        }

        /* 2. Wrong Answer: Micro Shockwave Shake */
        @keyframes choiceWrongVibrate {

            0%,
            100% {
                transform: translate3d(0, 0, 0);
            }

            20% {
                transform: translate3d(-6px, 0, 0);
            }

            40% {
                transform: translate3d(6px, 0, 0);
            }

            60% {
                transform: translate3d(-4px, 0, 0);
            }

            80% {
                transform: translate3d(4px, 0, 0);
            }
        }

        .choice-btn.wrong {
            animation: choiceWrongVibrate 0.4s ease-out !important;
            background: rgba(244, 63, 94, 0.28) !important;
            border-color: #f43f5e !important;
            color: #fb7185 !important;
            box-shadow: 0 0 25px rgba(244, 63, 94, 0.45) !important;
        }

        @keyframes arenaShake {

            0%,
            100% {
                transform: translate3d(0, 0, 0);
            }

            20% {
                transform: translate3d(-8px, -2px, 0);
                border-color: #ef4444;
                box-shadow: 0 0 40px rgba(239, 68, 68, 0.5);
            }

            40% {
                transform: translate3d(8px, 2px, 0);
            }

            60% {
                transform: translate3d(-5px, 1px, 0);
            }

            80% {
                transform: translate3d(5px, -1px, 0);
            }
        }

        .arena-wrong-shake {
            animation: arenaShake 0.55s cubic-bezier(0.36, 0.07, 0.19, 0.97);
        }

        /* 3. Floating Neon Score Particles (+PTS Up / -PTS Down) */
        @keyframes floatScoreAscend {
            0% {
                opacity: 0;
                transform: translate3d(-50%, 10px, 0) scale(0.7);
            }

            25% {
                opacity: 1;
                transform: translate3d(-50%, -15px, 0) scale(1.15);
                filter: drop-shadow(0 0 14px #34d399);
            }

            70% {
                opacity: 1;
                transform: translate3d(-50%, -38px, 0) scale(1);
            }

            100% {
                opacity: 0;
                transform: translate3d(-50%, -58px, 0) scale(0.85);
            }
        }

        @keyframes floatScoreDescend {
            0% {
                opacity: 0;
                transform: translate3d(-50%, -10px, 0) scale(0.7);
            }

            25% {
                opacity: 1;
                transform: translate3d(-50%, 15px, 0) scale(1.15);
                filter: drop-shadow(0 0 14px #f43f5e);
            }

            70% {
                opacity: 1;
                transform: translate3d(-50%, 38px, 0) scale(1);
            }

            100% {
                opacity: 0;
                transform: translate3d(-50%, 58px, 0) scale(0.85);
            }
        }

        .floating-score-chip {
            position: absolute;
            left: 50%;
            top: 40%;
            z-index: 70;
            pointer-events: none;
            will-change: transform, opacity;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.85rem;
            font-weight: 900;
            padding: 0.4rem 1.4rem;
            border-radius: 9999px;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .floating-score-chip.gain {
            animation: floatScoreAscend 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.8), 0 8px 24px rgba(0, 0, 0, 0.5);
            border: 2px solid #a7f3d0;
        }

        .floating-score-chip.loss {
            animation: floatScoreDescend 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
            box-shadow: 0 0 30px rgba(239, 68, 68, 0.8), 0 8px 24px rgba(0, 0, 0, 0.5);
            border: 2px solid #fecaca;
        }

        /* 4. Broken Streak Animation */
        @keyframes streakBrokenDrop {
            0% {
                opacity: 0;
                transform: translate3d(-50%, -20px, 0) scale(0.6);
            }

            30% {
                opacity: 1;
                transform: translate3d(-50%, 0, 0) scale(1.12);
            }

            60% {
                transform: translate3d(-50%, 0, 0) scale(0.98);
            }

            80% {
                opacity: 1;
                transform: translate3d(-50%, 0, 0) scale(1);
            }

            100% {
                opacity: 0;
                transform: translate3d(-50%, 15px, 0) scale(0.9);
            }
        }

        .streak-shatter-pop {
            display: block !important;
            animation: streakBrokenDrop 1.2s ease-out forwards;
        }

        @keyframes streakBadgeExtinguish {
            0% {
                transform: scale3d(1.25, 1.25, 1);
                filter: brightness(1.7);
            }

            30% {
                transform: scale3d(0.8, 0.8, 1) rotate(-8deg);
                filter: grayscale(1) brightness(0.6);
            }

            60% {
                transform: scale3d(1.05, 1.05, 1) rotate(8deg);
            }

            100% {
                transform: scale3d(1, 1, 1) rotate(0);
                filter: none;
            }
        }

        .streak-badge-shattered {
            animation: streakBadgeExtinguish 0.7s ease-out;
            background: rgba(239, 68, 68, 0.25) !important;
            border-color: #ef4444 !important;
            color: #f87171 !important;
        }

        /* 5. Streak Ignite Combo Animation */
        @keyframes streakIgnite {
            0% {
                transform: scale3d(1, 1, 1);
            }

            40% {
                transform: scale3d(1.22, 1.22, 1) rotate(3deg);
                box-shadow: 0 0 25px rgba(245, 158, 11, 0.8);
            }

            100% {
                transform: scale3d(1.08, 1.08, 1) rotate(0);
            }
        }

        .streak-ignited {
            animation: streakIgnite 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* 6. Question Content Fade & Slide Transition */
        @keyframes questionPop {
            0% {
                opacity: 0;
                transform: translate3d(0, 6px, 0);
            }

            100% {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .question-fade-in {
            animation: questionPop 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @media (max-width: 640px) {
            .arena-top-bar {
                padding: 0.5rem 0.65rem !important;
                margin-bottom: 0.5rem !important;
                gap: 0.35rem !important;
            }

            .arena-card {
                padding: 0.85rem 0.65rem !important;
                margin-bottom: 0.75rem !important;
            }

            .timer-wrapper {
                width: 60px !important;
                height: 60px !important;
            }

            .timer-wrapper svg {
                width: 60px !important;
                height: 60px !important;
            }

            #timer-number {
                font-size: 1.35rem !important;
            }

            #question-text {
                font-size: 0.95rem !important;
                line-height: 1.35 !important;
            }

            .choices-container {
                grid-template-columns: 1fr !important;
                gap: 0.45rem !important;
            }

            .choice-btn {
                padding: 0.65rem 0.8rem !important;
                min-height: 44px !important;
                font-size: 0.88rem !important;
                gap: 0.55rem !important;
                border-radius: 0.75rem !important;
            }

            .choice-letter {
                width: 28px !important;
                height: 28px !important;
                font-size: 0.8rem !important;
            }

            .floating-score-chip {
                font-size: 1.3rem !important;
                padding: 0.25rem 0.85rem !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        const sessionId = {{ $session->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentSettings = @json($settings);

        let currentQuestionData = null;
        let timerInterval = null;
        let totalTime = parseFloat(currentSettings.easy_timer_seconds || 5.0);
        let timeLeft = totalTime;
        let isAnsweringLocked = false;
        let isPausedForBreak = false;
        let isPausedForBankruptcy = false;

        // Load initial question state
        document.addEventListener('DOMContentLoaded', () => {
            fetchQuestionState();
        });

        async function fetchQuestionState() {
            try {
                const res = await fetch(`/game/${sessionId}/state`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await res.json();

                if (data.status === 'completed') {
                    window.location.href = `/game/${sessionId}/summary`;
                    return;
                }

                if (data.status === 'bankrupt_paused' || data.user_points <= 0) {
                    showBankruptcyModal();
                    return;
                }

                updateHUD(data);
                if (data.question) {
                    renderQuestion(data.question);
                }
            } catch (err) {
                console.error('Failed to load question state:', err);
            }
        }

        function updateHUD(data) {
            if (!data) return;

            if (data.settings) {
                currentSettings = Object.assign({}, currentSettings, data.settings);
            }

            const qIndexEl = document.getElementById('question-index-text');
            if (qIndexEl && data.current_question_index) qIndexEl.textContent = data.current_question_index;

            const pointsDeltaEl = document.getElementById('points-delta');
            if (pointsDeltaEl && data.points_delta !== undefined) {
                pointsDeltaEl.textContent = data.points_delta >= 0 ? '+' + data.points_delta : data.points_delta;
                pointsDeltaEl.style.color = data.points_delta >= 0 ? '#34d399' : '#fb7185';
            }

            const playerBalanceEl = document.getElementById('player-balance');
            if (playerBalanceEl && data.user_points !== undefined) {
                playerBalanceEl.textContent = Number(data.user_points).toLocaleString();
            }

            const streakCountEl = document.getElementById('streak-count');
            if (streakCountEl && data.current_streak !== undefined) {
                streakCountEl.textContent = data.current_streak;
            }

            // Progress bar (30 total questions)
            const progressFill = document.getElementById('progress-bar-fill');
            if (progressFill && data.current_question_index) {
                const progressPct = (data.current_question_index / 30) * 100;
                progressFill.style.width = `${progressPct}%`;
            }

            const round = data.current_round || 1;

            if (data.timer_seconds && parseFloat(data.timer_seconds) > 0) {
                totalTime = parseFloat(data.timer_seconds);
            } else {
                if (round === 1) totalTime = parseFloat(currentSettings.easy_timer_seconds || 5.0);
                else if (round === 2) totalTime = parseFloat(currentSettings.medium_timer_seconds || 5.0);
                else totalTime = parseFloat(currentSettings.hard_timer_seconds || 5.0);
            }

            const easyCorrect = currentSettings.easy_correct_points || 2;
            const easyWrong = currentSettings.easy_wrong_penalty || 3;
            const medCorrect = currentSettings.medium_correct_points || 3;
            const medWrong = currentSettings.medium_wrong_penalty || 5;
            const hardCorrect = currentSettings.hard_correct_points || 5;
            const hardWrong = currentSettings.hard_wrong_penalty || 10;

            // Update Round Badge
            const roundBadge = document.getElementById('round-badge');
            const roundText = document.getElementById('round-text');
            const roundIndicator = document.getElementById('round-indicator-text');

            if (roundBadge && roundText && roundIndicator) {
                if (round === 1) {
                    roundBadge.style.background = 'rgba(16, 185, 129, 0.2)';
                    roundBadge.style.borderColor = 'rgba(16, 185, 129, 0.4)';
                    roundBadge.style.color = '#34d399';
                    roundText.textContent = 'ROUND 1 • EASY';
                    roundIndicator.textContent = `Round 1: +${easyCorrect} / -${easyWrong} PTS`;
                    roundIndicator.style.color = '#34d399';
                } else if (round === 2) {
                    roundBadge.style.background = 'rgba(245, 158, 11, 0.2)';
                    roundBadge.style.borderColor = 'rgba(245, 158, 11, 0.4)';
                    roundBadge.style.color = '#fbbf24';
                    roundText.textContent = 'ROUND 2 • NORMAL';
                    roundIndicator.textContent = `Round 2: +${medCorrect} / -${medWrong} PTS`;
                    roundIndicator.style.color = '#fbbf24';
                } else {
                    roundBadge.style.background = 'rgba(244, 63, 94, 0.2)';
                    roundBadge.style.borderColor = 'rgba(244, 63, 94, 0.4)';
                    roundBadge.style.color = '#fb7185';
                    roundText.textContent = 'ROUND 3 • HARD';
                    roundIndicator.textContent = `Round 3: +${hardCorrect} / -${hardWrong} PTS`;
                    roundIndicator.style.color = '#fb7185';
                }
            }

            // Streak visual fire bounce if streak >= 3
            const streakContainer = document.getElementById('streak-container');
            if (streakContainer) {
                if (data.current_streak >= 3) {
                    streakContainer.style.transform = 'scale(1.08)';
                    streakContainer.style.boxShadow = '0 0 16px rgba(245, 158, 11, 0.4)';
                } else {
                    streakContainer.style.transform = 'scale(1)';
                    streakContainer.style.boxShadow = 'none';
                }
            }
        }

        function renderQuestion(q) {
            if (!q) return;
            currentQuestionData = q;
            isAnsweringLocked = false;

            const catEl = document.getElementById('category-text');
            if (catEl) catEl.textContent = q.category || 'General';

            const qTextEl = document.getElementById('question-text');
            if (qTextEl) {
                qTextEl.innerHTML = q.question_text;
                qTextEl.classList.remove('question-fade-in');
                void qTextEl.offsetWidth; // trigger reflow
                qTextEl.classList.add('question-fade-in');
            }

            // Reset feedback
            const fbContainer = document.getElementById('feedback-container');
            if (fbContainer) {
                fbContainer.style.opacity = '0';
                fbContainer.textContent = '';
            }
            const fbText = document.getElementById('feedback-text');
            if (fbText) fbText.innerHTML = '';

            // Render Choices
            const grid = document.getElementById('choices-grid');
            if (grid) {
                grid.innerHTML = '';
                grid.classList.remove('question-fade-in');
                void grid.offsetWidth; // trigger reflow
                grid.classList.add('question-fade-in');
                const letters = ['A', 'B', 'C', 'D'];
                const choices = Array.isArray(q.choices) ? q.choices : [];
                choices.forEach((choice, idx) => {
                    const letter = letters[idx] || (idx + 1);
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'choice-btn';
                    btn.dataset.choice = choice;
                    btn.innerHTML = `
                        <div class="choice-letter">${letter}</div>
                        <div style="flex: 1; word-break: break-word;">${choice}</div>
                    `;
                    btn.onclick = () => selectChoice(choice, btn);
                    grid.appendChild(btn);
                });
            }

            // Start countdown timer
            startQuestionTimer();
        }

        function startQuestionTimer() {
            clearInterval(timerInterval);
            if (!totalTime || isNaN(totalTime) || totalTime <= 0) {
                totalTime = 5.0;
            }
            timeLeft = totalTime;
            updateTimerDisplay(timeLeft);

            const startTime = Date.now();
            const durationMs = totalTime * 1000;

            timerInterval = setInterval(() => {
                if (isPausedForBreak || isPausedForBankruptcy) return;

                const elapsed = Date.now() - startTime;
                timeLeft = Math.max(0, (durationMs - elapsed) / 1000);
                updateTimerDisplay(timeLeft);

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    handleTimeOut();
                }
            }, 50);
        }

        function updateTimerDisplay(timeSec) {
            const timerNum = document.getElementById('timer-number');
            const timerCircle = document.getElementById('timer-circle');

            const wholeSec = Math.ceil(timeSec);
            if (timerNum) timerNum.textContent = wholeSec;

            // Circular stroke offset: full circumference is 2 * PI * 36 ≈ 226
            const circumference = 226;
            const safeTotal = (totalTime && totalTime > 0) ? totalTime : 5.0;
            const progress = Math.min(1, Math.max(0, timeSec / safeTotal));
            const offset = circumference * (1 - progress);

            if (timerCircle) {
                timerCircle.style.strokeDashoffset = offset;
                // Dynamic color based on percentage of time remaining: >50% Green, 25-50% Amber, <25% Red
                const ratio = progress;
                if (ratio > 0.5) {
                    timerCircle.style.stroke = '#10b981';
                    if (timerNum) timerNum.style.color = '#fff';
                } else if (ratio > 0.25) {
                    timerCircle.style.stroke = '#f59e0b';
                    if (timerNum) timerNum.style.color = '#fbbf24';
                    if (Math.abs(timeSec - Math.round(timeSec)) < 0.06 && window.soundFX && typeof window.soundFX.tick ===
                        'function') {
                        window.soundFX.tick();
                    }
                } else {
                    timerCircle.style.stroke = '#f43f5e';
                    if (timerNum) timerNum.style.color = '#fb7185';
                    if (Math.abs(timeSec - Math.round(timeSec)) < 0.06 && window.soundFX && typeof window.soundFX.tick ===
                        'function') {
                        window.soundFX.tick();
                    }
                }
            }
        }

        function selectChoice(choice, btnElement) {
            if (isAnsweringLocked || isPausedForBreak || isPausedForBankruptcy) return;
            isAnsweringLocked = true;
            clearInterval(timerInterval);

            // Immediate 0ms tactile feedback
            if (btnElement) {
                btnElement.classList.add('selected');
                btnElement.style.transform = 'scale(0.97)';
                btnElement.style.borderColor = '#818cf8';
                btnElement.style.background = 'rgba(99, 102, 241, 0.35)';
            }

            // Immediately dim other choices
            document.querySelectorAll('.choice-btn').forEach(btn => {
                btn.disabled = true;
                if (btn !== btnElement) {
                    btn.style.opacity = '0.55';
                }
            });

            submitAnswer(choice);
        }

        function handleTimeOut() {
            if (isAnsweringLocked || isPausedForBreak || isPausedForBankruptcy) return;
            isAnsweringLocked = true;
            submitAnswer(null); // timed out
        }

        async function submitAnswer(userChoice) {
            const prevStreak = parseInt(document.getElementById('streak-count')?.textContent || '0', 10);

            try {
                const res = await fetch(`/game/${sessionId}/answer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        answer: userChoice
                    })
                });

                const result = await res.json();
                if (!result.success) {
                    alert(result.error || 'Failed to submit answer.');
                    return;
                }

                // Instant answer reveal
                revealAnswerUI(result);

                const arenaCard = document.getElementById('main-arena-card');
                const streakContainer = document.getElementById('streak-container');

                // GPU-Accelerated Dynamic Visual & Audio FX
                if (result.is_correct) {
                    if (arenaCard) {
                        arenaCard.classList.remove('arena-wrong-shake');
                        arenaCard.classList.add('arena-correct-pulse');
                        setTimeout(() => arenaCard.classList.remove('arena-correct-pulse'), 500);
                    }

                    spawnFloatingScore(`+${result.points_awarded} PTS`, true);

                    if (streakContainer && result.current_streak >= 3) {
                        streakContainer.classList.add('streak-ignited');
                        setTimeout(() => streakContainer.classList.remove('streak-ignited'), 450);
                    }

                    if (result.current_streak >= 3) {
                        if (window.soundFX && typeof window.soundFX.streak === 'function') window.soundFX.streak();
                    } else {
                        if (window.soundFX && typeof window.soundFX.correct === 'function') window.soundFX.correct();
                    }
                } else {
                    if (arenaCard) {
                        arenaCard.classList.remove('arena-correct-pulse');
                        arenaCard.classList.add('arena-wrong-shake');
                        setTimeout(() => arenaCard.classList.remove('arena-wrong-shake'), 500);
                    }

                    spawnFloatingScore(`${result.points_awarded} PTS`, false);

                    if (prevStreak >= 3) {
                        triggerStreakBrokenEffect(prevStreak);
                        if (window.soundFX && typeof window.soundFX.streakLost === 'function') window.soundFX
                            .streakLost();
                    } else {
                        if (window.soundFX && typeof window.soundFX.wrong === 'function') window.soundFX.wrong();
                    }
                }

                // Streak Pop notification
                const comboPop = document.getElementById('combo-pop');
                const comboText = document.getElementById('combo-text');
                if (comboPop && comboText) {
                    if (result.is_correct && result.streak_bonus > 0) {
                        comboText.textContent =
                            `🔥 ${result.current_streak}x STREAK COMBO (+${result.streak_bonus} BONUS PTS)!`;
                        comboPop.style.opacity = '1';
                    } else {
                        comboPop.style.opacity = '0';
                    }
                }

                // Feedback Message
                const fbContainer = document.getElementById('feedback-container');
                const fbText = document.getElementById('feedback-text');
                if (fbContainer) fbContainer.style.opacity = '1';

                if (fbText) {
                    if (result.is_correct) {
                        fbText.innerHTML =
                            `<span style="color: #34d399;"><i class="fa-solid fa-circle-check"></i> CORRECT! +${result.points_awarded} PTS</span>`;
                    } else {
                        if (userChoice === null) {
                            fbText.innerHTML =
                                `<span style="color: #fb7185;"><i class="fa-solid fa-clock"></i> TIMED OUT! ${result.points_awarded} PTS (Correct: ${result.correct_answer})</span>`;
                        } else {
                            fbText.innerHTML =
                                `<span style="color: #fb7185;"><i class="fa-solid fa-circle-xmark"></i> WRONG! ${result.points_awarded} PTS (Correct: ${result.correct_answer})</span>`;
                        }
                    }
                }

                // Update live HUD
                const pointsDeltaEl = document.getElementById('points-delta');
                if (pointsDeltaEl) {
                    pointsDeltaEl.textContent = result.points_delta >= 0 ? '+' + result.points_delta : result
                        .points_delta;
                    pointsDeltaEl.style.color = result.points_delta >= 0 ? '#34d399' : '#fb7185';
                }
                const playerBalanceEl = document.getElementById('player-balance');
                if (playerBalanceEl) {
                    playerBalanceEl.textContent = Number(result.user_points).toLocaleString();
                }
                const streakCountEl = document.getElementById('streak-count');
                if (streakCountEl) {
                    streakCountEl.textContent = result.current_streak;
                }

                // Crisp, snappy transition to next question (400ms)
                setTimeout(() => {
                    if (comboPop) comboPop.style.opacity = '0';

                    // Check Bankruptcy (User points <= 0)
                    if (result.is_bankrupt) {
                        showBankruptcyModal();
                        return;
                    }

                    // Check Game Complete
                    if (result.is_completed) {
                        isGameCompletedState = true;
                        window.location.href = `/game/${sessionId}/summary`;
                        return;
                    }

                    // Check Round Intermission Break (after Q10 and Q20)
                    if (result.is_round_break) {
                        showRoundBreakModal(result);
                        return;
                    }

                    if (result.next_question) {
                        updateHUD({
                            current_question_index: result.next_question_index,
                            points_delta: result.points_delta,
                            user_points: result.user_points,
                            current_streak: result.current_streak,
                            current_round: result.next_round,
                            timer_seconds: result.timer_seconds,
                            settings: result.settings,
                        });
                        renderQuestion(result.next_question);
                    } else {
                        fetchQuestionState();
                    }
                }, 400);

            } catch (err) {
                console.error('Error submitting answer:', err);
            }
        }

        function revealAnswerUI(result) {
            const buttons = document.querySelectorAll('.choice-btn');
            buttons.forEach(btn => {
                btn.disabled = true;
                const choiceVal = btn.dataset.choice;
                if (choiceVal === result.correct_answer) {
                    btn.classList.add('correct');
                } else if (choiceVal === result.user_answer && !result.is_correct) {
                    btn.classList.add('wrong');
                }
            });
        }

        // Floating Score Particle Animation (+PTS / -PTS)
        function spawnFloatingScore(text, isPositive) {
            const arenaCard = document.getElementById('main-arena-card');
            if (!arenaCard) return;

            const chip = document.createElement('div');
            chip.className = `floating-score-chip ${isPositive ? 'gain' : 'loss'}`;
            chip.innerHTML = isPositive ?
                `<i class="fa-solid fa-arrow-trend-up"></i> ${text}` :
                `<i class="fa-solid fa-arrow-trend-down"></i> ${text}`;

            arenaCard.appendChild(chip);
            setTimeout(() => {
                if (chip && chip.parentNode) chip.parentNode.removeChild(chip);
            }, 850);
        }

        // Streak Shatter / Broken Alert Animation
        function triggerStreakBrokenEffect(lostStreakCount) {
            const pop = document.getElementById('streak-broken-pop');
            const textEl = document.getElementById('streak-broken-text');
            const streakContainer = document.getElementById('streak-container');

            if (textEl) {
                textEl.textContent = `STREAK BROKEN! (${lostStreakCount}x Streak Lost)`;
            }
            if (pop) {
                pop.style.display = 'block';
                pop.classList.remove('streak-shatter-pop');
                void pop.offsetWidth; // Force CSS reflow
                pop.classList.add('streak-shatter-pop');
                setTimeout(() => {
                    pop.classList.remove('streak-shatter-pop');
                    pop.style.display = 'none';
                }, 1250);
            }
            if (streakContainer) {
                streakContainer.classList.add('streak-badge-shattered');
                setTimeout(() => streakContainer.classList.remove('streak-badge-shattered'), 750);
            }
        }

        let pendingRoundBreakResult = null;

        // Round Intermission Break Logic
        function showRoundBreakModal(result) {
            isPausedForBreak = true;
            pendingRoundBreakResult = result;
            clearInterval(timerInterval);

            if (result.settings) {
                currentSettings = Object.assign({}, currentSettings, result.settings);
            }

            const modal = document.getElementById('roundBreakModal');
            const title = document.getElementById('break-title');
            const banner = document.getElementById('next-round-banner');

            const medCorrect = currentSettings.medium_correct_points || 3;
            const medWrong = currentSettings.medium_wrong_penalty || 5;
            const hardCorrect = currentSettings.hard_correct_points || 5;
            const hardWrong = currentSettings.hard_wrong_penalty || 10;

            if (result.next_round === 2) {
                title.textContent = 'Round 1 (Easy) Completed! 🎉';
                banner.innerHTML =
                    `<strong>Next: Round 2 (Questions 11–20)</strong> &bull; Medium difficulty &bull; <span style="color: #34d399;">+${medCorrect} PTS</span> / <span style="color: #fb7185;">-${medWrong} PTS</span>`;
            } else if (result.next_round === 3) {
                title.textContent = 'Round 2 (Normal) Completed! 🔥';
                banner.innerHTML =
                    `<strong>Next: Round 3 (Questions 21–30)</strong> &bull; Hard difficulty &bull; <span style="color: #34d399;">+${hardCorrect} PTS</span> / <span style="color: #fb7185;">-${hardWrong} PTS</span>`;
            }

            document.getElementById('break-correct').textContent =
                `${result.next_question_index - 1 - (result.is_correct ? 0 : 1)} Qs`;
            document.getElementById('break-streak').textContent = `${result.current_streak} 🔥`;
            document.getElementById('break-points').textContent =
                `${result.points_delta >= 0 ? '+' : ''}${result.points_delta} PTS`;

            modal.classList.add('active');
            if (window.soundFX && typeof window.soundFX.streak === 'function') {
                window.soundFX.streak();
            }
        }

        function proceedToNextRound() {
            isPausedForBreak = false;
            isPausedForBankruptcy = false;
            isAnsweringLocked = false;
            clearInterval(timerInterval);

            const modal = document.getElementById('roundBreakModal');
            if (modal) modal.classList.remove('active');

            if (window.soundFX && typeof window.soundFX.correct === 'function') {
                window.soundFX.correct();
            }

            const cached = pendingRoundBreakResult;
            pendingRoundBreakResult = null;

            if (cached && cached.next_question) {
                updateHUD({
                    current_question_index: cached.next_question_index,
                    points_delta: cached.points_delta,
                    user_points: cached.user_points,
                    current_streak: cached.current_streak,
                    current_round: cached.next_round,
                    timer_seconds: cached.timer_seconds,
                    settings: cached.settings || currentSettings
                });
                renderQuestion(cached.next_question);
            } else {
                fetchQuestionState();
            }
        }

        // Bankruptcy Modal Logic
        function showBankruptcyModal() {
            isPausedForBankruptcy = true;
            clearInterval(timerInterval);
            document.getElementById('bankruptcyModal').classList.add('active');
            window.soundFX.wrong();
        }

        function setBankruptTopUp(amt) {
            document.getElementById('bankruptPointsInput').value = amt;
        }

        async function submitBankruptTopUp() {
            const amtInput = document.getElementById('bankruptPointsInput');
            const amount = parseInt(amtInput.value, 10);
            if (!amount || amount < 20) {
                alert('Please enter at least 20 points.');
                return;
            }

            const confirmBtn = document.getElementById('bankruptConfirmBtn');
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';

            try {
                const res = await fetch(`/game/${sessionId}/top-up`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        amount: amount
                    })
                });

                const data = await res.json();
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fa-solid fa-coins"></i> Confirm & Resume Game';

                if (data.success) {
                    document.getElementById('player-balance').textContent = Number(data.new_points).toLocaleString();
                    document.getElementById('bankruptcyModal').classList.remove('active');
                    isPausedForBankruptcy = false;
                    window.soundFX.correct();
                    // Resume game
                    fetchQuestionState();
                } else {
                    alert(data.error || 'Failed to top up points.');
                }
            } catch (err) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fa-solid fa-coins"></i> Confirm & Resume Game';
                alert('Top-up connection error.');
            }
        }

        // ==========================================
        // ANTI-CHEAT & TAB-SWITCH DETECTION ENGINE
        // ==========================================
        let hasDisqualified = false;
        let isGameCompletedState = false;

        // Detect Tab Switch / Loss of Page Visibility
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && !hasDisqualified && !isGameCompletedState) {
                triggerAntiCheatDisqualification('tab_switched');
            }
        });

        // Detect Window Blur (clicking outside browser window / switching apps)
        window.addEventListener('blur', () => {
            if (!hasDisqualified && !isGameCompletedState) {
                triggerAntiCheatDisqualification('tab_switched');
            }
        });

        // Warn on page reload/exit
        window.addEventListener('beforeunload', (e) => {
            if (!hasDisqualified && !isGameCompletedState) {
                e.preventDefault();
                e.returnValue =
                    'Warning: Leaving or reloading the page will forfeit your match and cannot be resumed!';
                try {
                    navigator.sendBeacon(`/game/${sessionId}/abandon?reason=page_closed`);
                } catch (err) {}
            }
        });

        function triggerAntiCheatDisqualification(reason) {
            if (hasDisqualified || isGameCompletedState) return;
            hasDisqualified = true;
            clearInterval(timerInterval);

            window.soundFX.wrong();
            const modal = document.getElementById('antiCheatModal');
            if (modal) modal.classList.add('active');

            // Cancel match on backend immediately
            fetch(`/game/${sessionId}/abandon`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    reason: reason
                })
            }).catch(() => {});

            let cd = 2;
            const cdEl = document.getElementById('antiCheatCountdown');
            const intv = setInterval(() => {
                cd--;
                if (cdEl) cdEl.textContent = cd;
                if (cd <= 0) {
                    clearInterval(intv);
                    window.location.href = "{{ route('user.home') }}";
                }
            }, 1000);
        }
    </script>
@endpush
