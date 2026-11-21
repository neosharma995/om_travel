@extends('layouts.admin')

@section('title', 'Destinations')
@section('page_title', 'Destinations')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">All Destinations</h2>
            <a href="{{ route('admin.destinations.create') }}" class="btn btn-primary">+ Add Destination</a>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
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
                        <tr><td colspan="6">No destinations found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $destinations->links() }}
    </div>
@endsection
