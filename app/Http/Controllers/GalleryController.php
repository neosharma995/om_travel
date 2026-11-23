<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(Request $request): View
    {
        $selectedCategory = $request->string('category')->trim()->toString();

        $allGalleryItems = GalleryItem::query()
            ->active()
            ->ordered()
            ->get();

        // Extract distinct countries/regions from locations (e.g. "Baa Atoll, Maldives" -> "Maldives")
        $availableCategories = $allGalleryItems
            ->pluck('location')
            ->map(function (?string $location): ?string {
                if (! $location) {
                    return null;
                }
                $parts = explode(',', $location);

                return trim(end($parts));
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        $defaultCategories = ['Maldives', 'Switzerland', 'Greece', 'Thailand', 'Italy'];
        $categories = ! empty($availableCategories) ? $availableCategories : $defaultCategories;

        $galleryItems = $allGalleryItems->when(
            $selectedCategory !== '' && strtolower($selectedCategory) !== 'all',
            function ($collection) use ($selectedCategory) {
                return $collection->filter(function (GalleryItem $item) use ($selectedCategory) {
                    return str_contains(strtolower($item->location), strtolower($selectedCategory))
                        || str_contains(strtolower($item->title), strtolower($selectedCategory));
                });
            }
        );

        return view('website.gallery.index', [
            'galleryItems' => $galleryItems,
            'totalItemsCount' => $allGalleryItems->count(),
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
        ]);
    }
}
