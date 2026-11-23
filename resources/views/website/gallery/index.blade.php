@extends('layouts.website')

@section('title', 'Editorial Photography & Gallery — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    {{-- Editorial Hero Banner --}}
    <section class="page-banner gallery-hero-banner">
        <div class="container">
            <div class="gallery-banner-content">
                <span class="gallery-eyebrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                    Curated Visual Chronicles
                </span>
                <h1 class="page-banner-title">Moments &amp; Horizons Across The Globe</h1>
                <p class="gallery-banner-desc">An intimate visual anthology of turquoise atolls, dramatic alpine ridges, and sun-drenched coastal vistas captured on expedition by our travelers and resident guides.</p>
                <div class="breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span>&rsaquo;</span>
                    <span>Gallery</span>
                </div>

                <div class="gallery-banner-meta-strip">
                    <div class="gallery-meta-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <span>4K Ultra-Definition</span>
                    </div>
                    <div class="gallery-meta-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                        <span>Authentic Field Captures</span>
                    </div>
                    <div class="gallery-meta-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>5 Global Continents</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Editorial Photo Showcase Section --}}
    <section class="page-section gallery-showcase-section">
        <div class="container">
            {{-- Category Navigation & Filter Bar --}}
            <div class="gallery-nav-bar">
                <div class="gallery-category-tabs" role="tablist" aria-label="Photo regions">
                    <a href="{{ route('gallery.index') }}"
                       data-gallery-filter="all"
                       class="gallery-filter-pill {{ empty($selectedCategory) || strtolower($selectedCategory) === 'all' ? 'is-active' : '' }}"
                       role="tab"
                       aria-selected="{{ empty($selectedCategory) || strtolower($selectedCategory) === 'all' ? 'true' : 'false' }}">
                        <span>All Captures</span>
                        <span class="gallery-pill-count">{{ $totalItemsCount }}</span>
                    </a>

                    @foreach ($categories as $cat)
                        @php
                            $isActiveCat = strtolower($selectedCategory) === strtolower($cat);
                        @endphp
                        <a href="{{ route('gallery.index', ['category' => $cat]) }}"
                           data-gallery-filter="{{ strtolower($cat) }}"
                           class="gallery-filter-pill {{ $isActiveCat ? 'is-active' : '' }}"
                           role="tab"
                           aria-selected="{{ $isActiveCat ? 'true' : 'false' }}">
                            <span>{{ $cat }}</span>
                        </a>
                    @endforeach
                </div>

                <div class="gallery-nav-hint">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                    <span>Click any photograph to view in full resolution</span>
                </div>
            </div>

            {{-- Empty State (Server Side) --}}
            @if ($galleryItems->isEmpty())
                <div class="empty-state gallery-empty-state">
                    <div class="empty-state-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="48" height="48">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                    </div>
                    <h3>No photographs in this collection</h3>
                    <p>We couldn't find any captures matching "{{ $selectedCategory }}". Explore our full anthology of destinations.</p>
                    <a href="{{ route('gallery.index') }}" class="btn btn-cyan">
                        <span>View All Captures</span>
                    </a>
                </div>
            @else
                {{-- Interactive Client-Side Empty State Alert (Hidden by default) --}}
                <div class="empty-state gallery-empty-state" id="galleryEmptyNotice" style="display: none;">
                    <div class="empty-state-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="48" height="48">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </div>
                    <h3>No captures found</h3>
                    <p>No photographs match the active filter. Select "All Captures" to display every photograph.</p>
                    <a href="{{ route('gallery.index') }}" class="btn btn-cyan" data-gallery-filter="all">
                        <span>Reset Filters</span>
                    </a>
                </div>

                {{-- Asymmetric Editorial Masonry Showcase --}}
                <div class="photo-editorial-masonry" id="photoEditorialMasonry">
                    @foreach ($galleryItems as $index => $item)
                        @php
                            // Extract country or primary region keyword for categorization
                            $locParts = explode(',', $item->location);
                            $regionName = trim(end($locParts));

                            // Modifiers to give varied editorial proportions
                            $cardModifiers = [
                                'photo-card--tall',       // 3:4 portrait
                                'photo-card--wide',       // 16:10 landscape
                                'photo-card--vertical',   // 4:5 vertical
                                'photo-card--cinema',     // 16:9 cinema
                                'photo-card--square',     // 1:1 square
                                'photo-card--tall',       // 3:4 portrait
                                'photo-card--panoramic',  // 16:10 panoramic
                            ];
                            $proportionClass = $cardModifiers[$index % count($cardModifiers)];
                        @endphp

                        <article class="photo-masonry-card {{ $proportionClass }}"
                                 tabindex="0"
                                 role="button"
                                 aria-label="View {{ $item->title }} in high-resolution preview"
                                 data-lightbox-src="{{ $item->imageUrl() }}"
                                 data-lightbox-title="{{ $item->title }}"
                                 data-lightbox-location="{{ $item->location }}"
                                 data-photo-category="{{ strtolower($regionName) }}"
                                 data-photo-location="{{ strtolower($item->location) }}"
                                 data-photo-title="{{ strtolower($item->title) }}">

                            <div class="photo-card-media">
                                <img src="{{ $item->imageUrl() }}"
                                     alt="{{ $item->title }} &mdash; {{ $item->location }}"
                                     loading="lazy"
                                     class="photo-card-img">

                                <div class="photo-card-overlay"></div>

                                {{-- Top Meta Strip --}}
                                <div class="photo-card-top">
                                    <span class="photo-badge-location">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="12" height="12">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                            <circle cx="12" cy="10" r="3"/>
                                        </svg>
                                        <span>{{ $item->location }}</span>
                                    </span>

                                    <span class="photo-zoom-button" aria-hidden="true" title="Open preview">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="14" height="14">
                                            <polyline points="15 3 21 3 21 9"/>
                                            <polyline points="9 21 3 21 3 15"/>
                                            <line x1="21" y1="3" x2="14" y2="10"/>
                                            <line x1="3" y1="21" x2="10" y2="14"/>
                                        </svg>
                                    </span>
                                </div>

                                {{-- Bottom Editorial Details --}}
                                <div class="photo-card-bottom">
                                    <div class="photo-card-caption">
                                        <span class="photo-index-number">Plate {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                        <h2 class="photo-card-title">{{ $item->title }}</h2>
                                    </div>

                                    <div class="photo-card-action">
                                        <span class="photo-action-hint">
                                            <span>Full Resolution</span>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="12" height="12">
                                                <line x1="5" y1="12" x2="19" y2="12"/>
                                                <polyline points="12 5 19 12 12 19"/>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            {{-- Bottom Editorial Inspiration Banner --}}
            <div class="gallery-experience-cta">
                <div class="gallery-cta-content">
                    <span class="gallery-cta-eyebrow">Experience It In Reality</span>
                    <h3 class="gallery-cta-heading">Turn These Horizons Into Your Next Journey</h3>
                    <p class="gallery-cta-text">Each photograph marks a genuine destination on our curated expedition itineraries. Let our travel specialists craft your bespoke passage.</p>
                </div>
                <div class="gallery-cta-actions">
                    <a href="{{ route('tours.index') }}" class="btn btn-cyan gallery-cta-primary">
                        <span>Explore Tour Packages</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="15" height="15"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                    <a href="{{ route('destinations.index') }}" class="btn btn-outline-white gallery-cta-secondary">
                        <span>Browse Destinations</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
