<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TourTypeRequest;
use App\Models\TourType;
use App\Models\TrashItem;
use App\Support\SlugGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourTypeController extends Controller
{
    public function index(): View
    {
        $tourTypes = TourType::query()
            ->withCount('tours')
            ->ordered()
            ->paginate(10);

        return view('admin.tour-types.index', [
            'tourTypes' => $tourTypes,
        ]);
    }

    public function create(): View
    {
        return view('admin.tour-types.create', [
            'tourType' => new TourType(['is_active' => true, 'sort_order' => 0]),
        ]);
    }

    public function store(TourTypeRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        TourType::query()->create([
            'name' => $validated['name'],
            'slug' => SlugGenerator::unique('tour_types', $validated['slug'] ?? $validated['name']),
            'description' => $validated['description'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.tour-types.index')
            ->with('success', 'Tour type created successfully.');
    }

    public function show(TourType $tourType): View
    {
        $tourType->loadCount('tours');

        $tours = $tourType->tours()
            ->with(['destination', 'category'])
            ->ordered()
            ->limit(10)
            ->get();

        return view('admin.tour-types.show', [
            'tourType' => $tourType,
            'tours' => $tours,
        ]);
    }

    public function edit(TourType $tourType): View
    {
        return view('admin.tour-types.edit', [
            'tourType' => $tourType,
        ]);
    }

    public function update(TourTypeRequest $request, TourType $tourType): RedirectResponse
    {
        $validated = $request->validated();

        $tourType->update([
            'name' => $validated['name'],
            'slug' => SlugGenerator::unique('tour_types', $validated['slug'] ?? $validated['name'], $tourType->id),
            'description' => $validated['description'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.tour-types.index')
            ->with('success', 'Tour type updated successfully.');
    }

    public function destroy(TourType $tourType): RedirectResponse
    {
        $tourType->delete();

        return redirect()
            ->route('admin.tour-types.index')
            ->with('success', 'Tour type deleted successfully.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.tour-types.index');
        }

        $action = $request->string('action')->trim()->toString();
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one tour type.');
        }

        $tourTypes = TourType::query()->whereIn('id', $ids)->get();

        if ($action === 'activate') {
            TourType::query()->whereIn('id', $ids)->update(['is_active' => true]);

            return back()->with('success', count($tourTypes).' tour type(s) activated.');
        }

        if ($action === 'deactivate') {
            TourType::query()->whereIn('id', $ids)->update(['is_active' => false]);

            return back()->with('success', count($tourTypes).' tour type(s) deactivated.');
        }

        if ($action === 'trash') {
            foreach ($tourTypes as $tourType) {
                TrashItem::trash($tourType);
            }

            return back()->with('success', count($tourTypes).' tour type(s) moved to trash bin.');
        }

        if ($action === 'delete') {
            foreach ($tourTypes as $tourType) {
                $tourType->delete();
            }

            return back()->with('success', count($tourTypes).' tour type(s) deleted permanently.');
        }

        return back()->with('error', 'Invalid action selected.');
    }
}
