@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Overview')

@section('content')
    <div class="kpi-grid">
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
            <div class="kpi-sub">{{ $stats['newInquiriesCount'] }} Unread New Messages</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Content Items</div>
            <div class="kpi-value">{{ $stats['galleryCount'] + $stats['guidesCount'] + $stats['blogPostsCount'] }}</div>
            <div class="kpi-sub">{{ $stats['guidesCount'] }} Guides &bull; {{ $stats['blogPostsCount'] }} Articles</div>
        </div>
    </div>

    <div class="two-col-grid">
        <div class="panel-card">
            <div class="panel-header">
                <h2 class="panel-title">Recent Bookings</h2>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Traveller</th>
                            <th>Tour</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentBookings as $booking)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking) }}" style="font-weight: 700;">{{ $booking->customer_name }}</a>
                                    <div style="font-size: 12px; color: var(--admin-muted);">{{ $booking->travel_date->format('M d, Y') }}</div>
                                </td>
                                <td>{{ $booking->tour?->title ?? 'Deleted Tour' }}</td>
                                <td>${{ number_format((float) $booking->total_price, 2) }}</td>
                                <td>
                                    <span class="badge {{ $booking->status === 'confirmed' ? 'badge-success' : ($booking->status === 'cancelled' ? 'badge-danger' : 'badge-warning') }}">
                                        {{ $booking->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No bookings yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel-card">
            <div class="panel-header">
                <h2 class="panel-title">Recent Inquiries</h2>
                <a href="{{ route('admin.inquiries.index') }}" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Sender</th>
                            <th>Subject</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentInquiries as $inquiry)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.inquiries.show', $inquiry) }}" style="font-weight: 700;">{{ $inquiry->name }}</a>
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
                            <tr><td colspan="3">No inquiries yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
