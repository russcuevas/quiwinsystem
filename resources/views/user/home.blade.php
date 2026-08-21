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
                    <h1 style="font-size: clamp(1.45rem, 4.2vw, 2.35rem); font-weight: 900; line-height: 1.2; color: #fff; letter-spacing: -0.5px;">
                        Ready for the <span
                            style="background: linear-gradient(135deg, #818cf8, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Quiwin
                            Battle</span>?
                    </h1>
                    <p style="color: #cbd5e1; font-size: clamp(0.85rem, 1.8vw, 1rem); margin-top: 0.45rem; line-height: 1.45;">
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

                    <div style="display: flex; gap: 0.45rem; margin-top: 0.65rem;">
                        <button type="button" class="btn btn-outline"
                            style="flex: 1; font-size: 0.78rem; padding: 0.45rem 0.35rem;" onclick="openTopUpModal()">
                            <i class="fa-solid fa-wallet text-amber-400"></i> Top-Up
                        </button>
                        <button type="button" class="btn btn-outline"
                            style="flex: 1; font-size: 0.78rem; padding: 0.45rem 0.35rem; border-color: rgba(16, 185, 129, 0.4); color: #34d399;" onclick="openWithdrawModal()">
                            <i class="fa-solid fa-money-bill-transfer text-emerald-400"></i> Withdraw
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 Stats Cards Grid (Responsive 2x2 on Mobile) -->
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem;">

            <div class="glass-card stat-card"
                style="padding: 0.95rem 0.85rem; display: flex; align-items: center; gap: 0.75rem; border-left: 4px solid #6366f1;">
                <div
                    style="width: 38px; height: 38px; border-radius: 10px; background: rgba(99, 102, 241, 0.15); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.15rem; flex-shrink: 0;">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Matches</div>
                    <div style="font-size: clamp(1.15rem, 3vw, 1.45rem); font-weight: 800; color: #fff; line-height: 1.2;">{{ $totalGames }}</div>
                </div>
            </div>

            <div class="glass-card stat-card"
                style="padding: 0.95rem 0.85rem; display: flex; align-items: center; gap: 0.75rem; border-left: 4px solid #f59e0b;">
                <div
                    style="width: 38px; height: 38px; border-radius: 10px; background: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.15rem; flex-shrink: 0;">
                    <i class="fa-solid fa-fire"></i>
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Best Streak</div>
                    <div style="font-size: clamp(1.15rem, 3vw, 1.45rem); font-weight: 800; color: #fff; line-height: 1.2;">{{ $bestStreak }} 🔥</div>
                </div>
            </div>

            <div class="glass-card stat-card"
                style="padding: 0.95rem 0.85rem; display: flex; align-items: center; gap: 0.75rem; border-left: 4px solid #10b981;">
                <div
                    style="width: 38px; height: 38px; border-radius: 10px; background: rgba(16, 185, 129, 0.15); display: flex; align-items: center; justify-content: center; color: #34d399; font-size: 1.15rem; flex-shrink: 0;">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Accuracy</div>
                    <div style="font-size: clamp(1.15rem, 3vw, 1.45rem); font-weight: 800; color: #fff; line-height: 1.2;">{{ $accuracy }}%</div>
                </div>
            </div>

            <div class="glass-card stat-card"
                style="padding: 0.95rem 0.85rem; display: flex; align-items: center; gap: 0.75rem; border-left: 4px solid #06b6d4;">
                <div
                    style="width: 38px; height: 38px; border-radius: 10px; background: rgba(6, 182, 212, 0.15); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 1.15rem; flex-shrink: 0;">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div style="font-size: 0.72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Rank</div>
                    <div style="font-size: clamp(0.95rem, 2.5vw, 1.2rem); font-weight: 800; color: #fff; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
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

        <!-- Main Content 2 Columns: Game Rules & Recent Games / Referral & Leaderboard -->
        <div class="hub-main-grid" style="display: grid; grid-template-columns: 1.35fr 1fr; gap: 1.25rem;">

            <!-- Left Column: Game Rules / Scoring & Recent Games -->
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">

                <!-- Game Rules Card -->
                <div class="glass-card" style="padding: clamp(0.85rem, 2.5vw, 1.25rem);">
                    <h3
                        style="font-size: 1.05rem; font-weight: 800; color: #fff; margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.45rem;">
                        <i class="fa-solid fa-circle-info text-indigo-400"></i> Rules & Pointing System
                    </h3>

                    <!-- Responsive Rules Cards (Horizontal cards on mobile, 3-col on desktop) -->
                    <div class="rules-responsive-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.55rem; margin-bottom: 0.85rem;">

                        <!-- Round 1 -->
                        <div class="rule-box"
                            style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 0.65rem; padding: 0.65rem 0.5rem; text-align: center;">
                            <div style="font-size: 0.72rem; font-weight: 800; color: #34d399; text-transform: uppercase;">
                                Round 1 (Easy)</div>
                            <div style="font-size: 0.75rem; color: #cbd5e1; margin: 0.15rem 0;">Q1–10 &bull; 5s</div>
                            <div style="display: flex; justify-content: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 700; margin-top: 0.2rem;">
                                <span style="color: #34d399;">+2 PTS</span>
                                <span style="color: #64748b;">|</span>
                                <span style="color: #fb7185;">-3 PTS</span>
                            </div>
                        </div>

                        <!-- Round 2 -->
                        <div class="rule-box"
                            style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 0.65rem; padding: 0.65rem 0.5rem; text-align: center;">
                            <div style="font-size: 0.72rem; font-weight: 800; color: #fbbf24; text-transform: uppercase;">
                                Round 2 (Med)</div>
                            <div style="font-size: 0.75rem; color: #cbd5e1; margin: 0.15rem 0;">Q11–20 &bull; 5s</div>
                            <div style="display: flex; justify-content: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 700; margin-top: 0.2rem;">
                                <span style="color: #34d399;">+3 PTS</span>
                                <span style="color: #64748b;">|</span>
                                <span style="color: #fb7185;">-5 PTS</span>
                            </div>
                        </div>

                        <!-- Round 3 -->
                        <div class="rule-box"
                            style="background: rgba(244, 63, 94, 0.08); border: 1px solid rgba(244, 63, 94, 0.3); border-radius: 0.65rem; padding: 0.65rem 0.5rem; text-align: center;">
                            <div style="font-size: 0.72rem; font-weight: 800; color: #fb7185; text-transform: uppercase;">
                                Round 3 (Hard)</div>
                            <div style="font-size: 0.75rem; color: #cbd5e1; margin: 0.15rem 0;">Q21–30 &bull; 5s</div>
                            <div style="display: flex; justify-content: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 700; margin-top: 0.2rem;">
                                <span style="color: #34d399;">+5 PTS</span>
                                <span style="color: #64748b;">|</span>
                                <span style="color: #fb7185;">-10 PTS</span>
                            </div>
                        </div>

                    </div>

                    <div
                        style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 0.65rem; padding: 0.65rem 0.85rem; font-size: 0.78rem; color: #94a3b8; line-height: 1.35;">
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
                            <i class="fa-solid fa-gamepad" style="font-size: 1.5rem; opacity: 0.4; margin-bottom: 0.35rem; display: block;"></i>
                            No completed matches yet. Click "PLAY GAME" to start!
                        </div>
                    @else
                        <div class="table-responsive">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.82rem; min-width: 360px;">
                                <thead>
                                    <tr
                                        style="border-bottom: 1px solid rgba(255,255,255,0.08); color: #64748b; text-align: left;">
                                        <th style="padding: 0.45rem;">Match</th>
                                        <th style="padding: 0.45rem;">Score</th>
                                        <th style="padding: 0.45rem;">Streak</th>
                                        <th style="padding: 0.45rem;">Net PTS</th>
                                        <th style="padding: 0.45rem;">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentGames as $game)
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); color: #cbd5e1;">
                                            <td style="padding: 0.55rem 0.45rem; font-weight: 600;">#{{ $game->id }}</td>
                                            <td style="padding: 0.55rem 0.45rem;">
                                                <span
                                                    style="color: #34d399; font-weight: 700;">{{ $game->total_correct }}</span>/30
                                            </td>
                                            <td style="padding: 0.55rem 0.45rem;">
                                                {{ $game->max_streak }} 🔥
                                            </td>
                                            <td
                                                style="padding: 0.55rem 0.45rem; font-weight: 700; color: {{ $game->points_delta >= 0 ? '#34d399' : '#fb7185' }};">
                                                {{ $game->points_delta >= 0 ? '+' . $game->points_delta : $game->points_delta }}
                                            </td>
                                            <td style="padding: 0.55rem 0.45rem; color: #64748b; font-size: 0.72rem;">
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

            <!-- Right Column: Referral Quest, Leaderboard & Points Ledger -->
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">

                <!-- REFERRAL / COUPON CODE & 5/5 QUEST CARD -->
                <div class="glass-card" style="padding: clamp(0.85rem, 2.5vw, 1.25rem); background: linear-gradient(135deg, rgba(30, 27, 75, 0.7), rgba(15, 23, 42, 0.85)); border: 1px solid rgba(245, 158, 11, 0.35); position: relative; overflow: hidden;">
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.4rem;">
                        <div style="display: flex; align-items: center; gap: 0.45rem;">
                            <div style="width: 32px; height: 32px; border-radius: 9px; background: rgba(245, 158, 11, 0.2); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 0.95rem;">
                                <i class="fa-solid fa-gift"></i>
                            </div>
                            <h3 style="font-size: 1.05rem; font-weight: 800; color: #fff;">Invite & Earn Quest</h3>
                        </div>
                        <span style="font-size: 0.68rem; font-weight: 800; background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 0.12rem 0.45rem; border-radius: 9999px;">
                            +1,000 PTS REWARD
                        </span>
                    </div>

                    <!-- Personal Coupon Code Display Box -->
                    <div style="background: rgba(15, 23, 42, 0.8); border: 1px dashed rgba(99, 102, 241, 0.5); border-radius: 0.65rem; padding: 0.75rem; margin-bottom: 0.85rem;">
                        <div style="font-size: 0.72rem; font-weight: 700; color: #94a3b8; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            Your Personal Coupon Code
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.45rem; flex-wrap: wrap;">
                            <div id="referralCodeText" style="font-family: monospace; font-size: 1.15rem; font-weight: 900; color: #38bdf8; letter-spacing: 1px;">
                                {{ $user->referral_code }}
                            </div>
                            <div style="display: flex; gap: 0.3rem;">
                                <button type="button" class="btn btn-outline" style="padding: 0.3rem 0.55rem; font-size: 0.75rem; border-color: rgba(99, 102, 241, 0.4); color: #a5b4fc; min-height: 32px;" onclick="copyReferralCode('{{ $user->referral_code }}')" id="copyCodeBtn" title="Copy Code">
                                    <i class="fa-solid fa-copy"></i> Copy
                                </button>
                                <button type="button" class="btn btn-gold" style="padding: 0.3rem 0.55rem; font-size: 0.75rem; min-height: 32px;" onclick="copyReferralLink('{{ url('/register?ref=' . $user->referral_code) }}')" id="copyLinkBtn" title="Copy Invite Link">
                                    <i class="fa-solid fa-link"></i> Link
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 5/5 Mission Progress -->
                    <div style="margin-bottom: 0.75rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                            <span style="font-size: 0.78rem; font-weight: 700; color: #e2e8f0;">
                                🎯 Mission: Invite 5 Approved Friends
                            </span>
                            <span style="font-size: 0.82rem; font-weight: 900; color: {{ $approvedReferralsCount >= 5 ? '#34d399' : '#fbbf24' }};">
                                {{ $referralQuestProgress }} / 5
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div style="width: 100%; height: 7px; background: rgba(255,255,255,0.08); border-radius: 9999px; overflow: hidden; position: relative;">
                            <div style="height: 100%; width: {{ ($referralQuestProgress / 5) * 100 }}%; background: linear-gradient(90deg, #f59e0b, #10b981); border-radius: 9999px; transition: width 0.5s ease;"></div>
                        </div>

                        @if($user->quest_rewarded || $approvedReferralsCount >= 5)
                            <div style="margin-top: 0.45rem; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); border-radius: 0.55rem; padding: 0.4rem 0.55rem; font-size: 0.75rem; color: #34d399; font-weight: 700; text-align: center;">
                                <i class="fa-solid fa-trophy"></i> Quest Completed! +1,000 PTS Reward Awarded!
                            </div>
                        @else
                            <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.35rem; line-height: 1.35;">
                                Share your coupon code. You will receive <strong>1,000 bonus points</strong> once 5 friends register & get approved!
                            </div>
                        @endif
                    </div>

                    <!-- Friends Invited List -->
                    @if($referralsList->isNotEmpty())
                        <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 0.55rem;">
                            <div style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.35rem;">
                                Your Invited Friends ({{ $referralsList->count() }})
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                                @foreach($referralsList as $refFriend)
                                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem; padding: 0.25rem 0.45rem; background: rgba(255,255,255,0.02); border-radius: 0.35rem;">
                                        <div style="color: #cbd5e1; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 140px;">
                                            <i class="fa-solid fa-circle-user text-indigo-400"></i> {{ $refFriend->name }}
                                        </div>
                                        <div>
                                            @if($refFriend->status === 'approved')
                                                <span style="color: #34d399; font-weight: 700; font-size: 0.7rem;">
                                                    <i class="fa-solid fa-check"></i> Approved (+1)
                                                </span>
                                            @elseif($refFriend->status === 'pending')
                                                <span style="color: #fbbf24; font-weight: 600; font-size: 0.7rem;">
                                                    <i class="fa-solid fa-clock"></i> Pending
                                                </span>
                                            @else
                                                <span style="color: #f87171; font-size: 0.7rem;">
                                                    <i class="fa-solid fa-ban"></i> Rejected
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Leaderboard -->
                <div class="glass-card" style="padding: clamp(0.85rem, 2.5vw, 1.25rem);">
                    <div
                        style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <h3
                            style="font-size: 1.05rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 0.45rem;">
                            <i class="fa-solid fa-trophy text-amber-400"></i> Leaderboard
                        </h3>
                        <span style="font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; font-weight: 600;">Top Players</span>
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
                                <div style="font-weight: 800; color: {{ $tx->amount >= 0 ? '#34d399' : '#fb7185' }}; white-space: nowrap; flex-shrink: 0;">
                                    {{ $tx->amount >= 0 ? '+' . $tx->amount : $tx->amount }} PTS
                                </div>
                            </div>
                        @empty
                            <p style="color: #64748b; font-size: 0.78rem; text-align: center;">No transactions recorded yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- GCash Withdrawal Payout History -->
                <div class="glass-card" style="padding: clamp(0.85rem, 2.5vw, 1.25rem); border: 1px solid rgba(16, 185, 129, 0.3);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.4rem;">
                        <h3 style="font-size: 1.05rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 0.45rem;">
                            <i class="fa-solid fa-money-bill-transfer text-emerald-400"></i> GCash Payouts
                        </h3>
                        <button type="button" class="btn btn-outline" style="font-size: 0.72rem; padding: 0.25rem 0.55rem; min-height: 30px; border-color: rgba(16, 185, 129, 0.4); color: #34d399;" onclick="openWithdrawModal()">
                            + Request
                        </button>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.45rem;">
                        @forelse($withdrawals as $w)
                            <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid {{ $w->status === 'approved' ? 'rgba(16, 185, 129, 0.4)' : ($w->status === 'rejected' ? 'rgba(244, 63, 94, 0.3)' : 'rgba(245, 158, 11, 0.3)') }}; border-radius: 0.65rem; padding: 0.55rem 0.65rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.2rem; flex-wrap: wrap; gap: 0.25rem;">
                                    <span style="font-weight: 800; font-size: 0.9rem; color: #fff;">
                                        ₱{{ number_format($w->amount) }} <span style="font-size: 0.68rem; color: #94a3b8;">PHP</span>
                                    </span>
                                    <span style="font-size: 0.68rem; font-weight: 700; padding: 0.1rem 0.4rem; border-radius: 9999px; text-transform: uppercase;
                                        @if($w->status === 'approved') background: rgba(16, 185, 129, 0.2); color: #34d399;
                                        @elseif($w->status === 'pending') background: rgba(245, 158, 11, 0.2); color: #fbbf24;
                                        @else background: rgba(244, 63, 94, 0.2); color: #fb7185;
                                        @endif">
                                        {{ $w->status === 'approved' ? 'Sent' : $w->status }}
                                    </span>
                                </div>
                                <div style="font-size: 0.72rem; color: #94a3b8; display: flex; justify-content: space-between; align-items: center;">
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

    <style>
        @media (max-width: 900px) {
            .hub-main-grid {
                grid-template-columns: 1fr !important;
            }
            .hero-action-card {
                max-width: 100% !important;
            }
        }

        @media (max-width: 600px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 0.55rem !important;
            }
            .stat-card {
                padding: 0.75rem 0.65rem !important;
                gap: 0.5rem !important;
            }
            .rules-responsive-grid {
                grid-template-columns: 1fr !important;
                gap: 0.45rem !important;
            }
            .rule-box {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                text-align: left !important;
                padding: 0.55rem 0.75rem !important;
            }
        }

        @keyframes spinOrb {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes spinOrbReverse {
            0% { transform: rotate(360deg); }
            100% { transform: rotate(0deg); }
        }
    </style>
@endsection

@push('scripts')
    <script>
        let isSubmittingGame = false;

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
