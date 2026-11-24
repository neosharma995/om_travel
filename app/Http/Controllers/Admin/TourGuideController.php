<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TourGuideRequest;
use App\Models\TourGuide;
use App\Support\MediaUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TourGuideController extends Controller
{
    public function index(): View
    {
        $guides = TourGuide::query()
            ->ordered()
            ->paginate(10);

        return view('admin.guides.index', [
            'guides' => $guides,
        ]);
    }

    public function create(): View
    {
        return view('admin.guides.create', [
            'guide' => new TourGuide([
                'role' => 'Tourist Guide',
                'is_active' => true,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(TourGuideRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $photoPath = MediaUrl::storeUpload(
            $request->file('photo_file'),
            'guides',
            null,
            $validated['photo'] ?? '/images/seed/guide-1.jpg',
        );

        TourGuide::query()->create([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'bio' => $validated['bio'] ?? null,
            'photo' => $photoPath,
            'facebook_url' => $validated['facebook_url'] ?? null,
            'twitter_url' => $validated['twitter_url'] ?? null,
            'instagram_url' => $validated['instagram_url'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.guides.index')
            ->with('success', 'Tour guide created successfully.');
    }

    public function show(TourGuide $guide): View
    {
        return view('admin.guides.show', [
            'guide' => $guide,
        ]);
    }

    public function edit(TourGuide $guide): View
    {
        return view('admin.guides.edit', [
            'guide' => $guide,
        ]);
    }

    public function update(TourGuideRequest $request, TourGuide $guide): RedirectResponse
    {
        $validated = $request->validated();

        $photoPath = MediaUrl::storeUpload(
            $request->file('photo_file'),
            'guides',
            $guide->photo,
            $validated['photo'] ?? null,
        );

        $guide->update([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'bio' => $validated['bio'] ?? null,
            'photo' => $photoPath,
            'facebook_url' => $validated['facebook_url'] ?? null,
            'twitter_url' => $validated['twitter_url'] ?? null,
            'instagram_url' => $validated['instagram_url'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.guides.index')
            ->with('success', 'Tour guide updated successfully.');
    }

    public function destroy(TourGuide $guide): RedirectResponse
    {
        MediaUrl::deleteIfStored($guide->photo);
        $guide->delete();

        return redirect()
            ->route('admin.guides.index')
            ->with('success', 'Tour guide deleted successfully.');
    }
}
