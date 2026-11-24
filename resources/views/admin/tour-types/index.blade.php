@extends('layouts.admin')

@section('title', 'Tour Types')
@section('page_title', 'Tour Types')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">All Tour Types</h2>
            <a href="{{ route('admin.tour-types.create') }}" class="btn btn-primary">+ Add Tour Type</a>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Tours</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tourTypes as $tourType)
                        <tr>
                            <td>
                                <strong>{{ $tourType->name }}</strong>
                                <div style="font-size: 12px; color: var(--admin-muted);">{{ $tourType->slug }}</div>
                            </td>
                            <td>{{ $tourType->description ? \Illuminate\Support\Str::limit($tourType->description, 80) : '—' }}</td>
                            <td>{{ $tourType->tours_count }}</td>
                            <td>{{ $tourType->sort_order }}</td>
                            <td>
                                <span class="badge {{ $tourType->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $tourType->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.tour-types.show', $tourType) }}" class="btn btn-view btn-sm">View</a>
                                    <a href="{{ route('admin.tour-types.edit', $tourType) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form action="{{ route('admin.tour-types.destroy', $tourType) }}" method="POST" onsubmit="return confirm('Delete this tour type?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No tour types found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $tourTypes->links() }}
    </div>
@endsection
