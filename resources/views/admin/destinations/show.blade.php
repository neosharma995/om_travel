@extends('layouts.admin')

@section('title', 'Destination Details')

@section('content')
    <div class="detail-hero-card">
        <div class="detail-hero-top">
            <div class="detail-hero-main">
                <img src="{{ $destination->imageUrl() }}" alt="{{ $destination->name }}" class="detail-hero-thumb">
                <div class="detail-hero-info">
                    <div class="detail-badges">
                        <span class="status-pill {{ $destination->is_active ? 'is-active' : 'is-inactive' }}">
                            {{ $destination->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if ($destination->is_popular)
                            <span class="status-pill is-featured">Popular</span>
                        @endif
                    </div>
                    <h2 class="detail-title">{{ $destination->name }}</h2>
                    <p class="detail-subtitle">
                        {{ $destination->country ?: 'Country not specified' }} &bull; {{ $destination->displayListingsCount() }}
                    </p>
                </div>
            </div>

            <div class="detail-hero-actions">
                <a href="{{ route('admin.destinations.index') }}" class="btn btn-light btn-sm">&larr; Back</a>
                <a href="{{ route('admin.destinations.edit', $destination) }}" class="btn btn-primary btn-sm">Edit Destination</a>
                <form method="POST" action="{{ route('admin.destinations.destroy', $destination) }}"
                    onsubmit="return confirm('Delete this destination?');">
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
                    <h3 class="panel-title">Destination Overview</h3>
                </div>
                <div class="detail-section-pad">
                    @if ($destination->description)
                        <div class="admin-detail-prose">{{ $destination->description }}</div>
                    @else
                        <p class="detail-subtitle">No destination description provided.</p>
                    @endif
                </div>
            </section>

            <section class="panel-card">
                <div class="panel-header">
                    <h3 class="panel-title">Tour Packages in {{ $destination->name }} ({{ $destination->tours_count }})</h3>
                </div>

                @if ($tours->isEmpty())
                    <p class="detail-empty-state">No tour packages are assigned to this destination yet.</p>
                @else
                    <div class="table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th>Category</th>
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
                                            <div class="cell-sub">{{ $tour->tourType?->name ?? 'Standard' }}</div>
                                        </td>
                                        <td>{{ $tour->category?->name ?? 'Uncategorized' }}</td>
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
                <h3 class="panel-title">Destination Metadata</h3>
            </div>
            <div class="detail-meta-list">
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Slug</span>
                    <span class="detail-meta-value">{{ $destination->slug }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Country</span>
                    <span class="detail-meta-value">{{ $destination->country ?: '—' }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Listings Label</span>
                    <span class="detail-meta-value">{{ $destination->listings_label ?: $destination->displayListingsCount() }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Total Tours</span>
                    <span class="detail-meta-value">{{ $destination->tours_count }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Sort Order</span>
                    <span class="detail-meta-value">{{ $destination->sort_order }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Popular Flag</span>
                    <span class="detail-meta-value">{{ $destination->is_popular ? 'Yes' : 'No' }}</span>
                </div>
            </div>
        </aside>
    </div>
@endsection
