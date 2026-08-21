@extends('layouts.app')

@section('title', 'Admin Dashboard - Quiwin')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.25rem; padding-bottom: 2rem;">

    <!-- Top Admin Header -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 0.85rem;">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 0.45rem; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.78rem; color: #a5b4fc; font-weight: 600; margin-bottom: 0.35rem;">
                <i class="fa-solid fa-shield-halved text-indigo-400"></i> Admin Command Center
            </div>
            <h1 style="font-size: clamp(1.35rem, 3.8vw, 2.1rem); font-weight: 900; color: #fff; letter-spacing: -0.5px;">Platform Analytics & Overview</h1>
        </div>

        <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
            <a href="{{ route('admin.withdrawals') }}" class="btn btn-outline" style="border-color: rgba(245, 158, 11, 0.4); color: #fbbf24; font-size: 0.8rem; padding: 0.4rem 0.65rem; min-height: 34px;">
                <i class="fa-solid fa-money-bill-wave"></i> Payouts
                @if($pendingWithdrawalsCount > 0)
                    <span style="background: #ef4444; color: white; border-radius: 9999px; font-size: 0.65rem; padding: 0.05rem 0.35rem; margin-left: 0.2rem;">{{ $pendingWithdrawalsCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.users') }}" class="btn btn-outline" style="font-size: 0.8rem; padding: 0.4rem 0.65rem; min-height: 34px;">
                <i class="fa-solid fa-users text-cyan-400"></i> Players
            </a>
            <a href="{{ route('admin.questions') }}" class="btn btn-outline" style="font-size: 0.8rem; padding: 0.4rem 0.65rem; min-height: 34px;">
                <i class="fa-solid fa-database text-purple-400"></i> Questions
            </a>
            <a href="{{ route('admin.settings') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); font-size: 0.8rem; padding: 0.4rem 0.65rem; min-height: 34px;">
                <i class="fa-solid fa-sliders"></i> Rules
            </a>
        </div>
    </div>

    <!-- Analytics Metrics Cards (2x2 on Mobile) -->
    <div class="admin-metrics-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem;">
        
        <div class="glass-card" style="padding: 0.95rem; border-left: 4px solid #f59e0b; background: {{ $pendingUsersCount > 0 ? 'rgba(245, 158, 11, 0.12)' : 'var(--bg-card)' }};">
            <div style="font-size: 0.75rem; color: #fbbf24; font-weight: 700; display: flex; align-items: center; justify-content: space-between;">
                <span>Pending Players</span>
                @if($pendingUsersCount > 0)
                    <span style="background: #ef4444; color: #fff; font-size: 0.62rem; padding: 0.1rem 0.35rem; border-radius: 9999px;">Action</span>
                @endif
            </div>
            <div style="font-size: clamp(1.25rem, 3.2vw, 1.75rem); font-weight: 900; color: #fbbf24; margin-top: 0.15rem;">{{ number_format($pendingUsersCount) }}</div>
        </div>

        <div class="glass-card" style="padding: 0.95rem; border-left: 4px solid #10b981; background: {{ $pendingWithdrawalsCount > 0 ? 'rgba(16, 185, 129, 0.12)' : 'var(--bg-card)' }};">
            <div style="font-size: 0.75rem; color: #34d399; font-weight: 700; display: flex; align-items: center; justify-content: space-between;">
                <span>Pending Payouts</span>
                @if($pendingWithdrawalsCount > 0)
                    <span style="background: #ef4444; color: #fff; font-size: 0.62rem; padding: 0.1rem 0.35rem; border-radius: 9999px;">{{ $pendingWithdrawalsCount }}</span>
                @endif
            </div>
            <div style="font-size: clamp(1.25rem, 3.2vw, 1.75rem); font-weight: 900; color: #34d399; margin-top: 0.15rem;">{{ number_format($pendingWithdrawalsCount) }}</div>
            <div style="font-size: 0.68rem; color: #94a3b8; margin-top: 0.15rem;">Paid: ₱{{ number_format($totalApprovedWithdrawalsAmount) }}</div>
        </div>

        <div class="glass-card" style="padding: 0.95rem; border-left: 4px solid #6366f1;">
            <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">Approved Players</div>
            <div style="font-size: clamp(1.25rem, 3.2vw, 1.75rem); font-weight: 900; color: #fff; margin-top: 0.15rem;">{{ number_format($totalApprovedUsers) }}</div>
        </div>

        <div class="glass-card" style="padding: 0.95rem; border-left: 4px solid #06b6d4;">
            <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">Matches Played</div>
            <div style="font-size: clamp(1.25rem, 3.2vw, 1.75rem); font-weight: 900; color: #fff; margin-top: 0.15rem;">{{ number_format($totalGames) }}</div>
        </div>

        <div class="glass-card" style="padding: 0.95rem; border-left: 4px solid #f59e0b;">
            <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">Circulating Points</div>
            <div style="font-size: clamp(1.25rem, 3.2vw, 1.75rem); font-weight: 900; color: #fbbf24; margin-top: 0.15rem;">{{ number_format($totalPointsInCirculation) }} PTS</div>
        </div>

        <div class="glass-card" style="padding: 0.95rem; border-left: 4px solid #a855f7;">
            <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">Questions Bank</div>
            <div style="font-size: clamp(1.25rem, 3.2vw, 1.75rem); font-weight: 900; color: #c084fc; margin-top: 0.15rem;">{{ number_format($totalQuestionsInDb) }}</div>
        </div>

    </div>

    <!-- PENDING WITHDRAWALS SECTION (When there are pending payouts) -->
    @if($pendingWithdrawals->isNotEmpty())
    <div class="glass-card" style="padding: clamp(0.85rem, 2.5vw, 1.25rem); border: 1px solid rgba(16, 185, 129, 0.4); background: rgba(16, 185, 129, 0.04);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 0.45rem;">
                    <i class="fa-solid fa-money-bill-transfer text-emerald-400"></i> Pending GCash Payouts ({{ $pendingWithdrawals->count() }})
                </h3>
                <p style="color: #94a3b8; font-size: 0.78rem; margin-top: 0.15rem;">
                    Send funds to player's GCash and click Approve to deduct points.
                </p>
            </div>
            <a href="{{ route('admin.withdrawals', ['status' => 'pending']) }}" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.3rem 0.65rem; border-color: rgba(16, 185, 129, 0.5); color: #34d399;">
                View All
            </a>
        </div>

        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.82rem; min-width: 540px;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.08); color: #64748b; text-align: left;">
                        <th style="padding: 0.45rem;">ID</th>
                        <th style="padding: 0.45rem;">Player</th>
                        <th style="padding: 0.45rem;">Balance</th>
                        <th style="padding: 0.45rem;">Amount</th>
                        <th style="padding: 0.45rem;">GCash Details</th>
                        <th style="padding: 0.45rem;">Requested</th>
                        <th style="padding: 0.45rem; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingWithdrawals as $pw)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); color: #cbd5e1;">
                        <td style="padding: 0.55rem 0.45rem; font-weight: 700; color: #64748b;">#{{ $pw->id }}</td>
                        <td style="padding: 0.55rem 0.45rem; font-weight: 700; color: #fff;">
                            <i class="fa-solid fa-circle-user text-indigo-400"></i> {{ $pw->user->name ?? 'User' }}
                        </td>
                        <td style="padding: 0.55rem 0.45rem;">
                            <span style="font-weight: 700; color: {{ ($pw->user && $pw->user->points >= $pw->amount) ? '#34d399' : '#fb7185' }};">
                                {{ number_format($pw->user->points ?? 0) }} PTS
                            </span>
                        </td>
                        <td style="padding: 0.55rem 0.45rem; font-weight: 900; color: #fbbf24;">
                            ₱{{ number_format($pw->amount) }}
                        </td>
                        <td style="padding: 0.55rem 0.45rem;">
                            <div style="font-family: monospace; font-weight: 700; color: #38bdf8;">{{ $pw->gcash_number }}</div>
                            <div style="font-size: 0.72rem; color: #cbd5e1;">{{ $pw->gcash_name }}</div>
                        </td>
                        <td style="padding: 0.55rem 0.45rem; color: #64748b; font-size: 0.72rem;">
                            {{ $pw->created_at->diffForHumans() }}
                        </td>
                        <td style="padding: 0.55rem 0.45rem; text-align: right;">
                            <div style="display: inline-flex; gap: 0.3rem;">
                                <form action="{{ route('admin.withdrawals.approve', $pw->id) }}" method="POST"
                                    onsubmit="return confirm('Approve & payout ₱{{ number_format($pw->amount) }} to GCash {{ $pw->gcash_number }} ({{ $pw->gcash_name }})?');"
                                    style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="btn btn-success"
                                        style="padding: 0.3rem 0.55rem; font-size: 0.75rem; font-weight: 700; min-height: 28px;"
                                        {{ ($pw->user && $pw->user->points < $pw->amount) ? 'disabled' : '' }}>
                                        <i class="fa-solid fa-check"></i> Approve
                                    </button>
                                </form>
                                <a href="{{ route('admin.withdrawals') }}" class="btn btn-outline" style="padding: 0.3rem 0.45rem; font-size: 0.75rem; min-height: 28px;">
                                    Details
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- PENDING APPROVALS SECTION (When there are pending registrations) -->
    @if($pendingUsers->isNotEmpty())
    <div class="glass-card" style="padding: clamp(0.85rem, 2.5vw, 1.25rem); border: 1px solid rgba(245, 158, 11, 0.4); background: rgba(245, 158, 11, 0.04);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 0.45rem;">
                    <i class="fa-solid fa-user-clock text-amber-400"></i> Pending Player Approvals ({{ $pendingUsers->count() }})
                </h3>
                <p style="color: #94a3b8; font-size: 0.78rem; margin-top: 0.15rem;">
                    Approve players to grant 200 Welcome Bonus PTS and enable login.
                </p>
            </div>
            <a href="{{ route('admin.users', ['status' => 'pending']) }}" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.3rem 0.65rem; border-color: rgba(245, 158, 11, 0.5); color: #fbbf24;">
                View All
            </a>
        </div>

        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.82rem; min-width: 520px;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.08); color: #64748b; text-align: left;">
                        <th style="padding: 0.45rem;">Player Name</th>
                        <th style="padding: 0.45rem;">Email</th>
                        <th style="padding: 0.45rem;">Coupon</th>
                        <th style="padding: 0.45rem;">Referred By</th>
                        <th style="padding: 0.45rem;">Registered</th>
                        <th style="padding: 0.45rem; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingUsers as $pUser)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); color: #cbd5e1;">
                        <td style="padding: 0.55rem 0.45rem; font-weight: 700; color: #fff;">
                            <i class="fa-solid fa-circle-user text-amber-400"></i> {{ $pUser->name }}
                        </td>
                        <td style="padding: 0.55rem 0.45rem; color: #94a3b8; font-size: 0.75rem;">{{ $pUser->email }}</td>
                        <td style="padding: 0.55rem 0.45rem;">
                            <code style="background: rgba(99, 102, 241, 0.2); color: #a5b4fc; padding: 0.12rem 0.35rem; border-radius: 4px; font-weight: 700; font-size: 0.75rem;">{{ $pUser->referral_code ?? 'None' }}</code>
                        </td>
                        <td style="padding: 0.55rem 0.45rem; font-size: 0.75rem;">
                            @if($pUser->referrer)
                                <span style="color: #34d399; font-weight: 600;">{{ $pUser->referrer->name }}</span>
                            @else
                                <span style="color: #64748b;">Direct</span>
                            @endif
                        </td>
                        <td style="padding: 0.55rem 0.45rem; color: #64748b; font-size: 0.72rem;">
                            {{ $pUser->created_at->diffForHumans() }}
                        </td>
                        <td style="padding: 0.55rem 0.45rem; text-align: right;">
                            <div style="display: inline-flex; gap: 0.3rem;">
                                <form action="{{ route('admin.users.approve', ['userId' => $pUser->id]) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" style="padding: 0.3rem 0.55rem; font-size: 0.75rem; background: linear-gradient(135deg, #10b981, #059669); min-height: 28px;">
                                        <i class="fa-solid fa-check"></i> Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.reject', ['userId' => $pUser->id]) }}" method="POST" style="margin:0;" onsubmit="return confirm('Reject registration for {{ $pUser->name }}?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline" style="padding: 0.3rem 0.45rem; font-size: 0.75rem; color: #fb7185; border-color: rgba(244, 63, 94, 0.4); min-height: 28px;">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- 2 Column Layout: Recent Game Sessions & Top Players + OpenTDB -->
    <div class="admin-dash-grid" style="display: grid; grid-template-columns: 1.35fr 1fr; gap: 1.25rem; align-items: start;">
        
        <!-- Left: Recent Game Sessions -->
        <div class="glass-card" style="padding: clamp(0.85rem, 2.5vw, 1.25rem); overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 0.45rem;">
                    <i class="fa-solid fa-gamepad text-indigo-400"></i> Live Game Sessions
                </h3>
                <span style="font-size: 0.72rem; color: #94a3b8;">Recent matches</span>
            </div>

            <!-- Desktop View: Table Format -->
            <div class="sessions-desktop-table table-responsive">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.82rem; min-width: 420px;">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.08); color: #64748b; text-align: left;">
                            <th style="padding: 0.45rem;">ID</th>
                            <th style="padding: 0.45rem;">Player</th>
                            <th style="padding: 0.45rem;">Status</th>
                            <th style="padding: 0.45rem;">Progress</th>
                            <th style="padding: 0.45rem;">Delta</th>
                            <th style="padding: 0.45rem;">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSessions as $sess)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); color: #cbd5e1;">
                            <td style="padding: 0.5rem 0.45rem; font-weight: 700; color: #94a3b8;">#{{ $sess->id }}</td>
                            <td style="padding: 0.5rem 0.45rem; font-weight: 600; color: #fff;">{{ $sess->user->name ?? 'Deleted User' }}</td>
                            <td style="padding: 0.5rem 0.45rem;">
                                <span style="font-size: 0.68rem; padding: 0.12rem 0.4rem; border-radius: 9999px; font-weight: 700; text-transform: uppercase;
                                    @if($sess->status === 'completed') background: rgba(16, 185, 129, 0.2); color: #34d399;
                                    @elseif($sess->status === 'active') background: rgba(6, 182, 212, 0.2); color: #38bdf8;
                                    @elseif($sess->status === 'bankrupt_paused') background: rgba(239, 68, 68, 0.2); color: #f87171;
                                    @else background: rgba(148, 163, 184, 0.2); color: #94a3b8;
                                    @endif">
                                    {{ $sess->status }}
                                </span>
                            </td>
                            <td style="padding: 0.5rem 0.45rem; color: #a5b4fc; font-size: 0.75rem;">
                                R{{ $sess->current_round }} (Q{{ $sess->current_question_index }}/30)
                            </td>
                            <td style="padding: 0.5rem 0.45rem; font-weight: 700; color: {{ $sess->points_delta >= 0 ? '#34d399' : '#fb7185' }};">
                                {{ $sess->points_delta >= 0 ? '+' . $sess->points_delta : $sess->points_delta }} PTS
                            </td>
                            <td style="padding: 0.5rem 0.45rem; color: #64748b; font-size: 0.72rem;">
                                {{ $sess->created_at->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b; padding: 1.25rem;">
                                <i class="fa-solid fa-gamepad" style="font-size: 1.5rem; opacity: 0.4; margin-bottom: 0.35rem; display: block;"></i>
                                No game sessions recorded yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View: Clean Card List (for <= 640px) -->
            <div class="sessions-mobile-list">
                @forelse($recentSessions as $sess)
                <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.65rem; padding: 0.65rem; margin-bottom: 0.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                        <div style="display: flex; align-items: center; gap: 0.4rem; min-width: 0;">
                            <span style="font-weight: 800; font-size: 0.75rem; color: #64748b;">#{{ $sess->id }}</span>
                            <span style="font-weight: 700; color: #fff; font-size: 0.85rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $sess->user->name ?? 'Deleted User' }}
                            </span>
                        </div>
                        <span style="font-size: 0.65rem; padding: 0.1rem 0.35rem; border-radius: 9999px; font-weight: 700; text-transform: uppercase; flex-shrink: 0;
                            @if($sess->status === 'completed') background: rgba(16, 185, 129, 0.2); color: #34d399;
                            @elseif($sess->status === 'active') background: rgba(6, 182, 212, 0.2); color: #38bdf8;
                            @elseif($sess->status === 'bankrupt_paused') background: rgba(239, 68, 68, 0.2); color: #f87171;
                            @else background: rgba(148, 163, 184, 0.2); color: #94a3b8;
                            @endif">
                            {{ $sess->status }}
                        </span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem;">
                        <span style="color: #a5b4fc;">
                            Round {{ $sess->current_round }} (Q{{ $sess->current_question_index }}/30)
                        </span>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-weight: 800; color: {{ $sess->points_delta >= 0 ? '#34d399' : '#fb7185' }};">
                                {{ $sess->points_delta >= 0 ? '+' . $sess->points_delta : $sess->points_delta }} PTS
                            </span>
                            <span style="color: #64748b; font-size: 0.7rem;">
                                {{ $sess->created_at->diffForHumans(null, true) }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align: center; color: #64748b; padding: 1.25rem 0.5rem;">
                    <i class="fa-solid fa-gamepad" style="font-size: 1.5rem; opacity: 0.4; margin-bottom: 0.35rem; display: block;"></i>
                    No game sessions recorded yet.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Top Players Leaderboard & Quick OpenTDB Sync -->
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            
            <!-- Top Players -->
            <div class="glass-card" style="padding: clamp(0.85rem, 2.5vw, 1.25rem);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 0.45rem;">
                        <i class="fa-solid fa-trophy text-amber-400"></i> Top Player Balances
                    </h3>
                    <a href="{{ route('admin.users') }}" style="font-size: 0.72rem; color: #818cf8; text-decoration: none;">View All</a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                    @forelse($topPlayers as $idx => $p)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.45rem 0.65rem; border-radius: 0.65rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); gap: 0.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.45rem; min-width: 0; flex: 1;">
                            <span style="font-size: 0.72rem; font-weight: 900; width: 20px; text-align: center; color: {{ $idx === 0 ? '#fbbf24' : ($idx === 1 ? '#cbd5e1' : ($idx === 2 ? '#d97706' : '#64748b')) }};">
                                #{{ $idx + 1 }}
                            </span>
                            <span style="font-weight: 600; color: #e2e8f0; font-size: 0.82rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $p->name }}
                            </span>
                        </div>
                        <span style="font-weight: 800; color: #fbbf24; font-size: 0.82rem; flex-shrink: 0;">
                            {{ number_format($p->points) }} PTS
                        </span>
                    </div>
                    @empty
                    <div style="text-align: center; color: #64748b; padding: 1rem 0; font-size: 0.8rem;">
                        No player records found.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Question Bank Summary & OpenTDB Sync Card -->
            <div class="glass-card opentdb-card" style="padding: clamp(0.85rem, 2.5vw, 1.25rem); border: 1px solid rgba(168, 85, 247, 0.25); background: rgba(168, 85, 247, 0.04);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <h3 style="font-size: 1rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 0.45rem;">
                        <i class="fa-solid fa-cloud-arrow-down text-purple-400"></i> OpenTDB Sync
                    </h3>
                    <a href="{{ route('admin.questions') }}" style="font-size: 0.72rem; color: #c084fc; text-decoration: none;">Bank</a>
                </div>

                <!-- 3 Mini Metric Counters (Easy / Med / Hard) -->
                <div class="opentdb-counter-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.4rem; margin-bottom: 0.85rem; text-align: center;">
                    <div style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 0.5rem; padding: 0.35rem 0.2rem;">
                        <div style="font-size: 0.68rem; color: #34d399; font-weight: 700; text-transform: uppercase;">Easy</div>
                        <div style="font-size: 0.95rem; font-weight: 900; color: #fff; margin-top: 0.1rem;">{{ $easyCount }}</div>
                    </div>
                    <div style="background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: 0.5rem; padding: 0.35rem 0.2rem;">
                        <div style="font-size: 0.68rem; color: #fbbf24; font-weight: 700; text-transform: uppercase;">Medium</div>
                        <div style="font-size: 0.95rem; font-weight: 900; color: #fff; margin-top: 0.1rem;">{{ $mediumCount }}</div>
                    </div>
                    <div style="background: rgba(244, 63, 94, 0.12); border: 1px solid rgba(244, 63, 94, 0.25); border-radius: 0.5rem; padding: 0.35rem 0.2rem;">
                        <div style="font-size: 0.68rem; color: #fb7185; font-weight: 700; text-transform: uppercase;">Hard</div>
                        <div style="font-size: 0.95rem; font-weight: 900; color: #fff; margin-top: 0.1rem;">{{ $hardCount }}</div>
                    </div>
                </div>

                <!-- Sync Action Button -->
                <form action="{{ route('admin.questions.sync') }}" method="POST" style="margin: 0;">
                    @csrf
                    <input type="hidden" name="difficulty" value="all">
                    <button type="submit" class="btn btn-outline opentdb-sync-btn" style="width: 100%; border-color: rgba(168, 85, 247, 0.45); color: #c084fc; font-size: 0.8rem; font-weight: 700; padding: 0.55rem 0.5rem; min-height: 38px; display: flex; align-items: center; justify-content: center; gap: 0.4rem; line-height: 1.2; text-align: center;">
                        <i class="fa-solid fa-arrows-rotate"></i>
                        <span>Sync Questions (OpenTDB)</span>
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>

<style>
    /* Desktop table vs Mobile card toggle */
    .sessions-desktop-table {
        display: block;
    }
    .sessions-mobile-list {
        display: none;
    }

    @media (max-width: 900px) {
        .admin-dash-grid {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 640px) {
        .sessions-desktop-table {
            display: none !important;
        }
        .sessions-mobile-list {
            display: block !important;
        }
        .admin-metrics-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 0.55rem !important;
        }
        .opentdb-sync-btn {
            font-size: 0.76rem !important;
        }
    }

    @media (max-width: 360px) {
        .opentdb-counter-grid {
            gap: 0.25rem !important;
        }
        .opentdb-sync-btn span {
            font-size: 0.72rem !important;
        }
    }
</style>
@endsection
