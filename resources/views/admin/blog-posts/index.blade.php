@extends('layouts.admin')

@section('title', 'Blog Articles')
@section('page_title', 'Blog & Articles')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">All Articles</h2>
                <div style="font-size: 12.5px; color: var(--admin-muted); margin-top: 2px;">
                    Publish travel tips, itineraries, and stories about Himachal Pradesh
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                @php $trashedPostsCount = \App\Models\TrashItem::query()->where('item_type', 'blog_post')->count(); @endphp
                <a href="{{ route('admin.trash.index', ['type' => 'blog_post']) }}" class="btn btn-outline btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                    Trashed Articles ({{ $trashedPostsCount }})
                </a>
                <a href="{{ route('admin.blog-posts.create') }}" class="btn btn-primary">+ Write Article</a>
            </div>
        </div>

        {{-- Standalone Bulk Actions Form --}}
        <form action="{{ route('admin.blog-posts.bulk') }}" method="POST" id="blogPostsBulkForm">
            @csrf
            <div class="bulk-toolbar" id="blogPostsBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 12px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 10px 16px; border-radius: 10px; margin-bottom: 16px;">
                <div style="font-weight: 700; color: var(--admin-sidebar); font-size: 13.5px;">
                    <span id="blogPostsSelectedCount">0</span> articles selected
                </div>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <button type="submit" name="action" value="publish" class="btn btn-outline btn-sm" style="color: #065F46; border-color: #A7F3D0;">Publish</button>
                    <button type="submit" name="action" value="unpublish" class="btn btn-outline btn-sm">Draft / Unpublish</button>
                    <button type="submit" name="action" value="trash" class="btn btn-outline btn-sm" style="color: #92400E; border-color: #FCD34D;">Move to Trash</button>
                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Permanently delete the selected articles? This cannot be undone.')">Delete Permanently</button>
                </div>
            </div>
        </form>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 38px; text-align: center;">
                            <input type="checkbox" id="selectAllBlogPosts" aria-label="Select all blog posts">
                        </th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Published Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="ids[]" value="{{ $post->id }}" form="blogPostsBulkForm" class="blogpost-row-checkbox" aria-label="Select {{ $post->title }}">
                            </td>
                            <td><img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="admin-thumb"></td>
                            <td>
                                <strong>{{ $post->title }}</strong>
                                <div style="font-size: 12px; color: var(--admin-muted);">{{ $post->read_time }}</div>
                            </td>
                            <td>{{ $post->category }}</td>
                            <td>{{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}</td>
                            <td>
                                <span class="badge {{ $post->is_published ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $post->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.blog-posts.show', $post) }}" class="btn btn-view btn-sm">View</a>
                                    <a href="{{ route('admin.blog-posts.edit', $post) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form action="{{ route('admin.blog-posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete this article?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align: center; color: var(--admin-muted); padding: 32px;">No articles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $posts->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('selectAllBlogPosts');
            const checkboxes = Array.from(document.querySelectorAll('.blogpost-row-checkbox'));
            const bulkBar = document.getElementById('blogPostsBulkBar');
            const selectedCount = document.getElementById('blogPostsSelectedCount');

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
