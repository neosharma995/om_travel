@extends('layouts.admin')

@section('title', 'Bookings')
@section('page_title', 'Tour Bookings')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">All Reservations</h2>
            <div class="filter-tabs">
                <a href="{{ route('admin.bookings.index') }}" class="filter-tab {{ $currentStatus === '' ? 'is-active' : '' }}">All</a>
                <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="filter-tab {{ $currentStatus === 'pending' ? 'is-active' : '' }}">Pending</a>
                <a href="{{ route('admin.bookings.index', ['status' => 'confirmed']) }}" class="filter-tab {{ $currentStatus === 'confirmed' ? 'is-active' : '' }}">Confirmed</a>
                <a href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}" class="filter-tab {{ $currentStatus === 'cancelled' ? 'is-active' : '' }}">Cancelled</a>
            </div>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
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
                        <tr><td colspan="8">No bookings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $bookings->links() }}
    </div>
@endsection
