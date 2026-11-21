@extends('layouts.customer')

@section('title', 'My Inquiries')
@section('page_title', 'My Inquiries')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <h2 class="panel-title">Support &amp; Custom Trip Inquiries</h2>
            <a href="{{ route('contact.create') }}" class="btn btn-primary btn-sm">+ New Inquiry</a>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Sent</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inquiries as $inquiry)
                        <tr>
                            <td><strong>{{ $inquiry->subject }}</strong></td>
                            <td style="max-width: 460px;">{{ \Illuminate\Support\Str::limit($inquiry->message, 120) }}</td>
                            <td>{{ $inquiry->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="badge {{ $inquiry->status === 'replied' ? 'badge-success' : ($inquiry->status === 'new' ? 'badge-warning' : 'badge-neutral') }}">
                                    {{ $inquiry->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">You have not submitted any inquiries yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $inquiries->links() }}
    </div>
@endsection
