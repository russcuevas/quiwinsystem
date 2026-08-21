@extends('layouts.app')

@section('title', 'Manage Withdrawals - Admin Panel')

@section('content')
    <div style="display: flex; flex-direction: column; gap: 2rem;">

        <!-- Header Banner -->
        <div class="glass-card"
            style="padding: 2rem; background: linear-gradient(135deg, rgba(30, 27, 75, 0.9), rgba(15, 23, 42, 0.95)); border: 1px solid rgba(245, 158, 11, 0.35); position: relative; overflow: hidden;">
            <div
                style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.5rem; position: relative; z-index: 2;">
                <div>
                    <div
                        style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.85rem; color: #fbbf24; font-weight: 700; margin-bottom: 0.75rem;">
                        <i class="fa-solid fa-money-bill-wave"></i> GCash Payout Management
                    </div>
                    <h1 style="font-size: 2.2rem; font-weight: 900; color: #fff; letter-spacing: -0.5px;">
                        Player <span
                            style="background: linear-gradient(135deg, #fbbf24, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Withdrawals</span>
                    </h1>
                    <p style="color: #cbd5e1; font-size: 0.95rem; margin-top: 0.5rem; max-width: 650px;">
                        Review and approve GCash cash-out requests. Points will only be deducted from players' accounts once approved and marked as <strong>"Already sent by the admin"</strong>.
                    </p>
                </div>

                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">
                        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- 3 Stat Metrics -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
            
            <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid #f59e0b;">
                <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Pending Requests</div>
                <div style="font-size: 1.85rem; font-weight: 900; color: #fbbf24; margin-top: 0.25rem;">
                    {{ number_format($pendingCount) }}
                </div>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">
                    Total Pending: ₱{{ number_format($totalPendingAmount) }}
                </div>
            </div>

            <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid #10b981;">
                <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Total Payouts Released</div>
                <div style="font-size: 1.85rem; font-weight: 900; color: #34d399; margin-top: 0.25rem;">
                    ₱{{ number_format($totalApprovedAmount) }}
                </div>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">
                    Marked "Already sent by admin"
                </div>
            </div>

            <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid #6366f1;">
                <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Total Transactions</div>
                <div style="font-size: 1.85rem; font-weight: 900; color: #fff; margin-top: 0.25rem;">
                    {{ number_format($withdrawals->total()) }}
                </div>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">
                    All-time recorded requests
                </div>
            </div>

        </div>

        <!-- Filter & Search Bar -->
        <div class="glass-card" style="padding: 1.25rem;">
            <form method="GET" action="{{ route('admin.withdrawals') }}"
                style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; justify-content: space-between;">
                
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <a href="{{ route('admin.withdrawals', ['status' => 'all', 'search' => $search]) }}"
                        class="btn {{ $status === 'all' ? 'btn-primary' : 'btn-outline' }}"
                        style="padding: 0.45rem 1rem; font-size: 0.85rem;">
                        All ({{ $withdrawals->total() }})
                    </a>
                    <a href="{{ route('admin.withdrawals', ['status' => 'pending', 'search' => $search]) }}"
                        class="btn {{ $status === 'pending' ? 'btn-gold' : 'btn-outline' }}"
                        style="padding: 0.45rem 1rem; font-size: 0.85rem; border-color: rgba(245, 158, 11, 0.4);">
                        <i class="fa-solid fa-clock"></i> Pending ({{ $pendingCount }})
                    </a>
                    <a href="{{ route('admin.withdrawals', ['status' => 'approved', 'search' => $search]) }}"
                        class="btn {{ $status === 'approved' ? 'btn-success' : 'btn-outline' }}"
                        style="padding: 0.45rem 1rem; font-size: 0.85rem; border-color: rgba(16, 185, 129, 0.4);">
                        <i class="fa-solid fa-circle-check"></i> Approved
                    </a>
                    <a href="{{ route('admin.withdrawals', ['status' => 'rejected', 'search' => $search]) }}"
                        class="btn {{ $status === 'rejected' ? 'btn-danger' : 'btn-outline' }}"
                        style="padding: 0.45rem 1rem; font-size: 0.85rem; border-color: rgba(244, 63, 94, 0.4);">
                        <i class="fa-solid fa-ban"></i> Rejected
                    </a>
                </div>

                <div style="display: flex; gap: 0.5rem; flex: 1; max-width: 380px;">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Search player, GCash number or name..." class="form-input"
                        style="padding: 0.45rem 0.85rem; font-size: 0.85rem;">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <button type="submit" class="btn btn-primary" style="padding: 0.45rem 1rem; font-size: 0.85rem;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    @if ($search)
                        <a href="{{ route('admin.withdrawals', ['status' => $status]) }}" class="btn btn-outline"
                            style="padding: 0.45rem 0.75rem; font-size: 0.85rem;" title="Clear Search">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Withdrawals Table -->
        <div class="glass-card" style="padding: 1.5rem; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.08); color: #64748b; text-align: left;">
                        <th style="padding: 0.75rem 0.5rem;">ID</th>
                        <th style="padding: 0.75rem 0.5rem;">Player Info</th>
                        <th style="padding: 0.75rem 0.5rem;">Current Points</th>
                        <th style="padding: 0.75rem 0.5rem;">Withdraw Amount</th>
                        <th style="padding: 0.75rem 0.5rem;">GCash Account</th>
                        <th style="padding: 0.75rem 0.5rem;">Status</th>
                        <th style="padding: 0.75rem 0.5rem;">Date Requested</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $w)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04); color: #cbd5e1;">
                            <td style="padding: 0.85rem 0.5rem; font-weight: 700; color: #64748b;">
                                #{{ $w->id }}
                            </td>
                            <td style="padding: 0.85rem 0.5rem;">
                                <div style="font-weight: 700; color: #fff;">{{ $w->user->name ?? 'Deleted User' }}</div>
                                <div style="font-size: 0.78rem; color: #64748b;">{{ $w->user->email ?? 'N/A' }}</div>
                            </td>
                            <td style="padding: 0.85rem 0.5rem;">
                                @if($w->user)
                                    <span style="font-weight: 800; color: {{ $w->user->points >= $w->amount ? '#34d399' : '#fb7185' }};">
                                        {{ number_format($w->user->points) }} PTS
                                    </span>
                                    @if($w->status === 'pending' && $w->user->points < $w->amount)
                                        <div style="font-size: 0.72rem; color: #fb7185; font-weight: 600;">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Insufficient PTS
                                        </div>
                                    @endif
                                @else
                                    <span style="color: #64748b;">—</span>
                                @endif
                            </td>
                            <td style="padding: 0.85rem 0.5rem;">
                                <span style="font-size: 1.15rem; font-weight: 900; color: #fbbf24;">
                                    ₱{{ number_format($w->amount) }}
                                </span>
                                <div style="font-size: 0.72rem; color: #94a3b8;">
                                    {{ number_format($w->amount) }} PTS
                                </div>
                            </td>
                            <td style="padding: 0.85rem 0.5rem;">
                                <div style="font-family: monospace; font-weight: 700; color: #38bdf8; font-size: 0.95rem;">
                                    <i class="fa-solid fa-mobile-screen"></i> {{ $w->gcash_number }}
                                </div>
                                <div style="font-size: 0.82rem; color: #e2e8f0; font-weight: 600;">
                                    {{ $w->gcash_name }}
                                </div>
                            </td>
                            <td style="padding: 0.85rem 0.5rem;">
                                @if ($w->status === 'approved')
                                    <span
                                        style="background: rgba(16, 185, 129, 0.18); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 800; display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <i class="fa-solid fa-circle-check"></i> Already sent by the admin
                                    </span>
                                    @if($w->approved_at)
                                        <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.25rem;">
                                            Sent: {{ $w->approved_at->format('M d, H:i') }}
                                        </div>
                                    @endif
                                @elseif($w->status === 'pending')
                                    <span
                                        style="background: rgba(245, 158, 11, 0.18); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 800; display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <i class="fa-solid fa-clock"></i> Pending Approval
                                    </span>
                                    <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.25rem;">
                                        Wala pang bawas
                                    </div>
                                @else
                                    <span
                                        style="background: rgba(244, 63, 94, 0.18); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.4); padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 800; display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <i class="fa-solid fa-ban"></i> Rejected
                                    </span>
                                    @if($w->admin_remarks)
                                        <div style="font-size: 0.72rem; color: #fb7185; margin-top: 0.25rem; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $w->admin_remarks }}">
                                            {{ $w->admin_remarks }}
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td style="padding: 0.85rem 0.5rem; font-size: 0.8rem; color: #94a3b8;">
                                {{ $w->created_at->format('M d, Y H:i') }}
                                <div style="color: #64748b; font-size: 0.75rem;">{{ $w->created_at->diffForHumans() }}</div>
                            </td>
                            <td style="padding: 0.85rem 0.5rem; text-align: right;">
                                @if ($w->status === 'pending')
                                    <div style="display: inline-flex; gap: 0.4rem;">
                                        <!-- Approve & Send Button -->
                                        <form action="{{ route('admin.withdrawals.approve', $w->id) }}" method="POST"
                                            onsubmit="return confirm('Confirm approve & payout ₱{{ number_format($w->amount) }} to GCash {{ $w->gcash_number }} ({{ $w->gcash_name }})? This will deduct {{ number_format($w->amount) }} PTS from {{ $w->user->name ?? 'User' }} and notify them: Already sent by the admin.');"
                                            style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-success"
                                                style="padding: 0.4rem 0.75rem; font-size: 0.8rem; font-weight: 700; background: linear-gradient(135deg, #10b981, #059669);"
                                                {{ ($w->user && $w->user->points < $w->amount) ? 'disabled' : '' }}
                                                title="Approve and mark Already Sent">
                                                <i class="fa-solid fa-check"></i> Approve & Send
                                            </button>
                                        </form>

                                        <!-- Reject Button Modal Trigger -->
                                        <button type="button" class="btn btn-danger"
                                            style="padding: 0.4rem 0.65rem; font-size: 0.8rem;"
                                            onclick="openRejectModal({{ $w->id }}, '{{ $w->user->name ?? 'User' }}', '{{ number_format($w->amount) }}')"
                                            title="Reject request">
                                            <i class="fa-solid fa-xmark"></i> Reject
                                        </button>
                                    </div>
                                @else
                                    <span style="font-size: 0.8rem; color: #64748b;">Completed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2.5rem; color: #64748b;">
                                <i class="fa-solid fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                                <div>No withdrawal records found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            @if ($withdrawals->hasPages())
                <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
                    {{ $withdrawals->appends(['search' => $search, 'status' => $status])->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Reject Withdrawal Reason Modal -->
    <div id="rejectModal" class="modal-overlay">
        <div class="modal-card" style="max-width: 440px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(244, 63, 94, 0.2); display: flex; align-items: center; justify-content: center; color: #fb7185;">
                        <i class="fa-solid fa-ban fa-lg"></i>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #fff;">Reject Withdrawal</h3>
                </div>
                <button onclick="closeRejectModal()"
                    style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <p id="rejectModalDesc" style="color: #cbd5e1; font-size: 0.9rem; margin-bottom: 1.25rem;">
                Reject withdrawal request.
            </p>

            <form id="rejectWithdrawalForm" method="POST" action="">
                @csrf
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">
                        Reason for Rejection (Optional)
                    </label>
                    <textarea name="remarks" class="form-input" rows="3" placeholder="e.g. Invalid GCash details or duplicate request"></textarea>
                </div>

                <div style="display: flex; gap: 0.75rem;">
                    <button type="button" class="btn btn-outline" style="flex: 1;" onclick="closeRejectModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger" style="flex: 2;">
                        <i class="fa-solid fa-ban"></i> Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(id, playerName, amount) {
            const form = document.getElementById('rejectWithdrawalForm');
            form.action = `/admin/withdrawals/${id}/reject`;
            const desc = document.getElementById('rejectModalDesc');
            desc.innerHTML = `Are you sure you want to reject <strong>₱${amount}</strong> withdrawal request for <strong>${playerName}</strong>?`;
            const modal = document.getElementById('rejectModal');
            if (modal) modal.classList.add('active');
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            if (modal) modal.classList.remove('active');
        }
    </script>
@endsection
