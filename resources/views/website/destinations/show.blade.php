@extends('layouts.website')

@section('title', $destination->name.' — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    {{-- 1. Premium Editorial Hero Banner --}}
    <section class="page-banner dest-detail-hero-banner">
        <div class="container">
            <div class="dest-detail-banner-content">
                <span class="dest-detail-eyebrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                    Curated Destination Dossier
                </span>
                <h1 class="page-banner-title dest-detail-hero-title">{{ $destination->name }}</h1>
                <div class="breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('destinations.index') }}">Destinations</a>
                    <span>&rsaquo;</span>
                    <span>{{ $destination->name }}</span>
                </div>

                <div class="dest-detail-stats-ribbon">
                    @if ($destination->country)
                        <div class="dest-stat-pill">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>{{ $destination->country }}</span>
                        </div>
                    @endif
                    <div class="dest-stat-pill">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <span>{{ $destination->displayListingsCount() }}</span>
                    </div>
                    <div class="dest-stat-pill">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span>Verified Native Leaders</span>
                    </div>
                    <div class="dest-stat-pill">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Bespoke Pacing</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. Destination Dossier Overview Section --}}
    <section class="page-section dest-dossier-section">
        <div class="container">
            <div class="dest-dossier-card">
                <div class="dest-dossier-media">
                    <img src="{{ $destination->imageUrl() }}" alt="{{ $destination->name }}" loading="lazy">
                    <div class="dest-dossier-badge">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="13" height="13"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <span>Premier Destination</span>
                    </div>
                </div>

                <div class="dest-dossier-body">
                    <div class="dest-dossier-meta">
                        <span class="section-script">Destination Guide</span>
                        <h2 class="dest-dossier-title">Explore {{ $destination->name }}</h2>
                        <div class="dest-dossier-tag-list">
                            @if ($destination->country)
                                <span class="dest-dossier-tag">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    {{ $destination->country }}
                                </span>
                            @endif
                            <span class="dest-dossier-tag dest-dossier-tag--count">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                {{ $destination->displayListingsCount() }}
                            </span>
                        </div>
                    </div>

                    @if ($destination->description)
                        <div class="dest-dossier-desc">
                            <p>{{ $destination->description }}</p>
                        </div>
                    @endif

                    <div class="dest-dossier-perks-grid">
                        <div class="dest-perk-box">
                            <div class="dest-perk-box-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </div>
                            <div class="dest-perk-box-text">
                                <strong>Certified Guides</strong>
                                <span>Native Expedition Leaders</span>
                            </div>
                        </div>

                        <div class="dest-perk-box">
                            <div class="dest-perk-box-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                            <div class="dest-perk-box-text">
                                <strong>Custom Pacing</strong>
                                <span>Flexible Journeys &amp; Stays</span>
                            </div>
                        </div>

                        <div class="dest-perk-box">
                            <div class="dest-perk-box-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            </div>
                            <div class="dest-perk-box-text">
                                <strong>Bespoke Planning</strong>
                                <span>Tailored Custom Itineraries</span>
                            </div>
                        </div>
                    </div>

                    <div class="dest-dossier-actions">
                        <a href="#availableTours" class="btn btn-cyan dest-dossier-primary-btn">
                            <span>Browse Available Tours</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </a>
                        <a href="{{ route('contact.create') }}" class="btn btn-outline-soft dest-dossier-secondary-btn">
                            <span>Request Custom Itinerary</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Available Handcrafted Tours in Destination --}}
    <section class="page-section dest-tours-section" id="availableTours">
        <div class="container">
            <div class="dest-tours-header">
                <div class="dest-tours-heading-wrap">
                    <span class="section-script">Handcrafted Experiences</span>
                    <h2 class="section-title">Available Tours in {{ $destination->name }}</h2>
                </div>
                <div class="dest-tours-count-badge">
                    <span>{{ $tours->total() }} Curated {{ \Illuminate\Support\Str::plural('Journey', $tours->total()) }}</span>
                </div>
            </div>

            @if ($tours->isEmpty())
                <div class="empty-state dest-empty-state">
                    <div class="empty-state-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="48" height="48"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                    </div>
                    <h3>No active packages in {{ $destination->name }} yet.</h3>
                    <p>Browse our full collection of curated global tours or ask our travel specialists for a tailored itinerary.</p>
                    <div class="dest-empty-actions">
                        <a href="{{ route('tours.index') }}" class="btn btn-cyan">
                            <span>Browse All Tours</span>
                        </a>
                        <a href="{{ route('contact.create') }}" class="btn btn-outline-soft">
                            <span>Request Bespoke Itinerary</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="dest-tours-grid">
                    @foreach ($tours as $tour)
                        <article class="dest-tour-card">
                            <a href="{{ route('tours.show', $tour) }}" class="dest-tour-thumb" aria-label="{{ $tour->title }}">
                                <img src="{{ $tour->imageUrl() }}" alt="{{ $tour->title }}" loading="lazy">
                                <div class="dest-tour-thumb-overlay"></div>
                                <div class="dest-tour-thumb-badges">
                                    <span class="dest-tour-duration-badge">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        {{ $tour->displayDuration() }}
                                    </span>
                                    @if ($tour->category)
                                        <span class="dest-tour-category-badge">{{ $tour->category->name }}</span>
                                    @endif
                                </div>
                            </a>

                            <div class="dest-tour-body">
                                <div class="dest-tour-meta">
                                    <span class="dest-tour-duration">{{ $tour->displayDuration() }}</span>
                                    @if ($tour->category)
                                        <span class="dest-tour-sep">&bull;</span>
                                        <span class="dest-tour-category">{{ $tour->category->name }}</span>
                                    @endif
                                </div>

                                <h3 class="dest-tour-title">
                                    <a href="{{ route('tours.show', $tour) }}">{{ $tour->title }}</a>
                                </h3>

                                <div class="dest-tour-price-line">
                                    <span class="dest-tour-price-from">From</span>
                                    <strong class="dest-tour-price-amount">₹{{ number_format((float) $tour->price, 2) }}</strong>
                                    <span class="dest-tour-price-unit">/ Person</span>
                                </div>

                                <div class="dest-tour-footer">
                                    <a href="{{ route('tours.show', $tour) }}" class="btn btn-cyan dest-tour-book-btn">
                                        <span>Book Now</span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="dest-pagination-wrap">
                    {{ $tours->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
