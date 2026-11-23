@extends('layouts.website')

@section('title', 'Destinations — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    <section class="page-banner destinations-hero-banner">
        <div class="container">
            <div class="destinations-banner-content">
                <span class="destinations-eyebrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                    Curated Global Sanctuaries
                </span>
                <h1 class="page-banner-title">World Destinations &amp; Odysseys</h1>
                <p class="destinations-banner-desc">From crystalline turquoise lagoons and secluded coral atolls to dramatic alpine summits and sun-drenched coastal cliffs, discover our handcrafted travel sanctuaries.</p>
                <div class="breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span>&rsaquo;</span>
                    <span>Destinations</span>
                </div>
            </div>
        </div>
    </section>

    @php
        $hasSearch = !empty($filters['search']);
        $isPopularFilter = ($filters['filter'] === 'popular');
    @endphp

    <section class="page-section destinations-discovery-section">
        <div class="container">
            {{-- Interactive Discovery & Navigation Toolbar --}}
            <div class="destinations-toolbar">
                <div class="destinations-filter-tabs">
                    <a href="{{ route('destinations.index') }}" class="dest-filter-tab {{ !$isPopularFilter ? 'is-active' : '' }}">
                        <span>All Destinations</span>
                        <span class="dest-tab-count">{{ $destinations->count() }}</span>
                    </a>
                    <a href="{{ route('destinations.index', ['filter' => 'popular']) }}" class="dest-filter-tab {{ $isPopularFilter ? 'is-active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="13" height="13"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <span>Top Destinations</span>
                    </a>
                </div>

                <form action="{{ route('destinations.index') }}" method="GET" class="dest-search-bar">
                    @if ($isPopularFilter)
                        <input type="hidden" name="filter" value="popular">
                    @endif
                    <div class="dest-search-input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" name="search" value="{{ $filters['search'] }}" class="dest-search-input" placeholder="Search destination or country...">
                        @if ($hasSearch)
                            <a href="{{ route('destinations.index', $isPopularFilter ? ['filter' => 'popular'] : []) }}" class="dest-search-clear" title="Clear search">&times;</a>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-cyan dest-search-submit" aria-label="Search destinations">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </form>
            </div>

            @if ($destinations->isEmpty())
                <div class="empty-state dest-empty-state">
                    <div class="empty-state-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="48" height="48"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                    </div>
                    <h3>No destinations matched your search</h3>
                    <p>We couldn't find any travel sanctuaries matching "{{ $filters['search'] }}". Try searching for another country or clear your search.</p>
                    <a href="{{ route('destinations.index') }}" class="btn btn-cyan">
                        <span>View All Destinations</span>
                    </a>
                </div>
            @else
                {{-- 1. Visually Prominent Featured Destination Spotlight --}}
                @if ($featuredDestination && !$hasSearch)
                    @php
                        $remainingDestinations = $destinations->reject(fn ($d) => $d->id === $featuredDestination->id);
                    @endphp

                    <div class="dest-spotlight-wrapper">
                        <div class="dest-spotlight-card">
                            <a href="{{ route('destinations.show', $featuredDestination) }}" class="dest-spotlight-media" aria-label="{{ $featuredDestination->name }}">
                                <img src="{{ $featuredDestination->imageUrl() }}" alt="{{ $featuredDestination->name }}" loading="lazy">
                                <div class="dest-spotlight-badges">
                                    <span class="spotlight-badge spotlight-badge--lead">
                                        <svg viewBox="0 0 24 24" fill="currentColor" width="12" height="12"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        Featured Sanctuary
                                    </span>
                                    @if ($featuredDestination->country)
                                        <span class="spotlight-badge spotlight-badge--country">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                            {{ $featuredDestination->country }}
                                        </span>
                                    @endif
                                </div>
                                <div class="dest-spotlight-listings-tag">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    <span>{{ $featuredDestination->displayListingsCount() }}</span>
                                </div>
                            </a>

                            <div class="dest-spotlight-content">
                                <div class="dest-spotlight-header">
                                    <span class="dest-spotlight-kicker">Premier Destination Guide</span>
                                    <h2 class="dest-spotlight-title">
                                        <a href="{{ route('destinations.show', $featuredDestination) }}">{{ $featuredDestination->name }}</a>
                                    </h2>
                                    @if ($featuredDestination->description)
                                        <p class="dest-spotlight-desc">{{ $featuredDestination->description }}</p>
                                    @endif
                                </div>

                                <div class="dest-spotlight-perks">
                                    <div class="dest-perk-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                        <div>
                                            <strong>{{ $featuredDestination->displayListingsCount() }}</strong>
                                            <span>Curated Journeys</span>
                                        </div>
                                    </div>
                                    <div class="dest-perk-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        <div>
                                            <strong>Expert Guides</strong>
                                            <span>Native Expedition Leaders</span>
                                        </div>
                                    </div>
                                    <div class="dest-perk-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <div>
                                            <strong>Tailored Escapes</strong>
                                            <span>Bespoke Custom Planning</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="dest-spotlight-actions">
                                    <a href="{{ route('destinations.show', $featuredDestination) }}" class="btn btn-cyan dest-spotlight-primary-btn">
                                        <span>Explore {{ $featuredDestination->name }}</span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                    </a>
                                    <a href="{{ route('tours.index', ['destination' => $featuredDestination->slug]) }}" class="btn btn-outline-soft dest-spotlight-secondary-btn">
                                        <span>Browse Available Tours</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @php
                        $remainingDestinations = $destinations;
                    @endphp
                @endif

                {{-- 2. Creative Asymmetrical Destination Mosaic Grid --}}
                @if ($remainingDestinations->isNotEmpty())
                    <div class="dest-mosaic-header">
                        <div class="dest-mosaic-heading-group">
                            <span class="section-script">Curated Escapes</span>
                            <h2 class="section-title">All Destination Collections</h2>
                        </div>
                        <span class="dest-mosaic-count-badge">{{ $remainingDestinations->count() }} Regions Available</span>
                    </div>

                    <div class="destinations-mosaic-grid">
                        @foreach ($remainingDestinations as $index => $destination)
                            @php
                                $cardVariant = $index % 5;
                            @endphp

                            <article class="dest-mosaic-card dest-mosaic-card--variant-{{ $cardVariant }}">
                                <a href="{{ route('destinations.show', $destination) }}" class="dest-mosaic-thumb" aria-label="Explore {{ $destination->name }}">
                                    <img src="{{ $destination->imageUrl() }}" alt="{{ $destination->name }}" loading="lazy">
                                    <div class="dest-mosaic-overlay"></div>

                                    <div class="dest-mosaic-badges">
                                        @if ($destination->country)
                                            <span class="dest-mosaic-pill dest-mosaic-pill--country">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11" height="11"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                                {{ $destination->country }}
                                            </span>
                                        @endif
                                        @if ($destination->is_popular)
                                            <span class="dest-mosaic-pill dest-mosaic-pill--popular">
                                                <svg viewBox="0 0 24 24" fill="currentColor" width="11" height="11"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                                Popular
                                            </span>
                                        @endif
                                    </div>

                                    <div class="dest-mosaic-listings-capsule">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        <span>{{ $destination->displayListingsCount() }}</span>
                                    </div>
                                </a>

                                <div class="dest-mosaic-body">
                                    <div class="dest-mosaic-meta">
                                        <span class="dest-mosaic-index">Region {{ sprintf('%02d', $index + 1) }}</span>
                                    </div>

                                    <h3 class="dest-mosaic-title">
                                        <a href="{{ route('destinations.show', $destination) }}">{{ $destination->name }}</a>
                                    </h3>

                                    @if ($destination->description)
                                        <p class="dest-mosaic-desc">{{ \Illuminate\Support\Str::limit($destination->description, 110) }}</p>
                                    @endif

                                    <div class="dest-mosaic-footer">
                                        <a href="{{ route('destinations.show', $destination) }}" class="dest-mosaic-link">
                                            <span>Explore {{ $destination->name }}</span>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                        </a>
                                        <a href="{{ route('tours.index', ['destination' => $destination->slug]) }}" class="dest-mosaic-tours-btn" title="View all tours in {{ $destination->name }}">
                                            <span>{{ $destination->displayListingsCount() }}</span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection
