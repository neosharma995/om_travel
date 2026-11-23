<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TourTypeRequest;
use App\Models\TourType;
use App\Support\SlugGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TourTypeController extends Controller
{
    public function index(): View
    {
        $tourTypes = TourType::query()
            ->withCount('tours')
            ->ordered()
            ->paginate(15);

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
}
