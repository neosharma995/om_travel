@extends('layouts.admin')

@section('title', 'Booking #'.$booking->id)
@section('page_title', 'Booking #'.$booking->id)

@section('content')
    <div class="two-col-grid">
        <div class="panel-card">
            <div class="panel-header">
                <h2 class="panel-title">Reservation Details</h2>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
            </div>
            <table class="admin-table">
                <tbody>
                    <tr><th>Tour Package</th><td>{{ $booking->tour?->title ?? 'Deleted Tour' }}</td></tr>
                    <tr><th>Destination</th><td>{{ $booking->tour?->destination?->name ?? '—' }}</td></tr>
                    <tr><th>Customer Name</th><td>{{ $booking->customer_name }}</td></tr>
                    <tr><th>Email</th><td>{{ $booking->customer_email }}</td></tr>
                    <tr><th>Phone</th><td>{{ $booking->customer_phone ?: '—' }}</td></tr>
                    <tr><th>Travel Date</th><td>{{ $booking->travel_date->format('F d, Y') }}</td></tr>
                    <tr><th>Guests</th><td>{{ $booking->guests }}</td></tr>
                    <tr><th>Total Price</th><td><strong>₹{{ number_format((float) $booking->total_price, 2) }}</strong></td></tr>
                    <tr><th>Special Requests</th><td>{{ $booking->special_requests ?: 'None' }}</td></tr>
                </tbody>
            </table>
        </div>

        <div class="panel-card">
            <div class="panel-header">
                <h2 class="panel-title">Update Booking Status</h2>
            </div>

            <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="status" class="form-label">Status *</label>
                    <select id="status" name="status" class="form-control">
                        <option value="pending" @selected(old('status', $booking->status) === 'pending')>Pending</option>
                        <option value="confirmed" @selected(old('status', $booking->status) === 'confirmed')>Confirmed</option>
                        <option value="cancelled" @selected(old('status', $booking->status) === 'cancelled')>Cancelled</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="admin_notes" class="form-label">Internal Admin Notes</label>
                    <textarea id="admin_notes" name="admin_notes" class="form-control">{{ old('admin_notes', $booking->admin_notes) }}</textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Booking Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
