<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;

class BookingController extends Controller
{
    public function store(StoreBookingRequest $request, Tour $tour): RedirectResponse
    {
        abort_unless($tour->is_active, 404);

        $validated = $request->validated();
        $guests = (int) $validated['guests'];
        $totalPrice = round((float) $tour->price * $guests, 2);

        $booking = Booking::query()->create([
            'tour_id' => $tour->id,
            'user_id' => $request->user()?->id,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'travel_date' => $validated['travel_date'],
            'guests' => $guests,
            'total_price' => $totalPrice,
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('tours.show', $tour)
            ->with('booking_success', "Booking #{$booking->id} received! Our travel team will confirm your reservation shortly.");
    }
}
