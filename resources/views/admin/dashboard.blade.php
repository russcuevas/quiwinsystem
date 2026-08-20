@extends('layouts.app')

@section('title', 'Admin Dashboard - Quiwin')

@section('content')
<div style="display: flex; flex-direction: column; gap: 2rem;">

    <!-- Top Admin Header -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.85rem; color: #a5b4fc; font-weight: 600; margin-bottom: 0.5rem;">
                <i class="fa-solid fa-shield-halved text-indigo-400"></i> Admin Command Center
            </div>
            <h1 style="font-size: 2.25rem; font-weight: 900; color: #fff; letter-spacing: -0.5px;">Platform Analytics & Overview</h1>
        </div>

        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ route('admin.users') }}" class="btn btn-outline">
                <i class="fa-solid fa-users"></i> Manage Players
            </a>
            <a href="{{ route('admin.questions') }}" class="btn btn-outline">
                <i class="fa-solid fa-database"></i> Question Bank
            </a>
            <a href="{{ route('admin.settings') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fa-solid fa-sliders"></i> Pointing System & Rules
            </a>
        </div>
    </div>

    <!-- 5 Analytics Metrics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">
        
        <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid #6366f1;">
            <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Registered Players</div>
            <div style="font-size: 2rem; font-weight: 900; color: #fff; margin-top: 0.25rem;">{{ number_format($totalUsers) }}</div>
        </div>

        <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid #06b6d4;">
            <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Matches Played</div>
            <div style="font-size: 2rem; font-weight: 900; color: #fff; margin-top: 0.25rem;">{{ number_format($totalGames) }}</div>
        </div>

        <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid #10b981;">
            <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Completed Matches</div>
            <div style="font-size: 2rem; font-weight: 900; color: #fff; margin-top: 0.25rem;">{{ number_format($totalCompletedGames) }}</div>
        </div>

        <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid #f59e0b;">
            <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Circulating Points</div>
            <div style="font-size: 2rem; font-weight: 900; color: #fbbf24; margin-top: 0.25rem;">{{ number_format($totalPointsInCirculation) }} PTS</div>
        </div>

        <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid #a855f7;">
            <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Cached Questions</div>
            <div style="font-size: 2rem; font-weight: 900; color: #c084fc; margin-top: 0.25rem;">{{ number_format($totalQuestionsInDb) }}</div>
        </div>

    </div>

    <!-- 2 Column Layout: Recent Game Sessions & Top Players -->
    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem;">
        
        <!-- Left: Recent Game Sessions -->
        <div class="glass-card" style="padding: 1.75rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-gamepad text-indigo-400"></i> Live Game Sessions
                </h3>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.08); color: #64748b; text-align: left;">
                            <th style="padding: 0.6rem;">ID</th>
                            <th style="padding: 0.6rem;">Player</th>
                            <th style="padding: 0.6rem;">Status</th>
                            <th style="padding: 0.6rem;">Round/Q</th>
                            <th style="padding: 0.6rem;">Delta</th>
                            <th style="padding: 0.6rem;">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSessions as $sess)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); color: #cbd5e1;">
                            <td style="padding: 0.65rem 0.5rem; font-weight: 700;">#{{ $sess->id }}</td>
                            <td style="padding: 0.65rem 0.5rem; font-weight: 600; color: #fff;">{{ $sess->user->name ?? 'Deleted User' }}</td>
                            <td style="padding: 0.65rem 0.5rem;">
                                <span style="font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 9999px; font-weight: 700; text-transform: uppercase;
                                    @if($sess->status === 'completed') background: rgba(16, 185, 129, 0.2); color: #34d399;
                                    @elseif($sess->status === 'active') background: rgba(6, 182, 212, 0.2); color: #38bdf8;
                                    @elseif($sess->status === 'bankrupt_paused') background: rgba(239, 68, 68, 0.2); color: #f87171;
                                    @else background: rgba(148, 163, 184, 0.2); color: #94a3b8;
                                    @endif">
                                    {{ $sess->status }}
                                </span>
                            </td>
                            <td style="padding: 0.65rem 0.5rem; color: #a5b4fc;">
                                R{{ $sess->current_round }} (Q{{ $sess->current_question_index }}/30)
                            </td>
                            <td style="padding: 0.65rem 0.5rem; font-weight: 700; color: {{ $sess->points_delta >= 0 ? '#34d399' : '#fb7185' }};">
                                {{ $sess->points_delta >= 0 ? '+' . $sess->points_delta : $sess->points_delta }} PTS
                            </td>
                            <td style="padding: 0.65rem 0.5rem; color: #64748b; font-size: 0.8rem;">
                                {{ $sess->created_at->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b; padding: 1.5rem;">No games recorded.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Top Players Leaderboard & Quick Sync -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Top Players -->
            <div class="glass-card" style="padding: 1.75rem;">
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #fff; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-trophy text-amber-400"></i> Top Player Balances
                </h3>

                <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                    @foreach($topPlayers as $idx => $p)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.85rem; border-radius: 0.75rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <span style="font-size: 0.85rem; font-weight: 800; color: #64748b;">#{{ $idx + 1 }}</span>
                            <span style="font-weight: 600; color: #e2e8f0; font-size: 0.9rem;">{{ $p->name }}</span>
                        </div>
                        <span style="font-weight: 800; color: #fbbf24; font-size: 0.9rem;">{{ number_format($p->points) }} PTS</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Question Bank Summary & Sync -->
            <div class="glass-card" style="padding: 1.75rem;">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #fff; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-cloud-arrow-down text-purple-400"></i> OpenTDB Sync
                </h3>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; margin-bottom: 1.25rem; text-align: center;">
                    <div style="background: rgba(16, 185, 129, 0.1); border-radius: 0.5rem; padding: 0.5rem;">
                        <div style="font-size: 0.75rem; color: #34d399; font-weight: 700;">Easy</div>
                        <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">{{ $easyCount }}</div>
                    </div>
                    <div style="background: rgba(245, 158, 11, 0.1); border-radius: 0.5rem; padding: 0.5rem;">
                        <div style="font-size: 0.75rem; color: #fbbf24; font-weight: 700;">Medium</div>
                        <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">{{ $mediumCount }}</div>
                    </div>
                    <div style="background: rgba(244, 63, 94, 0.1); border-radius: 0.5rem; padding: 0.5rem;">
                        <div style="font-size: 0.75rem; color: #fb7185; font-weight: 700;">Hard</div>
                        <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">{{ $hardCount }}</div>
                    </div>
                </div>

                <form action="{{ route('admin.questions.sync') }}" method="POST">
                    @csrf
                    <input type="hidden" name="difficulty" value="all">
                    <button type="submit" class="btn btn-outline" style="width: 100%; border-color: rgba(168, 85, 247, 0.4); color: #c084fc;">
                        <i class="fa-solid fa-arrows-rotate"></i> Fetch +15 Questions from OpenTDB
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
