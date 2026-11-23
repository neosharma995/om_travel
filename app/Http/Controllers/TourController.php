<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Destination;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\TourType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourController extends Controller
{
    public function index(Request $request): View
    {
        $destinationSlug = $request->string('destination')->trim()->toString();
        $categorySlug = $request->string('category')->trim()->toString();
        $tourTypeSlug = $request->string('tour_type')->trim()->toString();
        $activitySlug = $request->string('activity')->trim()->toString();
        $duration = $request->string('duration')->trim()->toString();
        $maxPrice = $request->input('max_price');
        $search = $request->string('search')->trim()->toString();

        $tours = Tour::query()
            ->active()
            ->with(['destination', 'category', 'tourType', 'activity'])
            ->when($destinationSlug !== '', function (Builder $query) use ($destinationSlug): void {
                $query->whereHas('destination', fn (Builder $sub): Builder => $sub->where('slug', $destinationSlug));
            })
            ->when($categorySlug !== '', function (Builder $query) use ($categorySlug): void {
                $query->whereHas('category', fn (Builder $sub): Builder => $sub->where('slug', $categorySlug));
            })
            ->when($tourTypeSlug !== '', function (Builder $query) use ($tourTypeSlug): void {
                $query->whereHas('tourType', fn (Builder $sub): Builder => $sub->where('slug', $tourTypeSlug));
            })
            ->when($activitySlug !== '', function (Builder $query) use ($activitySlug): void {
                $query->whereHas('activity', fn (Builder $sub): Builder => $sub->where('slug', $activitySlug));
            })
            ->when($duration === 'short', fn (Builder $query): Builder => $query->where('duration_days', '<=', 3))
            ->when($duration === 'medium', fn (Builder $query): Builder => $query->whereBetween('duration_days', [4, 6]))
            ->when($duration === 'long', fn (Builder $query): Builder => $query->where('duration_days', '>=', 7))
            ->when(is_numeric($maxPrice) && (float) $maxPrice > 0, function (Builder $query) use ($maxPrice): void {
                $query->where('price', '<=', (float) $maxPrice);
            })
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $sub) use ($search): void {
                    $sub->where('title', 'like', '%'.$search.'%')
                        ->orWhere('location', 'like', '%'.$search.'%')
                        ->orWhere('summary', 'like', '%'.$search.'%');
                });
            })
            ->ordered()
            ->paginate(9)
            ->withQueryString();

        return view('website.tours.index', [
            'tours' => $tours,
            'destinations' => Destination::query()->active()->ordered()->get(),
            'categories' => TourCategory::query()->active()->ordered()->get(),
            'tourTypes' => TourType::query()->active()->ordered()->get(),
            'activities' => Activity::query()->active()->ordered()->get(),
            'filters' => [
                'destination' => $destinationSlug,
                'category' => $categorySlug,
                'tour_type' => $tourTypeSlug,
                'activity' => $activitySlug,
                'duration' => $duration,
                'max_price' => is_numeric($maxPrice) ? $maxPrice : '',
                'search' => $search,
            ],
        ]);
    }

    public function show(Tour $tour): View
    {
        abort_unless($tour->is_active, 404);

        $tour->load(['destination', 'category', 'tourType', 'activity']);

        $relatedTours = Tour::query()
            ->active()
            ->where('id', '!=', $tour->id)
            ->when($tour->destination_id !== null, function (Builder $query) use ($tour): void {
                $query->orderByRaw('CASE WHEN destination_id = ? THEN 0 ELSE 1 END', [$tour->destination_id]);
            })
            ->with(['destination', 'category', 'tourType', 'activity'])
            ->ordered()
            ->limit(3)
            ->get();

        return view('website.tours.show', [
            'tour' => $tour,
            'relatedTours' => $relatedTours,
        ]);
    }
}
