<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Destination;
use App\Models\GalleryItem;
use App\Models\Testimonial;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\TourGuide;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = TourCategory::query()
            ->active()
            ->ordered()
            ->get();

        $popularDestinations = Destination::query()
            ->active()
            ->popular()
            ->withCount(['tours' => fn (Builder $query): Builder => $query->active()])
            ->ordered()
            ->get();

        $allDestinations = Destination::query()
            ->active()
            ->ordered()
            ->get();

        $featuredTours = Tour::query()
            ->active()
            ->featured()
            ->with(['destination', 'category', 'tourType'])
            ->ordered()
            ->limit(8)
            ->get();

        $galleryItems = GalleryItem::query()
            ->active()
            ->ordered()
            ->limit(7)
            ->get();

        $guides = TourGuide::query()
            ->active()
            ->ordered()
            ->limit(8)
            ->get();

        $testimonials = Testimonial::query()
            ->active()
            ->ordered()
            ->limit(6)
            ->get();

        $blogPosts = BlogPost::query()
            ->published()
            ->latest('published_at')
            ->latest('id')
            ->limit(3)
            ->get();

        return view('website.home', [
            'categories' => $categories,
            'popularDestinations' => $popularDestinations,
            'allDestinations' => $allDestinations,
            'featuredTours' => $featuredTours,
            'galleryItems' => $galleryItems,
            'guides' => $guides,
            'testimonials' => $testimonials,
            'blogPosts' => $blogPosts,
        ]);
    }
}
