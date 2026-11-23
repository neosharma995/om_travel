<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'toursCount' => Tour::query()->count(),
            'destinationsCount' => Destination::query()->count(),
            'categoriesCount' => TourCategory::query()->count(),
            'tourTypesCount' => TourType::query()->count(),
            'galleryCount' => GalleryItem::query()->count(),
            'guidesCount' => TourGuide::query()->count(),
            'testimonialsCount' => Testimonial::query()->count(),
            'blogPostsCount' => BlogPost::query()->count(),
            'bookingsCount' => Booking::query()->count(),
            'pendingBookingsCount' => Booking::query()->where('status', 'pending')->count(),
            'inquiriesCount' => Inquiry::query()->count(),
            'newInquiriesCount' => Inquiry::query()->where('status', 'new')->count(),
        ];

        $recentBookings = Booking::query()
            ->with('tour')
            ->latest()
            ->limit(5)
            ->get();

        $recentInquiries = Inquiry::query()
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'recentInquiries' => $recentInquiries,
        ]);
    }
}
