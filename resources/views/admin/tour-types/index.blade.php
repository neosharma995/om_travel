@extends('layouts.admin')

@section('title', 'Tour Types')
@section('page_title', 'Tour Types')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">All Tour Types</h2>
                <div style="font-size: 12.5px; color: var(--admin-muted); margin-top: 2px;">
                    Configure expedition formats like Guided, Self-drive, and Group
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                @php $trashedTourTypesCount = \App\Models\TrashItem::query()->where('item_type', 'tour_type')->count(); @endphp
                <a href="{{ route('admin.trash.index', ['type' => 'tour_type']) }}" class="btn btn-outline btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                    Trashed Types ({{ $trashedTourTypesCount }})
                </a>
                <a href="{{ route('admin.tour-types.create') }}" class="btn btn-primary">+ Add Tour Type</a>
            </div>
        </div>

        {{-- Standalone Bulk Actions Form --}}
        <form action="{{ route('admin.tour-types.bulk') }}" method="POST" id="tourTypesBulkForm">
            @csrf
            <div class="bulk-toolbar" id="tourTypesBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 12px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 10px 16px; border-radius: 10px; margin-bottom: 16px;">
                <div style="font-weight: 700; color: var(--admin-sidebar); font-size: 13.5px;">
                    <span id="tourTypesSelectedCount">0</span> tour types selected
                </div>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <button type="submit" name="action" value="activate" class="btn btn-outline btn-sm">Set Active</button>
                    <button type="submit" name="action" value="deactivate" class="btn btn-outline btn-sm">Set Inactive</button>
                    <button type="submit" name="action" value="trash" class="btn btn-outline btn-sm" style="color: #92400E; border-color: #FCD34D;">Move to Trash</button>
                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Permanently delete the selected tour types? This cannot be undone.')">Delete Permanently</button>
                </div>
            </div>
        </form>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 38px; text-align: center;">
                            <input type="checkbox" id="selectAllTourTypes" aria-label="Select all tour types">
                        </th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Tours</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tourTypes as $tourType)
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="ids[]" value="{{ $tourType->id }}" form="tourTypesBulkForm" class="tourtype-row-checkbox" aria-label="Select {{ $tourType->name }}">
                            </td>
                            <td>
                                <strong>{{ $tourType->name }}</strong>
                                <div style="font-size: 12px; color: var(--admin-muted);">{{ $tourType->slug }}</div>
                            </td>
                            <td>{{ $tourType->description ? \Illuminate\Support\Str::limit($tourType->description, 80) : '—' }}</td>
                            <td>{{ $tourType->tours_count }}</td>
                            <td>{{ $tourType->sort_order }}</td>
                            <td>
                                <span class="badge {{ $tourType->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $tourType->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.tour-types.show', $tourType) }}" class="btn btn-view btn-sm">View</a>
                                    <a href="{{ route('admin.tour-types.edit', $tourType) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form action="{{ route('admin.tour-types.destroy', $tourType) }}" method="POST" onsubmit="return confirm('Delete this tour type?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align: center; color: var(--admin-muted); padding: 32px;">No tour types found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $tourTypes->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('selectAllTourTypes');
            const checkboxes = Array.from(document.querySelectorAll('.tourtype-row-checkbox'));
            const bulkBar = document.getElementById('tourTypesBulkBar');
            const selectedCount = document.getElementById('tourTypesSelectedCount');

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
