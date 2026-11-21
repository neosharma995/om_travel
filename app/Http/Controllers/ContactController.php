<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInquiryRequest;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('website.contact');
    }

    public function store(StoreInquiryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Inquiry::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        return redirect()
            ->route('contact.create')
            ->with('contact_success', 'Thank you for reaching out! Our travel specialist will reply within 24 hours.');
    }
}
