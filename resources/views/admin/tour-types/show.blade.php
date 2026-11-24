@extends('layouts.admin')

@section('title', 'Tour Type Details')

@section('content')
    <div class="detail-hero-card">
        <div class="detail-hero-top">
            <div class="detail-hero-main">
                <div class="detail-hero-info">
                    <div class="detail-badges">
                        <span class="status-pill {{ $tourType->is_active ? 'is-active' : 'is-inactive' }}">
                            {{ $tourType->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="detail-badge-neutral">
                            {{ $tourType->tours_count }} {{ Str::plural('tour package', $tourType->tours_count) }}
                        </span>
                    </div>
                    <h2 class="detail-title">{{ $tourType->name }}</h2>
                    <p class="detail-subtitle">Tour Type classification &bull; Slug: {{ $tourType->slug }}</p>
                </div>
            </div>

            <div class="detail-hero-actions">
                <a href="{{ route('admin.tour-types.index') }}" class="btn btn-light btn-sm">&larr; Back</a>
                <a href="{{ route('admin.tour-types.edit', $tourType) }}" class="btn btn-primary btn-sm">Edit Tour Type</a>
                <form method="POST" action="{{ route('admin.tour-types.destroy', $tourType) }}"
                    onsubmit="return confirm('Delete this tour type?');">
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
                    <h3 class="panel-title">Description</h3>
                </div>
                <div class="detail-section-pad">
                    @if ($tourType->description)
                        <div class="admin-detail-prose">{{ $tourType->description }}</div>
                    @else
                        <p class="detail-subtitle">No description provided.</p>
                    @endif
                </div>
            </section>

            <section class="panel-card">
                <div class="panel-header">
                    <h3 class="panel-title">Assigned Tour Packages ({{ $tourType->tours_count }})</h3>
                </div>

                @if ($tours->isEmpty())
                    <p class="detail-empty-state">No tour packages are assigned to this tour type yet.</p>
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
                <h3 class="panel-title">Tour Type Metadata</h3>
            </div>
            <div class="detail-meta-list">
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Slug</span>
                    <span class="detail-meta-value">{{ $tourType->slug }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Assigned Tours</span>
                    <span class="detail-meta-value">{{ $tourType->tours_count }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Sort Order</span>
                    <span class="detail-meta-value">{{ $tourType->sort_order }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Last Updated</span>
                    <span class="detail-meta-value">{{ $tourType->updated_at?->format('M d, Y') }}</span>
                </div>
            </div>
        </aside>
    </div>
@endsection
