<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DestinationRequest;
use App\Models\Destination;
use App\Models\TrashItem;
use App\Support\MediaUrl;
use App\Support\SlugGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function index(): View
    {
        $destinations = Destination::query()
            ->withCount('tours')
            ->ordered()
            ->paginate(10);

        return view('admin.destinations.index', [
            'destinations' => $destinations,
        ]);
    }

    public function create(): View
    {
        return view('admin.destinations.create', [
            'destination' => new Destination([
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(DestinationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = MediaUrl::storeUpload(
            $request->file('image_file'),
            'destinations',
            null,
            $validated['image'] ?? '/images/seed/tour-greece.jpg',
        );

        Destination::query()->create([
            'name' => $validated['name'],
            'slug' => SlugGenerator::unique('destinations', $validated['slug'] ?? $validated['name']),
            'country' => $validated['country'] ?? null,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'listings_label' => $validated['listings_label'] ?? null,
            'is_popular' => $request->boolean('is_popular'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.destinations.index')
            ->with('success', 'Destination created successfully.');
    }

    public function show(Destination $destination): View
    {
        $destination->loadCount('tours');

        $tours = $destination->tours()
            ->with(['category', 'tourType', 'activity'])
            ->ordered()
            ->limit(10)
            ->get();

        return view('admin.destinations.show', [
            'destination' => $destination,
            'tours' => $tours,
        ]);
    }

    public function edit(Destination $destination): View
    {
        return view('admin.destinations.edit', [
            'destination' => $destination,
        ]);
    }

    public function update(DestinationRequest $request, Destination $destination): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = MediaUrl::storeUpload(
            $request->file('image_file'),
            'destinations',
            $destination->image,
            $validated['image'] ?? null,
        );

        $destination->update([
            'name' => $validated['name'],
            'slug' => SlugGenerator::unique('destinations', $validated['slug'] ?? $validated['name'], $destination->id),
            'country' => $validated['country'] ?? null,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'listings_label' => $validated['listings_label'] ?? null,
            'is_popular' => $request->boolean('is_popular'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.destinations.index')
            ->with('success', 'Destination updated successfully.');
    }

    public function destroy(Destination $destination): RedirectResponse
    {
        MediaUrl::deleteIfStored($destination->image);
        $destination->delete();

        return redirect()
            ->route('admin.destinations.index')
            ->with('success', 'Destination deleted successfully.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.destinations.index');
        }

        $action = $request->string('action')->trim()->toString();
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one destination.');
        }

        $destinations = Destination::query()->whereIn('id', $ids)->get();

        if ($action === 'activate') {
            Destination::query()->whereIn('id', $ids)->update(['is_active' => true]);

            return back()->with('success', count($destinations).' destination(s) activated.');
        }

        if ($action === 'deactivate') {
            Destination::query()->whereIn('id', $ids)->update(['is_active' => false]);

            return back()->with('success', count($destinations).' destination(s) deactivated.');
        }

        if ($action === 'popular') {
            Destination::query()->whereIn('id', $ids)->update(['is_popular' => true]);

            return back()->with('success', count($destinations).' destination(s) marked popular.');
        }

        if ($action === 'unpopular') {
            Destination::query()->whereIn('id', $ids)->update(['is_popular' => false]);

            return back()->with('success', count($destinations).' destination(s) marked standard.');
        }

        if ($action === 'trash') {
            foreach ($destinations as $destination) {
                TrashItem::trash($destination);
            }

            return back()->with('success', count($destinations).' destination(s) moved to trash bin.');
        }

        if ($action === 'delete') {
            foreach ($destinations as $destination) {
                MediaUrl::deleteIfStored($destination->image);
                $destination->delete();
            }

            return back()->with('success', count($destinations).' destination(s) deleted permanently.');
        }

        return back()->with('error', 'Invalid action selected.');
    }
}
