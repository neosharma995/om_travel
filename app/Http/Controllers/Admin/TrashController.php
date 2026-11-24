<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrashItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrashController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->string('type')->trim()->toString();
        $search = $request->string('search')->trim()->toString();

        $query = TrashItem::query()->latest();

        if ($type !== '' && $type !== 'all') {
            $query->where('item_type', $type);
        }

        if ($search !== '') {
            $query->where(function ($sub) use ($search): void {
                $sub->where('title', 'like', '%'.$search.'%')
                    ->orWhere('subtitle', 'like', '%'.$search.'%');
            });
        }

        $items = $query->paginate(12)->withQueryString();

        $countsByType = TrashItem::query()
            ->selectRaw('item_type, count(*) as count')
            ->groupBy('item_type')
            ->pluck('count', 'item_type')
            ->all();

        return view('admin.trash.index', [
            'items' => $items,
            'currentType' => $type !== '' ? $type : 'all',
            'search' => $search,
            'totalCount' => TrashItem::query()->count(),
            'countsByType' => $countsByType,
        ]);
    }

    public function restore(TrashItem $trashItem): RedirectResponse
    {
        $type = $trashItem->typeLabel();
        $trashItem->restoreItem();

        return back()->with('success', "{$type} restored successfully.");
    }

    public function destroy(TrashItem $trashItem): RedirectResponse
    {
        $type = $trashItem->typeLabel();
        $trashItem->purge();

        return back()->with('success', "{$type} permanently purged from trash.");
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.trash.index');
        }

        $action = $request->string('action')->trim()->toString();
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one item.');
        }

        $items = TrashItem::query()->whereIn('id', $ids)->get();

        if ($action === 'restore') {
            foreach ($items as $item) {
                $item->restoreItem();
            }

            return back()->with('success', count($items).' item(s) restored successfully.');
        }

        if ($action === 'purge' || $action === 'delete') {
            foreach ($items as $item) {
                $item->purge();
            }

            return back()->with('success', count($items).' item(s) permanently deleted.');
        }

        return back()->with('error', 'Invalid action selected.');
    }

    public function emptyTrash(): RedirectResponse
    {
        $items = TrashItem::query()->get();
        foreach ($items as $item) {
            $item->purge();
        }

        return redirect()
            ->route('admin.trash.index')
            ->with('success', 'Trash bin has been emptied completely.');
    }
}
