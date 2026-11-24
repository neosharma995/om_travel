<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TourCategoryRequest;
use App\Models\TourCategory;
use App\Models\TrashItem;
use App\Support\MediaUrl;
use App\Support\SlugGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourCategoryController extends Controller
{
    public function index(): View
    {
        $categories = TourCategory::query()
            ->with('parent')
            ->withCount('tours')
            ->ordered()
            ->paginate(10);

        return view('admin.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'category' => new TourCategory(['is_active' => true, 'sort_order' => 0]),
            'parentCategories' => TourCategory::query()->with('parent')->ordered()->get(),
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
            'parent_id' => ($validated['parent_id'] ?? null) ?: null,
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

    public function show(TourCategory $category): View
    {
        $category->load(['parent', 'children'])
            ->loadCount('tours');

        $tours = $category->tours()
            ->with(['destination', 'tourType'])
            ->ordered()
            ->limit(10)
            ->get();

        return view('admin.categories.show', [
            'category' => $category,
            'tours' => $tours,
        ]);
    }

    public function edit(TourCategory $category): View
    {
        $excludedIds = array_merge([$category->id], $category->descendantIds());

        return view('admin.categories.edit', [
            'category' => $category,
            'parentCategories' => TourCategory::query()
                ->with('parent')
                ->whereNotIn('id', $excludedIds)
                ->ordered()
                ->get(),
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
            'parent_id' => ($validated['parent_id'] ?? null) ?: null,
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

    public function bulkAction(Request $request): RedirectResponse
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.categories.index');
        }

        $action = $request->string('action')->trim()->toString();
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one category.');
        }

        $categories = TourCategory::query()->whereIn('id', $ids)->get();

        if ($action === 'activate') {
            TourCategory::query()->whereIn('id', $ids)->update(['is_active' => true]);

            return back()->with('success', count($categories).' category(ies) activated.');
        }

        if ($action === 'deactivate') {
            TourCategory::query()->whereIn('id', $ids)->update(['is_active' => false]);

            return back()->with('success', count($categories).' category(ies) deactivated.');
        }

        if ($action === 'trash') {
            foreach ($categories as $category) {
                TrashItem::trash($category);
            }

            return back()->with('success', count($categories).' category(ies) moved to trash bin.');
        }

        if ($action === 'delete') {
            foreach ($categories as $category) {
                MediaUrl::deleteIfStored($category->image);
                $category->delete();
            }

            return back()->with('success', count($categories).' category(ies) deleted permanently.');
        }

        return back()->with('error', 'Invalid action selected.');
    }
}
