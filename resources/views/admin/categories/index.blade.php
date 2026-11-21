@extends('layouts.admin')

@section('title', 'Tour Categories')
@section('page_title', 'Tour Categories')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">All Categories</h2>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">+ Add Category</a>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Tours</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td><img src="{{ $category->imageUrl() }}" alt="{{ $category->name }}" class="admin-thumb"></td>
                            <td>
                                <strong>{{ $category->name }}</strong>
                                <div style="font-size: 12px; color: var(--admin-muted);">{{ $category->slug }}</div>
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
                        <tr><td colspan="6">No categories found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $categories->links() }}
    </div>
@endsection
