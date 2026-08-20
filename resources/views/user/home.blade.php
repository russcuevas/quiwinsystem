@extends('layouts.app')

@section('title', 'Player Hub - Quiwin')

@section('content')
    <div style="display: flex; flex-direction: column; gap: 2rem;">

        <!-- Hero Banner with Play Action -->
        <div class="glass-card"
            style="padding: 2.5rem; background: linear-gradient(135deg, rgba(30, 27, 75, 0.8), rgba(15, 23, 42, 0.9)); border: 1px solid rgba(99, 102, 241, 0.3); position: relative; overflow: hidden;">
            <div
                style="position: absolute; right: -20px; bottom: -20px; font-size: 15rem; color: rgba(99, 102, 241, 0.04); pointer-events: none;">
                <i class="fa-solid fa-gamepad"></i>
            </div>

            <div
                style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 2rem; position: relative; z-index: 2;">
                <div style="max-width: 600px;">
                    <div
                        style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.85rem; color: #a5b4fc; font-weight: 600; margin-bottom: 1rem;">
                        <i class="fa-solid fa-bolt text-amber-400"></i> Season 1 Live Arena
                    </div>
                    <h1 style="font-size: 2.5rem; font-weight: 900; line-height: 1.15; color: #fff; letter-spacing: -1px;">
                        Ready for the <span
                            style="background: linear-gradient(135deg, #818cf8, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Quiwin
                            Battle</span>?
                    </h1>
                    <p style="color: #cbd5e1; font-size: 1.05rem; margin-top: 0.75rem; line-height: 1.5;">
                        Answer 30 progressive questions across 3 intense rounds. Build streaks for multiplier bonuses, but
                        beware of harsh penalties on mistakes!
                    </p>

                    <div style="display: flex; align-items: center; gap: 1.5rem; margin-top: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; color: #94a3b8;">
                            <i class="fa-solid fa-layer-group text-indigo-400"></i> 3 Rounds (Easy &bull; Normal &bull;
                            Hard)
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; color: #94a3b8;">
                            <i class="fa-solid fa-stopwatch text-amber-400"></i> Random Timer 5 - 25secs
                        </div>
                    </div>
                </div>

                <!-- Main Play Card / Action -->
                <div class="glass-card"
                    style="padding: 1.75rem; background: rgba(15, 23, 42, 0.85); border: 1px solid rgba(255, 255, 255, 0.12); min-width: 280px; text-align: center; box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);">

                    <div
                        style="font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">
                        Your Points Balance
                    </div>
                    <div
                        style="font-size: 2.5rem; font-weight: 900; color: #fbbf24; display: flex; align-items: center; justify-content: center; gap: 0.5rem; text-shadow: 0 0 20px rgba(245, 158, 11, 0.35);">
                        <i class="fa-solid fa-coins"></i>
                        <span>{{ number_format($user->points) }}</span>
                    </div>
                    <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 1.25rem;">
                        Match Entry Fee: <strong style="color: #f87171;">-{{ $entryFee ?? 50 }} PTS</strong>
                    </div>

                    @if ($user->points >= ($entryFee ?? 50))
                        <form action="{{ route('game.start') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn btn-primary"
                                style="width: 100%; padding: 1rem; font-size: 1.15rem; font-weight: 800; border-radius: 1rem; background: linear-gradient(135deg, #6366f1, #06b6d4); box-shadow: 0 0 24px rgba(99, 102, 241, 0.5);">
                                <i class="fa-solid fa-gamepad"></i> PLAY GAME (-{{ $entryFee ?? 50 }} PTS)
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-gold"
                            style="width: 100%; padding: 1rem; font-size: 1rem; border-radius: 1rem;"
                            onclick="openTopUpModal()">
                            <i class="fa-solid fa-plus-circle"></i> Need {{ $entryFee ?? 50 }} PTS (Top-Up)
                        </button>
                    @endif

                    <div style="margin-top: 0.85rem;">
                        <button type="button" class="btn btn-outline"
                            style="width: 100%; font-size: 0.85rem; padding: 0.5rem;" onclick="openTopUpModal()">
                            <i class="fa-solid fa-wallet text-amber-400"></i> Add More Points
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 Stats Cards Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">

            <div class="glass-card"
                style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; border-left: 4px solid #6366f1;">
                <div
                    style="width: 50px; height: 50px; border-radius: 14px; background: rgba(99, 102, 241, 0.15); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.5rem;">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
                <div>
                    <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Matches Completed</div>
                    <div style="font-size: 1.75rem; font-weight: 800; color: #fff;">{{ $totalGames }}</div>
                </div>
            </div>

            <div class="glass-card"
                style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; border-left: 4px solid #f59e0b;">
                <div
                    style="width: 50px; height: 50px; border-radius: 14px; background: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.5rem;">
                    <i class="fa-solid fa-fire"></i>
                </div>
                <div>
                    <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Highest Streak</div>
                    <div style="font-size: 1.75rem; font-weight: 800; color: #fff;">{{ $bestStreak }} 🔥</div>
                </div>
            </div>

            <div class="glass-card"
                style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; border-left: 4px solid #10b981;">
                <div
                    style="width: 50px; height: 50px; border-radius: 14px; background: rgba(16, 185, 129, 0.15); display: flex; align-items: center; justify-content: center; color: #34d399; font-size: 1.5rem;">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <div>
                    <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Overall Accuracy</div>
                    <div style="font-size: 1.75rem; font-weight: 800; color: #fff;">{{ $accuracy }}%</div>
                </div>
            </div>

            <div class="glass-card"
                style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; border-left: 4px solid #06b6d4;">
                <div
                    style="width: 50px; height: 50px; border-radius: 14px; background: rgba(6, 182, 212, 0.15); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 1.5rem;">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div>
                    <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Player Rank</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #fff;">
                        @if ($user->points >= 1000)
                            Master 👑
                        @elseif($user->points >= 500)
                            Veteran ⚡
                        @elseif($user->points >= 200)
                            Challenger ⚔️
                        @else
                            Novice 🛡️
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Main Content 2 Columns: Leaderboard & Game Rules / History -->
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem;">

            <!-- Left: Game Rules / Scoring & Recent Games -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                <!-- Game Rules Card -->
                <div class="glass-card" style="padding: 1.75rem;">
                    <h3
                        style="font-size: 1.25rem; font-weight: 800; color: #fff; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-circle-info text-indigo-400"></i> Rules & Progressive Scoring
                    </h3>

                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.25rem;">

                        <!-- Round 1 -->
                        <div
                            style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 0.85rem; padding: 1rem; text-align: center;">
                            <div style="font-size: 0.8rem; font-weight: 700; color: #34d399; text-transform: uppercase;">
                                Round 1 (Easy)</div>
                            <div style="font-size: 0.9rem; font-weight: 600; color: #cbd5e1; margin: 0.25rem 0;">Questions
                                1–10</div>
                            <div style="font-size: 0.85rem; color: #34d399; font-weight: 700;">+2 PTS (Correct)</div>
                            <div style="font-size: 0.85rem; color: #fb7185; font-weight: 700;">-3 PTS (Wrong)</div>
                        </div>

                        <!-- Round 2 -->
                        <div
                            style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 0.85rem; padding: 1rem; text-align: center;">
                            <div style="font-size: 0.8rem; font-weight: 700; color: #fbbf24; text-transform: uppercase;">
                                Round 2 (Normal)</div>
                            <div style="font-size: 0.9rem; font-weight: 600; color: #cbd5e1; margin: 0.25rem 0;">Questions
                                11–20</div>
                            <div style="font-size: 0.85rem; color: #34d399; font-weight: 700;">+3 PTS (Correct)</div>
                            <div style="font-size: 0.85rem; color: #fb7185; font-weight: 700;">-5 PTS (Wrong)</div>
                        </div>

                        <!-- Round 3 -->
                        <div
                            style="background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.3); border-radius: 0.85rem; padding: 1rem; text-align: center;">
                            <div style="font-size: 0.8rem; font-weight: 700; color: #fb7185; text-transform: uppercase;">
                                Round 3 (Hard)</div>
                            <div style="font-size: 0.9rem; font-weight: 600; color: #cbd5e1; margin: 0.25rem 0;">Questions
                                21–30</div>
                            <div style="font-size: 0.85rem; color: #34d399; font-weight: 700;">+5 PTS (Correct)</div>
                            <div style="font-size: 0.85rem; color: #fb7185; font-weight: 700;">-10 PTS (Wrong)</div>
                        </div>

                    </div>

                    <div
                        style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 0.85rem; padding: 1rem; font-size: 0.85rem; color: #94a3b8; display: flex; flex-direction: column; gap: 0.5rem;">
                        <div>🔥 <strong>Streak Bonuses</strong></div>
                    </div>
                </div>

                <!-- Recent Matches Table -->
                <div class="glass-card" style="padding: 1.75rem;">
                    <h3
                        style="font-size: 1.25rem; font-weight: 800; color: #fff; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-clock-rotate-left text-cyan-400"></i> Your Recent Matches
                    </h3>

                    @if ($recentGames->isEmpty())
                        <p style="color: #64748b; font-size: 0.9rem; text-align: center; padding: 1.5rem 0;">No completed
                            matches yet. Click "PLAY GAME" to start your first challenge!</p>
                    @else
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                                <thead>
                                    <tr
                                        style="border-bottom: 1px solid rgba(255,255,255,0.08); color: #64748b; text-align: left;">
                                        <th style="padding: 0.6rem 0.5rem;">Match</th>
                                        <th style="padding: 0.6rem 0.5rem;">Score</th>
                                        <th style="padding: 0.6rem 0.5rem;">Max Streak</th>
                                        <th style="padding: 0.6rem 0.5rem;">Net Points</th>
                                        <th style="padding: 0.6rem 0.5rem;">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentGames as $game)
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); color: #cbd5e1;">
                                            <td style="padding: 0.75rem 0.5rem; font-weight: 600;">#{{ $game->id }}
                                            </td>
                                            <td style="padding: 0.75rem 0.5rem;">
                                                <span
                                                    style="color: #34d399; font-weight: 700;">{{ $game->total_correct }}</span>/30
                                            </td>
                                            <td style="padding: 0.75rem 0.5rem;">
                                                {{ $game->max_streak }} 🔥
                                            </td>
                                            <td
                                                style="padding: 0.75rem 0.5rem; font-weight: 700; color: {{ $game->points_delta >= 0 ? '#34d399' : '#fb7185' }};">
                                                {{ $game->points_delta >= 0 ? '+' . $game->points_delta : $game->points_delta }}
                                                PTS
                                            </td>
                                            <td style="padding: 0.75rem 0.5rem; color: #64748b; font-size: 0.8rem;">
                                                {{ $game->created_at->diffForHumans() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>

            <!-- Right: Global Leaderboard & Points Ledger -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                <!-- Leaderboard -->
                <div class="glass-card" style="padding: 1.75rem;">
                    <div
                        style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                        <h3
                            style="font-size: 1.25rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-trophy text-amber-400"></i> Leaderboard
                        </h3>
                        <span style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 600;">Top
                            Players</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                        @foreach ($leaderboard as $index => $player)
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; padding: 0.65rem 0.85rem; border-radius: 0.75rem; background: {{ $player->id === $user->id ? 'rgba(99, 102, 241, 0.2)' : 'rgba(255, 255, 255, 0.03)' }}; border: 1px solid {{ $player->id === $user->id ? 'rgba(99, 102, 241, 0.4)' : 'rgba(255, 255, 255, 0.05)' }};">

                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div
                                        style="width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem;">
                                        @if ($index === 0)
                                            🥇
                                        @elseif($index === 1)
                                            🥈
                                        @elseif($index === 2)
                                            🥉
                                        @else
                                            <span style="color: #64748b;">#{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                    <div
                                        style="font-weight: 600; color: {{ $player->id === $user->id ? '#a5b4fc' : '#e2e8f0' }}; font-size: 0.9rem;">
                                        {{ $player->name }}
                                        @if ($player->id === $user->id)
                                            <span
                                                style="font-size: 0.7rem; background: #6366f1; color: white; padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.3rem;">YOU</span>
                                        @endif
                                    </div>
                                </div>

                                <div style="font-weight: 700; color: #fbbf24; font-size: 0.9rem;">
                                    {{ number_format($player->points) }} <span
                                        style="font-size: 0.75rem; color: #94a3b8;">PTS</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Point Transactions Ledger -->
                <div class="glass-card" style="padding: 1.75rem;">
                    <h3
                        style="font-size: 1.15rem; font-weight: 800; color: #fff; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-receipt text-emerald-400"></i> Points History
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @forelse($transactions as $tx)
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; font-size: 0.85rem; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.04);">
                                <div style="max-width: 65%;">
                                    <div
                                        style="color: #e2e8f0; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $tx->description }}</div>
                                    <div style="color: #64748b; font-size: 0.75rem;">
                                        {{ $tx->created_at->format('M d, H:i') }}</div>
                                </div>
                                <div style="font-weight: 700; color: {{ $tx->amount >= 0 ? '#34d399' : '#fb7185' }};">
                                    {{ $tx->amount >= 0 ? '+' . $tx->amount : $tx->amount }} PTS
                                </div>
                            </div>
                        @empty
                            <p style="color: #64748b; font-size: 0.85rem; text-align: center;">No transactions recorded
                                yet.</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

    </div>

    <style>
        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }
        }
    </style>
@endsection
