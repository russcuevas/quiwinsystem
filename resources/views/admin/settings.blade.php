@extends('layouts.app')

@section('title', 'Game Pointing System & Rules - Admin')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 1050px; margin: 0 auto;">

    <!-- Header -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 0.75rem;">
        <div>
            <a href="{{ route('admin.dashboard') }}" style="color: #94a3b8; font-size: 0.82rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem; margin-bottom: 0.3rem;">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1 style="font-size: clamp(1.4rem, 4vw, 2.25rem); font-weight: 900; color: #fff; letter-spacing: -0.5px;">Pointing System & Rules</h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 0.2rem;">
                Configure points, penalties, question timers, streaks, and entry fees.
            </p>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <form action="{{ route('admin.settings.reset') }}" method="POST" onsubmit="return confirm('Reset all game rules and pointing to defaults?')">
                @csrf
                <button type="submit" class="btn btn-outline" style="color: #fb7185; border-color: rgba(244, 63, 94, 0.4); font-size: 0.8rem; padding: 0.4rem 0.75rem; min-height: 36px;">
                    <i class="fa-solid fa-rotate-left"></i> Defaults
                </button>
            </form>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf

        <div style="display: flex; flex-direction: column; gap: 1.25rem;">

            <!-- 3 Difficulty Rounds Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                
                <!-- Round 1 (Easy) -->
                <div class="glass-card" style="padding: 1.25rem; border-top: 4px solid #10b981; background: rgba(15, 23, 42, 0.85);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.55rem;">
                            <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(16, 185, 129, 0.2); display: flex; align-items: center; justify-content: center; color: #34d399; font-weight: 800; font-size: 0.85rem;">
                                R1
                            </div>
                            <div>
                                <h3 style="font-size: 1.05rem; font-weight: 800; color: #fff;">Round 1 (Easy)</h3>
                                <span style="font-size: 0.72rem; color: #94a3b8;">Questions 1–10</span>
                            </div>
                        </div>
                        <span style="font-size: 0.72rem; padding: 0.15rem 0.45rem; border-radius: 9999px; background: rgba(16, 185, 129, 0.15); color: #34d399; font-weight: 700;">EASY</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.25rem;">
                                <i class="fa-solid fa-circle-plus text-emerald-400"></i> Correct Answer PTS
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="easy_correct_points" value="{{ old('easy_correct_points', $settings['easy_correct_points'] ?? 2) }}" min="1" max="100" class="form-input" style="border-color: rgba(16, 185, 129, 0.4); font-weight: 700; color: #34d399; min-height: 38px;" required>
                                <span style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #64748b;">PTS</span>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.25rem;">
                                <i class="fa-solid fa-circle-minus text-rose-400"></i> Penalty for Wrong Answer
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="easy_wrong_penalty" value="{{ old('easy_wrong_penalty', $settings['easy_wrong_penalty'] ?? 3) }}" min="0" max="100" class="form-input" style="border-color: rgba(244, 63, 94, 0.4); font-weight: 700; color: #fb7185; min-height: 38px;" required>
                                <span style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #64748b;">PTS</span>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.25rem;">
                                <i class="fa-solid fa-clock text-cyan-400"></i> Question Timer (Seconds)
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="easy_timer_seconds" value="{{ old('easy_timer_seconds', $settings['easy_timer_seconds'] ?? 5) }}" min="3" max="60" class="form-input" style="font-weight: 700; min-height: 38px;" required>
                                <span style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #64748b;">SEC</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Round 2 (Normal / Medium) -->
                <div class="glass-card" style="padding: 1.25rem; border-top: 4px solid #f59e0b; background: rgba(15, 23, 42, 0.85);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.55rem;">
                            <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(245, 158, 11, 0.2); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-weight: 800; font-size: 0.85rem;">
                                R2
                            </div>
                            <div>
                                <h3 style="font-size: 1.05rem; font-weight: 800; color: #fff;">Round 2 (Normal)</h3>
                                <span style="font-size: 0.72rem; color: #94a3b8;">Questions 11–20</span>
                            </div>
                        </div>
                        <span style="font-size: 0.72rem; padding: 0.15rem 0.45rem; border-radius: 9999px; background: rgba(245, 158, 11, 0.15); color: #fbbf24; font-weight: 700;">NORMAL</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.25rem;">
                                <i class="fa-solid fa-circle-plus text-emerald-400"></i> Correct Answer PTS
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="medium_correct_points" value="{{ old('medium_correct_points', $settings['medium_correct_points'] ?? 3) }}" min="1" max="100" class="form-input" style="border-color: rgba(16, 185, 129, 0.4); font-weight: 700; color: #34d399; min-height: 38px;" required>
                                <span style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #64748b;">PTS</span>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.25rem;">
                                <i class="fa-solid fa-circle-minus text-rose-400"></i> Penalty for Wrong Answer
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="medium_wrong_penalty" value="{{ old('medium_wrong_penalty', $settings['medium_wrong_penalty'] ?? 5) }}" min="0" max="100" class="form-input" style="border-color: rgba(244, 63, 94, 0.4); font-weight: 700; color: #fb7185; min-height: 38px;" required>
                                <span style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #64748b;">PTS</span>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.25rem;">
                                <i class="fa-solid fa-clock text-cyan-400"></i> Question Timer (Seconds)
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="medium_timer_seconds" value="{{ old('medium_timer_seconds', $settings['medium_timer_seconds'] ?? 5) }}" min="3" max="60" class="form-input" style="font-weight: 700; min-height: 38px;" required>
                                <span style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #64748b;">SEC</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Round 3 (Hard) -->
                <div class="glass-card" style="padding: 1.25rem; border-top: 4px solid #f43f5e; background: rgba(15, 23, 42, 0.85);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.55rem;">
                            <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(244, 63, 94, 0.2); display: flex; align-items: center; justify-content: center; color: #fb7185; font-weight: 800; font-size: 0.85rem;">
                                R3
                            </div>
                            <div>
                                <h3 style="font-size: 1.05rem; font-weight: 800; color: #fff;">Round 3 (Hard)</h3>
                                <span style="font-size: 0.72rem; color: #94a3b8;">Questions 21–30</span>
                            </div>
                        </div>
                        <span style="font-size: 0.72rem; padding: 0.15rem 0.45rem; border-radius: 9999px; background: rgba(244, 63, 94, 0.15); color: #fb7185; font-weight: 700;">HARD</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.25rem;">
                                <i class="fa-solid fa-circle-plus text-emerald-400"></i> Correct Answer PTS
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="hard_correct_points" value="{{ old('hard_correct_points', $settings['hard_correct_points'] ?? 5) }}" min="1" max="100" class="form-input" style="border-color: rgba(16, 185, 129, 0.4); font-weight: 700; color: #34d399; min-height: 38px;" required>
                                <span style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #64748b;">PTS</span>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.25rem;">
                                <i class="fa-solid fa-circle-minus text-rose-400"></i> Penalty for Wrong Answer
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="hard_wrong_penalty" value="{{ old('hard_wrong_penalty', $settings['hard_wrong_penalty'] ?? 10) }}" min="0" max="100" class="form-input" style="border-color: rgba(244, 63, 94, 0.4); font-weight: 700; color: #fb7185; min-height: 38px;" required>
                                <span style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #64748b;">PTS</span>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.25rem;">
                                <i class="fa-solid fa-clock text-cyan-400"></i> Question Timer (Seconds)
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="hard_timer_seconds" value="{{ old('hard_timer_seconds', $settings['hard_timer_seconds'] ?? 5) }}" min="3" max="60" class="form-input" style="font-weight: 700; min-height: 38px;" required>
                                <span style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #64748b;">SEC</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Economy & Streak Multipliers Row -->
            <div class="settings-secondary-grid" style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 1rem;">
                
                <!-- Game Economy Card -->
                <div class="glass-card" style="padding: 1.25rem; border-top: 4px solid #6366f1; background: rgba(15, 23, 42, 0.85);">
                    <div style="display: flex; align-items: center; gap: 0.55rem; margin-bottom: 1rem;">
                        <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(99, 102, 241, 0.2); display: flex; align-items: center; justify-content: center; color: #818cf8;">
                            <i class="fa-solid fa-coins"></i>
                        </div>
                        <h3 style="font-size: 1.05rem; font-weight: 800; color: #fff;">Game Economy</h3>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.25rem;">
                                <i class="fa-solid fa-ticket text-indigo-400"></i> Match Entry Fee
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="entry_fee" value="{{ old('entry_fee', $settings['entry_fee'] ?? 50) }}" min="0" max="1000" class="form-input" style="border-color: rgba(99, 102, 241, 0.4); font-weight: 700; color: #fbbf24; min-height: 38px;" required>
                                <span style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #64748b;">PTS</span>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.25rem;">
                                <i class="fa-solid fa-gift text-emerald-400"></i> Welcome Bonus
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="welcome_bonus" value="{{ old('welcome_bonus', $settings['welcome_bonus'] ?? 200) }}" min="0" max="5000" class="form-input" style="border-color: rgba(16, 185, 129, 0.4); font-weight: 700; color: #34d399; min-height: 38px;" required>
                                <span style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #64748b;">PTS</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Streak Bonuses Card -->
                <div class="glass-card" style="padding: 1.25rem; border-top: 4px solid #a855f7; background: rgba(15, 23, 42, 0.85);">
                    <div style="display: flex; align-items: center; gap: 0.55rem; margin-bottom: 1rem;">
                        <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(168, 85, 247, 0.2); display: flex; align-items: center; justify-content: center; color: #c084fc;">
                            <i class="fa-solid fa-fire"></i>
                        </div>
                        <h3 style="font-size: 1.05rem; font-weight: 800; color: #fff;">Streak Multipliers</h3>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(80px, 1fr)); gap: 0.5rem;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.25rem;">
                                3x Streak
                            </label>
                            <input type="number" name="streak_3_bonus" value="{{ old('streak_3_bonus', $settings['streak_3_bonus'] ?? 1) }}" min="0" max="50" class="form-input" style="border-color: rgba(245, 158, 11, 0.4); font-weight: 700; color: #fbbf24; min-height: 38px;" required>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.25rem;">
                                5x Streak
                            </label>
                            <input type="number" name="streak_5_bonus" value="{{ old('streak_5_bonus', $settings['streak_5_bonus'] ?? 2) }}" min="0" max="50" class="form-input" style="border-color: rgba(245, 158, 11, 0.4); font-weight: 700; color: #fbbf24; min-height: 38px;" required>
                        </div>

                <!-- Quest Rewards Card -->
                <div class="glass-card" style="padding: 1.25rem; border-top: 4px solid #06b6d4; background: rgba(15, 23, 42, 0.85); grid-column: 1 / -1;">
                    <div style="display: flex; align-items: center; gap: 0.55rem; margin-bottom: 1rem;">
                        <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(6, 182, 212, 0.2); display: flex; align-items: center; justify-content: center; color: #22d3ee;">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                        <h3 style="font-size: 1.05rem; font-weight: 800; color: #fff;">Missions & Quest Rewards</h3>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.25rem;">
                                <i class="fa-solid fa-calendar-check text-cyan-400"></i> 7-Day Daily Play Quest Reward
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="weekly_quest_reward" value="{{ old('weekly_quest_reward', $settings['weekly_quest_reward'] ?? 300) }}" min="0" max="10000" class="form-input" style="border-color: rgba(6, 182, 212, 0.4); font-weight: 700; color: #38bdf8; min-height: 38px;" required>
                                <span style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #64748b;">PTS</span>
                            </div>
                            <span style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.2rem; display: block;">Awarded automatically when a player plays daily for 7 consecutive days.</span>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.25rem;">
                                <i class="fa-solid fa-user-group text-amber-400"></i> 5 Friends Referral Quest Reward
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="referral_quest_reward" value="{{ old('referral_quest_reward', $settings['referral_quest_reward'] ?? 1000) }}" min="0" max="20000" class="form-input" style="border-color: rgba(245, 158, 11, 0.4); font-weight: 700; color: #fbbf24; min-height: 38px;" required>
                                <span style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #64748b;">PTS</span>
                            </div>
                            <span style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.2rem; display: block;">Awarded when 5 referred friends are approved by Admin.</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Submit Button -->
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="width: 100%; max-width: 320px; padding: 0.85rem; font-size: 1rem; font-weight: 800; background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);">
                    <i class="fa-solid fa-floppy-disk"></i> Save Rules & Settings
                </button>
            </div>

        </div>
    </form>

</div>

<style>
    @media (max-width: 800px) {
        .settings-secondary-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection
