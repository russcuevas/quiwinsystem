@extends('layouts.app')

@section('title', 'Manage Players - Admin')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Top Action Bar -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 0.75rem;">
        <div>
            <a href="{{ route('admin.dashboard') }}" style="color: #94a3b8; font-size: 0.82rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem; margin-bottom: 0.3rem;">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1 style="font-size: clamp(1.4rem, 4vw, 2rem); font-weight: 900; color: #fff; letter-spacing: -0.5px;">Player Management</h1>
        </div>
    </div>

    <!-- Filter Tabs & Search Bar -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 0.75rem;">
        
        <!-- Status Filter Tabs -->
        <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
            <a href="{{ route('admin.users', ['status' => 'all', 'search' => $search]) }}" class="btn {{ $status === 'all' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.4rem 0.75rem; font-size: 0.8rem; min-height: 36px;">
                All
            </a>
            <a href="{{ route('admin.users', ['status' => 'pending', 'search' => $search]) }}" class="btn {{ $status === 'pending' ? 'btn-gold' : 'btn-outline' }}" style="padding: 0.4rem 0.75rem; font-size: 0.8rem; min-height: 36px; border-color: rgba(245, 158, 11, 0.4); color: #fbbf24; position: relative;">
                <i class="fa-solid fa-clock"></i> Pending
                @if($pendingCount > 0)
                    <span style="background: #ef4444; color: #fff; font-size: 0.68rem; font-weight: 800; padding: 0.05rem 0.35rem; border-radius: 9999px; margin-left: 0.2rem;">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.users', ['status' => 'approved', 'search' => $search]) }}" class="btn {{ $status === 'approved' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.4rem 0.75rem; font-size: 0.8rem; min-height: 36px; background: {{ $status === 'approved' ? 'linear-gradient(135deg, #10b981, #059669)' : 'transparent' }}; border-color: rgba(16, 185, 129, 0.4); color: {{ $status === 'approved' ? '#fff' : '#34d399' }};">
                <i class="fa-solid fa-check-circle"></i> Approved
            </a>
            <a href="{{ route('admin.users', ['status' => 'rejected', 'search' => $search]) }}" class="btn {{ $status === 'rejected' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.4rem 0.75rem; font-size: 0.8rem; min-height: 36px; background: {{ $status === 'rejected' ? '#dc2626' : 'transparent' }}; border-color: rgba(239, 68, 68, 0.4); color: {{ $status === 'rejected' ? '#fff' : '#f87171' }};">
                <i class="fa-solid fa-times-circle"></i> Rejected
            </a>
        </div>

        <!-- Search Box -->
        <form action="{{ route('admin.users') }}" method="GET" style="display: flex; gap: 0.4rem; flex: 1; min-width: 240px; max-width: 420px;">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search name, email, coupon..." class="form-input" style="padding: 0.45rem 0.75rem; font-size: 0.85rem; min-height: 36px;">
            <button type="submit" class="btn btn-primary" style="padding: 0.45rem 0.85rem; min-height: 36px;">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            @if($search)
                <a href="{{ route('admin.users', ['status' => $status]) }}" class="btn btn-outline" style="padding: 0.45rem 0.65rem; min-height: 36px; font-size: 0.82rem;">Clear</a>
            @endif
        </form>
    </div>

    <!-- Players Table Card -->
    <div class="glass-card" style="padding: 1.25rem;">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; min-width: 680px;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.08); color: #64748b; text-align: left;">
                        <th style="padding: 0.5rem;">ID</th>
                        <th style="padding: 0.5rem;">Player Name</th>
                        <th style="padding: 0.5rem;">Coupon Code</th>
                        <th style="padding: 0.5rem;">Invited By</th>
                        <th style="padding: 0.5rem;">Points</th>
                        <th style="padding: 0.5rem;">Status</th>
                        <th style="padding: 0.5rem;">Registered</th>
                        <th style="padding: 0.5rem; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); color: #cbd5e1;">
                        <td style="padding: 0.65rem 0.5rem; font-weight: 700; color: #94a3b8;">#{{ $user->id }}</td>
                        <td style="padding: 0.65rem 0.5rem; font-weight: 600; color: #fff;">
                            <div>{{ $user->name }}</div>
                            <div style="font-size: 0.72rem; color: #64748b;">{{ $user->email }}</div>
                        </td>
                        <td style="padding: 0.65rem 0.5rem;">
                            <code style="background: rgba(99, 102, 241, 0.18); color: #a5b4fc; padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 800; font-family: monospace; font-size: 0.78rem;">{{ $user->referral_code ?? 'None' }}</code>
                        </td>
                        <td style="padding: 0.65rem 0.5rem;">
                            @if($user->referrer)
                                <div style="color: #34d399; font-size: 0.8rem; font-weight: 600;">{{ $user->referrer->name }}</div>
                            @else
                                <div style="color: #64748b; font-size: 0.75rem;">Direct Signup</div>
                            @endif
                            <div style="font-size: 0.72rem; color: #fbbf24;">
                                🎯 Invites: {{ $user->approvedReferrals->count() }}/5
                            </div>
                        </td>
                        <td style="padding: 0.65rem 0.5rem; font-weight: 800; color: #fbbf24;">
                            {{ number_format($user->points) }} PTS
                        </td>
                        <td style="padding: 0.65rem 0.5rem;">
                            @if($user->status === 'approved')
                                <span style="font-size: 0.72rem; padding: 0.15rem 0.45rem; border-radius: 9999px; font-weight: 700; background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4);">
                                    <i class="fa-solid fa-check"></i> Approved
                                </span>
                            @elseif($user->status === 'pending')
                                <span style="font-size: 0.72rem; padding: 0.15rem 0.45rem; border-radius: 9999px; font-weight: 700; background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4);">
                                    <i class="fa-solid fa-clock"></i> Pending
                                </span>
                            @else
                                <span style="font-size: 0.72rem; padding: 0.15rem 0.45rem; border-radius: 9999px; font-weight: 700; background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4);">
                                    <i class="fa-solid fa-ban"></i> Rejected
                                </span>
                            @endif
                        </td>
                        <td style="padding: 0.65rem 0.5rem; color: #64748b; font-size: 0.75rem;">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td style="padding: 0.65rem 0.5rem; text-align: right;">
                            <div style="display: inline-flex; gap: 0.3rem; flex-wrap: wrap; justify-content: flex-end;">
                                @if($user->status === 'pending')
                                    <!-- Quick Approve Button -->
                                    <form action="{{ route('admin.users.approve', ['userId' => $user->id]) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; background: linear-gradient(135deg, #10b981, #059669); min-height: 30px;" title="Approve & Grant 200 PTS">
                                            <i class="fa-solid fa-check"></i> Approve
                                        </button>
                                    </form>

                                    <!-- Quick Reject Button -->
                                    <form action="{{ route('admin.users.reject', ['userId' => $user->id]) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Reject player registration?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline" style="padding: 0.3rem 0.5rem; font-size: 0.75rem; color: #fb7185; border-color: rgba(244, 63, 94, 0.4); min-height: 30px;" title="Reject Registration">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                @else
                                    @if($user->status === 'rejected')
                                        <form action="{{ route('admin.users.approve', ['userId' => $user->id]) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-primary" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; background: linear-gradient(135deg, #10b981, #059669); min-height: 30px;">
                                                <i class="fa-solid fa-rotate-left"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Adjust Points Modal Trigger -->
                                    <button type="button" class="btn btn-outline" style="padding: 0.3rem 0.55rem; font-size: 0.75rem; color: #fbbf24; border-color: rgba(245, 158, 11, 0.3); min-height: 30px;" onclick="openAdjustModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->points }})">
                                        <i class="fa-solid fa-coins"></i> Adjust
                                    </button>

                                    <!-- Toggle Status Form -->
                                    <form action="{{ route('admin.users.toggle', ['userId' => $user->id]) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Change active status for {{ addslashes($user->name) }}?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline" style="padding: 0.3rem 0.5rem; font-size: 0.75rem; color: {{ $user->is_active ? '#f87171' : '#34d399' }}; min-height: 30px;" title="{{ $user->is_active ? 'Deactivate Account' : 'Activate Account' }}">
                                            <i class="fa-solid {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: #64748b; padding: 1.5rem;">No players found matching your criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1.25rem;">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>

</div>

<!-- Admin Point Adjustment Modal -->
<div id="adjustModal" class="modal-overlay">
    <div class="modal-card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.55rem;">
                <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(99, 102, 241, 0.2); display: flex; align-items: center; justify-content: center; color: #818cf8;">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: #fff;">Adjust Points</h3>
            </div>
            <button onclick="closeAdjustModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer; padding: 0.25rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div style="background: rgba(255,255,255,0.04); border-radius: 0.75rem; padding: 0.75rem 0.85rem; margin-bottom: 1rem;">
            <div style="font-size: 0.78rem; color: #94a3b8;">Player: <strong id="adjustUserName" style="color: #fff;"></strong></div>
            <div style="font-size: 0.78rem; color: #94a3b8;">Current Balance: <strong id="adjustUserPoints" style="color: #fbbf24;"></strong> PTS</div>
        </div>

        <form id="adjustForm" method="POST" action="">
            @csrf
            <div style="margin-bottom: 0.85rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
                    Points Delta (e.g. +100 or -50)
                </label>
                <input type="number" name="amount" id="adjustAmount" class="form-input" placeholder="e.g. 100 or -50" required>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.35rem;">
                    Reason / Note (Optional)
                </label>
                <input type="text" name="reason" class="form-input" placeholder="e.g. Tournament reward, manual fix">
            </div>

            <div style="display: flex; gap: 0.65rem;">
                <button type="button" class="btn btn-outline" style="flex: 1;" onclick="closeAdjustModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 2;">
                    <i class="fa-solid fa-check"></i> Apply
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openAdjustModal(id, name, points) {
        document.getElementById('adjustUserName').textContent = name;
        document.getElementById('adjustUserPoints').textContent = Number(points).toLocaleString();
        document.getElementById('adjustForm').action = `/admin/users/${id}/points`;
        document.getElementById('adjustModal').classList.add('active');
    }
    function closeAdjustModal() {
        document.getElementById('adjustModal').classList.remove('active');
    }
</script>
@endpush
