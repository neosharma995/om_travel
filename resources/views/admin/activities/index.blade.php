@extends('layouts.admin')

@section('title', 'Activities')
@section('page_title', 'Activities')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">All Activities</h2>
            <a href="{{ route('admin.activities.create') }}" class="btn btn-primary">+ Add Activity</a>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
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
                        <tr><td colspan="7">No activities found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $activities->links() }}
    </div>
@endsection
