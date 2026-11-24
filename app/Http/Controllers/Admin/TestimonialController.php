<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimonialRequest;
use App\Models\Testimonial;
use App\Models\TrashItem;
use App\Support\MediaUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(): View
    {
        $testimonials = Testimonial::query()
            ->ordered()
            ->paginate(10);

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

    public function show(Testimonial $testimonial): View
    {
        return view('admin.testimonials.show', [
            'testimonial' => $testimonial,
        ]);
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

    public function bulkAction(Request $request): RedirectResponse
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.testimonials.index');
        }

        $action = $request->string('action')->trim()->toString();
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one testimonial.');
        }

        $testimonials = Testimonial::query()->whereIn('id', $ids)->get();

        if ($action === 'activate') {
            Testimonial::query()->whereIn('id', $ids)->update(['is_active' => true]);

            return back()->with('success', count($testimonials).' testimonial(s) activated.');
        }

        if ($action === 'deactivate') {
            Testimonial::query()->whereIn('id', $ids)->update(['is_active' => false]);

            return back()->with('success', count($testimonials).' testimonial(s) deactivated.');
        }

        if ($action === 'feature') {
            Testimonial::query()->whereIn('id', $ids)->update(['is_featured' => true]);

            return back()->with('success', count($testimonials).' testimonial(s) marked featured.');
        }

        if ($action === 'unfeature') {
            Testimonial::query()->whereIn('id', $ids)->update(['is_featured' => false]);

            return back()->with('success', count($testimonials).' testimonial(s) unfeatured.');
        }

        if ($action === 'trash') {
            foreach ($testimonials as $testimonial) {
                TrashItem::trash($testimonial);
            }

            return back()->with('success', count($testimonials).' testimonial(s) moved to trash bin.');
        }

        if ($action === 'delete') {
            foreach ($testimonials as $testimonial) {
                MediaUrl::deleteIfStored($testimonial->avatar);
                $testimonial->delete();
            }

            return back()->with('success', count($testimonials).' testimonial(s) deleted permanently.');
        }

        return back()->with('error', 'Invalid action selected.');
    }
}
