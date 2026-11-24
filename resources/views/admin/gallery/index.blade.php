@extends('layouts.admin')

@section('title', 'Recent Gallery')
@section('page_title', 'Recent Gallery')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Gallery Photos</h2>
                <div style="font-size: 12.5px; color: var(--admin-muted); margin-top: 2px;">
                    Curate stunning visual impressions of Himachal landscape & journeys
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                @php $trashedGalleryCount = \App\Models\TrashItem::query()->where('item_type', 'gallery')->count(); @endphp
                <a href="{{ route('admin.trash.index', ['type' => 'gallery']) }}" class="btn btn-outline btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                    Trashed Gallery ({{ $trashedGalleryCount }})
                </a>
                <a href="{{ route('admin.gallery.create') }}" class="btn btn-primary">+ Add Gallery Photo</a>
            </div>
        </div>

        {{-- Standalone Bulk Actions Form --}}
        <form action="{{ route('admin.gallery.bulk') }}" method="POST" id="galleryBulkForm">
            @csrf
            <div class="bulk-toolbar" id="galleryBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 12px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 10px 16px; border-radius: 10px; margin-bottom: 16px;">
                <div style="font-weight: 700; color: var(--admin-sidebar); font-size: 13.5px;">
                    <span id="gallerySelectedCount">0</span> gallery photos selected
                </div>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <button type="submit" name="action" value="activate" class="btn btn-outline btn-sm">Set Active</button>
                    <button type="submit" name="action" value="deactivate" class="btn btn-outline btn-sm">Set Inactive</button>
                    <button type="submit" name="action" value="trash" class="btn btn-outline btn-sm" style="color: #92400E; border-color: #FCD34D;">Move to Trash</button>
                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Permanently delete the selected photos? This cannot be undone.')">Delete Permanently</button>
                </div>
            </div>
        </form>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 38px; text-align: center;">
                            <input type="checkbox" id="selectAllGallery" aria-label="Select all gallery items">
                        </th>
                        <th>Photo</th>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="ids[]" value="{{ $item->id }}" form="galleryBulkForm" class="gallery-row-checkbox" aria-label="Select {{ $item->title }}">
                            </td>
                            <td><img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" class="admin-thumb"></td>
                            <td><strong>{{ $item->title }}</strong></td>
                            <td>{{ $item->location ?: '—' }}</td>
                            <td>{{ $item->sort_order }}</td>
                            <td>
                                <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $item->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.gallery.show', $item) }}" class="btn btn-view btn-sm">View</a>
                                    <a href="{{ route('admin.gallery.edit', $item) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form action="{{ route('admin.gallery.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this gallery item?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align: center; color: var(--admin-muted); padding: 32px;">No gallery items found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $items->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('selectAllGallery');
            const checkboxes = Array.from(document.querySelectorAll('.gallery-row-checkbox'));
            const bulkBar = document.getElementById('galleryBulkBar');
            const selectedCount = document.getElementById('gallerySelectedCount');

            if (!selectAll || !bulkBar || !selectedCount) return;

            const updateBulkBar = () => {
                const checked = checkboxes.filter(cb => cb.checked);
                const count = checked.length;
                selectedCount.textContent = count;
                bulkBar.style.display = count > 0 ? 'flex' : 'none';
                selectAll.checked = count > 0 && count === checkboxes.length;
                selectAll.indeterminate = count > 0 && count < checkboxes.length;
            };

            selectAll.addEventListener('change', () => {
                checkboxes.forEach(cb => { cb.checked = selectAll.checked; });
                updateBulkBar();
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkBar);
            });
        });
    </script>
@endsection
