@extends('layouts.admin')

@section('title', 'Tour Packages')
@section('page_title', 'Tour Packages')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">All Tours</h2>
            <a href="{{ route('admin.tours.create') }}" class="btn btn-primary">+ Add Tour Package</a>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Tour Title</th>
                        <th>Destination</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tours as $tour)
                        <tr>
                            <td><img src="{{ $tour->imageUrl() }}" alt="{{ $tour->title }}" class="admin-thumb"></td>
                            <td>
                                <strong>{{ $tour->title }}</strong>
                                <div style="font-size: 12px; color: var(--admin-muted);">
                                    {{ $tour->category?->name ?? 'Uncategorized' }}
                                    @if ($tour->tourType)
                                        &bull; {{ $tour->tourType->name }}
                                    @endif
                                </div>
                            </td>
                            <td>{{ $tour->destination?->name ?? '—' }}</td>
                            <td>${{ number_format((float) $tour->price, 2) }}</td>
                            <td>{{ $tour->displayDuration() }}</td>
                            <td>
                                <span class="badge {{ $tour->is_featured ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $tour->is_featured ? 'Featured' : 'Standard' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $tour->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $tour->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.tours.edit', $tour) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form action="{{ route('admin.tours.destroy', $tour) }}" method="POST" onsubmit="return confirm('Delete this tour package?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">No tours found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $tours->links() }}
    </div>
@endsection
