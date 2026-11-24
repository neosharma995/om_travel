<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateInquiryRequest;
use App\Models\Inquiry;
use App\Models\TrashItem;
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
            ->paginate(10)
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

    public function bulkAction(Request $request): RedirectResponse
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.inquiries.index');
        }

        $action = $request->string('action')->trim()->toString();
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one inquiry.');
        }

        $inquiries = Inquiry::query()->whereIn('id', $ids)->get();

        if (in_array($action, ['new', 'replied', 'closed'], true)) {
            Inquiry::query()->whereIn('id', $ids)->update(['status' => $action]);

            return back()->with('success', count($inquiries)." inquiry(s) marked as {$action}.");
        }

        if ($action === 'trash') {
            foreach ($inquiries as $inquiry) {
                TrashItem::trash($inquiry);
            }

            return back()->with('success', count($inquiries).' inquiry(s) moved to trash bin.');
        }

        if ($action === 'delete') {
            foreach ($inquiries as $inquiry) {
                $inquiry->delete();
            }

            return back()->with('success', count($inquiries).' inquiry(s) deleted permanently.');
        }

        return back()->with('error', 'Invalid action selected.');
    }
}
