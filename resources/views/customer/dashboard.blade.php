@extends('layouts.customer')

@section('title', 'My Dashboard')
@section('page_title', 'Traveller Dashboard')

@section('content')
    <div class="customer-hero-banner">
        <div>
            <h2>Welcome back, {{ $user->name }}!</h2>
            <p>Track your upcoming adventures, manage reservations, and reach out to your travel concierge anytime.</p>
        </div>
        <a href="{{ route('tours.index') }}" class="btn btn-primary">Explore Tours &rarr;</a>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">Total Bookings</div>
            <div class="kpi-value">{{ $stats['totalBookings'] }}</div>
            <div class="kpi-sub">All recorded reservations</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Confirmed Trips</div>
            <div class="kpi-value">{{ $stats['confirmedBookings'] }}</div>
            <div class="kpi-sub">Ready for departure</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Pending Approval</div>
            <div class="kpi-value">{{ $stats['pendingBookings'] }}</div>
            <div class="kpi-sub">Awaiting confirmation</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Total Travel Spend</div>
            <div class="kpi-value">${{ number_format($stats['totalSpent'], 0) }}</div>
            <div class="kpi-sub">Excludes cancelled trips</div>
        </div>
    </div>

    <div class="two-col-grid">
        <div class="panel-card">
            <div class="panel-header">
                <h2 class="panel-title">My Recent Bookings</h2>
                <a href="{{ route('customer.bookings.index') }}" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Tour</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentBookings as $booking)
                            <tr>
                                <td>
                                    <strong>{{ $booking->tour?->title ?? 'Tour Package' }}</strong>
                                    <div style="font-size: 12px; color: var(--admin-muted);">{{ $booking->guests }} {{ \Illuminate\Support\Str::plural('guest', $booking->guests) }}</div>
                                </td>
                                <td>{{ $booking->travel_date->format('M d, Y') }}</td>
                                <td>${{ number_format((float) $booking->total_price, 2) }}</td>
                                <td>
                                    <span class="badge {{ $booking->status === 'confirmed' ? 'badge-success' : ($booking->status === 'cancelled' ? 'badge-danger' : 'badge-warning') }}">
                                        {{ $booking->status }}
                                    </span>
                                </td>
                                <td>
                                    @if ($booking->status === 'pending')
                                        <form action="{{ route('customer.bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Cancel this booking?')">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                        </form>
                                    @else
                                        <span style="font-size: 12px; color: var(--admin-muted);">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">You have no bookings yet. <a href="{{ route('tours.index') }}" style="color: var(--admin-accent-dark); font-weight: 700;">Browse tours</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel-card">
            <div class="panel-header">
                <h2 class="panel-title">Recent Inquiries</h2>
                <a href="{{ route('customer.inquiries.index') }}" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Sent</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentInquiries as $inquiry)
                            <tr>
                                <td><strong>{{ $inquiry->subject }}</strong></td>
                                <td>{{ $inquiry->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge {{ $inquiry->status === 'replied' ? 'badge-success' : ($inquiry->status === 'new' ? 'badge-warning' : 'badge-neutral') }}">
                                        {{ $inquiry->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">No inquiries sent yet. <a href="{{ route('contact.create') }}" style="color: var(--admin-accent-dark); font-weight: 700;">Send a message</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
