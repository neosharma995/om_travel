<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\TrashItem;
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
            ->with('tour')
            ->when($status !== '', fn (Builder $query): Builder => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'currentStatus' => $status,
        ]);
    }

    public function show(Booking $booking): View
    {
        $booking->load('tour.destination');

        return view('admin.bookings.show', [
            'booking' => $booking,
        ]);
    }

    public function update(UpdateBookingRequest $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validated();

        $booking->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.bookings.show', $booking)
            ->with('success', 'Booking status updated successfully.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.bookings.index');
        }

        $action = $request->string('action')->trim()->toString();
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one booking.');
        }

        $bookings = Booking::query()->whereIn('id', $ids)->get();

        if (in_array($action, ['confirmed', 'pending', 'cancelled'], true)) {
            Booking::query()->whereIn('id', $ids)->update(['status' => $action]);

            return back()->with('success', count($bookings)." booking(s) marked as {$action}.");
        }

        if ($action === 'trash') {
            foreach ($bookings as $booking) {
                TrashItem::trash($booking);
            }

            return back()->with('success', count($bookings).' booking(s) moved to trash bin.');
        }

        if ($action === 'delete') {
            foreach ($bookings as $booking) {
                $booking->delete();
            }

            return back()->with('success', count($bookings).' booking(s) deleted permanently.');
        }

        return back()->with('error', 'Invalid action selected.');
    }
}
