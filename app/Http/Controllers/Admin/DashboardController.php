<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\GalleryItem;
use App\Models\Inquiry;
use App\Models\Testimonial;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\TourGuide;
use App\Models\TourType;
use App\Models\TrashItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $stats = [
            'toursCount' => Tour::query()->count(),
            'activeToursCount' => Tour::query()->where('is_active', true)->count(),
            'destinationsCount' => Destination::query()->count(),
            'popularDestinationsCount' => Destination::query()->where('is_popular', true)->count(),
            'categoriesCount' => TourCategory::query()->count(),
            'tourTypesCount' => TourType::query()->count(),
            'activitiesCount' => Activity::query()->count(),
            'galleryCount' => GalleryItem::query()->count(),
            'guidesCount' => TourGuide::query()->count(),
            'testimonialsCount' => Testimonial::query()->count(),
            'blogPostsCount' => BlogPost::query()->count(),
            'bookingsCount' => Booking::query()->count(),
            'pendingBookingsCount' => Booking::query()->where('status', 'pending')->count(),
            'confirmedBookingsCount' => Booking::query()->where('status', 'confirmed')->count(),
            'totalRevenue' => Booking::query()->where('status', 'confirmed')->sum('total_price'),
            'inquiriesCount' => Inquiry::query()->count(),
            'newInquiriesCount' => Inquiry::query()->where('status', 'new')->count(),
            'trashedCount' => TrashItem::query()->count(),
        ];

        $bookingStatus = $request->string('booking_status')->trim()->toString();
        $inquiryStatus = $request->string('inquiry_status')->trim()->toString();

        $bookings = Booking::query()
            ->with('tour')
            ->when($bookingStatus !== '', fn ($q) => $q->where('status', $bookingStatus))
            ->latest()
            ->paginate(5, ['*'], 'bookings_page')
            ->withQueryString();

        $inquiries = Inquiry::query()
            ->when($inquiryStatus !== '', fn ($q) => $q->where('status', $inquiryStatus))
            ->latest()
            ->paginate(5, ['*'], 'inquiries_page')
            ->withQueryString();

        $recentTrash = TrashItem::query()
            ->latest()
            ->paginate(5, ['*'], 'trash_page')
            ->withQueryString();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentBookings' => $bookings,
            'recentInquiries' => $inquiries,
            'recentTrash' => $recentTrash,
            'bookingStatus' => $bookingStatus,
            'inquiryStatus' => $inquiryStatus,
        ]);
    }
}
