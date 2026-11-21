@extends('layouts.customer')

@section('title', 'My Bookings')
@section('page_title', 'My Tour Bookings')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">Reservation History</h2>
            <div class="filter-tabs">
                <a href="{{ route('customer.bookings.index') }}" class="filter-tab {{ $currentStatus === '' ? 'is-active' : '' }}">All</a>
                <a href="{{ route('customer.bookings.index', ['status' => 'pending']) }}" class="filter-tab {{ $currentStatus === 'pending' ? 'is-active' : '' }}">Pending</a>
                <a href="{{ route('customer.bookings.index', ['status' => 'confirmed']) }}" class="filter-tab {{ $currentStatus === 'confirmed' ? 'is-active' : '' }}">Confirmed</a>
                <a href="{{ route('customer.bookings.index', ['status' => 'cancelled']) }}" class="filter-tab {{ $currentStatus === 'cancelled' ? 'is-active' : '' }}">Cancelled</a>
            </div>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Photo</th>
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
                            <td>
                                @if ($booking->tour)
                                    <img src="{{ $booking->tour->imageUrl() }}" alt="{{ $booking->tour->title }}" class="admin-thumb">
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <strong>{{ $booking->tour?->title ?? 'Tour Package' }}</strong>
                                @if ($booking->tour?->destination)
                                    <div style="font-size: 12px; color: var(--admin-muted);">{{ $booking->tour->destination->name }}</div>
                                @endif
                            </td>
                            <td>{{ $booking->travel_date->format('M d, Y') }}</td>
                            <td>{{ $booking->guests }}</td>
                            <td><strong>${{ number_format((float) $booking->total_price, 2) }}</strong></td>
                            <td>
                                <span class="badge {{ $booking->status === 'confirmed' ? 'badge-success' : ($booking->status === 'cancelled' ? 'badge-danger' : 'badge-warning') }}">
                                    {{ $booking->status }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    @if ($booking->tour)
                                        <a href="{{ route('tours.show', $booking->tour) }}" class="btn btn-outline btn-sm">View Tour</a>
                                    @endif
                                    @if ($booking->status === 'pending')
                                        <form action="{{ route('customer.bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Cancel booking #{{ $booking->id }}?')">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No bookings found for this filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $bookings->links() }}
    </div>
@endsection
