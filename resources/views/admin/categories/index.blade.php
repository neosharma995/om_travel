@extends('layouts.admin')

@section('title', 'Tour Categories')
@section('page_title', 'Tour Categories')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">All Categories</h2>
                <div style="font-size: 12.5px; color: var(--admin-muted); margin-top: 2px;">
                    Organize tours into signature themed collections
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                @php $trashedCategoriesCount = \App\Models\TrashItem::query()->where('item_type', 'category')->count(); @endphp
                <a href="{{ route('admin.trash.index', ['type' => 'category']) }}" class="btn btn-outline btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                    Trashed Categories ({{ $trashedCategoriesCount }})
                </a>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">+ Add Category</a>
            </div>
        </div>

        {{-- Standalone Bulk Actions Form --}}
        <form action="{{ route('admin.categories.bulk') }}" method="POST" id="categoriesBulkForm">
            @csrf
            <div class="bulk-toolbar" id="categoriesBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 12px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 10px 16px; border-radius: 10px; margin-bottom: 16px;">
                <div style="font-weight: 700; color: var(--admin-sidebar); font-size: 13.5px;">
                    <span id="categoriesSelectedCount">0</span> categories selected
                </div>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <button type="submit" name="action" value="activate" class="btn btn-outline btn-sm">Set Active</button>
                    <button type="submit" name="action" value="deactivate" class="btn btn-outline btn-sm">Set Inactive</button>
                    <button type="submit" name="action" value="trash" class="btn btn-outline btn-sm" style="color: #92400E; border-color: #FCD34D;">Move to Trash</button>
                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Permanently delete the selected categories? This cannot be undone.')">Delete Permanently</button>
                </div>
            </div>
        </form>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 38px; text-align: center;">
                            <input type="checkbox" id="selectAllCategories" aria-label="Select all categories">
                        </th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Parent Category</th>
                        <th>Tours</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="ids[]" value="{{ $category->id }}" form="categoriesBulkForm" class="category-row-checkbox" aria-label="Select {{ $category->name }}">
                            </td>
                            <td><img src="{{ $category->imageUrl() }}" alt="{{ $category->name }}" class="admin-thumb"></td>
                            <td>
                                <strong>{{ $category->name }}</strong>
                                <div style="font-size: 12px; color: var(--admin-muted);">{{ $category->slug }}</div>
                            </td>
                            <td>
                                @if ($category->parent)
                                    <span class="badge badge-neutral">{{ $category->parent->name }}</span>
                                @else
                                    <span style="color: var(--admin-muted);">Top Level</span>
                                @endif
                            </td>
                            <td>{{ $category->tours_count }}</td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-view btn-sm">View</a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" style="text-align: center; color: var(--admin-muted); padding: 32px;">No categories found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $categories->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('selectAllCategories');
            const checkboxes = Array.from(document.querySelectorAll('.category-row-checkbox'));
            const bulkBar = document.getElementById('categoriesBulkBar');
            const selectedCount = document.getElementById('categoriesSelectedCount');

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
