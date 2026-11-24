@extends('layouts.admin')

@section('title', 'Bookings')
@section('page_title', 'Tour Bookings')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">All Reservations</h2>
                <div style="font-size: 12.5px; color: var(--admin-muted); margin-top: 2px;">
                    Track and manage traveler bookings and ticket status
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                @php $trashedBookingsCount = \App\Models\TrashItem::query()->where('item_type', 'booking')->count(); @endphp
                <a href="{{ route('admin.trash.index', ['type' => 'booking']) }}" class="btn btn-outline btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                    Trashed Bookings ({{ $trashedBookingsCount }})
                </a>
            </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; flex-wrap: wrap;">
            <div class="filter-tabs">
                <a href="{{ route('admin.bookings.index') }}" class="filter-tab {{ $currentStatus === '' ? 'is-active' : '' }}">All</a>
                <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="filter-tab {{ $currentStatus === 'pending' ? 'is-active' : '' }}">Pending</a>
                <a href="{{ route('admin.bookings.index', ['status' => 'confirmed']) }}" class="filter-tab {{ $currentStatus === 'confirmed' ? 'is-active' : '' }}">Confirmed</a>
                <a href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}" class="filter-tab {{ $currentStatus === 'cancelled' ? 'is-active' : '' }}">Cancelled</a>
            </div>
        </div>

        {{-- Standalone Bulk Actions Form --}}
        <form action="{{ route('admin.bookings.bulk') }}" method="POST" id="bookingsBulkForm">
            @csrf
            <div class="bulk-toolbar" id="bookingsBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 12px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 10px 16px; border-radius: 10px; margin-bottom: 16px;">
                <div style="font-weight: 700; color: var(--admin-sidebar); font-size: 13.5px;">
                    <span id="bookingsSelectedCount">0</span> bookings selected
                </div>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <button type="submit" name="action" value="confirmed" class="btn btn-outline btn-sm" style="color: #065F46; border-color: #A7F3D0;">Confirm</button>
                    <button type="submit" name="action" value="cancelled" class="btn btn-outline btn-sm" style="color: #991B1B; border-color: #FECACA;">Cancel</button>
                    <button type="submit" name="action" value="pending" class="btn btn-outline btn-sm">Mark Pending</button>
                    <button type="submit" name="action" value="trash" class="btn btn-outline btn-sm" style="color: #92400E; border-color: #FCD34D;">Move to Trash</button>
                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Permanently delete the selected bookings? This cannot be undone.')">Delete Permanently</button>
                </div>
            </div>
        </form>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 38px; text-align: center;">
                            <input type="checkbox" id="selectAllBookings" aria-label="Select all bookings">
                        </th>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Tour Package</th>
                        <th>Travel Date</th>
                        <th>Guests</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="ids[]" value="{{ $booking->id }}" form="bookingsBulkForm" class="booking-row-checkbox" aria-label="Select booking #{{ $booking->id }}">
                            </td>
                            <td>#{{ $booking->id }}</td>
                            <td>
                                <strong>{{ $booking->customer_name }}</strong>
                                <div style="font-size: 12px; color: var(--admin-muted);">{{ $booking->customer_email }}</div>
                            </td>
                            <td>{{ $booking->tour?->title ?? 'Deleted Tour' }}</td>
                            <td>{{ $booking->travel_date->format('M d, Y') }}</td>
                            <td>{{ $booking->guests }}</td>
                            <td>₹{{ number_format((float) $booking->total_price, 2) }}</td>
                            <td>
                                <span class="badge {{ $booking->status === 'confirmed' ? 'badge-success' : ($booking->status === 'cancelled' ? 'badge-danger' : 'badge-warning') }}">
                                    {{ $booking->status }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-outline btn-sm">Manage</a>
                                    <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('Delete this booking?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" style="text-align: center; color: var(--admin-muted); padding: 32px;">No bookings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $bookings->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('selectAllBookings');
            const checkboxes = Array.from(document.querySelectorAll('.booking-row-checkbox'));
            const bulkBar = document.getElementById('bookingsBulkBar');
            const selectedCount = document.getElementById('bookingsSelectedCount');

            if (!selectAll || !bulkBar || !selectedCount) return;

            const updateBulkBar = () => {
                const checked = checkboxes.filter(cb => cb.checked);
                const count = checked.length;
                selectedCount.textContent = count;
                bulkBar.style.display = count > 0 ? 'flex' : 'none';
                selectAll.checked = count > 0 && count === checkboxes.length;
                selectAll.indeterminate = count > 0 && count < checkboxes.length;
            };

            selectAll.addEventListener('change', () => {
                checkboxes.forEach(cb => { cb.checked = selectAll.checked; });
                updateBulkBar();
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkBar);
            });
        });
    </script>
@endsection
