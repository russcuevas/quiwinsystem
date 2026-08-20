@extends('layouts.app')

@section('title', 'Manage Players - Admin')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- Top Action Bar -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
        <div>
            <a href="{{ route('admin.dashboard') }}" style="color: #94a3b8; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem; margin-bottom: 0.4rem;">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1 style="font-size: 2rem; font-weight: 900; color: #fff; letter-spacing: -0.5px;">Player Management</h1>
        </div>

        <!-- Search Box -->
        <form action="{{ route('admin.users') }}" method="GET" style="display: flex; gap: 0.5rem;">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search player name or email..." class="form-input" style="width: 280px; padding: 0.6rem 0.9rem;">
            <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1rem;">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            @if($search)
                <a href="{{ route('admin.users') }}" class="btn btn-outline" style="padding: 0.6rem 0.85rem;">Clear</a>
            @endif
        </form>
    </div>

    <!-- Players Table Card -->
    <div class="glass-card" style="padding: 1.75rem;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.08); color: #64748b; text-align: left;">
                        <th style="padding: 0.75rem 0.5rem;">ID</th>
                        <th style="padding: 0.75rem 0.5rem;">Player Name</th>
                        <th style="padding: 0.75rem 0.5rem;">Email</th>
                        <th style="padding: 0.75rem 0.5rem;">Points Balance</th>
                        <th style="padding: 0.75rem 0.5rem;">Status</th>
                        <th style="padding: 0.75rem 0.5rem;">Joined</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); color: #cbd5e1;">
                        <td style="padding: 0.85rem 0.5rem; font-weight: 700; color: #94a3b8;">#{{ $user->id }}</td>
                        <td style="padding: 0.85rem 0.5rem; font-weight: 600; color: #fff;">
                            {{ $user->name }}
                        </td>
                        <td style="padding: 0.85rem 0.5rem; color: #94a3b8;">{{ $user->email }}</td>
                        <td style="padding: 0.85rem 0.5rem; font-weight: 800; color: #fbbf24;">
                            {{ number_format($user->points) }} PTS
                        </td>
                        <td style="padding: 0.85rem 0.5rem;">
                            <span style="font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 9999px; font-weight: 700;
                                {{ $user->is_active ? 'background: rgba(16, 185, 129, 0.2); color: #34d399;' : 'background: rgba(239, 68, 68, 0.2); color: #f87171;' }}">
                                {{ $user->is_active ? 'Active' : 'Deactivated' }}
                            </span>
                        </td>
                        <td style="padding: 0.85rem 0.5rem; color: #64748b; font-size: 0.8rem;">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td style="padding: 0.85rem 0.5rem; text-align: right;">
                            <div style="display: inline-flex; gap: 0.5rem;">
                                <!-- Adjust Points Modal Trigger -->
                                <button type="button" class="btn btn-outline" style="padding: 0.4rem 0.75rem; font-size: 0.8rem; color: #fbbf24; border-color: rgba(245, 158, 11, 0.3);" onclick="openAdjustModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->points }})">
                                    <i class="fa-solid fa-coins"></i> Adjust PTS
                                </button>

                                <!-- Toggle Status Form -->
                                <form action="{{ route('admin.users.toggle', ['userId' => $user->id]) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Change status for {{ addslashes($user->name) }}?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline" style="padding: 0.4rem 0.75rem; font-size: 0.8rem; color: {{ $user->is_active ? '#f87171' : '#34d399' }};">
                                        <i class="fa-solid {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #64748b; padding: 2rem;">No players found matching your criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1.5rem;">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>

</div>

<!-- Admin Point Adjustment Modal -->
<div id="adjustModal" class="modal-overlay">
    <div class="modal-card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(99, 102, 241, 0.2); display: flex; align-items: center; justify-content: center; color: #818cf8;">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #fff;">Adjust Points</h3>
            </div>
            <button onclick="closeAdjustModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div style="background: rgba(255,255,255,0.04); border-radius: 0.85rem; padding: 0.85rem 1rem; margin-bottom: 1.25rem;">
            <div style="font-size: 0.8rem; color: #94a3b8;">Player: <strong id="adjustUserName" style="color: #fff;"></strong></div>
            <div style="font-size: 0.8rem; color: #94a3b8;">Current Balance: <strong id="adjustUserPoints" style="color: #fbbf24;"></strong> PTS</div>
        </div>

        <form id="adjustForm" method="POST" action="">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">
                    Points Delta (Positive to add, Negative to deduct)
                </label>
                <input type="number" name="amount" id="adjustAmount" class="form-input" placeholder="e.g. 100 or -50" required>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">
                    Reason / Note (Optional)
                </label>
                <input type="text" name="reason" class="form-input" placeholder="e.g. Tournament reward, manual fix">
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <button type="button" class="btn btn-outline" style="flex: 1;" onclick="closeAdjustModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 2;">
                    <i class="fa-solid fa-check"></i> Apply Adjustment
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
