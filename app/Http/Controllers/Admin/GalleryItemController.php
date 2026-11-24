<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryItemRequest;
use App\Models\GalleryItem;
use App\Models\TrashItem;
use App\Support\MediaUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryItemController extends Controller
{
    public function index(): View
    {
        $items = GalleryItem::query()
            ->ordered()
            ->paginate(10);

        return view('admin.gallery.index', [
            'items' => $items,
        ]);
    }

    public function create(): View
    {
        return view('admin.gallery.create', [
            'item' => new GalleryItem(['is_active' => true, 'sort_order' => 0]),
        ]);
    }

    public function store(GalleryItemRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = MediaUrl::storeUpload(
            $request->file('image_file'),
            'gallery',
            null,
            $validated['image'] ?? '/images/seed/cat-beach.jpg',
        );

        GalleryItem::query()->create([
            'title' => $validated['title'],
            'location' => $validated['location'] ?? null,
            'image' => $imagePath ?? '/images/seed/cat-beach.jpg',
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Gallery item added successfully.');
    }

    public function show(GalleryItem $gallery): View
    {
        return view('admin.gallery.show', [
            'item' => $gallery,
        ]);
    }

    public function edit(GalleryItem $gallery): View
    {
        return view('admin.gallery.edit', [
            'item' => $gallery,
        ]);
    }

    public function update(GalleryItemRequest $request, GalleryItem $gallery): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = MediaUrl::storeUpload(
            $request->file('image_file'),
            'gallery',
            $gallery->image,
            $validated['image'] ?? null,
        );

        $gallery->update([
            'title' => $validated['title'],
            'location' => $validated['location'] ?? null,
            'image' => $imagePath ?? $gallery->image,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Gallery item updated successfully.');
    }

    public function destroy(GalleryItem $gallery): RedirectResponse
    {
        MediaUrl::deleteIfStored($gallery->image);
        $gallery->delete();

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Gallery item deleted successfully.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.gallery.index');
        }

        $action = $request->string('action')->trim()->toString();
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one gallery item.');
        }

        $items = GalleryItem::query()->whereIn('id', $ids)->get();

        if ($action === 'activate') {
            GalleryItem::query()->whereIn('id', $ids)->update(['is_active' => true]);

            return back()->with('success', count($items).' gallery item(s) activated.');
        }

        if ($action === 'deactivate') {
            GalleryItem::query()->whereIn('id', $ids)->update(['is_active' => false]);

            return back()->with('success', count($items).' gallery item(s) deactivated.');
        }

        if ($action === 'trash') {
            foreach ($items as $item) {
                TrashItem::trash($item);
            }

            return back()->with('success', count($items).' gallery item(s) moved to trash bin.');
        }

        if ($action === 'delete') {
            foreach ($items as $item) {
                MediaUrl::deleteIfStored($item->image);
                $item->delete();
            }

            return back()->with('success', count($items).' gallery item(s) deleted permanently.');
        }

        return back()->with('error', 'Invalid action selected.');
    }
}
