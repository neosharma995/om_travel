@extends('layouts.admin')

@section('title', 'Blog Articles')
@section('page_title', 'Blog & Articles')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">All Articles</h2>
            <a href="{{ route('admin.blog-posts.create') }}" class="btn btn-primary">+ Write Article</a>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Published</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr>
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
                        <tr><td colspan="6">No articles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $posts->links() }}
    </div>
@endsection
