@extends('layouts.admin')

@section('title', 'Recent Gallery')
@section('page_title', 'Recent Gallery')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">Gallery Photos</h2>
            <a href="{{ route('admin.gallery.create') }}" class="btn btn-primary">+ Add Gallery Photo</a>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td><img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" class="admin-thumb"></td>
                            <td><strong>{{ $item->title }}</strong></td>
                            <td>{{ $item->location ?: '—' }}</td>
                            <td>{{ $item->sort_order }}</td>
                            <td>
                                <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $item->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.gallery.show', $item) }}" class="btn btn-view btn-sm">View</a>
                                    <a href="{{ route('admin.gallery.edit', $item) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form action="{{ route('admin.gallery.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this gallery item?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No gallery items found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $items->links() }}
    </div>
@endsection
