@extends('layouts.admin')

@section('title', 'Inquiry #'.$inquiry->id)
@section('page_title', 'Inquiry #'.$inquiry->id)

@section('content')
    <div class="two-col-grid">
        <div class="panel-card">
            <div class="panel-header">
                <h2 class="panel-title">{{ $inquiry->subject }}</h2>
                <a href="{{ route('admin.inquiries.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
            </div>
            <table class="admin-table" style="margin-bottom: 18px;">
                <tbody>
                    <tr><th>From</th><td>{{ $inquiry->name }}</td></tr>
                    <tr><th>Email</th><td>{{ $inquiry->email }}</td></tr>
                    <tr><th>Phone</th><td>{{ $inquiry->phone ?: '—' }}</td></tr>
                    <tr><th>Received</th><td>{{ $inquiry->created_at->format('F d, Y H:i') }}</td></tr>
                </tbody>
            </table>
            <div style="background: #F7FBFC; border: 1px solid var(--admin-border); border-radius: 12px; padding: 16px; white-space: pre-line;">
                {{ $inquiry->message }}
            </div>
        </div>

        <div class="panel-card">
            <div class="panel-header">
                <h2 class="panel-title">Update Inquiry Status</h2>
            </div>

            <form action="{{ route('admin.inquiries.update', $inquiry) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="status" class="form-label">Status *</label>
                    <select id="status" name="status" class="form-control">
                        <option value="new" @selected(old('status', $inquiry->status) === 'new')>New</option>
                        <option value="read" @selected(old('status', $inquiry->status) === 'read')>Read</option>
                        <option value="replied" @selected(old('status', $inquiry->status) === 'replied')>Replied</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
@endsection
