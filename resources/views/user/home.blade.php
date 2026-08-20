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
                        <form id="playGameForm" action="{{ route('game.start') }}" method="POST" style="margin: 0;"
                            onsubmit="handlePlayGameSubmit(event, this)">
                            @csrf
                            <button type="submit" id="playGameBtn" class="btn btn-primary"
                                style="width: 100%; padding: 1rem; font-size: 1.15rem; font-weight: 800; border-radius: 1rem; background: linear-gradient(135deg, #6366f1, #06b6d4); box-shadow: 0 0 24px rgba(99, 102, 241, 0.5); transition: all 0.2s ease;">
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

            <!-- Right: Referral Quest, Leaderboard & Points Ledger -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">

                <!-- REFERRAL / COUPON CODE & 5/5 QUEST CARD -->
                <div class="glass-card" style="padding: 1.75rem; background: linear-gradient(135deg, rgba(30, 27, 75, 0.7), rgba(15, 23, 42, 0.85)); border: 1px solid rgba(245, 158, 11, 0.35); position: relative; overflow: hidden;">
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(245, 158, 11, 0.2); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.1rem;">
                                <i class="fa-solid fa-gift"></i>
                            </div>
                            <h3 style="font-size: 1.2rem; font-weight: 800; color: #fff;">Invite & Earn Quest</h3>
                        </div>
                        <span style="font-size: 0.75rem; font-weight: 800; background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 0.2rem 0.6rem; border-radius: 9999px;">
                            +1,000 PTS REWARD
                        </span>
                    </div>

                    <!-- Personal Coupon Code Display Box -->
                    <div style="background: rgba(15, 23, 42, 0.8); border: 1px dashed rgba(99, 102, 241, 0.5); border-radius: 0.85rem; padding: 1rem; margin-bottom: 1.25rem;">
                        <div style="font-size: 0.8rem; font-weight: 700; color: #94a3b8; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            Your Personal Coupon Code
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                            <div id="referralCodeText" style="font-family: monospace; font-size: 1.4rem; font-weight: 900; color: #38bdf8; letter-spacing: 2px;">
                                {{ $user->referral_code }}
                            </div>
                            <div style="display: flex; gap: 0.35rem;">
                                <button type="button" class="btn btn-outline" style="padding: 0.4rem 0.75rem; font-size: 0.8rem; border-color: rgba(99, 102, 241, 0.4); color: #a5b4fc;" onclick="copyReferralCode('{{ $user->referral_code }}')" id="copyCodeBtn" title="Copy Code">
                                    <i class="fa-solid fa-copy"></i> Copy
                                </button>
                                <button type="button" class="btn btn-gold" style="padding: 0.4rem 0.75rem; font-size: 0.8rem;" onclick="copyReferralLink('{{ url('/register?ref=' . $user->referral_code) }}')" id="copyLinkBtn" title="Copy Invite Link">
                                    <i class="fa-solid fa-link"></i> Link
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 5/5 Mission Progress -->
                    <div style="margin-bottom: 1.25rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.85rem; font-weight: 700; color: #e2e8f0;">
                                🎯 Mission: Invite 5 Approved Friends
                            </span>
                            <span style="font-size: 0.9rem; font-weight: 900; color: {{ $approvedReferralsCount >= 5 ? '#34d399' : '#fbbf24' }};">
                                {{ $referralQuestProgress }} / 5
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div style="width: 100%; height: 10px; background: rgba(255,255,255,0.08); border-radius: 9999px; overflow: hidden; position: relative;">
                            <div style="height: 100%; width: {{ ($referralQuestProgress / 5) * 100 }}%; background: linear-gradient(90deg, #f59e0b, #10b981); border-radius: 9999px; transition: width 0.5s ease;"></div>
                        </div>

                        @if($user->quest_rewarded || $approvedReferralsCount >= 5)
                            <div style="margin-top: 0.6rem; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); border-radius: 0.6rem; padding: 0.5rem 0.75rem; font-size: 0.8rem; color: #34d399; font-weight: 700; text-align: center;">
                                <i class="fa-solid fa-trophy"></i> Quest Completed! +1,000 PTS Reward Awarded!
                            </div>
                        @else
                            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem; line-height: 1.4;">
                                Share your coupon code with friends. Once they register and get approved by Admin, you will receive <strong>1,000 bonus points</strong> upon reaching 5/5!
                            </div>
                        @endif
                    </div>

                    <!-- Friends Invited List -->
                    @if($referralsList->isNotEmpty())
                        <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 0.75rem;">
                            <div style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem;">
                                Your Invited Friends ({{ $referralsList->count() }})
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                @foreach($referralsList as $refFriend)
                                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.8rem; padding: 0.35rem 0.5rem; background: rgba(255,255,255,0.02); border-radius: 0.4rem;">
                                        <div style="color: #cbd5e1; font-weight: 500;">
                                            <i class="fa-solid fa-circle-user text-indigo-400"></i> {{ $refFriend->name }}
                                        </div>
                                        <div>
                                            @if($refFriend->status === 'approved')
                                                <span style="color: #34d399; font-weight: 700; font-size: 0.75rem;">
                                                    <i class="fa-solid fa-check"></i> Approved (+1)
                                                </span>
                                            @elseif($refFriend->status === 'pending')
                                                <span style="color: #fbbf24; font-weight: 600; font-size: 0.75rem;">
                                                    <i class="fa-solid fa-clock"></i> Pending Admin
                                                </span>
                                            @else
                                                <span style="color: #f87171; font-size: 0.75rem;">
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

    <!-- PREPARING GAME LOADING MODAL -->
    <div id="gameLoadingModal" class="modal-overlay"
        style="z-index: 9999; backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); background: rgba(10, 15, 29, 0.88);">
        <div class="modal-card"
            style="text-align: center; max-width: 440px; border: 1px solid rgba(99, 102, 241, 0.4); box-shadow: 0 0 60px rgba(99, 102, 241, 0.4); padding: 2.5rem 2rem;">

            <!-- Animated Glowing Portal Orb -->
            <div
                style="position: relative; width: 92px; height: 92px; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center;">
                <div
                    style="position: absolute; width: 100%; height: 100%; border-radius: 50%; border: 3px solid transparent; border-top-color: #6366f1; border-right-color: #06b6d4; animation: spinOrb 1s linear infinite;">
                </div>
                <div
                    style="position: absolute; width: 72%; height: 72%; border-radius: 50%; border: 3px dashed rgba(245, 158, 11, 0.6); animation: spinOrbReverse 2s linear infinite;">
                </div>
                <div
                    style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #06b6d4); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; box-shadow: 0 0 25px rgba(99, 102, 241, 0.9);">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
            </div>

            <h3 style="font-size: 1.6rem; font-weight: 900; color: #fff; margin-bottom: 0.5rem; letter-spacing: -0.5px;">
                Preparing for a game...
            </h3>
            <p id="loadingStatusText"
                style="color: #cbd5e1; font-size: 0.92rem; margin-bottom: 1.5rem; line-height: 1.5; min-height: 42px;">
                Loading...
            </p>

            <!-- Loading Step Progress Bar -->
            <div
                style="width: 100%; height: 7px; background: rgba(255,255,255,0.08); border-radius: 9999px; overflow: hidden; position: relative;">
                <div id="loadingProgressBar"
                    style="height: 100%; width: 25%; background: linear-gradient(90deg, #6366f1, #06b6d4, #10b981); border-radius: 9999px; transition: width 0.7s cubic-bezier(0.4, 0, 0.2, 1);">
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
