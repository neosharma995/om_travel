<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TourCategoryRequest;
use App\Models\TourCategory;
use App\Support\MediaUrl;
use App\Support\SlugGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TourCategoryController extends Controller
{
    public function index(): View
    {
        $categories = TourCategory::query()
            ->withCount('tours')
            ->ordered()
            ->paginate(15);

        return view('admin.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'category' => new TourCategory(['is_active' => true, 'sort_order' => 0]),
        ]);
    }

    public function store(TourCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = MediaUrl::storeUpload(
            $request->file('image_file'),
            'categories',
            null,
            $validated['image'] ?? '/images/seed/cat-cruise.jpg',
        );

        TourCategory::query()->create([
            'name' => $validated['name'],
            'slug' => SlugGenerator::unique('tour_categories', $validated['slug'] ?? $validated['name']),
            'image' => $imagePath,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Tour category created successfully.');
    }

    public function edit(TourCategory $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(TourCategoryRequest $request, TourCategory $category): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = MediaUrl::storeUpload(
            $request->file('image_file'),
            'categories',
            $category->image,
            $validated['image'] ?? null,
        );

        $category->update([
            'name' => $validated['name'],
            'slug' => SlugGenerator::unique('tour_categories', $validated['slug'] ?? $validated['name'], $category->id),
            'image' => $imagePath,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Tour category updated successfully.');
    }

    public function destroy(TourCategory $category): RedirectResponse
    {
        MediaUrl::deleteIfStored($category->image);
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Tour category deleted successfully.');
    }
}
