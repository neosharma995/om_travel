@extends('layouts.admin')

@section('title', 'Destinations')
@section('page_title', 'Destinations')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">All Destinations</h2>
                <div style="font-size: 12.5px; color: var(--admin-muted); margin-top: 2px;">
                    Showcase Himachal Pradesh regions like Shimla, Manali, Spiti, and Kinnaur
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                @php $trashedDestinationsCount = \App\Models\TrashItem::query()->where('item_type', 'destination')->count(); @endphp
                <a href="{{ route('admin.trash.index', ['type' => 'destination']) }}" class="btn btn-outline btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                    Trashed Destinations ({{ $trashedDestinationsCount }})
                </a>
                <a href="{{ route('admin.destinations.create') }}" class="btn btn-primary">+ Add Destination</a>
            </div>
        </div>

        {{-- Standalone Bulk Actions Form --}}
        <form action="{{ route('admin.destinations.bulk') }}" method="POST" id="destinationsBulkForm">
            @csrf
            <div class="bulk-toolbar" id="destinationsBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 12px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 10px 16px; border-radius: 10px; margin-bottom: 16px;">
                <div style="font-weight: 700; color: var(--admin-sidebar); font-size: 13.5px;">
                    <span id="destinationsSelectedCount">0</span> destinations selected
                </div>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <button type="submit" name="action" value="activate" class="btn btn-outline btn-sm">Set Active</button>
                    <button type="submit" name="action" value="deactivate" class="btn btn-outline btn-sm">Set Inactive</button>
                    <button type="submit" name="action" value="popular" class="btn btn-outline btn-sm">Mark Popular</button>
                    <button type="submit" name="action" value="unpopular" class="btn btn-outline btn-sm">Mark Standard</button>
                    <button type="submit" name="action" value="trash" class="btn btn-outline btn-sm" style="color: #92400E; border-color: #FCD34D;">Move to Trash</button>
                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Permanently delete the selected destinations? This cannot be undone.')">Delete Permanently</button>
                </div>
            </div>
        </form>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 38px; text-align: center;">
                            <input type="checkbox" id="selectAllDestinations" aria-label="Select all destinations">
                        </th>
                        <th>Image</th>
                        <th>Destination</th>
                        <th>Listings</th>
                        <th>Popular</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($destinations as $destination)
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="ids[]" value="{{ $destination->id }}" form="destinationsBulkForm" class="destination-row-checkbox" aria-label="Select {{ $destination->name }}">
                            </td>
                            <td><img src="{{ $destination->imageUrl() }}" alt="{{ $destination->name }}" class="admin-thumb"></td>
                            <td>
                                <strong>{{ $destination->name }}</strong>
                                <div style="font-size: 12px; color: var(--admin-muted);">{{ $destination->country ?: $destination->slug }}</div>
                            </td>
                            <td>{{ $destination->listings_label ?: $destination->tours_count.' Tours' }}</td>
                            <td>
                                <span class="badge {{ $destination->is_popular ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $destination->is_popular ? 'Popular' : 'Standard' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $destination->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $destination->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.destinations.show', $destination) }}" class="btn btn-view btn-sm">View</a>
                                    <a href="{{ route('admin.destinations.edit', $destination) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form action="{{ route('admin.destinations.destroy', $destination) }}" method="POST" onsubmit="return confirm('Delete this destination?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align: center; color: var(--admin-muted); padding: 32px;">No destinations found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $destinations->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('selectAllDestinations');
            const checkboxes = Array.from(document.querySelectorAll('.destination-row-checkbox'));
            const bulkBar = document.getElementById('destinationsBulkBar');
            const selectedCount = document.getElementById('destinationsSelectedCount');

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
