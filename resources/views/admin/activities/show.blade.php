@extends('layouts.admin')

@section('title', 'Activity Details')

@section('content')
    <div class="detail-hero-card">
        <div class="detail-hero-top">
            <div class="detail-hero-main">
                <img src="{{ $activity->imageUrl() }}" alt="{{ $activity->name }}" class="detail-hero-thumb">
                <div class="detail-hero-info">
                    <div class="detail-badges">
                        <span class="status-pill {{ $activity->is_active ? 'is-active' : 'is-inactive' }}">
                            {{ $activity->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="detail-badge-neutral">
                            {{ $activity->tours_count }} {{ Str::plural('tour package', $activity->tours_count) }}
                        </span>
                    </div>
                    <h2 class="detail-title">{{ $activity->name }}</h2>
                    <p class="detail-subtitle">Slug: {{ $activity->slug }}</p>
                </div>
            </div>

            <div class="detail-hero-actions">
                <a href="{{ route('admin.activities.index') }}" class="btn btn-light btn-sm">&larr; Back</a>
                <a href="{{ route('admin.activities.edit', $activity) }}" class="btn btn-primary btn-sm">Edit Activity</a>
                <form method="POST" action="{{ route('admin.activities.destroy', $activity) }}"
                    onsubmit="return confirm('Delete this activity?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <div class="detail-layout-grid">
        <div class="detail-main-col">
            <section class="panel-card">
                <div class="panel-header">
                    <h3 class="panel-title">Activity Overview</h3>
                </div>
                <div class="detail-section-pad">
                    @if ($activity->description)
                        <div class="admin-detail-prose">{{ $activity->description }}</div>
                    @else
                        <p class="detail-subtitle">No description provided.</p>
                    @endif
                </div>
            </section>

            <section class="panel-card">
                <div class="panel-header">
                    <h3 class="panel-title">Assigned Tour Packages ({{ $activity->tours_count }})</h3>
                </div>

                @if ($tours->isEmpty())
                    <p class="detail-empty-state">No tour packages are assigned to this activity yet.</p>
                @else
                    <div class="table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th>Destination</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tours as $tour)
                                    <tr>
                                        <td>
                                            <strong>{{ $tour->title }}</strong>
                                            <div class="cell-sub">{{ $tour->category?->name ?? 'Uncategorized' }}</div>
                                        </td>
                                        <td>{{ $tour->destination?->name ?? 'Unassigned' }}</td>
                                        <td>&#8377;{{ number_format((float) $tour->price, 2) }}</td>
                                        <td>
                                            <span class="status-pill {{ $tour->is_active ? 'is-active' : 'is-inactive' }}">
                                                {{ $tour->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.tours.show', $tour) }}" class="btn btn-view btn-sm">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        <aside class="panel-card detail-sidebar-card">
            <div class="panel-header">
                <h3 class="panel-title">Activity Metadata</h3>
            </div>
            <div class="detail-meta-list">
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Slug</span>
                    <span class="detail-meta-value">{{ $activity->slug }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Assigned Tours</span>
                    <span class="detail-meta-value">{{ $activity->tours_count }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Sort Order</span>
                    <span class="detail-meta-value">{{ $activity->sort_order }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Last Updated</span>
                    <span class="detail-meta-value">{{ $activity->updated_at?->format('M d, Y') }}</span>
                </div>
            </div>
        </aside>
    </div>
@endsection
