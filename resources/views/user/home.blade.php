@extends('layouts.app')

@section('title', 'Player Hub - Quiwin')

@section('content')
    <div style="display: flex; flex-direction: column; gap: 1.25rem; padding-bottom: 2.5rem;">

        <!-- Hero Banner with Play Action -->
        <div class="glass-card hero-banner"
            style="padding: clamp(1.15rem, 3.5vw, 2.25rem); background: linear-gradient(135deg, rgba(30, 27, 75, 0.85), rgba(15, 23, 42, 0.95)); border: 1px solid rgba(99, 102, 241, 0.35); position: relative; overflow: hidden;">
            <div
                style="position: absolute; right: -20px; bottom: -20px; font-size: clamp(7rem, 14vw, 13rem); color: rgba(99, 102, 241, 0.04); pointer-events: none;">
                <i class="fa-solid fa-gamepad"></i>
            </div>

            <div class="hero-flex"
                style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.25rem; position: relative; z-index: 2;">
                <div class="hero-text" style="flex: 1; min-width: 260px; max-width: 620px;">
                    <div
                        style="display: inline-flex; align-items: center; gap: 0.45rem; background: rgba(99, 102, 241, 0.18); border: 1px solid rgba(99, 102, 241, 0.35); padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.78rem; color: #a5b4fc; font-weight: 700; margin-bottom: 0.5rem;">
                        <i class="fa-solid fa-bolt text-amber-400"></i> Season 1 Live Arena
                    </div>
                    <h1
                        style="font-size: clamp(1.45rem, 4.2vw, 2.35rem); font-weight: 900; line-height: 1.2; color: #fff; letter-spacing: -0.5px;">
                        Ready for the <span
                            style="background: linear-gradient(135deg, #818cf8, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Quiwin
                            Battle</span>?
                    </h1>
                    <p
                        style="color: #cbd5e1; font-size: clamp(0.85rem, 1.8vw, 1rem); margin-top: 0.45rem; line-height: 1.45;">
                        Answer 30 progressive questions across 3 intense rounds. Build streaks for multiplier bonuses, but
                        watch out for penalties on mistakes!
                    </p>

                    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; margin-top: 0.85rem;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.78rem; color: #94a3b8;">
                            <i class="fa-solid fa-layer-group text-indigo-400"></i> 3 Rounds (Easy &bull; Med &bull; Hard)
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.78rem; color: #94a3b8;">
                            <i class="fa-solid fa-stopwatch text-amber-400"></i> Speed Timers per question
                        </div>
                    </div>
                </div>

                <!-- Main Play Card / Action -->
                <div class="glass-card hero-action-card"
                    style="padding: 1.25rem; background: rgba(15, 23, 42, 0.9); border: 1px solid rgba(255, 255, 255, 0.12); flex: 1; min-width: 240px; max-width: 360px; text-align: center; box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);">

                    <div
                        style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.25rem;">
                        Your Points Balance
                    </div>
                    <div
                        style="font-size: clamp(1.85rem, 4.5vw, 2.35rem); font-weight: 900; color: #fbbf24; display: flex; align-items: center; justify-content: center; gap: 0.45rem; text-shadow: 0 0 20px rgba(245, 158, 11, 0.35);">
                        <i class="fa-solid fa-coins"></i>
                        <span>{{ number_format($user->points) }}</span>
                    </div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.85rem;">
                        Match Entry Fee: <strong style="color: #f87171;">-{{ $entryFee ?? 50 }} PTS</strong>
                    </div>

                    @if ($user->points >= ($entryFee ?? 50))
                        <form id="playGameForm" action="{{ route('game.start') }}" method="POST" style="margin: 0;"
                            onsubmit="handlePlayGameSubmit(event, this)">
                            @csrf
                            <button type="submit" id="playGameBtn" class="btn btn-primary"
                                style="width: 100%; padding: 0.85rem; font-size: 1rem; font-weight: 800; border-radius: 0.85rem; background: linear-gradient(135deg, #6366f1, #06b6d4); box-shadow: 0 0 24px rgba(99, 102, 241, 0.5); transition: all 0.2s ease;">
                                <i class="fa-solid fa-gamepad"></i> PLAY GAME (-{{ $entryFee ?? 50 }} PTS)
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-gold"
                            style="width: 100%; padding: 0.8rem; font-size: 0.9rem; border-radius: 0.85rem;"
                            onclick="openTopUpModal()">
                            <i class="fa-solid fa-plus-circle"></i> Need {{ $entryFee ?? 50 }} PTS (Top-Up)
                        </button>
                    @endif

                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.35rem; margin-top: 0.65rem;">
                        <button type="button" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.45rem 0.2rem;"
                            onclick="openTopUpModal()">
                            <i class="fa-solid fa-wallet text-amber-400"></i> Top-Up
                        </button>
                        <button type="button" class="btn btn-outline"
                            style="font-size: 0.75rem; padding: 0.45rem 0.2rem; border-color: rgba(16, 185, 129, 0.4); color: #34d399;"
                            onclick="openWithdrawModal()">
                            <i class="fa-solid fa-money-bill-transfer text-emerald-400"></i> Withdraw
                        </button>
                        <button type="button" class="btn btn-outline"
                            style="font-size: 0.75rem; padding: 0.45rem 0.2rem; border-color: rgba(99, 102, 241, 0.4); color: #a5b4fc;"
                            onclick="openQuestModal('daily')">
                            <i class="fa-solid fa-bullseye text-cyan-400"></i> Quests
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 Stats Cards Grid (Responsive 2x2 on Mobile, 4x1 on Desktop) -->
        <div class="stats-grid">

            <div class="glass-card stat-card"
                style="padding: 0.95rem 0.85rem; display: flex; align-items: center; gap: 0.75rem; border-left: 4px solid #6366f1;">
                <div
                    style="width: 38px; height: 38px; border-radius: 10px; background: rgba(99, 102, 241, 0.15); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.15rem; flex-shrink: 0;">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Matches
                    </div>
                    <div style="font-size: clamp(1.15rem, 3vw, 1.45rem); font-weight: 800; color: #fff; line-height: 1.2;">
                        {{ $totalGames }}</div>
                </div>
            </div>

            <div class="glass-card stat-card"
                style="padding: 0.95rem 0.85rem; display: flex; align-items: center; gap: 0.75rem; border-left: 4px solid #f59e0b;">
                <div
                    style="width: 38px; height: 38px; border-radius: 10px; background: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.15rem; flex-shrink: 0;">
                    <i class="fa-solid fa-fire"></i>
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Best
                        Streak</div>
                    <div style="font-size: clamp(1.15rem, 3vw, 1.45rem); font-weight: 800; color: #fff; line-height: 1.2;">
                        {{ $bestStreak }} 🔥</div>
                </div>
            </div>

            <div class="glass-card stat-card"
                style="padding: 0.95rem 0.85rem; display: flex; align-items: center; gap: 0.75rem; border-left: 4px solid #10b981;">
                <div
                    style="width: 38px; height: 38px; border-radius: 10px; background: rgba(16, 185, 129, 0.15); display: flex; align-items: center; justify-content: center; color: #34d399; font-size: 1.15rem; flex-shrink: 0;">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Accuracy
                    </div>
                    <div style="font-size: clamp(1.15rem, 3vw, 1.45rem); font-weight: 800; color: #fff; line-height: 1.2;">
                        {{ $accuracy }}%</div>
                </div>
            </div>

            @php
                $userPts = (int) $user->points;
                if ($userPts >= 5000) {
                    $rankName = 'Legend';
                    $rankIcon = 'fa-fire-flame-curved';
                    $rankEmoji = '🔥';
                    $rankColor = '#f43f5e';
                    $rankBorder = '#f43f5e';
                    $nextRankText = 'Max Rank Reached';
                    $rankProgress = 100;
                } elseif ($userPts >= 2500) {
                    $rankName = 'Grandmaster';
                    $rankIcon = 'fa-gem';
                    $rankEmoji = '💎';
                    $rankColor = '#06b6d4';
                    $rankBorder = '#06b6d4';
                    $ptsNeeded = 5000 - $userPts;
                    $nextRankText = $ptsNeeded . ' PTS to Legend';
                    $rankProgress = min(100, round((($userPts - 2500) / 2500) * 100));
                } elseif ($userPts >= 1000) {
                    $rankName = 'Master';
                    $rankIcon = 'fa-crown';
                    $rankEmoji = '👑';
                    $rankColor = '#c084fc';
                    $rankBorder = '#a855f7';
                    $ptsNeeded = 2500 - $userPts;
                    $nextRankText = $ptsNeeded . ' PTS to Grandmaster';
                    $rankProgress = min(100, round((($userPts - 1000) / 1500) * 100));
                } elseif ($userPts >= 500) {
                    $rankName = 'Veteran';
                    $rankIcon = 'fa-bolt';
                    $rankEmoji = '⚡';
                    $rankColor = '#fbbf24';
                    $rankBorder = '#f59e0b';
                    $ptsNeeded = 1000 - $userPts;
                    $nextRankText = $ptsNeeded . ' PTS to Master';
                    $rankProgress = min(100, round((($userPts - 500) / 500) * 100));
                } elseif ($userPts >= 200) {
                    $rankName = 'Challenger';
                    $rankIcon = 'fa-shield';
                    $rankEmoji = '⚔️';
                    $rankColor = '#38bdf8';
                    $rankBorder = '#0284c7';
                    $ptsNeeded = 500 - $userPts;
                    $nextRankText = $ptsNeeded . ' PTS to Veteran';
                    $rankProgress = min(100, round((($userPts - 200) / 300) * 100));
                } else {
                    $rankName = 'Novice';
                    $rankIcon = 'fa-shield-halved';
                    $rankEmoji = '🛡️';
                    $rankColor = '#94a3b8';
                    $rankBorder = '#64748b';
                    $ptsNeeded = 200 - $userPts;
                    $nextRankText = $ptsNeeded . ' PTS to Challenger';
                    $rankProgress = min(100, round(($userPts / 200) * 100));
                }
            @endphp

            <!-- Interactive Clickable Rank Stat Card -->
            <div class="glass-card stat-card" onclick="openRankSystemModal()"
                style="padding: 0.95rem 0.85rem; display: flex; align-items: center; gap: 0.75rem; border-left: 4px solid {{ $rankBorder }}; cursor: pointer; transition: all 0.2s ease; position: relative;"
                title="Click to view Ranking System">
                <div
                    style="width: 38px; height: 38px; border-radius: 10px; background: rgba(255, 255, 255, 0.08); display: flex; align-items: center; justify-content: center; color: {{ $rankColor }}; font-size: 1.15rem; flex-shrink: 0; box-shadow: 0 0 12px {{ $rankColor }}33;">
                    <i class="fa-solid {{ $rankIcon }}"></i>
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.25rem;">
                        <span
                            style="font-size: 0.72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Rank</span>
                        <span style="font-size: 0.65rem; color: #6366f1; font-weight: 700;">Tiers <i
                                class="fa-solid fa-chevron-right" style="font-size: 0.55rem;"></i></span>
                    </div>
                    <div
                        style="font-size: clamp(0.95rem, 2.5vw, 1.2rem); font-weight: 800; color: {{ $rankColor }}; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $rankName }} {{ $rankEmoji }}
                    </div>
                </div>
            </div>

        </div>

        <!-- Main Content 2 Columns: Game Rules & Recent Games / Referral & Leaderboard -->
        <div class="hub-main-grid">

            <!-- Left Column: Game Rules / Scoring & Recent Games -->
            <div class="hub-column">

                <!-- Game Rules Card -->
                <div class="glass-card" style="padding: clamp(0.85rem, 2.5vw, 1.25rem);">
                    <h3
                        style="font-size: 1.05rem; font-weight: 800; color: #fff; margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.45rem;">
                        <i class="fa-solid fa-circle-info text-indigo-400"></i> Rules & Pointing System
                    </h3>

                    <!-- Responsive Rules Cards (Horizontal list on mobile, 3-col on desktop) -->
                    <div class="rules-responsive-grid">

                        <!-- Round 1 -->
                        <div class="rule-box"
                            style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 0.75rem; padding: 0.65rem 0.85rem;">
                            <div class="rule-box-info" style="min-width: 0;">
                                <div style="font-size: 0.8rem; font-weight: 800; color: #34d399; text-transform: uppercase; letter-spacing: 0.3px;">
                                    Round 1 (Easy)</div>
                                <div style="font-size: 0.72rem; color: #cbd5e1; margin-top: 0.15rem;">Q1–10 &bull; 5s per question</div>
                            </div>
                            <div class="rule-box-points" style="display: flex; align-items: center; gap: 0.35rem; flex-shrink: 0;">
                                <span style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.15rem 0.45rem; border-radius: 9999px; font-weight: 800; font-size: 0.72rem; white-space: nowrap;">+2 PTS</span>
                                <span style="background: rgba(244, 63, 94, 0.2); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.4); padding: 0.15rem 0.45rem; border-radius: 9999px; font-weight: 800; font-size: 0.72rem; white-space: nowrap;">-3 PTS</span>
                            </div>
                        </div>

                        <!-- Round 2 -->
                        <div class="rule-box"
                            style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 0.75rem; padding: 0.65rem 0.85rem;">
                            <div class="rule-box-info" style="min-width: 0;">
                                <div style="font-size: 0.8rem; font-weight: 800; color: #fbbf24; text-transform: uppercase; letter-spacing: 0.3px;">
                                    Round 2 (Med)</div>
                                <div style="font-size: 0.72rem; color: #cbd5e1; margin-top: 0.15rem;">Q11–20 &bull; 5s per question</div>
                            </div>
                            <div class="rule-box-points" style="display: flex; align-items: center; gap: 0.35rem; flex-shrink: 0;">
                                <span style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.15rem 0.45rem; border-radius: 9999px; font-weight: 800; font-size: 0.72rem; white-space: nowrap;">+3 PTS</span>
                                <span style="background: rgba(244, 63, 94, 0.2); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.4); padding: 0.15rem 0.45rem; border-radius: 9999px; font-weight: 800; font-size: 0.72rem; white-space: nowrap;">-5 PTS</span>
                            </div>
                        </div>

                        <!-- Round 3 -->
                        <div class="rule-box"
                            style="background: rgba(244, 63, 94, 0.08); border: 1px solid rgba(244, 63, 94, 0.3); border-radius: 0.75rem; padding: 0.65rem 0.85rem;">
                            <div class="rule-box-info" style="min-width: 0;">
                                <div style="font-size: 0.8rem; font-weight: 800; color: #fb7185; text-transform: uppercase; letter-spacing: 0.3px;">
                                    Round 3 (Hard)</div>
                                <div style="font-size: 0.72rem; color: #cbd5e1; margin-top: 0.15rem;">Q21–30 &bull; 5s per question</div>
                            </div>
                            <div class="rule-box-points" style="display: flex; align-items: center; gap: 0.35rem; flex-shrink: 0;">
                                <span style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.15rem 0.45rem; border-radius: 9999px; font-weight: 800; font-size: 0.72rem; white-space: nowrap;">+5 PTS</span>
                                <span style="background: rgba(244, 63, 94, 0.2); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.4); padding: 0.15rem 0.45rem; border-radius: 9999px; font-weight: 800; font-size: 0.72rem; white-space: nowrap;">-10 PTS</span>
                            </div>
                        </div>

                    </div>

                    <div
                        style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 0.65rem; padding: 0.65rem 0.85rem; font-size: 0.78rem; color: #94a3b8; line-height: 1.4; word-break: break-word; overflow-wrap: break-word;">
                        🔥 <strong>Streak Bonus:</strong> Consecutive correct answers trigger multiplier bonuses (+1, +2, +5 PTS)!
                    </div>
                </div>

                <!-- Recent Matches Table -->
                <div class="glass-card" style="padding: clamp(0.85rem, 2.5vw, 1.25rem);">
                    <h3
                        style="font-size: 1.05rem; font-weight: 800; color: #fff; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.45rem;">
                        <i class="fa-solid fa-clock-rotate-left text-cyan-400"></i> Your Recent Matches
                    </h3>

                    @if ($recentGames->isEmpty())
                        <div style="text-align: center; color: #64748b; padding: 1.25rem 0.5rem; font-size: 0.82rem;">
                            <i class="fa-solid fa-gamepad"
                                style="font-size: 1.5rem; opacity: 0.4; margin-bottom: 0.35rem; display: block;"></i>
                            No completed matches yet. Click "PLAY GAME" to start!
                        </div>
                    @else
                        <div class="table-responsive" style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem;">
                                <thead>
                                    <tr
                                        style="border-bottom: 1px solid rgba(255,255,255,0.08); color: #64748b; text-align: left; font-size: 0.72rem; text-transform: uppercase;">
                                        <th style="padding: 0.45rem 0.35rem;">Match</th>
                                        <th style="padding: 0.45rem 0.35rem;">Score</th>
                                        <th style="padding: 0.45rem 0.35rem;">Streak</th>
                                        <th style="padding: 0.45rem 0.35rem;">PTS</th>
                                        <th style="padding: 0.45rem 0.35rem; text-align: right;">Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentGames as $game)
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); color: #cbd5e1;">
                                            <td style="padding: 0.55rem 0.35rem; font-weight: 700; color: #fff; white-space: nowrap;">
                                                #{{ $game->id }}
                                            </td>
                                            <td style="padding: 0.55rem 0.35rem; white-space: nowrap;">
                                                <span style="color: #34d399; font-weight: 700;">{{ $game->total_correct }}</span>/30
                                            </td>
                                            <td style="padding: 0.55rem 0.35rem; white-space: nowrap;">
                                                {{ $game->max_streak }} 🔥
                                            </td>
                                            <td
                                                style="padding: 0.55rem 0.35rem; font-weight: 800; color: {{ $game->points_delta >= 0 ? '#34d399' : '#fb7185' }}; white-space: nowrap;">
                                                {{ $game->points_delta >= 0 ? '+' . $game->points_delta : $game->points_delta }}
                                            </td>
                                            <td style="padding: 0.55rem 0.35rem; color: #64748b; font-size: 0.72rem; text-align: right; white-space: nowrap;">
                                                {{ $game->created_at->diffForHumans(null, true, true) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>

            <!-- Right Column: Quests, Leaderboard & Points Ledger -->
            <div class="hub-column">

                <!-- COMPACT CLICKABLE QUESTS & REWARDS WIDGET -->
                <div class="glass-card"
                    style="padding: clamp(0.85rem, 2.5vw, 1.15rem); background: linear-gradient(135deg, rgba(15, 23, 42, 0.92), rgba(30, 27, 75, 0.75)); border: 1px solid rgba(99, 102, 241, 0.35); position: relative; overflow: hidden;">

                    <div
                        style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.45rem;">
                            <div
                                style="width: 30px; height: 30px; border-radius: 8px; background: rgba(99, 102, 241, 0.25); display: flex; align-items: center; justify-content: center; color: #a5b4fc; font-size: 0.95rem; flex-shrink: 0;">
                                <i class="fa-solid fa-bullseye text-cyan-400"></i>
                            </div>
                            <h3 style="font-size: 1rem; font-weight: 800; color: #fff;">Quests & Rewards</h3>
                        </div>
                        <button type="button" onclick="openQuestModal('daily')" class="btn btn-outline"
                            style="padding: 0.2rem 0.55rem; font-size: 0.72rem; min-height: 28px; border-color: rgba(99, 102, 241, 0.4); color: #a5b4fc; border-radius: 9999px; flex-shrink: 0;">
                            View All &rarr;
                        </button>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.55rem;">

                        <!-- Clickable Quest Item 1: 7-Day Daily Play -->
                        <div onclick="openQuestModal('daily')" class="quest-interactive-item"
                            style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(99, 102, 241, 0.25); border-radius: 0.75rem; padding: 0.65rem 0.75rem; cursor: pointer; transition: all 0.2s ease;">
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.35rem;">
                                <div style="display: flex; align-items: center; gap: 0.45rem; min-width: 0; flex: 1;">
                                    <div
                                        style="width: 26px; height: 26px; border-radius: 7px; background: rgba(99, 102, 241, 0.2); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 0.78rem; flex-shrink: 0;">
                                        <i class="fa-solid fa-calendar-check"></i>
                                    </div>
                                    <div style="min-width: 0; flex: 1;">
                                        <div style="font-size: 0.82rem; font-weight: 800; color: #fff; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            7-Day Daily Play Quest
                                        </div>
                                        <div
                                            style="font-size: 0.68rem; color: {{ $playedToday ? '#34d399' : '#fbbf24' }}; font-weight: 600; line-height: 1.2; margin-top: 0.12rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            @if ($playedToday)
                                                <i class="fa-solid fa-circle-check"></i> Played Today &bull; {{ $weeklyQuestProgress }}/7 Days
                                            @else
                                                <i class="fa-solid fa-fire text-amber-400"></i> Day {{ $weeklyQuestProgress + 1 }} Ready &bull; Play 1 Match
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align: right; display: flex; align-items: center; gap: 0.35rem; flex-shrink: 0;">
                                    <span
                                        style="font-size: 0.72rem; font-weight: 800; background: rgba(99, 102, 241, 0.2); color: #38bdf8; border: 1px solid rgba(99, 102, 241, 0.4); padding: 0.12rem 0.45rem; border-radius: 9999px; white-space: nowrap;">
                                        +{{ number_format($weeklyQuestReward) }} PTS
                                    </span>
                                    <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem; color: #64748b;"></i>
                                </div>
                            </div>
                            <div
                                style="width: 100%; height: 5px; background: rgba(255,255,255,0.06); border-radius: 9999px; overflow: hidden;">
                                <div
                                    style="height: 100%; width: {{ ($weeklyQuestProgress / 7) * 100 }}%; background: linear-gradient(90deg, #6366f1, #06b6d4); border-radius: 9999px;">
                                </div>
                            </div>
                        </div>

                        <!-- Clickable Quest Item 2: 5 Friends Referral -->
                        <div onclick="openQuestModal('referral')" class="quest-interactive-item"
                            style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: 0.75rem; padding: 0.65rem 0.75rem; cursor: pointer; transition: all 0.2s ease;">
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.35rem;">
                                <div style="display: flex; align-items: center; gap: 0.45rem; min-width: 0; flex: 1;">
                                    <div
                                        style="width: 26px; height: 26px; border-radius: 7px; background: rgba(245, 158, 11, 0.2); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 0.78rem; flex-shrink: 0;">
                                        <i class="fa-solid fa-gift"></i>
                                    </div>
                                    <div style="min-width: 0; flex: 1;">
                                        <div style="font-size: 0.82rem; font-weight: 800; color: #fff; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            Invite & Earn Quest
                                        </div>
                                        <div style="font-size: 0.68rem; color: #a5b4fc; font-weight: 600; line-height: 1.2; margin-top: 0.12rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <i class="fa-solid fa-users"></i> {{ $referralQuestProgress }}/5 Approved Friends
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align: right; display: flex; align-items: center; gap: 0.35rem; flex-shrink: 0;">
                                    <span
                                        style="font-size: 0.72rem; font-weight: 800; background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 0.12rem 0.45rem; border-radius: 9999px; white-space: nowrap;">
                                        +{{ number_format($referralQuestReward) }} PTS
                                    </span>
                                    <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem; color: #64748b;"></i>
                                </div>
                            </div>
                            <div
                                style="width: 100%; height: 5px; background: rgba(255,255,255,0.06); border-radius: 9999px; overflow: hidden;">
                                <div
                                    style="height: 100%; width: {{ ($referralQuestProgress / 5) * 100 }}%; background: linear-gradient(90deg, #f59e0b, #10b981); border-radius: 9999px;">
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Leaderboard -->
                <div class="glass-card" style="padding: clamp(0.85rem, 2.5vw, 1.25rem);">
                    <div
                        style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <h3
                            style="font-size: 1.05rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 0.45rem;">
                            <i class="fa-solid fa-trophy text-amber-400"></i> Leaderboard
                        </h3>
                        <span style="font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; font-weight: 600;">Top
                            Players</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                        @foreach ($leaderboard as $index => $player)
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; padding: 0.45rem 0.65rem; border-radius: 0.65rem; background: {{ $player->id === $user->id ? 'rgba(99, 102, 241, 0.2)' : 'rgba(255, 255, 255, 0.03)' }}; border: 1px solid {{ $player->id === $user->id ? 'rgba(99, 102, 241, 0.4)' : 'rgba(255, 255, 255, 0.05)' }}; gap: 0.4rem;">

                                <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 0; flex: 1;">
                                    <div
                                        style="width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; flex-shrink: 0;">
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
                                        style="font-weight: 600; color: {{ $player->id === $user->id ? '#a5b4fc' : '#e2e8f0' }}; font-size: 0.82rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $player->name }}
                                        @if ($player->id === $user->id)
                                            <span
                                                style="font-size: 0.62rem; background: #6366f1; color: white; padding: 0.08rem 0.3rem; border-radius: 4px; margin-left: 0.15rem;">YOU</span>
                                        @endif
                                    </div>
                                </div>

                                <div style="font-weight: 800; color: #fbbf24; font-size: 0.82rem; flex-shrink: 0;">
                                    {{ number_format($player->points) }} <span
                                        style="font-size: 0.68rem; color: #94a3b8;">PTS</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Point Transactions Ledger -->
                <div class="glass-card" style="padding: clamp(0.85rem, 2.5vw, 1.25rem);">
                    <h3
                        style="font-size: 1.05rem; font-weight: 800; color: #fff; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.45rem;">
                        <i class="fa-solid fa-receipt text-emerald-400"></i> Points History
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                        @forelse($transactions as $tx)
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; font-size: 0.78rem; padding: 0.35rem 0; border-bottom: 1px solid rgba(255,255,255,0.04); gap: 0.5rem;">
                                <div style="min-width: 0; flex: 1;">
                                    <div
                                        style="color: #e2e8f0; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $tx->description }}</div>
                                    <div style="color: #64748b; font-size: 0.68rem;">
                                        {{ $tx->created_at->format('M d, H:i') }}</div>
                                </div>
                                <div
                                    style="font-weight: 800; color: {{ $tx->amount >= 0 ? '#34d399' : '#fb7185' }}; white-space: nowrap; flex-shrink: 0;">
                                    {{ $tx->amount >= 0 ? '+' . $tx->amount : $tx->amount }} PTS
                                </div>
                            </div>
                        @empty
                            <p style="color: #64748b; font-size: 0.78rem; text-align: center;">No transactions recorded
                                yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- GCash Withdrawal Payout History -->
                <div class="glass-card"
                    style="padding: clamp(0.85rem, 2.5vw, 1.25rem); border: 1px solid rgba(16, 185, 129, 0.3);">
                    <div
                        style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.4rem;">
                        <h3
                            style="font-size: 1.05rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 0.45rem;">
                            <i class="fa-solid fa-money-bill-transfer text-emerald-400"></i> GCash Payouts
                        </h3>
                        <button type="button" class="btn btn-outline"
                            style="font-size: 0.72rem; padding: 0.25rem 0.55rem; min-height: 30px; border-color: rgba(16, 185, 129, 0.4); color: #34d399;"
                            onclick="openWithdrawModal()">
                            + Request
                        </button>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.45rem;">
                        @forelse($withdrawals as $w)
                            <div
                                style="background: rgba(15, 23, 42, 0.6); border: 1px solid {{ $w->status === 'approved' ? 'rgba(16, 185, 129, 0.4)' : ($w->status === 'rejected' ? 'rgba(244, 63, 94, 0.3)' : 'rgba(245, 158, 11, 0.3)') }}; border-radius: 0.65rem; padding: 0.55rem 0.65rem;">
                                <div
                                    style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.2rem; flex-wrap: wrap; gap: 0.25rem;">
                                    <span style="font-weight: 800; font-size: 0.9rem; color: #fff;">
                                        ₱{{ number_format($w->amount) }} <span
                                            style="font-size: 0.68rem; color: #94a3b8;">PHP</span>
                                    </span>
                                    <span
                                        style="font-size: 0.68rem; font-weight: 700; padding: 0.1rem 0.4rem; border-radius: 9999px; text-transform: uppercase;
                                        @if ($w->status === 'approved') background: rgba(16, 185, 129, 0.2); color: #34d399;
                                        @elseif($w->status === 'pending') background: rgba(245, 158, 11, 0.2); color: #fbbf24;
                                        @else background: rgba(244, 63, 94, 0.2); color: #fb7185; @endif">
                                        {{ $w->status === 'approved' ? 'Sent' : $w->status }}
                                    </span>
                                </div>
                                <div
                                    style="font-size: 0.72rem; color: #94a3b8; display: flex; justify-content: space-between; align-items: center;">
                                    <span>GCash: {{ $w->gcash_number }}</span>
                                    <span>{{ $w->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; color: #64748b; padding: 0.75rem 0; font-size: 0.78rem;">
                                No withdrawal requests yet.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Matchmaking / Game Preparing Modal -->
    <div id="gameLoadingModal" class="modal-overlay">
        <div class="modal-card"
            style="text-align: center; padding: 2rem 1.5rem; max-width: 420px; border: 1px solid rgba(99, 102, 241, 0.4); background: radial-gradient(circle at top, rgba(30, 27, 75, 0.95), #090d16 80%); box-shadow: 0 0 50px rgba(99, 102, 241, 0.4);">

            <!-- Animated Glowing Portal Orb -->
            <div
                style="position: relative; width: 80px; height: 80px; margin: 0 auto 1.25rem; display: flex; align-items: center; justify-content: center;">
                <div
                    style="position: absolute; width: 100%; height: 100%; border-radius: 50%; border: 3px solid transparent; border-top-color: #6366f1; border-right-color: #06b6d4; animation: spinOrb 1s linear infinite;">
                </div>
                <div
                    style="position: absolute; width: 72%; height: 72%; border-radius: 50%; border: 3px dashed rgba(245, 158, 11, 0.6); animation: spinOrbReverse 2s linear infinite;">
                </div>
                <img src="{{ asset('images/logo.jpg') }}" alt="Quiwin"
                    style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; box-shadow: 0 0 25px rgba(99, 102, 241, 0.9); z-index: 2; border: 1.5px solid rgba(99, 102, 241, 0.6);">
            </div>

            <h3 style="font-size: 1.35rem; font-weight: 900; color: #fff; margin-bottom: 0.35rem; letter-spacing: -0.5px;">
                Preparing for a game...
            </h3>
            <p id="loadingStatusText"
                style="color: #cbd5e1; font-size: 0.85rem; margin-bottom: 1.25rem; line-height: 1.4; min-height: 38px;">
                Loading questions...
            </p>

            <!-- Loading Step Progress Bar -->
            <div
                style="width: 100%; height: 6px; background: rgba(255,255,255,0.08); border-radius: 9999px; overflow: hidden; position: relative;">
                <div id="loadingProgressBar"
                    style="height: 100%; width: 25%; background: linear-gradient(90deg, #6366f1, #06b6d4, #10b981); border-radius: 9999px; transition: width 0.7s cubic-bezier(0.4, 0, 0.2, 1);">
                </div>
            </div>

        </div>
    </div>

    <!-- COMPREHENSIVE QUESTS & MISSIONS CENTER MODAL -->
    <div id="questsModal" class="modal-overlay">
        <div class="modal-card"
            style="max-width: 580px; width: 95%; padding: 0; overflow: hidden; border: 1px solid rgba(99, 102, 241, 0.4); background: radial-gradient(circle at top, rgba(30, 27, 75, 0.98), #090d16 85%); box-shadow: 0 0 50px rgba(0, 0, 0, 0.85); border-radius: 1.25rem;">

            <!-- Modal Header -->
            <div
                style="padding: 1.2rem 1.4rem 0.9rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.08); background: rgba(15, 23, 42, 0.7);">
                <div style="display: flex; align-items: center; gap: 0.65rem;">
                    <div
                        style="width: 38px; height: 38px; border-radius: 11px; background: linear-gradient(135deg, #6366f1, #06b6d4); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.15rem; box-shadow: 0 0 20px rgba(99, 102, 241, 0.5);">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.15rem; font-weight: 900; color: #fff; line-height: 1.2;">Quests & Rewards
                            Center</h3>
                        <p style="font-size: 0.75rem; color: #94a3b8; margin: 0;">Complete missions to claim thousands of
                            free points!</p>
                    </div>
                </div>
                <button type="button" onclick="closeQuestModal()"
                    style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: #94a3b8; font-size: 1rem; cursor: pointer; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Tab Switcher Navigation Bar -->
            <div
                style="display: flex; border-bottom: 1px solid rgba(255,255,255,0.08); background: rgba(15, 23, 42, 0.9);">
                <button type="button" id="tabBtnDaily" class="quest-tab-btn active" onclick="switchQuestTab('daily')">
                    <i class="fa-solid fa-calendar-check text-cyan-400"></i>
                    <span>7-Day Streak (+{{ number_format($weeklyQuestReward) }} PTS)</span>
                </button>
                <button type="button" id="tabBtnReferral" class="quest-tab-btn" onclick="switchQuestTab('referral')">
                    <i class="fa-solid fa-gift text-amber-400"></i>
                    <span>Invite & Earn (+{{ number_format($referralQuestReward) }} PTS)</span>
                </button>
            </div>

            <!-- Modal Content Body -->
            <div style="padding: 1.25rem 1.4rem; max-height: 72vh; overflow-y: auto;">

                <!-- TAB 1: 7-DAY DAILY PLAY QUEST -->
                <div id="questTabContentDaily" class="quest-tab-content active">

                    <!-- Reward Highlight Card -->
                    <div
                        style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(6, 182, 212, 0.15)); border: 1px solid rgba(99, 102, 241, 0.4); border-radius: 0.85rem; padding: 0.85rem 1rem; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                        <div>
                            <div style="font-size: 0.72rem; font-weight: 700; color: #a5b4fc; text-transform: uppercase;">
                                Mission Reward</div>
                            <div style="font-size: 1.25rem; font-weight: 900; color: #38bdf8;">
                                +{{ number_format($weeklyQuestReward) }} Bonus Points</div>
                        </div>
                        <div>
                            <span
                                style="font-size: 0.75rem; font-weight: 800; padding: 0.25rem 0.65rem; border-radius: 9999px; background: {{ $playedToday ? 'rgba(16, 185, 129, 0.2)' : 'rgba(245, 158, 11, 0.2)' }}; color: {{ $playedToday ? '#34d399' : '#fbbf24' }}; border: 1px solid {{ $playedToday ? 'rgba(16, 185, 129, 0.4)' : 'rgba(245, 158, 11, 0.4)' }};">
                                @if ($playedToday)
                                    <i class="fa-solid fa-circle-check"></i> Played Today
                                @else
                                    <i class="fa-solid fa-clock"></i> Match Required Today
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- 7-Day Timeline Tracker -->
                    <div
                        style="margin-bottom: 1rem; background: rgba(15, 23, 42, 0.85); border: 1px solid rgba(255,255,255,0.08); border-radius: 0.85rem; padding: 1rem 0.75rem;">
                        <div
                            style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.75rem; display: flex; justify-content: space-between;">
                            <span>7-Day Streak Timeline</span>
                            <span style="color: #fbbf24;">{{ $weeklyQuestProgress }}/7 Days</span>
                        </div>

                        <div
                            style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.35rem; text-align: center;">
                            @for ($day = 1; $day <= 7; $day++)
                                @php
                                    $isCompletedDay = $day <= $weeklyQuestProgress;
                                    $isTodayTarget = !$playedToday && $day === $weeklyQuestProgress + 1;
                                @endphp
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 0.3rem;">
                                    <div
                                        style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.82rem; font-weight: 800;
                                        @if ($isCompletedDay) background: linear-gradient(135deg, #10b981, #059669); color: #fff; box-shadow: 0 0 14px rgba(16, 185, 129, 0.6); border: 2px solid #34d399;
                                        @elseif($isTodayTarget)
                                            background: rgba(245, 158, 11, 0.25); color: #fbbf24; border: 2px dashed #f59e0b;
                                        @else
                                            background: rgba(255, 255, 255, 0.04); color: #64748b; border: 1px solid rgba(255,255,255,0.08); @endif
                                    ">
                                        @if ($isCompletedDay)
                                            <i class="fa-solid fa-check"></i>
                                        @elseif($isTodayTarget)
                                            <i class="fa-solid fa-fire text-amber-400"></i>
                                        @else
                                            {{ $day }}
                                        @endif
                                    </div>
                                    <span
                                        style="font-size: 0.7rem; font-weight: 700; color: {{ $isCompletedDay ? '#34d399' : ($isTodayTarget ? '#fbbf24' : '#64748b') }};">
                                        Day {{ $day }}
                                    </span>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div style="margin-bottom: 1rem;">
                        <div
                            style="display: flex; justify-content: space-between; font-size: 0.78rem; font-weight: 700; color: #cbd5e1; margin-bottom: 0.35rem;">
                            <span>Overall Progress</span>
                            <span style="color: #38bdf8;">{{ round(($weeklyQuestProgress / 7) * 100) }}%</span>
                        </div>
                        <div
                            style="width: 100%; height: 8px; background: rgba(255,255,255,0.08); border-radius: 9999px; overflow: hidden;">
                            <div
                                style="height: 100%; width: {{ ($weeklyQuestProgress / 7) * 100 }}%; background: linear-gradient(90deg, #6366f1, #06b6d4); border-radius: 9999px;">
                            </div>
                        </div>
                    </div>

                    <!-- Rules List -->
                    <div
                        style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 0.85rem; font-size: 0.78rem; color: #94a3b8; line-height: 1.5; margin-bottom: 1.25rem;">
                        <div style="font-weight: 700; color: #fff; margin-bottom: 0.25rem;"><i
                                class="fa-solid fa-circle-info text-indigo-400"></i> How it works:</div>
                        &bull; Play at least 1 match everyday to build your daily streak.<br>
                        &bull; Once you reach <strong>7 consecutive days</strong>, +300 points will be credited to your
                        account automatically!<br>
                        &bull; Completed so far: <strong>{{ $weeklyQuestClaims }}x weekly cycles</strong>. Keep your streak
                        going!
                    </div>

                    <!-- Action Button -->
                    @if (!$playedToday)
                        <form action="{{ route('game.start') }}" method="POST"
                            onsubmit="handlePlayGameSubmit(event, this)">
                            @csrf
                            <button type="submit" class="btn btn-primary"
                                style="width: 100%; padding: 0.75rem; font-size: 0.95rem; font-weight: 800; border-radius: 0.75rem; background: linear-gradient(135deg, #6366f1, #06b6d4);">
                                <i class="fa-solid fa-play"></i> Play Today's Match (-{{ $entryFee ?? 50 }} PTS)
                            </button>
                        </form>
                    @else
                        <div
                            style="text-align: center; padding: 0.75rem; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); border-radius: 0.75rem; color: #34d399; font-size: 0.88rem; font-weight: 700;">
                            <i class="fa-solid fa-circle-check"></i> Great job! Today's streak match is completed. Come
                            back tomorrow!
                        </div>
                    @endif

                </div>

                <!-- TAB 2: INVITE & EARN QUEST -->
                <div id="questTabContentReferral" class="quest-tab-content">

                    <!-- Reward Highlight Card -->
                    <div
                        style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(239, 68, 68, 0.15)); border: 1px solid rgba(245, 158, 11, 0.4); border-radius: 0.85rem; padding: 0.85rem 1rem; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                        <div>
                            <div style="font-size: 0.72rem; font-weight: 700; color: #fbbf24; text-transform: uppercase;">
                                Mission Reward</div>
                            <div style="font-size: 1.25rem; font-weight: 900; color: #fbbf24;">
                                +{{ number_format($referralQuestReward) }} Bonus Points</div>
                        </div>
                        <div>
                            <span
                                style="font-size: 0.75rem; font-weight: 800; padding: 0.25rem 0.65rem; border-radius: 9999px; background: {{ $approvedReferralsCount >= 5 ? 'rgba(16, 185, 129, 0.2)' : 'rgba(245, 158, 11, 0.2)' }}; color: {{ $approvedReferralsCount >= 5 ? '#34d399' : '#fbbf24' }}; border: 1px solid {{ $approvedReferralsCount >= 5 ? 'rgba(16, 185, 129, 0.4)' : 'rgba(245, 158, 11, 0.4)' }};">
                                {{ $approvedReferralsCount }} / 5 Approved Friends
                            </span>
                        </div>
                    </div>

                    <!-- Personal Coupon Code Display Box -->
                    <div
                        style="background: rgba(15, 23, 42, 0.85); border: 1px dashed rgba(99, 102, 241, 0.5); border-radius: 0.85rem; padding: 0.95rem; margin-bottom: 1rem;">
                        <div
                            style="font-size: 0.72rem; font-weight: 700; color: #94a3b8; margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            Your Personal Coupon Code
                        </div>
                        <div
                            style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap;">
                            <div
                                style="font-family: monospace; font-size: 1.35rem; font-weight: 900; color: #38bdf8; letter-spacing: 1.5px;">
                                {{ $user->referral_code }}
                            </div>
                            <div style="display: flex; gap: 0.4rem;">
                                <button type="button" class="btn btn-outline"
                                    style="padding: 0.35rem 0.75rem; font-size: 0.78rem; border-color: rgba(99, 102, 241, 0.4); color: #a5b4fc; min-height: 34px;"
                                    onclick="copyReferralCode('{{ $user->referral_code }}')" id="copyCodeBtn">
                                    <i class="fa-solid fa-copy"></i> Copy Code
                                </button>
                                <button type="button" class="btn btn-gold"
                                    style="padding: 0.35rem 0.75rem; font-size: 0.78rem; min-height: 34px;"
                                    onclick="copyReferralLink('{{ url('/register?ref=' . $user->referral_code) }}')"
                                    id="copyLinkBtn">
                                    <i class="fa-solid fa-link"></i> Copy Link
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div style="margin-bottom: 1rem;">
                        <div
                            style="display: flex; justify-content: space-between; font-size: 0.78rem; font-weight: 700; color: #cbd5e1; margin-bottom: 0.35rem;">
                            <span>Invited Friends Goal</span>
                            <span style="color: #fbbf24;">{{ $referralQuestProgress }} / 5
                                ({{ round(($referralQuestProgress / 5) * 100) }}%)</span>
                        </div>
                        <div
                            style="width: 100%; height: 8px; background: rgba(255,255,255,0.08); border-radius: 9999px; overflow: hidden;">
                            <div
                                style="height: 100%; width: {{ ($referralQuestProgress / 5) * 100 }}%; background: linear-gradient(90deg, #f59e0b, #10b981); border-radius: 9999px;">
                            </div>
                        </div>
                    </div>

                    <!-- Friends Invited List -->
                    @if ($referralsList->isNotEmpty())
                        <div
                            style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.85rem; padding: 0.85rem; margin-bottom: 1rem;">
                            <div
                                style="font-size: 0.72rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.45rem;">
                                Your Referred Friends ({{ $referralsList->count() }})
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                                @foreach ($referralsList as $refFriend)
                                    <div
                                        style="display: flex; align-items: center; justify-content: space-between; font-size: 0.78rem; padding: 0.35rem 0.55rem; background: rgba(255,255,255,0.02); border-radius: 0.45rem;">
                                        <div style="color: #cbd5e1; font-weight: 500;">
                                            <i class="fa-solid fa-circle-user text-indigo-400"></i> {{ $refFriend->name }}
                                        </div>
                                        <div>
                                            @if ($refFriend->status === 'approved')
                                                <span style="color: #34d399; font-weight: 700; font-size: 0.72rem;">
                                                    <i class="fa-solid fa-check"></i> Approved (+1)
                                                </span>
                                            @elseif($refFriend->status === 'pending')
                                                <span style="color: #fbbf24; font-weight: 600; font-size: 0.72rem;">
                                                    <i class="fa-solid fa-clock"></i> Pending Approval
                                                </span>
                                            @else
                                                <span style="color: #f87171; font-size: 0.72rem;">
                                                    <i class="fa-solid fa-ban"></i> Rejected
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div
                        style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 0.85rem; font-size: 0.78rem; color: #94a3b8; line-height: 1.5;">
                        <div style="font-weight: 700; color: #fff; margin-bottom: 0.25rem;"><i
                                class="fa-solid fa-circle-info text-amber-400"></i> How it works:</div>
                        Share your personal coupon code with friends. When they register using your code and are approved by
                        the admin, you'll earn <strong>1,000 points</strong> upon reaching 5 friends!
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- COMPREHENSIVE RANKING SYSTEM MODAL (0 to 5,000+ PTS) -->
    <div id="rankSystemModal" class="modal-overlay">
        <div class="modal-card"
            style="max-width: 620px; width: 95%; padding: 0; overflow: hidden; border: 1px solid rgba(99, 102, 241, 0.4); background: radial-gradient(circle at top, rgba(30, 27, 75, 0.98), #090d16 85%); box-shadow: 0 0 50px rgba(0, 0, 0, 0.9); border-radius: 1.25rem;">

            <!-- Header -->
            <div
                style="padding: 1.2rem 1.4rem 0.9rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.08); background: rgba(15, 23, 42, 0.7);">
                <div style="display: flex; align-items: center; gap: 0.65rem;">
                    <div
                        style="width: 38px; height: 38px; border-radius: 11px; background: linear-gradient(135deg, #f59e0b, #f43f5e); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.15rem; box-shadow: 0 0 20px rgba(244, 63, 94, 0.4);">
                        <i class="fa-solid fa-ranking-star"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.15rem; font-weight: 900; color: #fff; line-height: 1.2;">Quiwin Ranking
                            System</h3>
                        <p style="font-size: 0.75rem; color: #94a3b8; margin: 0;">Climb from Novice (0 PTS) all the way to
                            Quiwin Legend (5,000+ PTS)!</p>
                    </div>
                </div>
                <button type="button" onclick="closeRankSystemModal()"
                    style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: #94a3b8; font-size: 1rem; cursor: pointer; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div style="padding: 1.25rem 1.4rem; max-height: 72vh; overflow-y: auto;">

                <!-- Player Current Rank Banner -->
                <div
                    style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(30, 27, 75, 0.75)); border: 1px solid {{ $rankBorder }}; border-radius: 0.95rem; padding: 1rem; margin-bottom: 0.85rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div
                            style="width: 48px; height: 48px; border-radius: 14px; background: rgba(255,255,255,0.06); border: 2px solid {{ $rankBorder }}; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: {{ $rankColor }}; box-shadow: 0 0 20px {{ $rankColor }}44;">
                            <i class="fa-solid {{ $rankIcon }}"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.72rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">
                                Your Current Tier</div>
                            <div style="font-size: 1.25rem; font-weight: 900; color: {{ $rankColor }};">
                                {{ $rankName }} {{ $rankEmoji }}
                            </div>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 600;">Your Balance</div>
                        <div style="font-size: 1.2rem; font-weight: 900; color: #fbbf24;">
                            <span id="rankModalPointsDisplay">{{ number_format($userPts) }}</span> <span style="font-size: 0.75rem; color: #94a3b8;">PTS</span>
                        </div>
                    </div>
                </div>

                <!-- Anti-Abuse Protection Notice & Match Requirement Progress -->
                <div
                    style="background: rgba(15, 23, 42, 0.85); border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 0.85rem; padding: 0.75rem 0.95rem; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 0; flex: 1;">
                        <div
                            style="width: 32px; height: 32px; border-radius: 8px; background: rgba(99, 102, 241, 0.2); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 0.9rem; flex-shrink: 0;">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div style="min-width: 0; flex: 1;">
                            <div style="font-size: 0.78rem; font-weight: 800; color: #fff; line-height: 1.2;">
                                Anti-Abuse Rule: Play 20 Matches
                            </div>
                            <div style="font-size: 0.7rem; color: #94a3b8; line-height: 1.2; margin-top: 0.15rem;">
                                You must complete at least 20 matches before claiming rank milestone points.
                            </div>
                        </div>
                    </div>
                    <div style="text-align: right; flex-shrink: 0;">
                        <span
                            style="font-size: 0.75rem; font-weight: 800; padding: 0.25rem 0.65rem; border-radius: 9999px; background: {{ $totalGames >= 20 ? 'rgba(16, 185, 129, 0.2)' : 'rgba(245, 158, 11, 0.2)' }}; color: {{ $totalGames >= 20 ? '#34d399' : '#fbbf24' }}; border: 1px solid {{ $totalGames >= 20 ? 'rgba(16, 185, 129, 0.4)' : 'rgba(245, 158, 11, 0.4)' }};">
                            @if ($totalGames >= 20)
                                <i class="fa-solid fa-circle-check"></i> {{ $totalGames }}/20 Matches (Unlocked)
                            @else
                                <i class="fa-solid fa-gamepad"></i> {{ $totalGames }}/20 Matches ({{ 20 - $totalGames }} Left)
                            @endif
                        </span>
                    </div>
                </div>

                <!-- 6 Tiers Grid / Progression Ladder with Claimable Rewards -->
                <div style="display: flex; flex-direction: column; gap: 0.65rem;">

                    <!-- Tier 6: Legend (5000+ PTS) -> +1,000 PTS Reward -->
                    @php
                        $isCurrentLegend = ($userPts >= 5000);
                        $isClaimedLegend = in_array('legend', $claimedRankRewards ?? [], true);
                        $canClaimLegend = ($userPts >= 5000 && $totalGames >= 20 && !$isClaimedLegend);
                    @endphp
                    <div class="rank-tier-card {{ $isCurrentLegend ? 'active-tier' : '' }}"
                        style="background: rgba(15, 23, 42, 0.7); border: 1px solid {{ $isCurrentLegend ? '#f43f5e' : 'rgba(244, 63, 94, 0.3)' }}; border-radius: 0.85rem; padding: 0.85rem 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; position: relative;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                            <div
                                style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, rgba(244, 63, 94, 0.25), rgba(255, 0, 122, 0.35)); border: 1.5px solid #f43f5e; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #f43f5e; flex-shrink: 0; box-shadow: 0 0 15px rgba(244, 63, 94, 0.4);">
                                <i class="fa-solid fa-fire-flame-curved"></i>
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                    <span style="font-size: 0.95rem; font-weight: 900; color: #f43f5e;">Legend 🔥</span>
                                    <span
                                        style="font-size: 0.68rem; font-weight: 800; background: rgba(244, 63, 94, 0.2); color: #fda4af; padding: 0.1rem 0.45rem; border-radius: 9999px;">5,000+ PTS</span>
                                    <span
                                        style="font-size: 0.68rem; font-weight: 800; background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 0.1rem 0.45rem; border-radius: 9999px;">🎁 +1,000 PTS Reward</span>
                                </div>
                                <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.15rem;">The pinnacle of Quiwin trivia gods. Highest prestige & glory!</div>
                            </div>
                        </div>
                        <div style="flex-shrink: 0; text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 0.3rem;">
                            @if ($isClaimedLegend)
                                <span style="font-size: 0.72rem; font-weight: 800; color: #34d399; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.25rem 0.65rem; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.3rem;">
                                    <i class="fa-solid fa-circle-check"></i> Claimed (+1,000 PTS)
                                </span>
                            @elseif ($canClaimLegend)
                                <button type="button" onclick="claimRankTierReward('legend', this)" class="btn btn-gold claim-rank-btn"
                                    style="font-size: 0.75rem; font-weight: 800; padding: 0.3rem 0.85rem; border-radius: 9999px; cursor: pointer;">
                                    <i class="fa-solid fa-gift"></i> Claim +1,000 PTS
                                </button>
                            @elseif ($userPts >= 5000 && $totalGames < 20)
                                <span style="font-size: 0.7rem; font-weight: 700; color: #fbbf24; background: rgba(245, 158, 11, 0.15); border: 1px dashed rgba(245, 158, 11, 0.4); padding: 0.22rem 0.55rem; border-radius: 9999px;"
                                    title="Play 20 matches to unlock claim">
                                    <i class="fa-solid fa-lock text-amber-400"></i> Need 20 Matches ({{ $totalGames }}/20)
                                </span>
                            @else
                                <span style="font-size: 0.7rem; font-weight: 700; color: #64748b; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); padding: 0.22rem 0.55rem; border-radius: 9999px;">
                                    <i class="fa-solid fa-lock"></i> 5,000 PTS (+1k)
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Tier 5: Grandmaster (2500 - 4999 PTS) -> +500 PTS Reward -->
                    @php
                        $isCurrentGrandmaster = ($userPts >= 2500 && $userPts < 5000);
                        $isPastGrandmaster = ($userPts >= 5000);
                        $isClaimedGrandmaster = in_array('grandmaster', $claimedRankRewards ?? [], true);
                        $canClaimGrandmaster = ($userPts >= 2500 && $totalGames >= 20 && !$isClaimedGrandmaster);
                    @endphp
                    <div class="rank-tier-card {{ $isCurrentGrandmaster ? 'active-tier' : '' }}"
                        style="background: rgba(15, 23, 42, 0.7); border: 1px solid {{ $isCurrentGrandmaster ? '#06b6d4' : 'rgba(6, 182, 212, 0.3)' }}; border-radius: 0.85rem; padding: 0.85rem 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                            <div
                                style="width: 42px; height: 42px; border-radius: 12px; background: rgba(6, 182, 212, 0.2); border: 1.5px solid #06b6d4; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #22d3ee; flex-shrink: 0; box-shadow: 0 0 15px rgba(6, 182, 212, 0.3);">
                                <i class="fa-solid fa-gem"></i>
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                    <span style="font-size: 0.95rem; font-weight: 900; color: #38bdf8;">Grandmaster 💎</span>
                                    <span
                                        style="font-size: 0.68rem; font-weight: 800; background: rgba(6, 182, 212, 0.2); color: #67e8f9; padding: 0.1rem 0.45rem; border-radius: 9999px;">2,500 – 4,999 PTS</span>
                                    <span
                                        style="font-size: 0.68rem; font-weight: 800; background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 0.1rem 0.45rem; border-radius: 9999px;">🎁 +500 PTS Reward</span>
                                </div>
                                <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.15rem;">Exceptional diamond champion with extraordinary trivia mastery.</div>
                            </div>
                        </div>
                        <div style="flex-shrink: 0; text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 0.3rem;">
                            @if ($isClaimedGrandmaster)
                                <span style="font-size: 0.72rem; font-weight: 800; color: #34d399; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.25rem 0.65rem; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.3rem;">
                                    <i class="fa-solid fa-circle-check"></i> Claimed (+500 PTS)
                                </span>
                            @elseif ($canClaimGrandmaster)
                                <button type="button" onclick="claimRankTierReward('grandmaster', this)" class="btn btn-gold claim-rank-btn"
                                    style="font-size: 0.75rem; font-weight: 800; padding: 0.3rem 0.85rem; border-radius: 9999px; cursor: pointer;">
                                    <i class="fa-solid fa-gift"></i> Claim +500 PTS
                                </button>
                            @elseif ($userPts >= 2500 && $totalGames < 20)
                                <span style="font-size: 0.7rem; font-weight: 700; color: #fbbf24; background: rgba(245, 158, 11, 0.15); border: 1px dashed rgba(245, 158, 11, 0.4); padding: 0.22rem 0.55rem; border-radius: 9999px;">
                                    <i class="fa-solid fa-lock text-amber-400"></i> Need 20 Matches ({{ $totalGames }}/20)
                                </span>
                            @else
                                <span style="font-size: 0.7rem; font-weight: 700; color: #64748b; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); padding: 0.22rem 0.55rem; border-radius: 9999px;">
                                    <i class="fa-solid fa-lock"></i> 2,500 PTS (+500)
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Tier 4: Master (1000 - 2499 PTS) -> +100 PTS Reward -->
                    @php
                        $isCurrentMaster = ($userPts >= 1000 && $userPts < 2500);
                        $isPastMaster = ($userPts >= 2500);
                        $isClaimedMaster = in_array('master', $claimedRankRewards ?? [], true);
                        $canClaimMaster = ($userPts >= 1000 && $totalGames >= 20 && !$isClaimedMaster);
                    @endphp
                    <div class="rank-tier-card {{ $isCurrentMaster ? 'active-tier' : '' }}"
                        style="background: rgba(15, 23, 42, 0.7); border: 1px solid {{ $isCurrentMaster ? '#a855f7' : 'rgba(168, 85, 247, 0.3)' }}; border-radius: 0.85rem; padding: 0.85rem 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                            <div
                                style="width: 42px; height: 42px; border-radius: 12px; background: rgba(168, 85, 247, 0.2); border: 1.5px solid #a855f7; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #c084fc; flex-shrink: 0; box-shadow: 0 0 15px rgba(168, 85, 247, 0.3);">
                                <i class="fa-solid fa-crown"></i>
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                    <span style="font-size: 0.95rem; font-weight: 900; color: #c084fc;">Master 👑</span>
                                    <span
                                        style="font-size: 0.68rem; font-weight: 800; background: rgba(168, 85, 247, 0.2); color: #d8b4fe; padding: 0.1rem 0.45rem; border-radius: 9999px;">1,000 – 2,499 PTS</span>
                                    <span
                                        style="font-size: 0.68rem; font-weight: 800; background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 0.1rem 0.45rem; border-radius: 9999px;">🎁 +100 PTS Reward</span>
                                </div>
                                <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.15rem;">Elite trivia master commanding speed, knowledge, and high streaks.</div>
                            </div>
                        </div>
                        <div style="flex-shrink: 0; text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 0.3rem;">
                            @if ($isClaimedMaster)
                                <span style="font-size: 0.72rem; font-weight: 800; color: #34d399; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.25rem 0.65rem; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.3rem;">
                                    <i class="fa-solid fa-circle-check"></i> Claimed (+100 PTS)
                                </span>
                            @elseif ($canClaimMaster)
                                <button type="button" onclick="claimRankTierReward('master', this)" class="btn btn-gold claim-rank-btn"
                                    style="font-size: 0.75rem; font-weight: 800; padding: 0.3rem 0.85rem; border-radius: 9999px; cursor: pointer;">
                                    <i class="fa-solid fa-gift"></i> Claim +100 PTS
                                </button>
                            @elseif ($userPts >= 1000 && $totalGames < 20)
                                <span style="font-size: 0.7rem; font-weight: 700; color: #fbbf24; background: rgba(245, 158, 11, 0.15); border: 1px dashed rgba(245, 158, 11, 0.4); padding: 0.22rem 0.55rem; border-radius: 9999px;">
                                    <i class="fa-solid fa-lock text-amber-400"></i> Need 20 Matches ({{ $totalGames }}/20)
                                </span>
                            @else
                                <span style="font-size: 0.7rem; font-weight: 700; color: #64748b; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); padding: 0.22rem 0.55rem; border-radius: 9999px;">
                                    <i class="fa-solid fa-lock"></i> 1,000 PTS (+100)
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Tier 3: Veteran (500 - 999 PTS) -> +50 PTS Reward -->
                    @php
                        $isCurrentVeteran = ($userPts >= 500 && $userPts < 1000);
                        $isPastVeteran = ($userPts >= 1000);
                        $isClaimedVeteran = in_array('veteran', $claimedRankRewards ?? [], true);
                        $canClaimVeteran = ($userPts >= 500 && $totalGames >= 20 && !$isClaimedVeteran);
                    @endphp
                    <div class="rank-tier-card {{ $isCurrentVeteran ? 'active-tier' : '' }}"
                        style="background: rgba(15, 23, 42, 0.7); border: 1px solid {{ $isCurrentVeteran ? '#f59e0b' : 'rgba(245, 158, 11, 0.3)' }}; border-radius: 0.85rem; padding: 0.85rem 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                            <div
                                style="width: 42px; height: 42px; border-radius: 12px; background: rgba(245, 158, 11, 0.2); border: 1.5px solid #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #fbbf24; flex-shrink: 0; box-shadow: 0 0 15px rgba(245, 158, 11, 0.3);">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                    <span style="font-size: 0.95rem; font-weight: 900; color: #fbbf24;">Veteran ⚡</span>
                                    <span
                                        style="font-size: 0.68rem; font-weight: 800; background: rgba(245, 158, 11, 0.2); color: #fde68a; padding: 0.1rem 0.45rem; border-radius: 9999px;">500 – 999 PTS</span>
                                    <span
                                        style="font-size: 0.68rem; font-weight: 800; background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 0.1rem 0.45rem; border-radius: 9999px;">🎁 +50 PTS Reward</span>
                                </div>
                                <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.15rem;">Experienced competitor with strong trivia accuracy across rounds.</div>
                            </div>
                        </div>
                        <div style="flex-shrink: 0; text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 0.3rem;">
                            @if ($isClaimedVeteran)
                                <span style="font-size: 0.72rem; font-weight: 800; color: #34d399; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.25rem 0.65rem; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.3rem;">
                                    <i class="fa-solid fa-circle-check"></i> Claimed (+50 PTS)
                                </span>
                            @elseif ($canClaimVeteran)
                                <button type="button" onclick="claimRankTierReward('veteran', this)" class="btn btn-gold claim-rank-btn"
                                    style="font-size: 0.75rem; font-weight: 800; padding: 0.3rem 0.85rem; border-radius: 9999px; cursor: pointer;">
                                    <i class="fa-solid fa-gift"></i> Claim +50 PTS
                                </button>
                            @elseif ($userPts >= 500 && $totalGames < 20)
                                <span style="font-size: 0.7rem; font-weight: 700; color: #fbbf24; background: rgba(245, 158, 11, 0.15); border: 1px dashed rgba(245, 158, 11, 0.4); padding: 0.22rem 0.55rem; border-radius: 9999px;">
                                    <i class="fa-solid fa-lock text-amber-400"></i> Need 20 Matches ({{ $totalGames }}/20)
                                </span>
                            @else
                                <span style="font-size: 0.7rem; font-weight: 700; color: #64748b; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); padding: 0.22rem 0.55rem; border-radius: 9999px;">
                                    <i class="fa-solid fa-lock"></i> 500 PTS (+50)
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Tier 2: Challenger (200 - 499 PTS) -> +20 PTS Reward -->
                    @php
                        $isCurrentChallenger = ($userPts >= 200 && $userPts < 500);
                        $isPastChallenger = ($userPts >= 500);
                        $isClaimedChallenger = in_array('challenger', $claimedRankRewards ?? [], true);
                        $canClaimChallenger = ($userPts >= 200 && $totalGames >= 20 && !$isClaimedChallenger);
                    @endphp
                    <div class="rank-tier-card {{ $isCurrentChallenger ? 'active-tier' : '' }}"
                        style="background: rgba(15, 23, 42, 0.7); border: 1px solid {{ $isCurrentChallenger ? '#38bdf8' : 'rgba(56, 189, 248, 0.3)' }}; border-radius: 0.85rem; padding: 0.85rem 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                            <div
                                style="width: 42px; height: 42px; border-radius: 12px; background: rgba(56, 189, 248, 0.2); border: 1.5px solid #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #38bdf8; flex-shrink: 0; box-shadow: 0 0 15px rgba(56, 189, 248, 0.3);">
                                <i class="fa-solid fa-shield"></i>
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                    <span style="font-size: 0.95rem; font-weight: 900; color: #38bdf8;">Challenger ⚔️</span>
                                    <span
                                        style="font-size: 0.68rem; font-weight: 800; background: rgba(56, 189, 248, 0.2); color: #bae6fd; padding: 0.1rem 0.45rem; border-radius: 9999px;">200 – 499 PTS</span>
                                    <span
                                        style="font-size: 0.68rem; font-weight: 800; background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 0.1rem 0.45rem; border-radius: 9999px;">🎁 +20 PTS Reward</span>
                                </div>
                                <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.15rem;">Active arena fighter proving skills and building point multiplier streaks.</div>
                            </div>
                        </div>
                        <div style="flex-shrink: 0; text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 0.3rem;">
                            @if ($isClaimedChallenger)
                                <span style="font-size: 0.72rem; font-weight: 800; color: #34d399; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.25rem 0.65rem; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.3rem;">
                                    <i class="fa-solid fa-circle-check"></i> Claimed (+20 PTS)
                                </span>
                            @elseif ($canClaimChallenger)
                                <button type="button" onclick="claimRankTierReward('challenger', this)" class="btn btn-gold claim-rank-btn"
                                    style="font-size: 0.75rem; font-weight: 800; padding: 0.3rem 0.85rem; border-radius: 9999px; cursor: pointer;">
                                    <i class="fa-solid fa-gift"></i> Claim +20 PTS
                                </button>
                            @elseif ($userPts >= 200 && $totalGames < 20)
                                <span style="font-size: 0.7rem; font-weight: 700; color: #fbbf24; background: rgba(245, 158, 11, 0.15); border: 1px dashed rgba(245, 158, 11, 0.4); padding: 0.22rem 0.55rem; border-radius: 9999px;">
                                    <i class="fa-solid fa-lock text-amber-400"></i> Need 20 Matches ({{ $totalGames }}/20)
                                </span>
                            @else
                                <span style="font-size: 0.7rem; font-weight: 700; color: #64748b; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); padding: 0.22rem 0.55rem; border-radius: 9999px;">
                                    <i class="fa-solid fa-lock"></i> 200 PTS (+20)
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Tier 1: Novice (0 - 199 PTS) -> 0 PTS Reward -->
                    @php
                        $isCurrentNovice = ($userPts < 200);
                        $isPastNovice = ($userPts >= 200);
                    @endphp
                    <div class="rank-tier-card {{ $isCurrentNovice ? 'active-tier' : '' }}"
                        style="background: rgba(15, 23, 42, 0.7); border: 1px solid {{ $isCurrentNovice ? '#94a3b8' : 'rgba(148, 163, 184, 0.3)' }}; border-radius: 0.85rem; padding: 0.85rem 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                            <div
                                style="width: 42px; height: 42px; border-radius: 12px; background: rgba(148, 163, 184, 0.2); border: 1.5px solid #94a3b8; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #cbd5e1; flex-shrink: 0; box-shadow: 0 0 15px rgba(148, 163, 184, 0.3);">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                    <span style="font-size: 0.95rem; font-weight: 900; color: #cbd5e1;">Novice 🛡️</span>
                                    <span
                                        style="font-size: 0.68rem; font-weight: 800; background: rgba(148, 163, 184, 0.2); color: #e2e8f0; padding: 0.1rem 0.45rem; border-radius: 9999px;">0 – 199 PTS</span>
                                    <span
                                        style="font-size: 0.68rem; font-weight: 700; color: #64748b; padding: 0.1rem 0.45rem;">Starting Rank</span>
                                </div>
                                <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.15rem;">Entry tier for new players starting their journey to the top.</div>
                            </div>
                        </div>
                        <div style="flex-shrink: 0; text-align: right;">
                            @if ($isCurrentNovice)
                                <span
                                    style="font-size: 0.7rem; font-weight: 800; background: linear-gradient(135deg, #64748b, #475569); color: #fff; padding: 0.25rem 0.65rem; border-radius: 9999px; box-shadow: 0 0 12px rgba(148, 163, 184, 0.6);">
                                    YOU ARE HERE ⭐
                                </span>
                            @else
                                <span style="font-size: 0.75rem; color: #34d399; font-weight: 700;"><i
                                        class="fa-solid fa-circle-check"></i></span>
                            @endif
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <style>
        .rank-tier-card {
            transition: all 0.2s ease;
        }

        .rank-tier-card:hover {
            transform: translateX(4px);
            background: rgba(30, 27, 75, 0.5) !important;
        }

        .rank-tier-card.active-tier {
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.25);
            background: rgba(30, 27, 75, 0.6) !important;
        }

        .quest-interactive-item:hover {
            transform: translateY(-2px);
            border-color: rgba(99, 102, 241, 0.6) !important;
            background: rgba(30, 27, 75, 0.45) !important;
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.25);
        }

        .quest-tab-btn {
            flex: 1;
            padding: 0.85rem 0.75rem;
            font-size: 0.82rem;
            font-weight: 700;
            color: #94a3b8;
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            transition: all 0.2s ease;
        }

        .quest-tab-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.04);
        }

        .quest-tab-btn.active {
            color: #fff;
            border-bottom-color: #6366f1;
            background: rgba(99, 102, 241, 0.15);
        }

        .quest-tab-content {
            display: none;
        }

        .quest-tab-content.active {
            display: block;
            animation: fadeIn 0.25s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.75rem;
            width: 100%;
        }

        .hub-main-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(0, 1fr);
            gap: 1.25rem;
            width: 100%;
            align-items: start;
        }

        .hub-column {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            min-width: 0;
            width: 100%;
        }

        .hub-column .glass-card {
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }

        .rules-responsive-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.65rem;
            margin-bottom: 0.85rem;
        }

        .rule-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 0.35rem;
            transition: all 0.2s ease;
        }

        .rule-box-info {
            text-align: center;
        }

        .rule-box-points {
            margin-top: 0.2rem;
        }

        @media (max-width: 960px) {
            .hub-main-grid {
                display: flex !important;
                flex-direction: column !important;
                gap: 1rem !important;
                width: 100% !important;
            }

            .hub-column {
                width: 100% !important;
                gap: 1rem !important;
            }

            .hero-flex {
                flex-direction: column !important;
                align-items: stretch !important;
            }

            .hero-text {
                max-width: 100% !important;
                width: 100% !important;
            }

            .hero-action-card {
                max-width: 100% !important;
                width: 100% !important;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 0.55rem !important;
            }

            .stat-card {
                padding: 0.75rem 0.65rem !important;
                gap: 0.5rem !important;
                min-width: 0 !important;
            }

            .rules-responsive-grid {
                grid-template-columns: 1fr !important;
                gap: 0.45rem !important;
            }

            .rule-box {
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
                text-align: left !important;
                padding: 0.65rem 0.85rem !important;
            }

            .rule-box-info {
                text-align: left !important;
            }

            .rule-box-points {
                margin-top: 0 !important;
            }

            .quest-tab-btn {
                font-size: 0.72rem !important;
                padding: 0.65rem 0.35rem !important;
            }
        }

        @keyframes spinOrb {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes spinOrbReverse {
            0% {
                transform: rotate(360deg);
            }

            100% {
                transform: rotate(0deg);
            }
        }
        @keyframes pulseGlow {
            0% {
                transform: scale(1);
                box-shadow: 0 0 10px rgba(245, 158, 11, 0.4);
            }

            50% {
                transform: scale(1.04);
                box-shadow: 0 0 20px rgba(245, 158, 11, 0.8);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 10px rgba(245, 158, 11, 0.4);
            }
        }

        .claim-rank-btn {
            animation: pulseGlow 1.8s infinite ease-in-out;
            transition: all 0.2s ease;
        }

        .claim-rank-btn:hover {
            transform: scale(1.06) translateY(-1px);
        }
    </style>
@endsection

@push('scripts')
    <script>
        let isSubmittingGame = false;

        function openRankSystemModal() {
            const modal = document.getElementById('rankSystemModal');
            if (modal) modal.classList.add('active');
        }

        function closeRankSystemModal() {
            const modal = document.getElementById('rankSystemModal');
            if (modal) modal.classList.remove('active');
        }

        async function claimRankTierReward(tier, btn) {
            if (!btn) return;
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Claiming...';
            btn.style.opacity = '0.85';

            try {
                const response = await fetch("{{ route('user.rankreward.claim') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ tier: tier })
                });

                const data = await response.json();

                if (data.success) {
                    // Update button to claimed badge
                    btn.outerHTML = '<span style="font-size: 0.72rem; font-weight: 800; color: #34d399; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.25rem 0.65rem; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.3rem;"><i class="fa-solid fa-circle-check"></i> Claimed (+' + Number(data.reward_points).toLocaleString() + ' PTS)</span>';

                    // Update modal balance
                    const modalDisplay = document.getElementById('rankModalPointsDisplay');
                    if (modalDisplay) {
                        modalDisplay.textContent = Number(data.new_points).toLocaleString();
                    }

                    // Play reward sound
                    if (window.soundFX && typeof window.soundFX.correct === 'function') {
                        window.soundFX.correct();
                    }

                    // Toast notification alert
                    alert(data.message || "🎉 Reward Claimed Successfully!");
                    window.location.reload();
                } else {
                    alert(data.message || "Unable to claim rank reward.");
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                    btn.style.opacity = '1';
                }
            } catch (err) {
                alert("An error occurred while connecting to server. Please try again.");
                btn.disabled = false;
                btn.innerHTML = origHtml;
                btn.style.opacity = '1';
            }
        }

        function openQuestModal(tab = 'daily') {
            switchQuestTab(tab);
            const modal = document.getElementById('questsModal');
            if (modal) {
                modal.classList.add('active');
            }
        }

        function closeQuestModal() {
            const modal = document.getElementById('questsModal');
            if (modal) {
                modal.classList.remove('active');
            }
        }

        function switchQuestTab(tab) {
            const tabDailyBtn = document.getElementById('tabBtnDaily');
            const tabReferralBtn = document.getElementById('tabBtnReferral');
            const contentDaily = document.getElementById('questTabContentDaily');
            const contentReferral = document.getElementById('questTabContentReferral');

            if (tab === 'daily') {
                tabDailyBtn?.classList.add('active');
                tabReferralBtn?.classList.remove('active');
                contentDaily?.classList.add('active');
                contentReferral?.classList.remove('active');
            } else {
                tabDailyBtn?.classList.remove('active');
                tabReferralBtn?.classList.add('active');
                contentDaily?.classList.remove('active');
                contentReferral?.classList.add('active');
            }
        }

        function handlePlayGameSubmit(event, form) {
            if (isSubmittingGame) {
                event.preventDefault();
                return false;
            }
            isSubmittingGame = true;

            // Button state
            const btn = document.getElementById('playGameBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Preparing Arena...';
                btn.style.opacity = '0.85';
                btn.style.cursor = 'wait';
            }

            // Close modals if open
            closeQuestModal();
            closeRankSystemModal();

            // Show Modal
            const modal = document.getElementById('gameLoadingModal');
            if (modal) {
                modal.classList.add('active');
            }

            // Audio feedback
            if (window.soundFX && typeof window.soundFX.correct === 'function') {
                window.soundFX.correct();
            }

            // Status text cycle
            const statusText = document.getElementById('loadingStatusText');
            const progressBar = document.getElementById('loadingProgressBar');

            setTimeout(() => {
                if (statusText) statusText.innerHTML =
                    '<i class="fa-solid fa-cloud-arrow-down text-cyan-400"></i> Fetching 30 randomized non-repeating questions...';
                if (progressBar) progressBar.style.width = '65%';
            }, 500);

            setTimeout(() => {
                if (statusText) statusText.innerHTML =
                    '<i class="fa-solid fa-bolt text-amber-400"></i> Initializing Arena & Round 1 Easy Challenge...';
                if (progressBar) progressBar.style.width = '92%';
            }, 1200);

            return true;
        }

        function copyReferralCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                const btn = document.getElementById('copyCodeBtn');
                if (btn) {
                    const orig = btn.innerHTML;
                    btn.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';
                    btn.style.color = '#34d399';
                    setTimeout(() => {
                        btn.innerHTML = orig;
                        btn.style.color = '#a5b4fc';
                    }, 2000);
                }
            }).catch(() => {
                prompt('Your Referral / Coupon Code:', code);
            });
        }

        function copyReferralLink(link) {
            navigator.clipboard.writeText(link).then(() => {
                const btn = document.getElementById('copyLinkBtn');
                if (btn) {
                    const orig = btn.innerHTML;
                    btn.innerHTML = '<i class="fa-solid fa-check"></i> Link Copied!';
                    setTimeout(() => {
                        btn.innerHTML = orig;
                    }, 2000);
                }
            }).catch(() => {
                prompt('Your Registration Referral Link:', link);
            });
        }
    </script>
@endpush
