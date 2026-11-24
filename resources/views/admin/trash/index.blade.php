@extends('layouts.admin')

@section('title', 'Trash Bin')
@section('page_title', 'Trash Bin & Recovery')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Deleted Items Archive</h2>
                <p style="font-size: 13px; color: var(--admin-muted); margin: 4px 0 0;">
                    Items moved to trash can be safely restored to their original location or permanently purged.
                </p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                @if ($items->total() > 0)
                    <form action="{{ route('admin.trash.empty') }}" method="POST" onsubmit="return confirm('Empty the entire Trash Bin? This will permanently delete all trashed items and cannot be undone.')">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            Empty Trash Bin
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Filter & Search Ribbon --}}
        <div class="admin-toolbar" style="display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-bottom: 20px;">
            <div class="admin-tab-group" style="display: flex; gap: 8px; flex-wrap: wrap;">
                <a href="{{ route('admin.trash.index', array_merge(request()->except('type', 'page'), ['type' => 'all'])) }}" 
                   class="btn btn-sm {{ $currentType === 'all' ? 'btn-primary' : 'btn-outline' }}">
                    All Items ({{ $totalCount }})
                </a>
                @foreach ([
                    'tour' => 'Tours',
                    'destination' => 'Destinations',
                    'category' => 'Categories',
                    'tour_type' => 'Tour Types',
                    'activity' => 'Activities',
                    'blog_post' => 'Articles',
                    'guide' => 'Guides',
                    'testimonial' => 'Testimonials',
                    'gallery' => 'Gallery',
                    'booking' => 'Bookings',
                    'inquiry' => 'Inquiries',
                ] as $typeKey => $typeName)
                    @php $typeNum = $countsByType[$typeKey] ?? 0; @endphp
                    @if ($typeNum > 0 || $currentType === $typeKey)
                        <a href="{{ route('admin.trash.index', array_merge(request()->except('type', 'page'), ['type' => $typeKey])) }}" 
                           class="btn btn-sm {{ $currentType === $typeKey ? 'btn-primary' : 'btn-outline' }}">
                            {{ $typeName }} ({{ $typeNum }})
                        </a>
                    @endif
                @endforeach
            </div>

            <form action="{{ route('admin.trash.index') }}" method="GET" style="display: flex; gap: 8px;">
                @if ($currentType !== 'all')
                    <input type="hidden" name="type" value="{{ $currentType }}">
                @endif
                <input type="text" name="search" value="{{ $search }}" class="form-control" style="width: 220px; height: 36px; font-size: 13px;" placeholder="Search trashed items...">
                <button type="submit" class="btn btn-outline btn-sm">Search</button>
                @if ($search !== '' || $currentType !== 'all')
                    <a href="{{ route('admin.trash.index') }}" class="btn btn-outline btn-sm" title="Clear filters">Clear</a>
                @endif
            </form>
        </div>

        {{-- Bulk Action Form --}}
        <form action="{{ route('admin.trash.bulk') }}" method="POST" id="trashBulkForm">
            @csrf
            <div class="bulk-toolbar" id="trashBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 12px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 10px 16px; border-radius: 10px; margin-bottom: 16px;">
                <div style="font-weight: 700; color: var(--admin-sidebar); font-size: 13.5px;">
                    <span id="trashSelectedCount">0</span> items selected
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="submit" name="action" value="restore" class="btn btn-primary btn-sm">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                        Restore Selected
                    </button>
                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Permanently purge selected items? This cannot be undone.')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                        Delete Permanently
                    </button>
                </div>
            </div>

            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">
                                <input type="checkbox" id="selectAllTrash" title="Select All">
                            </th>
                            <th style="width: 140px;">Type</th>
                            <th>Item Title / Info</th>
                            <th>Details</th>
                            <th>Trashed Date</th>
                            <th style="width: 180px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="trash-checkbox">
                                </td>
                                <td>
                                    <span class="badge badge-neutral" style="text-transform: uppercase; font-size: 11px;">
                                        {{ $item->typeLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $item->title }}</strong>
                                    @if ($item->subtitle)
                                        <div style="font-size: 12px; color: var(--admin-muted); margin-top: 2px;">{{ $item->subtitle }}</div>
                                    @endif
                                </td>
                                <td style="font-size: 12.5px; color: var(--admin-muted);">
                                    Original ID: #{{ $item->item_id ?? 'N/A' }}
                                </td>
                                <td style="font-size: 12.5px; color: var(--admin-muted);">
                                    {{ $item->created_at->format('M d, Y • h:i A') }}
                                </td>
                                <td style="text-align: right;">
                                    <div class="table-actions" style="justify-content: flex-end;">
                                        <button type="submit" formaction="{{ route('admin.trash.restore', $item) }}" class="btn btn-view btn-sm" title="Restore this item">
                                            Restore
                                        </button>
                                        <button type="submit" formaction="{{ route('admin.trash.destroy', $item) }}" class="btn btn-danger btn-sm" onclick="return confirm('Permanently purge this item?')" title="Permanently delete">
                                            Purge
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 36px 20px; color: var(--admin-muted);">
                                    <div style="font-size: 32px; margin-bottom: 8px;">🗑️</div>
                                    <div style="font-weight: 700; font-size: 15px; color: var(--admin-sidebar);">Trash bin is empty</div>
                                    <div style="font-size: 13px; margin-top: 4px;">Deleted items will safely appear here for restoration or permanent purge.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <div style="margin-top: 20px;">
            {{ $items->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAllTrash');
            const checkboxes = Array.from(document.querySelectorAll('.trash-checkbox'));
            const bulkBar = document.getElementById('trashBulkBar');
            const counter = document.getElementById('trashSelectedCount');

            function syncBulkBar() {
                const checked = checkboxes.filter(cb => cb.checked);
                if (checked.length > 0) {
                    bulkBar.style.display = 'flex';
                    counter.textContent = checked.length;
                } else {
                    bulkBar.style.display = 'none';
                }
                if (selectAll) {
                    selectAll.checked = checkboxes.length > 0 && checked.length === checkboxes.length;
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(cb => cb.checked = selectAll.checked);
                    syncBulkBar();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', syncBulkBar);
            });
        });
    </script>
@endsection
