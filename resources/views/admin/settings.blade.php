@extends('layouts.app')

@section('title', 'Game Pointing System & Rules - Admin')

@section('content')
<div style="display: flex; flex-direction: column; gap: 2rem; max-width: 1050px; margin: 0 auto;">

    <!-- Header -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
        <div>
            <a href="{{ route('admin.dashboard') }}" style="color: #94a3b8; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem; margin-bottom: 0.4rem;">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1 style="font-size: 2.25rem; font-weight: 900; color: #fff; letter-spacing: -0.5px;">Pointing System & Game Rules</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.2rem;">
                Configure points awarded, penalties, question timers, streaks, and match entry fees for all rounds.
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <form action="{{ route('admin.settings.reset') }}" method="POST" onsubmit="return confirm('Are you sure you want to reset all game rules and pointing to factory defaults?')">
                @csrf
                <button type="submit" class="btn btn-outline" style="color: #fb7185; border-color: rgba(244, 63, 94, 0.4);">
                    <i class="fa-solid fa-rotate-left"></i> Reset Defaults
                </button>
            </form>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf

        <div style="display: flex; flex-direction: column; gap: 1.75rem;">

            <!-- 3 Difficulty Rounds Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(310px, 1fr)); gap: 1.5rem;">
                
                <!-- Round 1 (Easy) -->
                <div class="glass-card" style="padding: 1.75rem; border-top: 4px solid #10b981; background: rgba(15, 23, 42, 0.85);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16, 185, 129, 0.2); display: flex; align-items: center; justify-content: center; color: #34d399; font-weight: 800;">
                                R1
                            </div>
                            <div>
                                <h3 style="font-size: 1.15rem; font-weight: 800; color: #fff;">Round 1 (Easy)</h3>
                                <span style="font-size: 0.75rem; color: #94a3b8;">Questions 1–10</span>
                            </div>
                        </div>
                        <span style="font-size: 0.75rem; padding: 0.2rem 0.55rem; border-radius: 9999px; background: rgba(16, 185, 129, 0.15); color: #34d399; font-weight: 700;">EASY</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
                                <i class="fa-solid fa-circle-plus text-emerald-400"></i> Points for Correct Answer
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="easy_correct_points" value="{{ old('easy_correct_points', $settings['easy_correct_points'] ?? 2) }}" min="1" max="100" class="form-input" style="border-color: rgba(16, 185, 129, 0.4); font-weight: 700; color: #34d399;" required>
                                <span style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); font-size: 0.8rem; color: #64748b;">PTS</span>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
                                <i class="fa-solid fa-circle-minus text-rose-400"></i> Penalty for Wrong Answer
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="easy_wrong_penalty" value="{{ old('easy_wrong_penalty', $settings['easy_wrong_penalty'] ?? 3) }}" min="0" max="100" class="form-input" style="border-color: rgba(244, 63, 94, 0.4); font-weight: 700; color: #fb7185;" required>
                                <span style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); font-size: 0.8rem; color: #64748b;">PTS</span>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
                                <i class="fa-solid fa-clock text-cyan-400"></i> Question Timer
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="easy_timer_seconds" value="{{ old('easy_timer_seconds', $settings['easy_timer_seconds'] ?? 5) }}" min="3" max="60" class="form-input" style="font-weight: 700;" required>
                                <span style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); font-size: 0.8rem; color: #64748b;">SEC</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Round 2 (Normal / Medium) -->
                <div class="glass-card" style="padding: 1.75rem; border-top: 4px solid #f59e0b; background: rgba(15, 23, 42, 0.85);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(245, 158, 11, 0.2); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-weight: 800;">
                                R2
                            </div>
                            <div>
                                <h3 style="font-size: 1.15rem; font-weight: 800; color: #fff;">Round 2 (Normal)</h3>
                                <span style="font-size: 0.75rem; color: #94a3b8;">Questions 11–20</span>
                            </div>
                        </div>
                        <span style="font-size: 0.75rem; padding: 0.2rem 0.55rem; border-radius: 9999px; background: rgba(245, 158, 11, 0.15); color: #fbbf24; font-weight: 700;">NORMAL</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
                                <i class="fa-solid fa-circle-plus text-emerald-400"></i> Points for Correct Answer
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="medium_correct_points" value="{{ old('medium_correct_points', $settings['medium_correct_points'] ?? 3) }}" min="1" max="100" class="form-input" style="border-color: rgba(16, 185, 129, 0.4); font-weight: 700; color: #34d399;" required>
                                <span style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); font-size: 0.8rem; color: #64748b;">PTS</span>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
                                <i class="fa-solid fa-circle-minus text-rose-400"></i> Penalty for Wrong Answer
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="medium_wrong_penalty" value="{{ old('medium_wrong_penalty', $settings['medium_wrong_penalty'] ?? 5) }}" min="0" max="100" class="form-input" style="border-color: rgba(244, 63, 94, 0.4); font-weight: 700; color: #fb7185;" required>
                                <span style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); font-size: 0.8rem; color: #64748b;">PTS</span>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
                                <i class="fa-solid fa-clock text-cyan-400"></i> Question Timer
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="medium_timer_seconds" value="{{ old('medium_timer_seconds', $settings['medium_timer_seconds'] ?? 5) }}" min="3" max="60" class="form-input" style="font-weight: 700;" required>
                                <span style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); font-size: 0.8rem; color: #64748b;">SEC</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Round 3 (Hard) -->
                <div class="glass-card" style="padding: 1.75rem; border-top: 4px solid #f43f5e; background: rgba(15, 23, 42, 0.85);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(244, 63, 94, 0.2); display: flex; align-items: center; justify-content: center; color: #fb7185; font-weight: 800;">
                                R3
                            </div>
                            <div>
                                <h3 style="font-size: 1.15rem; font-weight: 800; color: #fff;">Round 3 (Hard)</h3>
                                <span style="font-size: 0.75rem; color: #94a3b8;">Questions 21–30</span>
                            </div>
                        </div>
                        <span style="font-size: 0.75rem; padding: 0.2rem 0.55rem; border-radius: 9999px; background: rgba(244, 63, 94, 0.15); color: #fb7185; font-weight: 700;">HARD</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
                                <i class="fa-solid fa-circle-plus text-emerald-400"></i> Points for Correct Answer
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="hard_correct_points" value="{{ old('hard_correct_points', $settings['hard_correct_points'] ?? 5) }}" min="1" max="100" class="form-input" style="border-color: rgba(16, 185, 129, 0.4); font-weight: 700; color: #34d399;" required>
                                <span style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); font-size: 0.8rem; color: #64748b;">PTS</span>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
                                <i class="fa-solid fa-circle-minus text-rose-400"></i> Penalty for Wrong Answer
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="hard_wrong_penalty" value="{{ old('hard_wrong_penalty', $settings['hard_wrong_penalty'] ?? 10) }}" min="0" max="100" class="form-input" style="border-color: rgba(244, 63, 94, 0.4); font-weight: 700; color: #fb7185;" required>
                                <span style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); font-size: 0.8rem; color: #64748b;">PTS</span>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
                                <i class="fa-solid fa-clock text-cyan-400"></i> Question Timer
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="hard_timer_seconds" value="{{ old('hard_timer_seconds', $settings['hard_timer_seconds'] ?? 5) }}" min="3" max="60" class="form-input" style="font-weight: 700;" required>
                                <span style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); font-size: 0.8rem; color: #64748b;">SEC</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Economy & Streak Multipliers Row -->
            <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 1.5rem;">
                
                <!-- Game Economy Card -->
                <div class="glass-card" style="padding: 1.75rem; border-top: 4px solid #6366f1; background: rgba(15, 23, 42, 0.85);">
                    <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1.25rem;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(99, 102, 241, 0.2); display: flex; align-items: center; justify-content: center; color: #818cf8;">
                            <i class="fa-solid fa-coins"></i>
                        </div>
                        <h3 style="font-size: 1.15rem; font-weight: 800; color: #fff;">Game Economy</h3>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
                                <i class="fa-solid fa-ticket text-indigo-400"></i> Match Entry Fee (Cost per game)
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="entry_fee" value="{{ old('entry_fee', $settings['entry_fee'] ?? 50) }}" min="0" max="1000" class="form-input" style="border-color: rgba(99, 102, 241, 0.4); font-weight: 700; color: #fbbf24;" required>
                                <span style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); font-size: 0.8rem; color: #64748b;">PTS</span>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
                                <i class="fa-solid fa-gift text-emerald-400"></i> New Player Welcome Bonus
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="welcome_bonus" value="{{ old('welcome_bonus', $settings['welcome_bonus'] ?? 200) }}" min="0" max="5000" class="form-input" style="border-color: rgba(16, 185, 129, 0.4); font-weight: 700; color: #34d399;" required>
                                <span style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); font-size: 0.8rem; color: #64748b;">PTS</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Streak Bonuses Card -->
                <div class="glass-card" style="padding: 1.75rem; border-top: 4px solid #a855f7; background: rgba(15, 23, 42, 0.85);">
                    <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1.25rem;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(168, 85, 247, 0.2); display: flex; align-items: center; justify-content: center; color: #c084fc;">
                            <i class="fa-solid fa-fire"></i>
                        </div>
                        <h3 style="font-size: 1.15rem; font-weight: 800; color: #fff;">Streak Multiplier Bonuses</h3>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
                                3x Streak Bonus
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="streak_3_bonus" value="{{ old('streak_3_bonus', $settings['streak_3_bonus'] ?? 1) }}" min="0" max="50" class="form-input" style="border-color: rgba(245, 158, 11, 0.4); font-weight: 700; color: #fbbf24;" required>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
                                5x Streak Bonus
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="streak_5_bonus" value="{{ old('streak_5_bonus', $settings['streak_5_bonus'] ?? 2) }}" min="0" max="50" class="form-input" style="border-color: rgba(245, 158, 11, 0.4); font-weight: 700; color: #fbbf24;" required>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
                                8+ Godlike Bonus
                            </label>
                            <div style="position: relative;">
                                <input type="number" name="streak_8_bonus" value="{{ old('streak_8_bonus', $settings['streak_8_bonus'] ?? 5) }}" min="0" max="100" class="form-input" style="border-color: rgba(168, 85, 247, 0.4); font-weight: 700; color: #c084fc;" required>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Submit Button -->
            <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.9rem 2.25rem; font-size: 1.05rem; font-weight: 800; background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);">
                    <i class="fa-solid fa-floppy-disk"></i> Save Pointing Rules & Settings
                </button>
            </div>

        </div>
    </form>

</div>
@endsection
