@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Overview')

@section('content')
    {{-- KPI Highlights Grid --}}
    <div class="kpi-grid kpi-grid-5">
        <div class="kpi-card">
            <div class="kpi-label">Tour Packages</div>
            <div class="kpi-value">{{ $stats['toursCount'] }}</div>
            <div class="kpi-sub">{{ $stats['categoriesCount'] }} Categories &bull; {{ $stats['destinationsCount'] }} Destinations</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Tour Bookings</div>
            <div class="kpi-value">{{ $stats['bookingsCount'] }}</div>
            <div class="kpi-sub">{{ $stats['pendingBookingsCount'] }} Pending Confirmation</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Contact Inquiries</div>
            <div class="kpi-value">{{ $stats['inquiriesCount'] }}</div>
            <div class="kpi-sub">{{ $stats['newInquiriesCount'] }} Unread Messages</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Content Items</div>
            <div class="kpi-value">{{ $stats['galleryCount'] + $stats['guidesCount'] + $stats['blogPostsCount'] }}</div>
            <div class="kpi-sub">{{ $stats['guidesCount'] }} Guides &bull; {{ $stats['blogPostsCount'] }} Articles</div>
        </div>
        <div class="kpi-card kpi-card-trash">
            <div class="kpi-label">Trash Bin</div>
            <div class="kpi-value">{{ $stats['trashedCount'] }}</div>
            <div class="kpi-sub">
                <a href="{{ route('admin.trash.index') }}" style="color: var(--admin-accent-dark); text-decoration: underline; font-weight: 700;">
                    Manage Trash &rarr;
                </a>
            </div>
        </div>
    </div>

    {{-- Main Two-Column Management Panels (Bookings & Inquiries with Bulk Actions & Pagination) --}}
    <div class="two-col-grid" style="margin-bottom: 24px;">
        {{-- Bookings Panel --}}
        <div class="panel-card">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Recent Bookings</h2>
                    <div style="font-size: 12.5px; color: var(--admin-muted); margin-top: 3px;">
                        Manage reservations, confirmations &amp; cancellations
                    </div>
                </div>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline btn-sm">All Bookings</a>
                </div>
            </div>

            {{-- Status Filter Tabs --}}
            <div style="display: flex; gap: 6px; margin-bottom: 14px; flex-wrap: wrap;">
                <a href="{{ route('admin.dashboard', array_merge(request()->except('booking_status', 'bookings_page'), ['booking_status' => ''])) }}" 
                   class="btn btn-sm {{ empty($bookingStatus) ? 'btn-primary' : 'btn-outline' }}">
                    All ({{ $stats['bookingsCount'] }})
                </a>
                <a href="{{ route('admin.dashboard', array_merge(request()->except('booking_status', 'bookings_page'), ['booking_status' => 'pending'])) }}" 
                   class="btn btn-sm {{ $bookingStatus === 'pending' ? 'btn-primary' : 'btn-outline' }}">
                    Pending ({{ $stats['pendingBookingsCount'] }})
                </a>
                <a href="{{ route('admin.dashboard', array_merge(request()->except('booking_status', 'bookings_page'), ['booking_status' => 'confirmed'])) }}" 
                   class="btn btn-sm {{ $bookingStatus === 'confirmed' ? 'btn-primary' : 'btn-outline' }}">
                    Confirmed
                </a>
                <a href="{{ route('admin.dashboard', array_merge(request()->except('booking_status', 'bookings_page'), ['booking_status' => 'cancelled'])) }}" 
                   class="btn btn-sm {{ $bookingStatus === 'cancelled' ? 'btn-primary' : 'btn-outline' }}">
                    Cancelled
                </a>
            </div>

            {{-- Bookings Bulk Actions Form --}}
            <form action="{{ route('admin.bookings.bulk') }}" method="POST" id="dashboardBookingsForm">
                @csrf
                <div class="bulk-toolbar" id="dashBookingsBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 10px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 8px 14px; border-radius: 8px; margin-bottom: 12px;">
                    <span style="font-weight: 700; font-size: 13px; color: var(--admin-sidebar);">
                        <span id="dashBookingsCount">0</span> bookings selected
                    </span>
                    <div style="display: flex; gap: 6px;">
                        <button type="submit" name="action" value="confirmed" class="btn btn-outline btn-sm" title="Mark selected as confirmed">Confirm</button>
                        <button type="submit" name="action" value="cancelled" class="btn btn-outline btn-sm" title="Mark selected as cancelled">Cancel</button>
                        <button type="submit" name="action" value="trash" class="btn btn-danger btn-sm" onclick="return confirm('Move selected bookings to trash bin?')" title="Move to trash bin">Trash</button>
                    </div>
                </div>

                <div class="table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 36px; text-align: center;">
                                    <input type="checkbox" id="selectAllDashBookings" title="Select all on this page">
                                </th>
                                <th>Traveller</th>
                                <th>Tour</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentBookings as $booking)
                                <tr>
                                    <td style="text-align: center;">
                                        <input type="checkbox" name="ids[]" value="{{ $booking->id }}" class="dash-booking-cb">
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.bookings.show', $booking) }}" style="font-weight: 700; color: var(--admin-sidebar);">{{ $booking->customer_name }}</a>
                                        <div style="font-size: 12px; color: var(--admin-muted);">{{ $booking->travel_date->format('M d, Y') }}</div>
                                    </td>
                                    <td>{{ $booking->tour?->title ?? 'Deleted Tour' }}</td>
                                    <td>₹{{ number_format((float) $booking->total_price, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $booking->status === 'confirmed' ? 'badge-success' : ($booking->status === 'cancelled' ? 'badge-danger' : 'badge-warning') }}">
                                            {{ $booking->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" style="text-align: center; color: var(--admin-muted); padding: 24px;">No bookings found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <div style="margin-top: 14px;">
                {{ $recentBookings->links() }}
            </div>
        </div>

        {{-- Inquiries Panel --}}
        <div class="panel-card">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Recent Inquiries</h2>
                    <div style="font-size: 12.5px; color: var(--admin-muted); margin-top: 3px;">
                        Manage customer questions &amp; message responses
                    </div>
                </div>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <a href="{{ route('admin.inquiries.index') }}" class="btn btn-outline btn-sm">All Inquiries</a>
                </div>
            </div>

            {{-- Status Filter Tabs --}}
            <div style="display: flex; gap: 6px; margin-bottom: 14px; flex-wrap: wrap;">
                <a href="{{ route('admin.dashboard', array_merge(request()->except('inquiry_status', 'inquiries_page'), ['inquiry_status' => ''])) }}" 
                   class="btn btn-sm {{ empty($inquiryStatus) ? 'btn-primary' : 'btn-outline' }}">
                    All ({{ $stats['inquiriesCount'] }})
                </a>
                <a href="{{ route('admin.dashboard', array_merge(request()->except('inquiry_status', 'inquiries_page'), ['inquiry_status' => 'new'])) }}" 
                   class="btn btn-sm {{ $inquiryStatus === 'new' ? 'btn-primary' : 'btn-outline' }}">
                    New ({{ $stats['newInquiriesCount'] }})
                </a>
                <a href="{{ route('admin.dashboard', array_merge(request()->except('inquiry_status', 'inquiries_page'), ['inquiry_status' => 'replied'])) }}" 
                   class="btn btn-sm {{ $inquiryStatus === 'replied' ? 'btn-primary' : 'btn-outline' }}">
                    Replied
                </a>
            </div>

            {{-- Inquiries Bulk Actions Form --}}
            <form action="{{ route('admin.inquiries.bulk') }}" method="POST" id="dashboardInquiriesForm">
                @csrf
                <div class="bulk-toolbar" id="dashInquiriesBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 10px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 8px 14px; border-radius: 8px; margin-bottom: 12px;">
                    <span style="font-weight: 700; font-size: 13px; color: var(--admin-sidebar);">
                        <span id="dashInquiriesCount">0</span> inquiries selected
                    </span>
                    <div style="display: flex; gap: 6px;">
                        <button type="submit" name="action" value="replied" class="btn btn-outline btn-sm" title="Mark selected as replied">Mark Replied</button>
                        <button type="submit" name="action" value="closed" class="btn btn-outline btn-sm" title="Mark selected as closed">Close</button>
                        <button type="submit" name="action" value="trash" class="btn btn-danger btn-sm" onclick="return confirm('Move selected inquiries to trash bin?')" title="Move to trash bin">Trash</button>
                    </div>
                </div>

                <div class="table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 36px; text-align: center;">
                                    <input type="checkbox" id="selectAllDashInquiries" title="Select all on this page">
                                </th>
                                <th>Sender</th>
                                <th>Subject</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentInquiries as $inquiry)
                                <tr>
                                    <td style="text-align: center;">
                                        <input type="checkbox" name="ids[]" value="{{ $inquiry->id }}" class="dash-inquiry-cb">
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.inquiries.show', $inquiry) }}" style="font-weight: 700; color: var(--admin-sidebar);">{{ $inquiry->name }}</a>
                                        <div style="font-size: 12px; color: var(--admin-muted);">{{ $inquiry->email }}</div>
                                    </td>
                                    <td>{{ $inquiry->subject }}</td>
                                    <td>
                                        <span class="badge {{ $inquiry->status === 'replied' ? 'badge-success' : ($inquiry->status === 'new' ? 'badge-warning' : 'badge-neutral') }}">
                                            {{ $inquiry->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align: center; color: var(--admin-muted); padding: 24px;">No inquiries found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <div style="margin-top: 14px;">
                {{ $recentInquiries->links() }}
            </div>
        </div>
    </div>

    {{-- Third Panel: Trash Bin Dashboard Module with Pagination & Quick Recovery --}}
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Trash Bin Quick Access</h2>
                <div style="font-size: 12.5px; color: var(--admin-muted); margin-top: 3px;">
                    Deleted items stored safely in the archive. Restore with one click or purge permanently.
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <a href="{{ route('admin.trash.index') }}" class="btn btn-outline btn-sm">Open Full Trash Bin ({{ $stats['trashedCount'] }})</a>
            </div>
        </div>

        <form action="{{ route('admin.trash.bulk') }}" method="POST" id="dashTrashBulkForm">
            @csrf
            <div class="bulk-toolbar" id="dashTrashBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 10px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 8px 14px; border-radius: 8px; margin-bottom: 12px;">
                <span style="font-weight: 700; font-size: 13px; color: var(--admin-sidebar);">
                    <span id="dashTrashCount">0</span> trashed items selected
                </span>
                <div style="display: flex; gap: 6px;">
                    <button type="submit" name="action" value="restore" class="btn btn-primary btn-sm">Restore Selected</button>
                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Permanently delete selected items? This cannot be undone.')">Purge Selected</button>
                </div>
            </div>

            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 36px; text-align: center;">
                                <input type="checkbox" id="selectAllDashTrash" title="Select all on this page">
                            </th>
                            <th style="width: 140px;">Item Type</th>
                            <th>Title / Summary</th>
                            <th>Original ID</th>
                            <th>Trashed Date</th>
                            <th style="width: 160px; text-align: right;">Quick Recovery</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTrash as $trashItem)
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="ids[]" value="{{ $trashItem->id }}" class="dash-trash-cb">
                                </td>
                                <td>
                                    <span class="badge badge-neutral" style="text-transform: uppercase; font-size: 11px;">
                                        {{ $trashItem->typeLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $trashItem->title }}</strong>
                                    @if ($trashItem->subtitle)
                                        <div style="font-size: 12px; color: var(--admin-muted);">{{ $trashItem->subtitle }}</div>
                                    @endif
                                </td>
                                <td style="font-size: 12.5px; color: var(--admin-muted);">
                                    #{{ $trashItem->item_id ?? 'N/A' }}
                                </td>
                                <td style="font-size: 12.5px; color: var(--admin-muted);">
                                    {{ $trashItem->created_at->diffForHumans() }}
                                </td>
                                <td style="text-align: right;">
                                    <div class="table-actions" style="justify-content: flex-end;">
                                        <button type="submit" formaction="{{ route('admin.trash.restore', $trashItem) }}" class="btn btn-view btn-sm">Restore</button>
                                        <button type="submit" formaction="{{ route('admin.trash.destroy', $trashItem) }}" class="btn btn-danger btn-sm" onclick="return confirm('Permanently purge this item?')">Purge</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--admin-muted); padding: 26px;">
                                    Trash bin is currently empty. Deleted records will appear here.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <div style="margin-top: 14px;">
            {{ $recentTrash->links() }}
        </div>
    </div>

    {{-- Interactive Bulk Selection Script for Dashboard --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function bindBulkHandler(selectAllId, checkboxClass, barId, countId) {
                const selectAll = document.getElementById(selectAllId);
                const checkboxes = Array.from(document.querySelectorAll('.' + checkboxClass));
                const bar = document.getElementById(barId);
                const count = document.getElementById(countId);

                if (!bar || !count) return;

                function sync() {
                    const checked = checkboxes.filter(cb => cb.checked);
                    if (checked.length > 0) {
                        bar.style.display = 'flex';
                        count.textContent = checked.length;
                    } else {
                        bar.style.display = 'none';
                    }
                    if (selectAll) {
                        selectAll.checked = checkboxes.length > 0 && checked.length === checkboxes.length;
                    }
                }

                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        checkboxes.forEach(cb => cb.checked = selectAll.checked);
                        sync();
                    });
                }

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', sync);
                });
            }

            bindBulkHandler('selectAllDashBookings', 'dash-booking-cb', 'dashBookingsBulkBar', 'dashBookingsCount');
            bindBulkHandler('selectAllDashInquiries', 'dash-inquiry-cb', 'dashInquiriesBulkBar', 'dashInquiriesCount');
            bindBulkHandler('selectAllDashTrash', 'dash-trash-cb', 'dashTrashBulkBar', 'dashTrashCount');
        });
    </script>
@endsection
