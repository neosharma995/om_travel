@extends('layouts.admin')

@section('title', 'Inquiries')
@section('page_title', 'Customer Inquiries')

@section('content')
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Messages</h2>
                <div style="font-size: 12.5px; color: var(--admin-muted); margin-top: 2px;">
                    Manage traveler questions and contact requests
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                @php $trashedInquiriesCount = \App\Models\TrashItem::query()->where('item_type', 'inquiry')->count(); @endphp
                <a href="{{ route('admin.trash.index', ['type' => 'inquiry']) }}" class="btn btn-outline btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                    Trashed Inquiries ({{ $trashedInquiriesCount }})
                </a>
            </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; flex-wrap: wrap;">
            <div class="filter-tabs">
                <a href="{{ route('admin.inquiries.index') }}" class="filter-tab {{ $currentStatus === '' ? 'is-active' : '' }}">All</a>
                <a href="{{ route('admin.inquiries.index', ['status' => 'new']) }}" class="filter-tab {{ $currentStatus === 'new' ? 'is-active' : '' }}">New</a>
                <a href="{{ route('admin.inquiries.index', ['status' => 'read']) }}" class="filter-tab {{ $currentStatus === 'read' ? 'is-active' : '' }}">Read</a>
                <a href="{{ route('admin.inquiries.index', ['status' => 'replied']) }}" class="filter-tab {{ $currentStatus === 'replied' ? 'is-active' : '' }}">Replied</a>
            </div>
        </div>

        {{-- Standalone Bulk Actions Form --}}
        <form action="{{ route('admin.inquiries.bulk') }}" method="POST" id="inquiriesBulkForm">
            @csrf
            <div class="bulk-toolbar" id="inquiriesBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 12px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 10px 16px; border-radius: 10px; margin-bottom: 16px;">
                <div style="font-weight: 700; color: var(--admin-sidebar); font-size: 13.5px;">
                    <span id="inquiriesSelectedCount">0</span> inquiries selected
                </div>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <button type="submit" name="action" value="replied" class="btn btn-outline btn-sm" style="color: #065F46; border-color: #A7F3D0;">Mark Replied</button>
                    <button type="submit" name="action" value="closed" class="btn btn-outline btn-sm">Close Inquiry</button>
                    <button type="submit" name="action" value="trash" class="btn btn-outline btn-sm" style="color: #92400E; border-color: #FCD34D;">Move to Trash</button>
                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Permanently delete the selected inquiries? This cannot be undone.')">Delete Permanently</button>
                </div>
            </div>
        </form>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 38px; text-align: center;">
                            <input type="checkbox" id="selectAllInquiries" aria-label="Select all inquiries">
                        </th>
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
                            <td style="text-align: center;">
                                <input type="checkbox" name="ids[]" value="{{ $inquiry->id }}" form="inquiriesBulkForm" class="inquiry-row-checkbox" aria-label="Select inquiry from {{ $inquiry->name }}">
                            </td>
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
                        <tr><td colspan="6" style="text-align: center; color: var(--admin-muted); padding: 32px;">No inquiries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $inquiries->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('selectAllInquiries');
            const checkboxes = Array.from(document.querySelectorAll('.inquiry-row-checkbox'));
            const bulkBar = document.getElementById('inquiriesBulkBar');
            const selectedCount = document.getElementById('inquiriesSelectedCount');

            if (!selectAll || !bulkBar || !selectedCount) return;

            const updateBulkBar = () => {
                const checked = checkboxes.filter(cb => cb.checked);
                const count = checked.length;
                selectedCount.textContent = count;
                bulkBar.style.display = count > 0 ? 'flex' : 'none';
                selectAll.checked = count > 0 && count === checkboxes.length;
                selectAll.indeterminate = count > 0 && count < checkboxes.length;
            };

            selectAll.addEventListener('change', () => {
                checkboxes.forEach(cb => { cb.checked = selectAll.checked; });
                updateBulkBar();
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkBar);
            });
        });
    </script>
@endsection
