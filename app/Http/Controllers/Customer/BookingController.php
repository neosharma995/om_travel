<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->trim()->toString();

        $bookings = Booking::query()
            ->forCustomer($request->user())
            ->with('tour.destination')
            ->when($status !== '', fn (Builder $query): Builder => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customer.bookings.index', [
            'bookings' => $bookings,
            'currentStatus' => $status,
        ]);
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->belongsToCustomer($request->user()), 403);

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Only pending bookings can be cancelled.');
        }

        $booking->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', "Booking #{$booking->id} has been cancelled.");
    }
}
