@extends('layouts.admin')

@section('title', 'Tour Guides')
@section('page_title', 'Tour Guides')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">All Guides</h2>
                <div style="font-size: 12.5px; color: var(--admin-muted); margin-top: 2px;">
                    Introduce your local mountain experts and certified tour leaders
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                @php $trashedGuidesCount = \App\Models\TrashItem::query()->where('item_type', 'guide')->count(); @endphp
                <a href="{{ route('admin.trash.index', ['type' => 'guide']) }}" class="btn btn-outline btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                    Trashed Guides ({{ $trashedGuidesCount }})
                </a>
                <a href="{{ route('admin.guides.create') }}" class="btn btn-primary">+ Add Guide</a>
            </div>
        </div>

        {{-- Standalone Bulk Actions Form --}}
        <form action="{{ route('admin.guides.bulk') }}" method="POST" id="guidesBulkForm">
            @csrf
            <div class="bulk-toolbar" id="guidesBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 12px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 10px 16px; border-radius: 10px; margin-bottom: 16px;">
                <div style="font-weight: 700; color: var(--admin-sidebar); font-size: 13.5px;">
                    <span id="guidesSelectedCount">0</span> guides selected
                </div>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <button type="submit" name="action" value="activate" class="btn btn-outline btn-sm">Set Active</button>
                    <button type="submit" name="action" value="deactivate" class="btn btn-outline btn-sm">Set Inactive</button>
                    <button type="submit" name="action" value="trash" class="btn btn-outline btn-sm" style="color: #92400E; border-color: #FCD34D;">Move to Trash</button>
                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Permanently delete the selected guides? This cannot be undone.')">Delete Permanently</button>
                </div>
            </div>
        </form>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 38px; text-align: center;">
                            <input type="checkbox" id="selectAllGuides" aria-label="Select all guides">
                        </th>
                        <th>Portrait</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($guides as $guide)
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="ids[]" value="{{ $guide->id }}" form="guidesBulkForm" class="guide-row-checkbox" aria-label="Select {{ $guide->name }}">
                            </td>
                            <td><img src="{{ $guide->photoUrl() }}" alt="{{ $guide->name }}" class="admin-avatar-thumb"></td>
                            <td><strong>{{ $guide->name }}</strong></td>
                            <td>{{ $guide->role }}</td>
                            <td>{{ $guide->sort_order }}</td>
                            <td>
                                <span class="badge {{ $guide->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $guide->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.guides.show', $guide) }}" class="btn btn-view btn-sm">View</a>
                                    <a href="{{ route('admin.guides.edit', $guide) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form action="{{ route('admin.guides.destroy', $guide) }}" method="POST" onsubmit="return confirm('Delete this tour guide?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align: center; color: var(--admin-muted); padding: 32px;">No guides found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $guides->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('selectAllGuides');
            const checkboxes = Array.from(document.querySelectorAll('.guide-row-checkbox'));
            const bulkBar = document.getElementById('guidesBulkBar');
            const selectedCount = document.getElementById('guidesSelectedCount');

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
