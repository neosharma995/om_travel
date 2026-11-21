@extends('layouts.admin')

@section('title', 'Inquiries')
@section('page_title', 'Customer Inquiries')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">Messages</h2>
            <div class="filter-tabs">
                <a href="{{ route('admin.inquiries.index') }}" class="filter-tab {{ $currentStatus === '' ? 'is-active' : '' }}">All</a>
                <a href="{{ route('admin.inquiries.index', ['status' => 'new']) }}" class="filter-tab {{ $currentStatus === 'new' ? 'is-active' : '' }}">New</a>
                <a href="{{ route('admin.inquiries.index', ['status' => 'read']) }}" class="filter-tab {{ $currentStatus === 'read' ? 'is-active' : '' }}">Read</a>
                <a href="{{ route('admin.inquiries.index', ['status' => 'replied']) }}" class="filter-tab {{ $currentStatus === 'replied' ? 'is-active' : '' }}">Replied</a>
            </div>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Sender</th>
                        <th>Subject</th>
                        <th>Received</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inquiries as $inquiry)
                        <tr>
                            <td>
                                <strong>{{ $inquiry->name }}</strong>
                                <div style="font-size: 12px; color: var(--admin-muted);">{{ $inquiry->email }}</div>
                            </td>
                            <td>{{ $inquiry->subject }}</td>
                            <td>{{ $inquiry->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="badge {{ $inquiry->status === 'replied' ? 'badge-success' : ($inquiry->status === 'new' ? 'badge-warning' : 'badge-neutral') }}">
                                    {{ $inquiry->status }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="btn btn-outline btn-sm">View</a>
                                    <form action="{{ route('admin.inquiries.destroy', $inquiry) }}" method="POST" onsubmit="return confirm('Delete this inquiry?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No inquiries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $inquiries->links() }}
    </div>
@endsection
