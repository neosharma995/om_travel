@extends('layouts.admin')

@section('title', 'Testimonials')
@section('page_title', 'Client Testimonials')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">All Testimonials</h2>
                <div style="font-size: 12.5px; color: var(--admin-muted); margin-top: 2px;">
                    Reviews and stories shared by verified travelers
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                @php $trashedTestimonialsCount = \App\Models\TrashItem::query()->where('item_type', 'testimonial')->count(); @endphp
                <a href="{{ route('admin.trash.index', ['type' => 'testimonial']) }}" class="btn btn-outline btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                    Trashed Testimonials ({{ $trashedTestimonialsCount }})
                </a>
                <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">+ Add Testimonial</a>
            </div>
        </div>

        {{-- Standalone Bulk Actions Form --}}
        <form action="{{ route('admin.testimonials.bulk') }}" method="POST" id="testimonialsBulkForm">
            @csrf
            <div class="bulk-toolbar" id="testimonialsBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 12px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 10px 16px; border-radius: 10px; margin-bottom: 16px;">
                <div style="font-weight: 700; color: var(--admin-sidebar); font-size: 13.5px;">
                    <span id="testimonialsSelectedCount">0</span> testimonials selected
                </div>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <button type="submit" name="action" value="activate" class="btn btn-outline btn-sm">Set Active</button>
                    <button type="submit" name="action" value="deactivate" class="btn btn-outline btn-sm">Set Inactive</button>
                    <button type="submit" name="action" value="feature" class="btn btn-outline btn-sm">Feature</button>
                    <button type="submit" name="action" value="unfeature" class="btn btn-outline btn-sm">Unfeature</button>
                    <button type="submit" name="action" value="trash" class="btn btn-outline btn-sm" style="color: #92400E; border-color: #FCD34D;">Move to Trash</button>
                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Permanently delete the selected testimonials? This cannot be undone.')">Delete Permanently</button>
                </div>
            </div>
        </form>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 38px; text-align: center;">
                            <input type="checkbox" id="selectAllTestimonials" aria-label="Select all testimonials">
                        </th>
                        <th>Avatar</th>
                        <th>Client</th>
                        <th>Rating</th>
                        <th>Quote</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($testimonials as $testimonial)
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="ids[]" value="{{ $testimonial->id }}" form="testimonialsBulkForm" class="testimonial-row-checkbox" aria-label="Select {{ $testimonial->client_name }}">
                            </td>
                            <td><img src="{{ $testimonial->avatarUrl() }}" alt="{{ $testimonial->client_name }}" class="admin-avatar-thumb"></td>
                            <td>
                                <strong>{{ $testimonial->client_name }}</strong>
                                <div style="font-size: 12px; color: var(--admin-muted);">{{ $testimonial->client_role }}</div>
                            </td>
                            <td>{{ $testimonial->rating }} / 5</td>
                            <td style="max-width: 300px;">{{ \Illuminate\Support\Str::limit($testimonial->quote, 80) }}</td>
                            <td>
                                <span class="badge {{ $testimonial->is_featured ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $testimonial->is_featured ? 'Featured' : 'Standard' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $testimonial->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $testimonial->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.testimonials.show', $testimonial) }}" class="btn btn-view btn-sm">View</a>
                                    <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST" onsubmit="return confirm('Delete this testimonial?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" style="text-align: center; color: var(--admin-muted); padding: 32px;">No testimonials found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $testimonials->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('selectAllTestimonials');
            const checkboxes = Array.from(document.querySelectorAll('.testimonial-row-checkbox'));
            const bulkBar = document.getElementById('testimonialsBulkBar');
            const selectedCount = document.getElementById('testimonialsSelectedCount');

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
