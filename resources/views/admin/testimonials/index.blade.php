@extends('layouts.admin')

@section('title', 'Testimonials')
@section('page_title', 'Client Testimonials')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">All Testimonials</h2>
            <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">+ Add Testimonial</a>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Avatar</th>
                        <th>Client</th>
                        <th>Rating</th>
                        <th>Quote</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($testimonials as $testimonial)
                        <tr>
                            <td><img src="{{ $testimonial->avatarUrl() }}" alt="{{ $testimonial->client_name }}" class="admin-avatar-thumb"></td>
                            <td>
                                <strong>{{ $testimonial->client_name }}</strong>
                                <div style="font-size: 12px; color: var(--admin-muted);">{{ $testimonial->client_role }}</div>
                            </td>
                            <td>{{ $testimonial->rating }} / 5</td>
                            <td style="max-width: 320px;">{{ \Illuminate\Support\Str::limit($testimonial->quote, 80) }}</td>
                            <td>
                                <span class="badge {{ $testimonial->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $testimonial->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
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
                        <tr><td colspan="6">No testimonials found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $testimonials->links() }}
    </div>
@endsection
