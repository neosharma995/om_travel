@extends('layouts.admin')

@section('title', 'Activities')
@section('page_title', 'Activities')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">All Activities</h2>
                <div style="font-size: 12.5px; color: var(--admin-muted); margin-top: 2px;">
                    Experiences like Trekking, Paragliding, Rafting, and Camping
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                @php $trashedActivitiesCount = \App\Models\TrashItem::query()->where('item_type', 'activity')->count(); @endphp
                <a href="{{ route('admin.trash.index', ['type' => 'activity']) }}" class="btn btn-outline btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                    Trashed Activities ({{ $trashedActivitiesCount }})
                </a>
                <a href="{{ route('admin.activities.create') }}" class="btn btn-primary">+ Add Activity</a>
            </div>
        </div>

        {{-- Standalone Bulk Actions Form --}}
        <form action="{{ route('admin.activities.bulk') }}" method="POST" id="activitiesBulkForm">
            @csrf
            <div class="bulk-toolbar" id="activitiesBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 12px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 10px 16px; border-radius: 10px; margin-bottom: 16px;">
                <div style="font-weight: 700; color: var(--admin-sidebar); font-size: 13.5px;">
                    <span id="activitiesSelectedCount">0</span> activities selected
                </div>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <button type="submit" name="action" value="activate" class="btn btn-outline btn-sm">Set Active</button>
                    <button type="submit" name="action" value="deactivate" class="btn btn-outline btn-sm">Set Inactive</button>
                    <button type="submit" name="action" value="trash" class="btn btn-outline btn-sm" style="color: #92400E; border-color: #FCD34D;">Move to Trash</button>
                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Permanently delete the selected activities? This cannot be undone.')">Delete Permanently</button>
                </div>
            </div>
        </form>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 38px; text-align: center;">
                            <input type="checkbox" id="selectAllActivities" aria-label="Select all activities">
                        </th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Tours</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $activity)
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="ids[]" value="{{ $activity->id }}" form="activitiesBulkForm" class="activity-row-checkbox" aria-label="Select {{ $activity->name }}">
                            </td>
                            <td><img src="{{ $activity->imageUrl() }}" alt="{{ $activity->name }}" class="admin-thumb"></td>
                            <td>
                                <strong>{{ $activity->name }}</strong>
                                <div style="font-size: 12px; color: var(--admin-muted);">{{ $activity->slug }}</div>
                            </td>
                            <td>{{ $activity->description ? \Illuminate\Support\Str::limit($activity->description, 80) : '—' }}</td>
                            <td>{{ $activity->tours_count }}</td>
                            <td>{{ $activity->sort_order }}</td>
                            <td>
                                <span class="badge {{ $activity->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $activity->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.activities.show', $activity) }}" class="btn btn-view btn-sm">View</a>
                                    <a href="{{ route('admin.activities.edit', $activity) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form action="{{ route('admin.activities.destroy', $activity) }}" method="POST" onsubmit="return confirm('Delete this activity?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" style="text-align: center; color: var(--admin-muted); padding: 32px;">No activities found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $activities->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('selectAllActivities');
            const checkboxes = Array.from(document.querySelectorAll('.activity-row-checkbox'));
            const bulkBar = document.getElementById('activitiesBulkBar');
            const selectedCount = document.getElementById('activitiesSelectedCount');

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
