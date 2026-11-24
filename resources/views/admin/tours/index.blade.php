@extends('layouts.admin')

@section('title', 'Tour Packages')
@section('page_title', 'Tour Packages')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">All Tours</h2>
                <div style="font-size: 12.5px; color: var(--admin-muted); margin-top: 2px;">
                    Curate and manage your signature travel expeditions
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                @php $trashedToursCount = \App\Models\TrashItem::query()->where('item_type', 'tour')->count(); @endphp
                <a href="{{ route('admin.trash.index', ['type' => 'tour']) }}" class="btn btn-outline btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                    Trashed Tours ({{ $trashedToursCount }})
                </a>
                <a href="{{ route('admin.tours.create') }}" class="btn btn-primary">+ Add Tour Package</a>
            </div>
        </div>

        {{-- Standalone Bulk Actions Form --}}
        <form action="{{ route('admin.tours.bulk') }}" method="POST" id="toursBulkForm">
            @csrf
            <div class="bulk-toolbar" id="toursBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 12px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 10px 16px; border-radius: 10px; margin-bottom: 16px;">
                <div style="font-weight: 700; color: var(--admin-sidebar); font-size: 13.5px;">
                    <span id="toursSelectedCount">0</span> tours selected
                </div>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <button type="submit" name="action" value="activate" class="btn btn-outline btn-sm">Set Active</button>
                    <button type="submit" name="action" value="deactivate" class="btn btn-outline btn-sm">Set Inactive</button>
                    <button type="submit" name="action" value="feature" class="btn btn-outline btn-sm">Feature</button>
                    <button type="submit" name="action" value="unfeature" class="btn btn-outline btn-sm">Unfeature</button>
                    <button type="submit" name="action" value="trash" class="btn btn-outline btn-sm" style="color: #92400E; border-color: #FCD34D;">Move to Trash</button>
                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Permanently delete the selected tours? This cannot be undone.')">Delete Permanently</button>
                </div>
            </div>
        </form>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 38px; text-align: center;">
                            <input type="checkbox" id="selectAllTours" aria-label="Select all tours">
                        </th>
                        <th>Photo</th>
                        <th>Tour Title</th>
                        <th>Destination</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tours as $tour)
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="ids[]" value="{{ $tour->id }}" form="toursBulkForm" class="tour-row-checkbox" aria-label="Select {{ $tour->title }}">
                            </td>
                            <td><img src="{{ $tour->imageUrl() }}" alt="{{ $tour->title }}" class="admin-thumb"></td>
                            <td>
                                <strong>{{ $tour->title }}</strong>
                                <div style="font-size: 12px; color: var(--admin-muted);">
                                    {{ $tour->category?->name ?? 'Uncategorized' }}
                                    @if ($tour->tourType)
                                        &bull; {{ $tour->tourType->name }}
                                    @endif
                                    @if ($tour->activity)
                                        &bull; {{ $tour->activity->name }}
                                    @endif
                                </div>
                            </td>
                            <td>{{ $tour->destination?->name ?? '—' }}</td>
                            <td>₹{{ number_format((float) $tour->price, 2) }}</td>
                            <td>{{ $tour->displayDuration() }}</td>
                            <td>
                                <span class="badge {{ $tour->is_featured ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $tour->is_featured ? 'Featured' : 'Standard' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $tour->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $tour->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.tours.show', $tour) }}" class="btn btn-view btn-sm">View</a>
                                    <a href="{{ route('admin.tours.edit', $tour) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form action="{{ route('admin.tours.destroy', $tour) }}" method="POST" onsubmit="return confirm('Delete this tour package?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" style="text-align: center; color: var(--admin-muted); padding: 32px;">No tour packages found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $tours->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('selectAllTours');
            const checkboxes = Array.from(document.querySelectorAll('.tour-row-checkbox'));
            const bulkBar = document.getElementById('toursBulkBar');
            const selectedCount = document.getElementById('toursSelectedCount');

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
