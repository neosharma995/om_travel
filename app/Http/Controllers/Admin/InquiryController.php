<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateInquiryRequest;
use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->trim()->toString();

        $inquiries = Inquiry::query()
            ->when($status !== '', fn (Builder $query): Builder => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.inquiries.index', [
            'inquiries' => $inquiries,
            'currentStatus' => $status,
        ]);
    }

    public function show(Inquiry $inquiry): View
    {
        return view('admin.inquiries.show', [
            'inquiry' => $inquiry,
        ]);
    }

    public function update(UpdateInquiryRequest $request, Inquiry $inquiry): RedirectResponse
    {
        $inquiry->update([
            'status' => $request->validated('status'),
        ]);

        return redirect()
            ->route('admin.inquiries.show', $inquiry)
            ->with('success', 'Inquiry status updated successfully.');
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect()
            ->route('admin.inquiries.index')
            ->with('success', 'Inquiry deleted successfully.');
    }
}
