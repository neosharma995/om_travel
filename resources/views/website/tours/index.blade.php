@extends('layouts.website')

@section('title', 'Tour Packages — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    <section class="page-banner tours-page-banner">
        <div class="container">
            <div class="tours-banner-content">
                <span class="tours-banner-eyebrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                    Curated Global Expeditions
                </span>
                <h1 class="page-banner-title">Handcrafted Tour Packages</h1>
                <p class="tours-banner-desc">Explore signature journeys across pristine coastlines, majestic alpine ridges, and untamed wildlife sanctuaries with our licensed guides.</p>
                <div class="breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span>&rsaquo;</span>
                    <span>Tours</span>
                </div>
            </div>
        </div>
    </section>

    @php
        $hasActiveFilters = !empty($filters['search'])
            || !empty($filters['destination'])
            || !empty($filters['category'])
            || !empty($filters['tour_type'])
            || !empty($filters['activity'])
            || !empty($filters['duration'])
            || !empty($filters['max_price']);

        $activeCount = 0;
        foreach ($filters as $f) {
            if (!empty($f)) {
                $activeCount++;
            }
        }
    @endphp

    <section class="page-section tours-discovery-section">
        <div class="container">
            {{-- Mobile Filter Toggle Bar --}}
            <div class="tours-mobile-filter-bar">
                <button type="button" class="btn btn-outline-soft tours-filter-toggle-btn" id="toursFilterToggle" aria-expanded="false" aria-controls="toursFilterSidebar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    <span>Filter Journeys</span>
                    @if ($hasActiveFilters)
                        <span class="filter-count-badge">{{ $activeCount }}</span>
                    @endif
                </button>
                <span class="mobile-tours-count">{{ $tours->total() }} {{ \Illuminate\Support\Str::plural('Journey', $tours->total()) }}</span>
            </div>

            <div class="tours-discovery-layout">
                {{-- Left Column: Sticky Filter Sidebar Console --}}
                <aside class="tours-filter-sidebar filter-panel" id="toursFilterSidebar" aria-label="Tour Filters">
                    <div class="filter-sidebar-card">
                        <div class="filter-sidebar-header">
                            <div class="filter-sidebar-title-group">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                                <h3>Filter Tours</h3>
                            </div>
                            @if ($hasActiveFilters)
                                <a href="{{ route('tours.index') }}" class="filter-reset-link" title="Reset all filters">Reset All</a>
                            @endif
                        </div>

                        <form action="{{ route('tours.index') }}" method="GET" class="filter-form tours-filter-form" id="toursFilterForm">
                            {{-- Search Keyword --}}
                            <div class="form-group filter-group">
                                <label for="filter-search" class="form-label filter-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    <span>Search Keyword</span>
                                </label>
                                <div class="filter-input-wrap">
                                    <input type="text" id="filter-search" name="search" value="{{ $filters['search'] }}" class="form-control" placeholder="Destination or title">
                                </div>
                            </div>

                            {{-- Destination --}}
                            <div class="form-group filter-group">
                                <label for="filter-destination" class="form-label filter-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <span>Destination</span>
                                </label>
                                <div class="filter-select-wrap">
                                    <select id="filter-destination" name="destination" class="form-control filter-select">
                                        <option value="">All Destinations</option>
                                        @foreach ($destinations as $dest)
                                            <option value="{{ $dest->slug }}" @selected($filters['destination'] === $dest->slug)>{{ $dest->name }}</option>
                                        @endforeach
                                    </select>
                                    <svg class="select-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                            </div>

                            {{-- Category --}}
                            <div class="form-group filter-group">
                                <label for="filter-category" class="form-label filter-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    <span>Category</span>
                                </label>
                                <div class="filter-select-wrap">
                                    <select id="filter-category" name="category" class="form-control filter-select">
                                        <option value="">All Categories</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->slug }}" @selected($filters['category'] === $cat->slug)>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    <svg class="select-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                            </div>

                            {{-- Tour Type --}}
                            <div class="form-group filter-group">
                                <label for="filter-tour-type" class="form-label filter-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                    <span>Tour Type</span>
                                </label>
                                <div class="filter-select-wrap">
                                    <select id="filter-tour-type" name="tour_type" class="form-control filter-select">
                                        <option value="">All Tour Types</option>
                                        @foreach ($tourTypes as $type)
                                            <option value="{{ $type->slug }}" @selected($filters['tour_type'] === $type->slug)>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <svg class="select-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                            </div>

                            {{-- Activity --}}
                            <div class="form-group filter-group">
                                <label for="filter-activity" class="form-label filter-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                    <span>Activity</span>
                                </label>
                                <div class="filter-select-wrap">
                                    <select id="filter-activity" name="activity" class="form-control filter-select">
                                        <option value="">All Activities</option>
                                        @foreach ($activities as $act)
                                            <option value="{{ $act->slug }}" @selected($filters['activity'] === $act->slug)>{{ $act->name }}</option>
                                        @endforeach
                                    </select>
                                    <svg class="select-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                            </div>

                            {{-- Duration --}}
                            <div class="form-group filter-group">
                                <label for="filter-duration" class="form-label filter-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <span>Duration</span>
                                </label>
                                <div class="filter-select-wrap">
                                    <select id="filter-duration" name="duration" class="form-control filter-select">
                                        <option value="">Any Duration</option>
                                        <option value="short" @selected($filters['duration'] === 'short')>1 - 3 Days</option>
                                        <option value="medium" @selected($filters['duration'] === 'medium')>4 - 6 Days</option>
                                        <option value="long" @selected($filters['duration'] === 'long')>7+ Days</option>
                                    </select>
                                    <svg class="select-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                            </div>

                            {{-- Max Budget ($) --}}
                            <div class="form-group filter-group">
                                <label for="filter-price" class="form-label filter-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    <span>Max Budget ($)</span>
                                </label>
                                <div class="filter-input-wrap">
                                    <input type="number" id="filter-price" name="max_price" value="{{ $filters['max_price'] }}" min="0" step="10" class="form-control" placeholder="e.g. 1200">
                                </div>
                            </div>

                            <div class="filter-actions">
                                <button type="submit" class="btn btn-cyan filter-submit-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                                    <span>Filter Tours</span>
                                </button>
                                @if ($hasActiveFilters)
                                    <a href="{{ route('tours.index') }}" class="btn btn-outline-soft filter-clear-btn">
                                        <span>Reset Filters</span>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </aside>

                {{-- Right Column: Catalog, Toolbar, Active Badges, Grid & Pagination --}}
                <div class="tours-catalog-area">
                    {{-- Discovery Toolbar Header --}}
                    <div class="tours-catalog-header">
                        <div class="tours-count-summary">
                            <span class="tours-count-number">{{ $tours->total() }}</span>
                            <span class="tours-count-label">{{ \Illuminate\Support\Str::plural('Signature Journey', $tours->total()) }} Available</span>
                        </div>
                        <div class="tours-catalog-badges">
                            <span class="tours-guarantee-tag">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                Best Price Guarantee
                            </span>
                            <span class="tours-guarantee-tag">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                Expert Verified Guides
                            </span>
                        </div>
                    </div>

                    {{-- Active Filter Tags Ribbon --}}
                    @if ($hasActiveFilters)
                        <div class="tours-active-filters-bar">
                            <span class="active-filters-title">Active Filters:</span>
                            <div class="active-filter-tags">
                                @if (!empty($filters['search']))
                                    <span class="active-filter-pill">
                                        Search: "{{ $filters['search'] }}"
                                        <a href="{{ route('tours.index', array_merge(request()->except('search', 'page'))) }}" class="remove-filter-pill" aria-label="Remove search filter">&times;</a>
                                    </span>
                                @endif
                                @if (!empty($filters['destination']))
                                    @php $activeDest = $destinations->firstWhere('slug', $filters['destination']); @endphp
                                    <span class="active-filter-pill">
                                        {{ $activeDest ? $activeDest->name : $filters['destination'] }}
                                        <a href="{{ route('tours.index', array_merge(request()->except('destination', 'page'))) }}" class="remove-filter-pill" aria-label="Remove destination filter">&times;</a>
                                    </span>
                                @endif
                                @if (!empty($filters['category']))
                                    @php $activeCat = $categories->firstWhere('slug', $filters['category']); @endphp
                                    <span class="active-filter-pill">
                                        {{ $activeCat ? $activeCat->name : $filters['category'] }}
                                        <a href="{{ route('tours.index', array_merge(request()->except('category', 'page'))) }}" class="remove-filter-pill" aria-label="Remove activity filter">&times;</a>
                                    </span>
                                @endif
                                @if (!empty($filters['tour_type']))
                                    @php $activeType = $tourTypes->firstWhere('slug', $filters['tour_type']); @endphp
                                    <span class="active-filter-pill">
                                        {{ $activeType ? $activeType->name : $filters['tour_type'] }}
                                        <a href="{{ route('tours.index', array_merge(request()->except('tour_type', 'page'))) }}" class="remove-filter-pill" aria-label="Remove tour type filter">&times;</a>
                                    </span>
                                @endif
                                @if (!empty($filters['activity']))
                                    @php $activeAct = $activities->firstWhere('slug', $filters['activity']); @endphp
                                    <span class="active-filter-pill">
                                        {{ $activeAct ? $activeAct->name : $filters['activity'] }}
                                        <a href="{{ route('tours.index', array_merge(request()->except('activity', 'page'))) }}" class="remove-filter-pill" aria-label="Remove activity filter">&times;</a>
                                    </span>
                                @endif
                                @if (!empty($filters['duration']))
                                    @php
                                        $durationLabels = ['short' => '1 - 3 Days', 'medium' => '4 - 6 Days', 'long' => '7+ Days'];
                                    @endphp
                                    <span class="active-filter-pill">
                                        {{ $durationLabels[$filters['duration']] ?? $filters['duration'] }}
                                        <a href="{{ route('tours.index', array_merge(request()->except('duration', 'page'))) }}" class="remove-filter-pill" aria-label="Remove duration filter">&times;</a>
                                    </span>
                                @endif
                                @if (!empty($filters['max_price']))
                                    <span class="active-filter-pill">
                                        Budget ≤ ${{ $filters['max_price'] }}
                                        <a href="{{ route('tours.index', array_merge(request()->except('max_price', 'page'))) }}" class="remove-filter-pill" aria-label="Remove max price filter">&times;</a>
                                    </span>
                                @endif
                                <a href="{{ route('tours.index') }}" class="clear-all-link">Clear all</a>
                            </div>
                        </div>
                    @endif

                    @if ($tours->isEmpty())
                        <div class="tours-empty-state empty-state">
                            <div class="empty-state-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="48" height="48"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                            </div>
                            <h3>No tour packages matched your filters</h3>
                            <p>We couldn't find any journeys matching your current filter selections. Try adjusting your parameters or clear filters to view all curated expeditions.</p>
                            <a href="{{ route('tours.index') }}" class="btn btn-cyan">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                                <span>Reset All Filters</span>
                            </a>
                        </div>
                    @else
                        <div class="tours-grid-catalog tours-grid-3">
                            @foreach ($tours as $tour)
                                <article class="discovery-tour-card tour-card">
                                    <a href="{{ route('tours.show', $tour) }}" class="discovery-tour-thumb tour-card-thumb" aria-label="{{ $tour->title }}">
                                        <img src="{{ $tour->imageUrl() }}" alt="{{ $tour->title }}" loading="lazy">
                                        <div class="discovery-tour-badges">
                                            @if ($tour->destination)
                                                <span class="tour-card-badge">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11" height="11"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                                    {{ $tour->destination->name }}
                                                </span>
                                            @endif
                                            @if ($tour->tourType)
                                                <span class="tour-card-type-badge">{{ $tour->tourType->name }}</span>
                                            @endif
                                            @if ($tour->is_featured)
                                                <span class="tour-card-featured-badge">
                                                    <svg viewBox="0 0 24 24" fill="currentColor" width="11" height="11"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                                    Featured
                                                </span>
                                            @endif
                                        </div>
                                        <div class="tour-card-duration-tag">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            <span>{{ $tour->displayDuration() }}</span>
                                        </div>
                                    </a>

                                    <div class="discovery-tour-body tour-card-body">
                                        <div class="discovery-meta-row">
                                            <div class="discovery-rating tour-rating">
                                                <span class="stars-row" aria-label="{{ number_format((float) $tour->rating, 1) }} rating">
                                                    @for ($i = 0; $i < 5; $i++)
                                                        <svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                                    @endfor
                                                </span>
                                                <span class="rating-score"><strong>{{ number_format((float) $tour->rating, 1) }}</strong></span>
                                                <span class="rating-reviews">({{ $tour->reviews_count }} reviews)</span>
                                            </div>
                                            @if ($tour->category || $tour->activity)
                                                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                                    @if ($tour->category)
                                                        <span class="discovery-category-tag">{{ $tour->category->name }}</span>
                                                    @endif
                                                    @if ($tour->activity)
                                                        <span class="discovery-category-tag">{{ $tour->activity->name }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <h2 class="discovery-tour-title tour-card-title">
                                            <a href="{{ route('tours.show', $tour) }}">{{ $tour->title }}</a>
                                        </h2>

                                        @if ($tour->summary)
                                            <p class="discovery-tour-summary">{{ \Illuminate\Support\Str::limit($tour->summary, 90) }}</p>
                                        @endif

                                        <div class="discovery-tour-footer tour-card-footer">
                                            <div class="discovery-price-lockup tour-price-line">
                                                <span class="price-eyebrow">From</span>
                                                <div class="price-val-row">
                                                    <span class="price-amount">${{ number_format((float) $tour->price, 0) }}</span>
                                                    <span class="price-suffix">/ guest</span>
                                                </div>
                                            </div>

                                            <a href="{{ route('tours.show', $tour) }}" class="btn btn-outline-soft btn-tour-book">
                                                <span>Book Now</span>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="tours-pagination-box">
                            {{ $tours->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
