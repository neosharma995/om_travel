<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActivityRequest;
use App\Models\Activity;
use App\Support\MediaUrl;
use App\Support\SlugGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(): View
    {
        $activities = Activity::query()
            ->withCount('tours')
            ->ordered()
            ->paginate(10);

        return view('admin.activities.index', [
            'activities' => $activities,
        ]);
    }

    public function create(): View
    {
        return view('admin.activities.create', [
            'activity' => new Activity(['is_active' => true, 'sort_order' => 0]),
        ]);
    }

    public function store(ActivityRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = MediaUrl::storeUpload(
            $request->file('image_file'),
            'activities',
            null,
            $validated['image'] ?? '/images/seed/cat-hiking.jpg',
        );

        Activity::query()->create([
            'name' => $validated['name'],
            'slug' => SlugGenerator::unique('activities', $validated['slug'] ?? $validated['name']),
            'image' => $imagePath,
            'description' => $validated['description'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.activities.index')
            ->with('success', 'Activity created successfully.');
    }

    public function show(Activity $activity): View
    {
        $activity->loadCount('tours');

        $tours = $activity->tours()
            ->with(['destination', 'category'])
            ->ordered()
            ->limit(10)
            ->get();

        return view('admin.activities.show', [
            'activity' => $activity,
            'tours' => $tours,
        ]);
    }

    public function edit(Activity $activity): View
    {
        return view('admin.activities.edit', [
            'activity' => $activity,
        ]);
    }

    public function update(ActivityRequest $request, Activity $activity): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = MediaUrl::storeUpload(
            $request->file('image_file'),
            'activities',
            $activity->image,
            $validated['image'] ?? null,
        );

        $activity->update([
            'name' => $validated['name'],
            'slug' => SlugGenerator::unique('activities', $validated['slug'] ?? $validated['name'], $activity->id),
            'image' => $imagePath,
            'description' => $validated['description'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.activities.index')
            ->with('success', 'Activity updated successfully.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        MediaUrl::deleteIfStored($activity->image);
        $activity->delete();

        return redirect()
            ->route('admin.activities.index')
            ->with('success', 'Activity deleted successfully.');
    }
}
