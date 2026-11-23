@extends('layouts.website')

@section('content')
    @php
        $heroBg = \App\Support\MediaUrl::resolve($siteSettings['hero_image'] ?? null, '/images/seed/hero-coast.jpg');
        $aboutImg1 = \App\Support\MediaUrl::resolve($siteSettings['about_image_1'] ?? null, '/images/seed/tour-switzerland.jpg');
        $aboutImg2 = \App\Support\MediaUrl::resolve($siteSettings['about_image_2'] ?? null, '/images/seed/tour-tropical.jpg');
        $aboutImg3 = \App\Support\MediaUrl::resolve($siteSettings['about_image_3'] ?? null, '/images/seed/cat-hiking.jpg');
    @endphp

    <section class="hero-section" style="background-image: url('{{ $heroBg }}');">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content">
                <span class="hero-script">{{ $siteSettings['hero_subtitle'] ?? 'Get unforgettable pleasure with us' }}</span>
                <h1 class="hero-title">{{ $siteSettings['hero_title'] ?? 'Natural Wonder of the world' }}</h1>
                <div class="hero-actions">
                    <a href="{{ route('tours.index') }}" class="btn btn-cyan">
                        <span>{{ $siteSettings['hero_primary_cta'] ?? 'Explore Tours' }}</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                    <a href="#plan-trip" class="btn btn-outline-white">
                        <span>{{ $siteSettings['hero_secondary_cta'] ?? 'Our Services' }}</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="hero-side-dots" aria-hidden="true">
            <span class="hero-side-dot is-active"></span>
            <span class="hero-side-dot"></span>
            <span class="hero-side-dot"></span>
        </div>
    </section>

    <div class="hero-search-wrap">
        <div class="container">
            <form action="{{ route('tours.index') }}" method="GET" class="hero-search-bar">
                <div class="search-field">
                    <span class="search-field-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </span>
                    <div class="search-field-body">
                        <label for="hero-destination" class="search-field-label">Destination</label>
                        <select id="hero-destination" name="destination">
                            <option value="">All Destinations</option>
                            @foreach ($allDestinations as $dest)
                                <option value="{{ $dest->slug }}">{{ $dest->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="search-field">
                    <span class="search-field-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </span>
                    <div class="search-field-body">
                        <label for="hero-category" class="search-field-label">Activity</label>
                        <select id="hero-category" name="category">
                            <option value="">All Activities</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="search-field">
                    <span class="search-field-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </span>
                    <div class="search-field-body">
                        <label for="hero-duration" class="search-field-label">Duration</label>
                        <select id="hero-duration" name="duration">
                            <option value="">Any Duration</option>
                            <option value="short">1 - 3 Days</option>
                            <option value="medium">4 - 6 Days</option>
                            <option value="long">7+ Days</option>
                        </select>
                    </div>
                </div>

                <div class="search-field">
                    <span class="search-field-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </span>
                    <div class="search-field-body">
                        <label for="hero-budget" class="search-field-label">Max Budget</label>
                        <select id="hero-budget" name="max_price">
                            <option value="">Any Budget</option>
                            <option value="600">Up to $600</option>
                            <option value="950">Up to $950</option>
                            <option value="1300">Up to $1,300</option>
                            <option value="2000">Up to $2,000</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-cyan hero-search-submit">Search</button>
            </form>
        </div>
    </div>

    <section class="categories-section">
        <div class="container">
            <div class="section-head">
                <span class="section-script">Wonderful Place For You</span>
                <h2 class="section-title">Tour Categories</h2>
            </div>

            @if ($categories->isNotEmpty())
                <div class="categories-fan" id="categoryFan">
                    @foreach ($categories as $category)
                        <a href="{{ route('tours.index', ['category' => $category->slug]) }}"
                           class="category-card {{ $loop->index >= 5 ? 'is-hidden-slide' : '' }}"
                           data-fan-pos="{{ $loop->index % 5 }}">
                            <div class="category-thumb">
                                <img src="{{ $category->imageUrl() }}" alt="{{ $category->name }}" loading="lazy">
                            </div>
                            <h3 class="category-name">{{ $category->name }}</h3>
                            <span class="category-link">See More</span>
                        </a>
                    @endforeach
                </div>
                <div class="carousel-dots" id="categoryDots"></div>
            @endif
        </div>
    </section>

    <section class="destinations-section" id="destinations">
        <div class="container">
            <div class="section-head">
                <span class="section-script">Top Destination</span>
                <h2 class="section-title">Popular Destination</h2>
            </div>

            @if ($popularDestinations->isNotEmpty())
                <div class="dest-carousel-wrap">
                    <button type="button" class="dest-nav-btn dest-nav-prev" id="destPrev" aria-label="Previous destination">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>

                    <div class="dest-stage" id="destStage">
                        @foreach ($popularDestinations as $destination)
                            <div class="dest-card" data-pos="hidden">
                                <img src="{{ $destination->imageUrl() }}" alt="{{ $destination->name }}" loading="lazy">
                                <div class="dest-card-overlay">
                                    <div>
                                        <h3 class="dest-card-title">{{ $destination->name }}</h3>
                                        <div class="dest-card-count">{{ $destination->displayListingsCount() }}</div>
                                    </div>
                                    <a href="{{ route('destinations.show', $destination) }}" class="btn btn-outline-white dest-card-btn">
                                        <span>View All</span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="dest-nav-btn dest-nav-next" id="destNext" aria-label="Next destination">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            @endif
        </div>
    </section>

    <section class="about-section" id="plan-trip">
        <div class="container about-grid">
            <div class="about-collage-wrap">
                <div class="about-collage">
                    <div class="about-photo-tall">
                        <img src="{{ $aboutImg1 }}" alt="Scenic alpine exploration" loading="lazy">
                    </div>
                    <div class="about-collage-stack">
                        <div class="about-photo-top">
                            <img src="{{ $aboutImg2 }}" alt="Lagoon boat voyage" loading="lazy">
                        </div>
                        <div class="about-photo-bottom">
                            <img src="{{ $aboutImg3 }}" alt="Mountain trail adventure" loading="lazy">
                        </div>
                    </div>
                </div>
                <div class="about-floating-badge">
                    <strong>{{ $siteSettings['stat_1_value'] ?? '12' }}+</strong>
                    <span>Years Crafting Journeys</span>
                </div>
            </div>

            <div class="about-content">
                <span class="section-script">{{ $siteSettings['about_subtitle'] ?? "Let's Go Together" }}</span>
                <h2 class="section-title">{{ $siteSettings['about_title'] ?? 'Plan Your Trip With Us' }}</h2>
                <p class="about-lead">{{ $siteSettings['about_description'] ?? '' }}</p>

                <div class="feature-list">
                    <div class="feature-item">
                        <span class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                        </span>
                        <div>
                            <h3 class="feature-title">{{ $siteSettings['about_feature_1_title'] ?? 'Exclusive Trip' }}</h3>
                            <p class="feature-text">{{ $siteSettings['about_feature_1_text'] ?? '' }}</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <span class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </span>
                        <div>
                            <h3 class="feature-title">{{ $siteSettings['about_feature_2_title'] ?? 'Professional Guide' }}</h3>
                            <p class="feature-text">{{ $siteSettings['about_feature_2_text'] ?? '' }}</p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('tours.index') }}" class="btn btn-dark">
                    <span>Learn More</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        </div>
    </section>

    <section class="tours-section">
        <div class="tours-shell">
            <div class="tours-backdrop">
                <div class="section-head">
                    <span class="section-script">Best Place For You</span>
                    <h2 class="section-title">Popular Tour Packages</h2>
                </div>
            </div>
        </div>

        @if ($featuredTours->isNotEmpty())
            <div class="tours-grid-pull">
                <div class="tours-grid">
                    @foreach ($featuredTours->take(4) as $tour)
                        <article class="tour-card">
                            <a href="{{ route('tours.show', $tour) }}" class="tour-card-thumb">
                                <img src="{{ $tour->imageUrl() }}" alt="{{ $tour->title }}" loading="lazy">
                                @if ($tour->destination)
                                    <span class="tour-card-badge">{{ $tour->destination->name }}</span>
                                @endif
                                @if ($tour->tourType)
                                    <span class="tour-card-type-badge">{{ $tour->tourType->name }}</span>
                                @endif
                            </a>
                            <div class="tour-card-body">
                                <h3 class="tour-card-title">
                                    <a href="{{ route('tours.show', $tour) }}">{{ $tour->title }}</a>
                                </h3>
                                <div class="tour-rating">
                                    <span class="stars-row" aria-label="{{ $tour->rating }} out of 5 stars">
                                        @for ($i = 0; $i < 5; $i++)
                                            <svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        @endfor
                                    </span>
                                    <span>({{ number_format((float) $tour->rating, 1) }} Rating)</span>
                                </div>
                                <div class="tour-price-line">
                                    ${{ number_format((float) $tour->price, 2) }} <span>/Person</span>
                                </div>
                                <div class="tour-card-footer">
                                    <span class="tour-duration">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <span>{{ $tour->displayDuration() }}</span>
                                    </span>
                                    <a href="{{ route('tours.show', $tour) }}" class="btn btn-outline-soft">
                                        <span>Book Now</span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="carousel-dots tours-dots" aria-hidden="true">
                    <span class="carousel-dot is-active"></span>
                    <span class="carousel-dot"></span>
                    <span class="carousel-dot"></span>
                </div>

                <div class="tours-view-all">
                    <a href="{{ route('tours.index') }}" class="btn btn-cyan">
                        <span>Explore All Packages</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </div>
        @endif
    </section>

    <section class="gallery-section" id="gallery">
        <div class="container">
            <div class="section-head">
                <span class="section-script">Make Your Tour More Pleasure</span>
                <h2 class="section-title">Recent Gallery</h2>
            </div>

            @if ($galleryItems->isNotEmpty())
                <div class="gallery-bento">
                    @isset($galleryItems[0])
                        <button type="button" class="gallery-card gallery-card-side"
                                data-lightbox-src="{{ $galleryItems[0]->imageUrl() }}"
                                data-lightbox-title="{{ $galleryItems[0]->title }}"
                                data-lightbox-location="{{ $galleryItems[0]->location }}">
                            <img src="{{ $galleryItems[0]->imageUrl() }}" alt="{{ $galleryItems[0]->title }}" loading="lazy">
                            <span class="gallery-card-hover">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            </span>
                        </button>
                    @endisset

                    @if (isset($galleryItems[1]) || isset($galleryItems[2]))
                        <div class="gallery-col-stack">
                            @isset($galleryItems[1])
                                <button type="button" class="gallery-card gallery-card-small"
                                        data-lightbox-src="{{ $galleryItems[1]->imageUrl() }}"
                                        data-lightbox-title="{{ $galleryItems[1]->title }}"
                                        data-lightbox-location="{{ $galleryItems[1]->location }}">
                                    <img src="{{ $galleryItems[1]->imageUrl() }}" alt="{{ $galleryItems[1]->title }}" loading="lazy">
                                    <span class="gallery-card-hover">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    </span>
                                </button>
                            @endisset
                            @isset($galleryItems[2])
                                <button type="button" class="gallery-card gallery-card-small"
                                        data-lightbox-src="{{ $galleryItems[2]->imageUrl() }}"
                                        data-lightbox-title="{{ $galleryItems[2]->title }}"
                                        data-lightbox-location="{{ $galleryItems[2]->location }}">
                                    <img src="{{ $galleryItems[2]->imageUrl() }}" alt="{{ $galleryItems[2]->title }}" loading="lazy">
                                    <span class="gallery-card-hover">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    </span>
                                </button>
                            @endisset
                        </div>
                    @endif

                    @isset($galleryItems[3])
                        <button type="button" class="gallery-card gallery-card-center"
                                data-lightbox-src="{{ $galleryItems[3]->imageUrl() }}"
                                data-lightbox-title="{{ $galleryItems[3]->title }}"
                                data-lightbox-location="{{ $galleryItems[3]->location }}">
                            <img src="{{ $galleryItems[3]->imageUrl() }}" alt="{{ $galleryItems[3]->title }}" loading="lazy">
                            <span class="gallery-card-hover">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            </span>
                        </button>
                    @endisset

                    @if (isset($galleryItems[4]) || isset($galleryItems[5]))
                        <div class="gallery-col-stack">
                            @isset($galleryItems[4])
                                <button type="button" class="gallery-card gallery-card-small"
                                        data-lightbox-src="{{ $galleryItems[4]->imageUrl() }}"
                                        data-lightbox-title="{{ $galleryItems[4]->title }}"
                                        data-lightbox-location="{{ $galleryItems[4]->location }}">
                                    <img src="{{ $galleryItems[4]->imageUrl() }}" alt="{{ $galleryItems[4]->title }}" loading="lazy">
                                    <span class="gallery-card-hover">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    </span>
                                </button>
                            @endisset
                            @isset($galleryItems[5])
                                <button type="button" class="gallery-card gallery-card-small"
                                        data-lightbox-src="{{ $galleryItems[5]->imageUrl() }}"
                                        data-lightbox-title="{{ $galleryItems[5]->title }}"
                                        data-lightbox-location="{{ $galleryItems[5]->location }}">
                                    <img src="{{ $galleryItems[5]->imageUrl() }}" alt="{{ $galleryItems[5]->title }}" loading="lazy">
                                    <span class="gallery-card-hover">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    </span>
                                </button>
                            @endisset
                        </div>
                    @endif

                    @isset($galleryItems[6])
                        <button type="button" class="gallery-card gallery-card-side"
                                data-lightbox-src="{{ $galleryItems[6]->imageUrl() }}"
                                data-lightbox-title="{{ $galleryItems[6]->title }}"
                                data-lightbox-location="{{ $galleryItems[6]->location }}">
                            <img src="{{ $galleryItems[6]->imageUrl() }}" alt="{{ $galleryItems[6]->title }}" loading="lazy">
                            <span class="gallery-card-hover">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            </span>
                        </button>
                    @endisset
                </div>
            @endif
        </div>
    </section>

    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                @for ($i = 1; $i <= 4; $i++)
                    <div class="stat-bubble">
                        <div class="stat-inner">
                            <div class="stat-value" data-stat-value="{{ $siteSettings['stat_'.$i.'_value'] ?? '' }}">
                                {{ $siteSettings['stat_'.$i.'_value'] ?? '' }}
                            </div>
                            <div class="stat-label">{{ $siteSettings['stat_'.$i.'_label'] ?? '' }}</div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </section>

    <section class="guides-section">
        <div class="container">
            <div class="section-head">
                <span class="section-script">Meet with Guide</span>
                <h2 class="section-title">Tour Guide</h2>
            </div>

            @if ($guides->isNotEmpty())
                <div class="guides-grid" id="guidesGrid">
                    @foreach ($guides as $guide)
                        <article class="guide-card {{ $loop->index >= 4 ? 'is-hidden-slide' : '' }}">
                            <div class="guide-avatar">
                                <img src="{{ $guide->photoUrl() }}" alt="{{ $guide->name }}" loading="lazy">
                            </div>
                            <div class="guide-panel">
                                <h3 class="guide-name">{{ $guide->name }}</h3>
                                <div class="guide-role">{{ $guide->role }}</div>
                                <div class="guide-socials">
                                    <a href="{{ $guide->facebook_url ?: '#facebook' }}" class="social-circle" aria-label="Facebook">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h1.5V2.14c-.326-.043-1.557-.14-2.857-.14C11.928 2 10 3.657 10 6.7v2.8H7v4h3V22h4v-8.5z"/></svg>
                                    </a>
                                    <a href="{{ $guide->twitter_url ?: '#twitter' }}" class="social-circle" aria-label="Twitter">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 5.92a8.24 8.24 0 0 1-2.36.65 4.12 4.12 0 0 0 1.81-2.27 8.22 8.22 0 0 1-2.61 1 4.1 4.1 0 0 0-7 3.74A11.65 11.65 0 0 1 3.39 4.7a4.1 4.1 0 0 0 1.27 5.48 4.07 4.07 0 0 1-1.86-.51v.05a4.11 4.11 0 0 0 3.29 4.02 4.1 4.1 0 0 1-1.85.07 4.11 4.11 0 0 0 3.83 2.85A8.23 8.23 0 0 1 2 18.39a11.62 11.62 0 0 0 6.29 1.84c7.55 0 11.67-6.25 11.67-11.67 0-.18 0-.35-.01-.53A8.35 8.35 0 0 0 22 5.92z"/></svg>
                                    </a>
                                    <a href="{{ $guide->instagram_url ?: '#instagram' }}" class="social-circle" aria-label="Instagram">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/></svg>
                                    </a>
                                    <a href="{{ $guide->linkedin_url ?: '#linkedin' }}" class="social-circle" aria-label="LinkedIn">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.94 5a2 2 0 1 1-4-.002 2 2 0 0 1 4 .002zM7 8.48H3V21h4V8.48zm6.32 0H9.34V21h3.94v-6.57c0-3.66 4.77-4 4.77 0V21H22v-7.93c0-6.17-7.06-5.94-8.72-2.91l.04-1.68z"/></svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="carousel-dots" id="guideDots"></div>
            @endif
        </div>
    </section>

    <section class="testimonials-section">
        <div class="container">
            <div class="section-head">
                <span class="section-script">Testimonial</span>
                <h2 class="section-title">What Client Say About us</h2>
            </div>

            @if ($testimonials->isNotEmpty())
                <div class="testimonials-grid" id="testimonialsGrid">
                    @foreach ($testimonials as $testimonial)
                        <article class="testimonial-card {{ $loop->index === 1 ? 'is-center-card' : '' }} {{ $loop->index >= 3 ? 'is-hidden-slide' : '' }}">
                            <div class="testimonial-head">
                                <div class="testimonial-author">
                                    <img src="{{ $testimonial->avatarUrl() }}" alt="{{ $testimonial->client_name }}" class="testimonial-avatar" loading="lazy">
                                    <div>
                                        <h3 class="testimonial-name">{{ $testimonial->client_name }}</h3>
                                        <div class="testimonial-role">{{ $testimonial->client_role }}</div>
                                    </div>
                                </div>
                                <span class="stars-row" aria-label="{{ $testimonial->rating }} stars">
                                    @for ($s = 0; $s < $testimonial->rating; $s++)
                                        <svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    @endfor
                                </span>
                            </div>
                            <p class="testimonial-quote">"{{ $testimonial->quote }}"</p>
                            <span class="testimonial-quote-badge" aria-hidden="true">&ldquo;</span>
                        </article>
                    @endforeach
                </div>
                <div class="carousel-dots" id="testimonialDots"></div>
            @endif
        </div>
    </section>

    <section class="partners-section" aria-label="Travel partners">
        <div class="container">
            <div class="partners-grid">
                <div class="partner-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 20l6-12 4 7 3-5 5 10H3z"/></svg>
                    <span>AlpinePeak</span>
                </div>
                <div class="partner-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 17l16 0M6 17L12 4l6 13M12 4v13"/></svg>
                    <span>CoralVoyage</span>
                </div>
                <div class="partner-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><polygon points="15.5 8.5 13 13 8.5 15.5 11 11 15.5 8.5"/></svg>
                    <span>Wanderlust</span>
                </div>
                <div class="partner-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 21v-9m0 0c-3-4-7-3-8 0 3 0 6 1 8 0zm0 0c3-4 7-3 8 0-3 0-6 1-8 0z"/><path d="M5 21h14"/></svg>
                    <span>PalmIsland</span>
                </div>
                <div class="partner-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="4"/><path d="M12 3v2m0 14v2M4.9 4.9l1.4 1.4m11.4 11.4l1.4 1.4M3 12h2m14 0h2"/></svg>
                    <span>SunTrail</span>
                </div>
                <div class="partner-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M22 2L11 13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    <span>SkyPass</span>
                </div>
                <div class="partner-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M2 14c2-2 4-2 6 0s4 2 6 0 4-2 6 0M2 19c2-2 4-2 6 0s4 2 6 0 4-2 6 0"/></svg>
                    <span>Coastline</span>
                </div>
                <div class="partner-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>ExploreCo</span>
                </div>
            </div>
        </div>
    </section>

    <section class="blog-section">
        <div class="container">
            <div class="section-head-split">
                <div class="section-head">
                    <span class="section-script">About Us</span>
                    <h2 class="section-title">News &amp; Articles From {{ $siteSettings['site_name'] ?? 'OmTravel' }}</h2>
                </div>
                <a href="{{ route('blog.index') }}" class="btn btn-outline-soft">
                    <span>See More Articles</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>

            @if ($blogPosts->isNotEmpty())
                <div class="blog-grid">
                    @foreach ($blogPosts as $post)
                        <article class="blog-card">
                            <a href="{{ route('blog.show', $post) }}" class="blog-thumb">
                                <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" loading="lazy">
                                @if ($post->category)
                                    <span class="blog-category-pill">{{ $post->category }}</span>
                                @endif
                            </a>
                            <div class="blog-meta">
                                <span>{{ ($post->published_at ?? $post->created_at)->format('F d, Y') }}</span>
                                <span class="blog-meta-sep"></span>
                                <span>{{ $post->read_time }}</span>
                            </div>
                            <h3 class="blog-title">
                                <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                            </h3>
                            @if ($post->excerpt)
                                <p class="blog-excerpt">{{ \Illuminate\Support\Str::limit($post->excerpt, 110) }}</p>
                            @endif
                            <div>
                                <a href="{{ route('blog.show', $post) }}" class="btn btn-outline-soft">
                                    <span>Read More</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
