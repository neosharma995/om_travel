<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $baseBookings = Booking::query()->forCustomer($user);

        $stats = [
            'totalBookings' => (clone $baseBookings)->count(),
            'confirmedBookings' => (clone $baseBookings)->where('status', 'confirmed')->count(),
            'pendingBookings' => (clone $baseBookings)->where('status', 'pending')->count(),
            'totalSpent' => (float) (clone $baseBookings)->where('status', '!=', 'cancelled')->sum('total_price'),
        ];

        $recentBookings = (clone $baseBookings)
            ->with('tour.destination')
            ->latest()
            ->limit(5)
            ->get();

        $recentInquiries = Inquiry::query()
            ->whereRaw('LOWER(email) = ?', [Str::lower($user->email)])
            ->latest()
            ->limit(5)
            ->get();

        return view('customer.dashboard', [
            'user' => $user,
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'recentInquiries' => $recentInquiries,
        ]);
    }
}
