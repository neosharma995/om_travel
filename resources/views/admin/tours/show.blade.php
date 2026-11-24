@extends('layouts.admin')

@section('title', 'Tour Package Details')

@section('content')
    <div class="detail-hero-card">
        <div class="detail-hero-top">
            <div class="detail-hero-main">
                <img src="{{ $tour->imageUrl() }}" alt="{{ $tour->title }}" class="detail-hero-thumb">
                <div class="detail-hero-info">
                    <div class="detail-badges">
                        <span class="status-pill {{ $tour->is_active ? 'is-active' : 'is-inactive' }}">
                            {{ $tour->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if ($tour->is_featured)
                            <span class="status-pill is-featured">Featured</span>
                        @endif
                        @if ($tour->category)
                            <span class="detail-badge-neutral">{{ $tour->category->displayNameWithParent() }}</span>
                        @endif
                    </div>
                    <h2 class="detail-title">{{ $tour->title }}</h2>
                    <p class="detail-subtitle">
                        {{ $tour->location ?: ($tour->destination?->name ?? 'Location not specified') }}
                        @if ($tour->destination && $tour->location)
                            • {{ $tour->destination->name }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="detail-hero-actions">
                <a href="{{ route('admin.tours.index') }}" class="btn btn-light btn-sm">&larr; Back</a>
                <a href="{{ route('admin.tours.edit', $tour) }}" class="btn btn-primary btn-sm">Edit Tour</a>
                <form method="POST" action="{{ route('admin.tours.destroy', $tour) }}"
                    onsubmit="return confirm('Delete this tour package?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </div>

        <div class="detail-kpi-strip">
            <div class="detail-kpi-item">
                <span class="detail-kpi-label">Package Price</span>
                <span class="detail-kpi-value">&#8377;{{ number_format((float) $tour->price, 2) }}</span>
            </div>
            <div class="detail-kpi-item">
                <span class="detail-kpi-label">Duration</span>
                <span class="detail-kpi-value">{{ $tour->displayDuration() }}</span>
            </div>
            <div class="detail-kpi-item">
                <span class="detail-kpi-label">Guest Rating</span>
                <span class="detail-kpi-value">{{ number_format((float) $tour->rating, 1) }} / 5 ({{ $tour->reviews_count }})</span>
            </div>
            <div class="detail-kpi-item">
                <span class="detail-kpi-label">Bookings</span>
                <span class="detail-kpi-value">{{ $tour->bookings_count }}</span>
            </div>
        </div>
    </div>

    <div class="detail-layout-grid">
        <div class="detail-main-col">
            <section class="panel-card">
                <div class="panel-header">
                    <h3 class="panel-title">Overview &amp; Highlights</h3>
                </div>
                <div class="detail-section-pad">
                    <div class="detail-block">
                        <h4 class="detail-block-title">Short Summary</h4>
                        @if ($tour->summary)
                            <p class="detail-lead">{{ $tour->summary }}</p>
                        @else
                            <p class="detail-subtitle">No summary provided.</p>
                        @endif
                    </div>

                    <div class="detail-block">
                        <h4 class="detail-block-title">Full Description</h4>
                        @if ($tour->description)
                            <div class="admin-detail-prose">{{ $tour->description }}</div>
                        @else
                            <p class="detail-subtitle">No description provided.</p>
                        @endif
                    </div>

                    <div class="detail-block">
                        <h4 class="detail-block-title">Tour Highlights</h4>
                        @if (count($tour->highlightsList()) > 0)
                            <ul class="detail-highlights-grid">
                                @foreach ($tour->highlightsList() as $highlight)
                                    <li class="detail-highlight-item">
                                        <span class="detail-highlight-dot">&bull;</span>
                                        <span>{{ $highlight }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="detail-subtitle">No highlights added yet.</p>
                        @endif
                    </div>
                </div>
            </section>
        </div>

        <aside class="panel-card detail-sidebar-card">
            <div class="panel-header">
                <h3 class="panel-title">Package Metadata</h3>
            </div>
            <div class="detail-meta-list">
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Slug</span>
                    <span class="detail-meta-value">{{ $tour->slug }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Destination</span>
                    <span class="detail-meta-value">
                        @if ($tour->destination)
                            <a href="{{ route('admin.destinations.show', $tour->destination) }}">
                                {{ $tour->destination->name }}
                            </a>
                        @else
                            Unassigned
                        @endif
                    </span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Category</span>
                    <span class="detail-meta-value">
                        @if ($tour->category)
                            <a href="{{ route('admin.categories.show', $tour->category) }}">
                                {{ $tour->category->displayNameWithParent() }}
                            </a>
                        @else
                            Uncategorized
                        @endif
                    </span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Tour Type</span>
                    <span class="detail-meta-value">
                        @if ($tour->tourType)
                            <a href="{{ route('admin.tour-types.show', $tour->tourType) }}">
                                {{ $tour->tourType->name }}
                            </a>
                        @else
                            Standard
                        @endif
                    </span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Activity</span>
                    <span class="detail-meta-value">
                        @if ($tour->activity)
                            <a href="{{ route('admin.activities.show', $tour->activity) }}">
                                {{ $tour->activity->name }}
                            </a>
                        @else
                            None
                        @endif
                    </span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Location</span>
                    <span class="detail-meta-value">{{ $tour->location ?: '—' }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Duration Label</span>
                    <span class="detail-meta-value">{{ $tour->displayDuration() }} ({{ $tour->duration_days }} days)</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Sort Order</span>
                    <span class="detail-meta-value">{{ $tour->sort_order }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Last Updated</span>
                    <span class="detail-meta-value">{{ $tour->updated_at?->format('M d, Y') }}</span>
                </div>
            </div>
        </aside>
    </div>
@endsection
