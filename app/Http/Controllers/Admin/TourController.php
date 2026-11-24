<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TourRequest;
use App\Models\Activity;
use App\Models\Destination;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\TourType;
use App\Models\TrashItem;
use App\Support\MediaUrl;
use App\Support\SlugGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourController extends Controller
{
    public function index(): View
    {
        $tours = Tour::query()
            ->with(['destination', 'category', 'tourType', 'activity'])
            ->ordered()
            ->paginate(10);

        return view('admin.tours.index', [
            'tours' => $tours,
        ]);
    }

    public function create(): View
    {
        return view('admin.tours.create', [
            'tour' => new Tour([
                'duration_days' => 5,
                'rating' => 4.8,
                'reviews_count' => 0,
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 0,
            ]),
            'destinations' => Destination::query()->ordered()->get(),
            'categories' => TourCategory::query()->with('parent')->ordered()->get(),
            'tourTypes' => TourType::query()->ordered()->get(),
            'activities' => Activity::query()->ordered()->get(),
        ]);
    }

    public function store(TourRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = MediaUrl::storeUpload(
            $request->file('image_file'),
            'tours',
            null,
            $validated['image'] ?? '/images/seed/tour-greece.jpg',
        );

        Tour::query()->create([
            'destination_id' => ($validated['destination_id'] ?? null) ?: null,
            'tour_category_id' => ($validated['tour_category_id'] ?? null) ?: null,
            'tour_type_id' => ($validated['tour_type_id'] ?? null) ?: null,
            'activity_id' => ($validated['activity_id'] ?? null) ?: null,
            'title' => $validated['title'],
            'slug' => SlugGenerator::unique('tours', $validated['slug'] ?? $validated['title']),
            'location' => $validated['location'] ?? null,
            'price' => $validated['price'],
            'duration_days' => (int) $validated['duration_days'],
            'duration_label' => $validated['duration_label'] ?? null,
            'rating' => $validated['rating'],
            'reviews_count' => (int) ($validated['reviews_count'] ?? 0),
            'summary' => $validated['summary'] ?? null,
            'description' => $validated['description'] ?? null,
            'highlights' => $validated['highlights'] ?? null,
            'image' => $imagePath,
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.tours.index')
            ->with('success', 'Tour package created successfully.');
    }

    public function show(Tour $tour): View
    {
        $tour->load(['destination', 'category.parent', 'tourType', 'activity'])
            ->loadCount('bookings');

        return view('admin.tours.show', [
            'tour' => $tour,
        ]);
    }

    public function edit(Tour $tour): View
    {
        return view('admin.tours.edit', [
            'tour' => $tour,
            'destinations' => Destination::query()->ordered()->get(),
            'categories' => TourCategory::query()->with('parent')->ordered()->get(),
            'tourTypes' => TourType::query()->ordered()->get(),
            'activities' => Activity::query()->ordered()->get(),
        ]);
    }

    public function update(TourRequest $request, Tour $tour): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = MediaUrl::storeUpload(
            $request->file('image_file'),
            'tours',
            $tour->image,
            $validated['image'] ?? null,
        );

        $tour->update([
            'destination_id' => ($validated['destination_id'] ?? null) ?: null,
            'tour_category_id' => ($validated['tour_category_id'] ?? null) ?: null,
            'tour_type_id' => ($validated['tour_type_id'] ?? null) ?: null,
            'activity_id' => ($validated['activity_id'] ?? null) ?: null,
            'title' => $validated['title'],
            'slug' => SlugGenerator::unique('tours', $validated['slug'] ?? $validated['title'], $tour->id),
            'location' => $validated['location'] ?? null,
            'price' => $validated['price'],
            'duration_days' => (int) $validated['duration_days'],
            'duration_label' => $validated['duration_label'] ?? null,
            'rating' => $validated['rating'],
            'reviews_count' => (int) ($validated['reviews_count'] ?? 0),
            'summary' => $validated['summary'] ?? null,
            'description' => $validated['description'] ?? null,
            'highlights' => $validated['highlights'] ?? null,
            'image' => $imagePath,
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.tours.index')
            ->with('success', 'Tour package updated successfully.');
    }

    public function destroy(Tour $tour): RedirectResponse
    {
        MediaUrl::deleteIfStored($tour->image);
        $tour->delete();

        return redirect()
            ->route('admin.tours.index')
            ->with('success', 'Tour package deleted successfully.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.tours.index');
        }

        $action = $request->string('action')->trim()->toString();
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one tour package.');
        }

        $tours = Tour::query()->whereIn('id', $ids)->get();

        if ($action === 'activate') {
            Tour::query()->whereIn('id', $ids)->update(['is_active' => true]);

            return back()->with('success', count($tours).' tour(s) activated.');
        }

        if ($action === 'deactivate') {
            Tour::query()->whereIn('id', $ids)->update(['is_active' => false]);

            return back()->with('success', count($tours).' tour(s) deactivated.');
        }

        if ($action === 'feature') {
            Tour::query()->whereIn('id', $ids)->update(['is_featured' => true]);

            return back()->with('success', count($tours).' tour(s) marked as featured.');
        }

        if ($action === 'unfeature') {
            Tour::query()->whereIn('id', $ids)->update(['is_featured' => false]);

            return back()->with('success', count($tours).' tour(s) unfeatured.');
        }

        if ($action === 'trash') {
            foreach ($tours as $tour) {
                TrashItem::trash($tour);
            }

            return back()->with('success', count($tours).' tour(s) moved to trash bin.');
        }

        if ($action === 'delete') {
            foreach ($tours as $tour) {
                MediaUrl::deleteIfStored($tour->image);
                $tour->delete();
            }

            return back()->with('success', count($tours).' tour(s) permanently deleted.');
        }

        return back()->with('error', 'Invalid action selected.');
    }
}
