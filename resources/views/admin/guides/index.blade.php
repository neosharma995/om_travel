@extends('layouts.admin')

@section('title', 'Tour Guides')
@section('page_title', 'Tour Guides')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">All Guides</h2>
            <a href="{{ route('admin.guides.create') }}" class="btn btn-primary">+ Add Guide</a>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Portrait</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($guides as $guide)
                        <tr>
                            <td><img src="{{ $guide->photoUrl() }}" alt="{{ $guide->name }}" class="admin-avatar-thumb"></td>
                            <td><strong>{{ $guide->name }}</strong></td>
                            <td>{{ $guide->role }}</td>
                            <td>{{ $guide->sort_order }}</td>
                            <td>
                                <span class="badge {{ $guide->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $guide->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.guides.edit', $guide) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form action="{{ route('admin.guides.destroy', $guide) }}" method="POST" onsubmit="return confirm('Delete this tour guide?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No guides found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $guides->links() }}
    </div>
@endsection
