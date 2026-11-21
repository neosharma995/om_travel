<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimonialRequest;
use App\Models\Testimonial;
use App\Support\MediaUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(): View
    {
        $testimonials = Testimonial::query()
            ->ordered()
            ->paginate(15);

        return view('admin.testimonials.index', [
            'testimonials' => $testimonials,
        ]);
    }

    public function create(): View
    {
        return view('admin.testimonials.create', [
            'testimonial' => new Testimonial([
                'client_role' => 'Traveller',
                'rating' => 5,
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(TestimonialRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $avatarPath = MediaUrl::storeUpload(
            $request->file('avatar_file'),
            'testimonials',
            null,
            $validated['avatar'] ?? '/images/seed/guide-1.jpg',
        );

        Testimonial::query()->create([
            'client_name' => $validated['client_name'],
            'client_role' => $validated['client_role'],
            'avatar' => $avatarPath,
            'rating' => (int) $validated['rating'],
            'quote' => $validated['quote'],
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.testimonials.index')
            ->with('success', 'Testimonial created successfully.');
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('admin.testimonials.edit', [
            'testimonial' => $testimonial,
        ]);
    }

    public function update(TestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        $validated = $request->validated();

        $avatarPath = MediaUrl::storeUpload(
            $request->file('avatar_file'),
            'testimonials',
            $testimonial->avatar,
            $validated['avatar'] ?? null,
        );

        $testimonial->update([
            'client_name' => $validated['client_name'],
            'client_role' => $validated['client_role'],
            'avatar' => $avatarPath,
            'rating' => (int) $validated['rating'],
            'quote' => $validated['quote'],
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.testimonials.index')
            ->with('success', 'Testimonial updated successfully.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        MediaUrl::deleteIfStored($testimonial->avatar);
        $testimonial->delete();

        return redirect()
            ->route('admin.testimonials.index')
            ->with('success', 'Testimonial deleted successfully.');
    }
}
