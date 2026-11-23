@extends('layouts.website')

@section('content')
    @php
        $heroBg = \App\Support\MediaUrl::resolve($siteSettings['hero_image'] ?? null, '/images/seed/hero-coast.jpg');
        $aboutImg1 = \App\Support\MediaUrl::resolve($siteSettings['about_image_1'] ?? null, '/images/seed/tour-switzerland.jpg');
        $aboutImg2 = \App\Support\MediaUrl::resolve($siteSettings['about_image_2'] ?? null, '/images/seed/tour-tropical.jpg');
        $aboutImg3 = \App\Support\MediaUrl::resolve($siteSettings['about_image_3'] ?? null, '/images/seed/cat-hiking.jpg');

        $heroSlides = [
            [
                'image' => $heroBg,
                'badge' => 'Curated Luxury Journeys',
                'subtitle' => $siteSettings['hero_subtitle'] ?? 'Get unforgettable pleasure with us',
                'title' => $siteSettings['hero_title'] ?? 'Natural Wonder of the world',
                'description' => 'Embark on handpicked expeditions across iconic landscapes, pristine waters, and breathtaking wonders with dedicated specialists.',
                'primary_cta_text' => $siteSettings['hero_primary_cta'] ?? 'Explore Tours',
                'primary_cta_url' => route('tours.index'),
                'secondary_cta_text' => $siteSettings['hero_secondary_cta'] ?? 'Our Services',
                'secondary_cta_url' => '#plan-trip',
            ],
            [
                'image' => asset('images/seed/tour-switzerland.jpg'),
                'badge' => 'Alpine Adventures & Glaciers',
                'subtitle' => 'High Altitude Escapes',
                'title' => 'Majestic Mountain Peaks & Alpine Wonders',
                'description' => 'Experience panoramic Swiss summits, turquoise glacial lakes, and secluded chalets guided by seasoned alpine mountaineers.',
                'primary_cta_text' => 'View Mountain Tours',
                'primary_cta_url' => route('tours.index', ['category' => 'hiking']),
                'secondary_cta_text' => 'Popular Destinations',
                'secondary_cta_url' => '#destinations',
            ],
            [
                'image' => asset('images/seed/tour-greece.jpg'),
                'badge' => 'Mediterranean Odysseys',
                'subtitle' => 'Sun, Sea & Ancient Culture',
                'title' => 'Unwind Along Sun-Drenched Aegean Coastlines',
                'description' => 'Sail across whitewashed Greek archipelagos, golden beaches, and centuries of vibrant history with curated private departures.',
                'primary_cta_text' => 'Explore Coastlines',
                'primary_cta_url' => route('tours.index'),
                'secondary_cta_text' => 'View Photo Gallery',
                'secondary_cta_url' => '#gallery',
            ],
            [
                'image' => asset('images/seed/tour-tropical.jpg'),
                'badge' => 'Exotic Island Retreats',
                'subtitle' => 'Tropical Escapes & Lagoons',
                'title' => 'Bespoke Island Havens & Crystal Waters',
                'description' => 'Immerse in turquoise lagoons, coral reefs, and tranquil overwater villas designed for rejuvenation and authentic discovery.',
                'primary_cta_text' => 'Book Paradise',
                'primary_cta_url' => route('tours.index', ['category' => 'beach']),
                'secondary_cta_text' => 'Contact Specialists',
                'secondary_cta_url' => route('contact.create'),
            ],
        ];
    @endphp

    <section class="hero-section" id="heroSlider" aria-label="Hero Showcase" data-autoplay="true" data-interval="6000">
        <div class="hero-slides">
            @foreach ($heroSlides as $index => $slide)
                <div class="hero-slide {{ $loop->first ? 'is-active' : '' }}" data-slide-index="{{ $index }}" style="background-image: url('{{ $slide['image'] }}');">
                    <div class="hero-overlay"></div>
                    <div class="container hero-slide-container">
                        <div class="hero-content">
                            <div class="hero-kicker">
                                <span class="hero-kicker-line"></span>
                                <span class="hero-kicker-text">DISCOVER THE WORLD</span>
                            </div>
                            <span class="hero-script">{{ $slide['subtitle'] }}</span>
                            <h1 class="hero-title">{{ $slide['title'] }}</h1>
                            @if (!empty($slide['description']))
                                <p class="hero-desc">{{ $slide['description'] }}</p>
                            @endif
                            <div class="hero-actions">
                                <a href="{{ $slide['primary_cta_url'] }}" class="btn btn-cyan hero-btn-main">
                                    <span>{{ $slide['primary_cta_text'] }}</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                                <a href="{{ $slide['secondary_cta_url'] }}" class="btn btn-outline-white hero-btn-sub">
                                    <span>{{ $slide['secondary_cta_text'] }}</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Functional Navigation Arrows --}}
        <!-- <button type="button" class="hero-nav-arrow hero-nav-prev" id="heroPrev" aria-label="Previous slide">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <button type="button" class="hero-nav-arrow hero-nav-next" id="heroNext" aria-label="Next slide">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg>
        </button> -->

        {{-- Centered Pagination Dots Capsule --}}
        <div class="hero-dots-capsule" id="heroDots" role="tablist" aria-label="Hero slider pagination">
            @foreach ($heroSlides as $index => $slide)
                <button type="button"
                        class="hero-side-dot {{ $loop->first ? 'is-active' : '' }}"
                        data-slide-to="{{ $index }}"
                        role="tab"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                        aria-label="Go to slide {{ $index + 1 }}">
                </button>
            @endforeach
        </div>

        {{-- Subtle bottom curved transition to white background --}}
        <div class="hero-bottom-curve" aria-hidden="true">
            <svg viewBox="0 0 1440 36" preserveAspectRatio="none" fill="none">
                <path d="M0,0 C420,32 1020,32 1440,0 L1440,36 L0,36 Z" fill="#FFFFFF"/>
            </svg>
        </div>

        {{-- Slide Progress Bar --}}
        <div class="hero-progress-bar" aria-hidden="true">
            <div class="hero-progress-fill" id="heroProgressFill"></div>
        </div>
    </section>

    <div class="hero-search-wrap">
        <div class="container">
            <form action="{{ route('tours.index') }}" method="GET" class="hero-search-bar" id="heroSearchForm">
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

                <div class="search-field-divider" aria-hidden="true"></div>

                <div class="search-field">
                    <span class="search-field-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="4" r="2"/><path d="m15 7-3 3-3-3"/><path d="M9 13l-2 8"/><path d="M15 13l2 8"/><path d="M8 10h8"/></svg>
                    </span>
                    <div class="search-field-body">
                        <label for="hero-category" class="search-field-label">Trip Type</label>
                        <select id="hero-category" name="category">
                            <option value="">All Activities</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="search-field-divider" aria-hidden="true"></div>

                <div class="search-field">
                    <span class="search-field-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </span>
                    <div class="search-field-body">
                        <label for="hero-duration" class="search-field-label">When</label>
                        <select id="hero-duration" name="duration">
                            <option value="">Any Duration</option>
                            <option value="short">1 - 3 Days</option>
                            <option value="medium">4 - 6 Days</option>
                            <option value="long">7+ Days</option>
                        </select>
                    </div>
                </div>

                <div class="search-field-divider" aria-hidden="true"></div>

                <div class="search-field">
                    <span class="search-field-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </span>
                    <div class="search-field-body">
                        <label for="hero-budget" class="search-field-label">People</label>
                        <select id="hero-budget" name="max_price">
                            <option value="">1 Adult(s)</option>
                            <option value="600">2 Adult(s)</option>
                            <option value="950">3 Adult(s)</option>
                            <option value="1300">4+ Adult(s)</option>
                            <option value="2000">Family Group</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="hero-search-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <span>Search</span>
                </button>
            </form>
        </div>
    </div>

    <section class="categories-section" id="tourCategories" aria-label="Tour Categories">
        <div class="container">
            <div class="section-head-split categories-head">
                <div class="section-head-content">
                    <span class="section-script">Wonderful Place For You</span>
                    <h2 class="section-title">Tour Categories</h2>
                </div>
                <div class="categories-head-meta">
                    <div class="categories-count-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        <span>{{ $categories->count() }} Handcrafted Travel Styles</span>
                    </div>
                    <p class="categories-head-desc">
                        From deep ocean voyages and alpine ridge ascents to tranquil island lagoons, choose your signature adventure.
                    </p>
                </div>
            </div>

            @if ($categories->isNotEmpty())
                @php
                    $categoryMeta = [
                        'cruises' => [
                            'tag' => 'Maritime Journeys',
                            'desc' => 'Luxury ocean voyages, private catamarans & sunset sails across pristine coastal waters.',
                            'badge' => 'Featured Collection',
                        ],
                        'hiking' => [
                            'tag' => 'Alpine & Trails',
                            'desc' => 'Scenic glacial ridges, panoramic summits & expert-guided mountain trails.',
                            'badge' => 'Adventure',
                        ],
                        'snorkeling' => [
                            'tag' => 'Marine & Reefs',
                            'desc' => 'Vibrant coral reefs, turquoise atolls & unforgettable encounters with sea life.',
                            'badge' => 'Discovery',
                        ],
                        'wildlife' => [
                            'tag' => 'Nature & Safaris',
                            'desc' => 'Iconic wildlife reserves, bird habitats & untamed natural encounters.',
                            'badge' => 'Eco Tour',
                        ],
                        'beach' => [
                            'tag' => 'Coastal Escapes',
                            'desc' => 'Sun-drenched shores, secluded coves & tranquil oceanfront relaxation.',
                            'badge' => 'Relaxation',
                        ],
                        'island-hopping' => [
                            'tag' => 'Lagoon Odysseys',
                            'desc' => 'Emerald waters, secluded archipelagos & bespoke island excursions.',
                            'badge' => 'Island Life',
                        ],
                    ];
                @endphp

                <div class="editorial-categories-showcase" id="editorialCategories">
                    @foreach ($categories as $index => $category)
                        @php
                            $meta = $categoryMeta[$category->slug] ?? [
                                'tag' => 'Curated Travel',
                                'desc' => 'Immerse in unforgettable experiences crafted for discerning travelers.',
                                'badge' => 'Explore',
                            ];
                            $numFormatted = sprintf('%02d', $index + 1);
                            $toursCount = $category->tours_count ?? null;
                        @endphp

                        <a href="{{ route('tours.index', ['category' => $category->slug]) }}"
                           class="btn editorial-category-card"
                           data-category-slug="{{ $category->slug }}"
                           aria-label="Explore {{ $category->name }} Tours">
                            <div class="editorial-card-media">
                                <img src="{{ $category->imageUrl() }}" alt="{{ $category->name }}" loading="lazy" class="editorial-card-img">
                                <div class="editorial-card-overlay"></div>
                            </div>

                            <div class="editorial-card-header">
                                <span class="editorial-card-index">
                                    <span class="editorial-card-num">{{ $numFormatted }}</span>
                                    <span class="editorial-card-sep">/</span>
                                    <span class="editorial-card-tag">{{ $meta['tag'] }}</span>
                                </span>
                                @if (!empty($meta['badge']))
                                    <span class="editorial-card-badge">{{ $meta['badge'] }}</span>
                                @endif
                            </div>

                            <div class="editorial-card-body">
                                @if ($toursCount !== null && $toursCount > 0)
                                    <span class="editorial-tours-count">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        {{ $toursCount }} {{ \Illuminate\Support\Str::plural('Tour', $toursCount) }} Available
                                    </span>
                                @endif

                                <h3 class="editorial-card-title">{{ $category->name }}</h3>

                                @if (!empty($meta['desc']))
                                    <p class="editorial-card-desc">{{ $meta['desc'] }}</p>
                                @endif

                                <div class="editorial-card-action">
                                    <span class="editorial-action-label">Explore Tours</span>
                                    <span class="editorial-action-arrow">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="destinations-section" id="destinations" aria-label="Popular Destinations">
        <div class="container">
            <div class="section-head-split dest-head">
                <div class="section-head-content">
                    <span class="section-script">Top Destination</span>
                    <h2 class="section-title">Popular Destination</h2>
                </div>
                <div class="dest-head-meta">
                    <a href="{{ route('tours.index') }}" class="btn dest-view-all-link">
                        <span>Explore All Destinations</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </div>

            @if ($popularDestinations->isNotEmpty())
                @php
                    $featuredSlug = 'maldives';
                    $featuredDestination = $popularDestinations->firstWhere('slug', $featuredSlug) ?? $popularDestinations->first();
                    $otherDestinations = $popularDestinations->reject(fn ($d) => $d->id === $featuredDestination?->id);
                    $orderedDestinations = $featuredDestination ? collect([$featuredDestination])->concat($otherDestinations) : $popularDestinations;

                    $destMeta = [
                        'maldives' => [
                            'region' => 'Indian Ocean',
                            'tagline' => 'Crystal lagoons, secluded atolls & luxury overwater villas crafted for pure escapism.',
                            'badge' => 'Curated Spotlight',
                        ],
                        'greece' => [
                            'region' => 'Mediterranean',
                            'tagline' => 'Whitewashed Aegean calderas, cobalt blue waters & vibrant ancient culture.',
                            'badge' => 'Coastal Odyssey',
                        ],
                        'thailand' => [
                            'region' => 'Southeast Asia',
                            'tagline' => 'Emerald limestone archipelagos, golden temples & tranquil island hideaways.',
                            'badge' => 'Tropical Haven',
                        ],
                        'switzerland' => [
                            'region' => 'Central Europe',
                            'tagline' => 'Majestic alpine summits, turquoise glacial lakes & scenic mountain chalets.',
                            'badge' => 'Alpine Splendor',
                        ],
                        'italy' => [
                            'region' => 'Southern Europe',
                            'tagline' => 'Sun-drenched Amalfi coastal cliffs, iconic canals & timeless cultural charm.',
                            'badge' => 'Timeless Heritage',
                        ],
                    ];
                @endphp

                <div class="editorial-dest-showcase" id="destShowcase">
                    @foreach ($orderedDestinations as $index => $destination)
                        @php
                            $isFeatured = $loop->first;
                            $meta = $destMeta[$destination->slug] ?? [
                                'region' => $destination->country ?? 'Global Journey',
                                'tagline' => $destination->description ?? 'Bespoke travel itineraries crafted for discerning explorers.',
                                'badge' => 'Top Pick',
                            ];
                            $numFormatted = sprintf('%02d', $index + 1);
                        @endphp

                        <a href="{{ route('destinations.show', $destination) }}"
                           class="editorial-dest-card"
                           data-destination-slug="{{ $destination->slug }}"
                           aria-label="Explore {{ $destination->name }}">
                            <div class="dest-card-media">
                                <img src="{{ $destination->imageUrl() }}" alt="{{ $destination->name }}" loading="lazy" class="dest-card-img">
                                <div class="dest-card-overlay"></div>
                            </div>

                            <div class="dest-card-header">
                                <span class="dest-card-meta-pill">
                                    <span class="dest-card-num">{{ $numFormatted }}</span>
                                    <span class="dest-card-sep">/</span>
                                    <span class="dest-card-region">{{ $meta['region'] }}</span>
                                </span>
                                <span class="dest-card-listings-badge">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <span>{{ $destination->displayListingsCount() }}</span>
                                </span>
                            </div>

                            <div class="dest-card-body">
                                @if (!empty($meta['badge']))
                                    <span class="dest-card-kicker">{{ $meta['badge'] }}</span>
                                @endif

                                <h3 class="dest-card-name">{{ $destination->name }}</h3>

                                @if (!empty($meta['tagline']))
                                    <p class="dest-card-tagline">{{ $meta['tagline'] }}</p>
                                @endif

                                <div class="dest-card-action">
                                    <span class="dest-action-label">Explore Destination</span>
                                    <span class="dest-action-arrow">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="about-section ai-planner-section" id="plan-trip" aria-label="Plan Your Trip With Us">
        <div class="container">
            {{-- 1. Editorial Header Bar with AI Highlight Badge --}}
            <div class="plan-header-split">
                <div class="plan-header-main">
                    <div class="ai-planner-kicker-strip">
                        <span class="ai-sparkle-pill">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/></svg>
                            <span>AI Trip Planner 2.0</span>
                        </span>
                        <span class="ai-kicker-meta">Instant Custom Itineraries &amp; Dedicated Concierge</span>
                    </div>
                    <span class="section-script">{{ $siteSettings['about_subtitle'] ?? "Let's Go Together" }}</span>
                    <h2 class="section-title">{{ $siteSettings['about_title'] ?? 'Plan Your Trip With Us' }}</h2>
                    <p class="plan-lead">{{ $siteSettings['about_description'] ?? 'From alpine peaks to hidden turquoise coves, our travel specialists design every itinerary around comfort, authentic local culture, and seamless logistics from touchdown to return.' }}</p>
                </div>
                <div class="plan-header-action">
                    <div class="plan-stat-seal">
                        <div class="stat-seal-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <div class="stat-seal-content">
                            <div class="stat-seal-num">
                                <span>{{ $siteSettings['stat_1_value'] ?? '12' }}</span><span class="stat-seal-plus">+</span>
                            </div>
                            <span class="stat-seal-text">Years Crafting Journeys</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Interlocking Editorial Collage & Features Showcase --}}
            <div class="plan-canvas-grid">
                {{-- Asymmetrical Image Collage (3 Images) --}}
                <div class="plan-collage-showcase">
                    {{-- Main Image: Strong Visual Presence --}}
                    <div class="plan-img-hero">
                        <img src="{{ $aboutImg1 }}" alt="Scenic alpine exploration" loading="lazy">
                        <div class="plan-img-vignette"></div>
                        <div class="plan-img-hero-pill">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                            <span>Curated Escapes</span>
                        </div>
                    </div>

                    {{-- Supporting Image 1: Lagoon boat voyage --}}
                    <div class="plan-img-sub-top">
                        <img src="{{ $aboutImg2 }}" alt="Lagoon boat voyage" loading="lazy">
                        <div class="plan-sub-tag">Coastal Journeys</div>
                    </div>

                    {{-- Supporting Image 2: Mountain trail adventure --}}
                    <div class="plan-img-sub-bottom">
                        <img src="{{ $aboutImg3 }}" alt="Mountain trail adventure" loading="lazy">
                        <div class="plan-sub-tag">Alpine Trails</div>
                    </div>

                    {{-- Floating Decorative Stamp --}}
                    <div class="plan-floating-stamp" aria-hidden="true">
                        <svg viewBox="0 0 100 100" class="rotating-text-svg">
                            <path id="circlePathPlan" d="M 50, 50 m -37, 0 a 37,37 0 1,1 74,0 a 37,37 0 1,1 -74,0" fill="none"/>
                            <text font-size="9.5" font-weight="700" letter-spacing="2" fill="#113D48">
                                <textPath href="#circlePathPlan">OMTRAVEL • VERIFIED EXPEDITIONS •</textPath>
                            </text>
                        </svg>
                        <div class="stamp-center-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/></svg>
                        </div>
                    </div>
                </div>

                {{-- 3. Features & Concierge Showcase --}}
                <div class="plan-features-column">
                    <div class="plan-feature-card">
                        <div class="plan-feature-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                        </div>
                        <div class="plan-feature-body">
                            <div class="plan-feature-header">
                                <span class="plan-feature-badge">Tailored Luxury</span>
                                <h3 class="plan-feature-title">{{ $siteSettings['about_feature_1_title'] ?? 'Exclusive Trip' }}</h3>
                            </div>
                            <p class="plan-feature-text">{{ $siteSettings['about_feature_1_text'] ?? 'Handpicked boutique stays, private transfers, and flexible schedules tailored around your pace.' }}</p>
                            <div class="plan-feature-perk">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Private departures &amp; 100% custom pacing</span>
                            </div>
                        </div>
                    </div>

                    <div class="plan-feature-card">
                        <div class="plan-feature-icon-wrap plan-feature-icon-wrap--teal">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div class="plan-feature-body">
                            <div class="plan-feature-header">
                                <span class="plan-feature-badge">Expert Insights</span>
                                <h3 class="plan-feature-title">{{ $siteSettings['about_feature_2_title'] ?? 'Professional Guide' }}</h3>
                            </div>
                            <p class="plan-feature-text">{{ $siteSettings['about_feature_2_text'] ?? 'Certified regional experts who unlock local stories, hidden viewpoints, and worry-free exploration.' }}</p>
                            <div class="plan-feature-perk">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Local storytellers &amp; 24/7 dedicated support</span>
                            </div>
                        </div>
                    </div>

                    {{-- AI Planner Value Tags --}}
                    <div class="ai-capabilities-strip">
                        <div class="ai-capability-item">
                            <span class="ai-cap-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></span>
                            <span>Smart Pacing &amp; Route Optimization</span>
                        </div>
                        <div class="ai-capability-item">
                            <span class="ai-cap-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
                            <span>100% Handpicked Boutique Stays</span>
                        </div>
                    </div>

                    <div class="plan-action-bar">
                        <a href="{{ route('tours.index') }}" class="btn btn-cyan plan-cta-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/></svg>
                            <span>Plan with AI Concierge</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </a>
                        <a href="{{ route('contact.create') }}" class="plan-contact-link">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span>Talk to an Expert</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="tours-section" id="packages" aria-label="Popular Tour Packages">
        <div class="container">
            <div class="section-head-split tours-header">
                <div class="section-head-content">
                    <span class="section-script">Best Place For You</span>
                    <h2 class="section-title">Popular Tour Packages</h2>
                </div>
                <div class="tours-header-meta">
                    <div class="tours-guarantee-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                        <span>Best Price & Quality Guaranteed</span>
                    </div>
                    <a href="{{ route('tours.index') }}" class="btn tours-all-link">
                        <span>View All Packages</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </div>

            @if ($featuredTours->isNotEmpty())
                @php
                    $packageMeta = [
                        'greece-tour-package' => [
                            'tag' => 'Mediterranean Odyssey',
                            'highlight' => 'Flagship Journey',
                            'summary' => 'Private caldera departures, cobalt Aegean sailing, and handpicked boutique cliffside chalets.',
                        ],
                        'italy-coast-sail-package' => [
                            'tag' => 'Coastal Sailing',
                            'highlight' => 'Scenic Voyage',
                            'summary' => 'Luxury catamaran charters along sun-drenched Amalfi cliffs and historic maritime harbors.',
                        ],
                        'switzerland-alpine-escape' => [
                            'tag' => 'Alpine Wonders',
                            'highlight' => 'Mountain Expedition',
                            'summary' => 'Panoramic Swiss summits, turquoise glacial lakes, and secluded high-altitude chalets.',
                        ],
                        'maldives-lagoon-retreat' => [
                            'tag' => 'Island Sanctuary',
                            'highlight' => 'Luxury Island Retreat',
                            'summary' => 'Crystal turquoise atolls, private overwater pavilions, and serene coral reef discovery.',
                        ],
                    ];
                @endphp

                <div class="editorial-packages-showcase" id="packagesShowcase">
                    @foreach ($featuredTours->take(4) as $index => $tour)
                        @php
                            $isHero = $loop->first;
                            $meta = $packageMeta[$tour->slug] ?? [
                                'tag' => $tour->destination ? $tour->destination->name : 'Curated Departure',
                                'highlight' => $isHero ? 'Featured Journey' : 'Signature Tour',
                                'summary' => $tour->summary ?? 'Handcrafted itinerary curated for unforgettable memories and seamless comfort.',
                            ];
                            $numFormatted = sprintf('%02d', $index + 1);
                        @endphp

                        <article class="editorial-package-card"
                                 data-package-slug="{{ $tour->slug }}">
                            <div class="package-card-media">
                                <img src="{{ $tour->imageUrl() }}" alt="{{ $tour->title }}" loading="lazy" class="package-card-img">
                                <div class="package-card-overlay"></div>
                            </div>

                            <div class="package-card-header">
                                <div class="package-tags-cluster">
                                    <span class="package-meta-pill">
                                        <span class="package-num">{{ $numFormatted }}</span>
                                        <span class="package-sep">/</span>
                                        <span class="package-region">{{ $meta['tag'] }}</span>
                                    </span>
                                    @if ($tour->destination)
                                        <span class="package-dest-badge">{{ $tour->destination->name }}</span>
                                    @endif
                                    @if ($tour->tourType)
                                        <span class="package-type-badge">{{ $tour->tourType->name }}</span>
                                    @endif
                                </div>

                                <div class="package-rating-capsule" aria-label="{{ $tour->rating }} out of 5 stars">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    <span class="package-rating-val">{{ number_format((float) $tour->rating, 1) }}</span>
                                    @if ($tour->reviews_count)
                                        <span class="package-reviews-count">({{ $tour->reviews_count }})</span>
                                    @endif
                                </div>
                            </div>

                            <div class="package-card-body">
                                <span class="package-duration-pill">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <span>{{ $tour->displayDuration() }}</span>
                                </span>

                                <h3 class="package-title">
                                    <a href="{{ route('tours.show', $tour) }}">{{ $tour->title }}</a>
                                </h3>

                                @if (!empty($meta['summary']))
                                    <p class="package-summary">{{ \Illuminate\Support\Str::limit($meta['summary'], 95) }}</p>
                                @endif

                                <div class="package-footer-bar">
                                    <div class="package-price-wrap">
                                        <span class="package-price-prefix">From</span>
                                        <div class="package-price-amount">
                                            <span class="package-currency">$</span>
                                            <span class="package-val">{{ number_format((float) $tour->price, 0) }}</span>
                                            <span class="package-unit">/ Person</span>
                                        </div>
                                    </div>

                                    <a href="{{ route('tours.show', $tour) }}" class="package-book-btn" aria-label="Book {{ $tour->title }}">
                                        <span>Book Now</span>
                                        <span class="package-book-arrow">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="packages-bottom-bar">
                    <a href="{{ route('tours.index') }}" class="btn btn-cyan packages-explore-all-btn">
                        <span>Explore All Packages</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            @endif
        </div>
    </section>

    <section class="gallery-section" id="gallery" aria-label="Recent Gallery">
        <div class="container">
            <div class="section-head-split gallery-header">
                <div class="section-head-content">
                    <span class="section-script">Make Your Tour More Pleasure</span>
                    <h2 class="section-title">Recent Gallery</h2>
                </div>
                <div class="gallery-header-meta">
                    <div class="gallery-curated-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                        <span>Curated Traveler Captures</span>
                    </div>
                    <p class="gallery-header-desc">
                        Authentic perspectives and unforgettable vistas captured across our global expeditions.
                    </p>
                </div>
            </div>

            @if ($galleryItems->isNotEmpty())
                @php
                    $totalItems = $galleryItems->count();
                @endphp

                <div class="editorial-gallery-showcase {{ $totalItems === 1 ? 'editorial-gallery-showcase--single' : '' }}" id="editorialGallery">
                    @foreach ($galleryItems as $index => $item)
                        @php
                            $isFeatured = ($index === 0);
                            $cardModifier = 'gallery-mosaic-card--idx-' . $index;
                        @endphp

                        <button type="button"
                                class="gallery-mosaic-card"
                                data-lightbox-src="{{ $item->imageUrl() }}"
                                data-lightbox-title="{{ $item->title }}"
                                data-lightbox-location="{{ $item->location ?? '' }}"
                                aria-label="Open photo: {{ $item->title }}">
                            
                            <div class="gallery-mosaic-media">
                                <img src="{{ $item->imageUrl() }}" 
                                     alt="{{ $item->title }}" 
                                     loading="lazy" 
                                     class="gallery-mosaic-img">
                                <div class="gallery-mosaic-overlay"></div>
                            </div>

                            <div class="gallery-mosaic-top">
                                @if ($item->location)
                                    <span class="gallery-mosaic-location">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        <span>{{ $item->location }}</span>
                                    </span>
                                @else
                                    <span class="gallery-mosaic-location">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
                                        <span>OmTravel Gallery</span>
                                    </span>
                                @endif

                                <span class="gallery-mosaic-zoom-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/><line x1="11" y1="8" x2="11" y2="14"/></svg>
                                </span>
                            </div>

                            <div class="gallery-mosaic-bottom">
                                <h3 class="gallery-mosaic-title">{{ $item->title }}</h3>
                                <span class="gallery-mosaic-hint">
                                    <span>Expand Capture</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>

                <div class="gallery-footer-bar">
                    <div class="gallery-tagline-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                        <span>Tag your adventures with <strong>#OmTravelMoments</strong> for a chance to be featured</span>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <section class="stats-section" id="statistics" aria-label="Company Statistics">
        <div class="container">
            <div class="editorial-stats-ribbon">
                @php
                    $statSubtitles = [
                        1 => 'Crafting bespoke journeys',
                        2 => 'Client loyalty & repeat journeys',
                        3 => 'Worldwide guided expeditions',
                        4 => 'Verified memories created',
                    ];
                @endphp

                @for ($i = 1; $i <= 4; $i++)
                    @php
                        $val = $siteSettings['stat_'.$i.'_value'] ?? '';
                        $label = $siteSettings['stat_'.$i.'_label'] ?? '';
                        $sub = $statSubtitles[$i] ?? '';
                    @endphp

                    <div class="editorial-stat-card">
                        <div class="stat-number-wrap">
                            <span class="stat-val-number stat-value" data-stat-value="{{ $val }}">{{ $val }}</span>
                        </div>
                        <h3 class="stat-metric-label">{{ $label }}</h3>
                        @if ($sub)
                            <p class="stat-metric-sub">{{ $sub }}</p>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </section>

    <section class="guides-section" id="guides" aria-label="Tour Guides">
        <div class="container">
            <div class="section-head-split guides-header">
                <div class="section-head-content">
                    <span class="section-script">Meet with Guide</span>
                    <h2 class="section-title">Tour Guide</h2>
                </div>
                <div class="guides-header-meta">
                    <div class="guides-curated-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span>Certified Expedition Leaders</span>
                    </div>
                    <p class="guides-header-desc">
                        Seasoned naturalists, alpine navigators, and cultural storytellers dedicated to safe, deeply enriching journeys.
                    </p>
                </div>
            </div>

            @if ($guides->isNotEmpty())
                <div class="guides-grid" id="guidesGrid">
                    @foreach ($guides as $guide)
                        <article class="guide-card {{ $loop->index >= 4 ? 'is-hidden-slide' : '' }}">
                            <div class="guide-card-media">
                                <img src="{{ $guide->photoUrl() }}" alt="{{ $guide->name }}" loading="lazy" class="guide-card-img">
                                <div class="guide-card-overlay"></div>
                            </div>

                            <div class="guide-card-top">
                                <span class="guide-certified-pill">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        <path d="m9 12 2 2 4-4"/>
                                    </svg>
                                    <span>Certified</span>
                                </span>
                            </div>

                            <div class="guide-card-body">
                                <span class="guide-role">{{ $guide->role }}</span>
                                <h3 class="guide-name">{{ $guide->name }}</h3>
                                <div class="guide-socials" aria-label="Connect with {{ $guide->name }}">
                                    @if ($guide->facebook_url)
                                        <a href="{{ $guide->facebook_url }}" class="social-circle" aria-label="{{ $guide->name }} Facebook" target="_blank" rel="noopener noreferrer">
                                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h1.5V2.14c-.326-.043-1.557-.14-2.857-.14C11.928 2 10 3.657 10 6.7v2.8H7v4h3V22h4v-8.5z"/></svg>
                                        </a>
                                    @endif
                                    @if ($guide->twitter_url)
                                        <a href="{{ $guide->twitter_url }}" class="social-circle" aria-label="{{ $guide->name }} Twitter / X" target="_blank" rel="noopener noreferrer">
                                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 5.92a8.24 8.24 0 0 1-2.36.65 4.12 4.12 0 0 0 1.81-2.27 8.22 8.22 0 0 1-2.61 1 4.1 4.1 0 0 0-7 3.74A11.65 11.65 0 0 1 3.39 4.7a4.1 4.1 0 0 0 1.27 5.48 4.07 4.07 0 0 1-1.86-.51v.05a4.11 4.11 0 0 0 3.29 4.02 4.1 4.1 0 0 1-1.85.07 4.11 4.11 0 0 0 3.83 2.85A8.23 8.23 0 0 1 2 18.39a11.62 11.62 0 0 0 6.29 1.84c7.55 0 11.67-6.25 11.67-11.67 0-.18 0-.35-.01-.53A8.35 8.35 0 0 0 22 5.92z"/></svg>
                                        </a>
                                    @endif
                                    @if ($guide->instagram_url)
                                        <a href="{{ $guide->instagram_url }}" class="social-circle" aria-label="{{ $guide->name }} Instagram" target="_blank" rel="noopener noreferrer">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/></svg>
                                        </a>
                                    @endif
                                    @if ($guide->linkedin_url)
                                        <a href="{{ $guide->linkedin_url }}" class="social-circle" aria-label="{{ $guide->name }} LinkedIn" target="_blank" rel="noopener noreferrer">
                                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.94 5a2 2 0 1 1-4-.002 2 2 0 0 1 4 .002zM7 8.48H3V21h4V8.48zm6.32 0H9.34V21h3.94v-6.57c0-3.66 4.77-4 4.77 0V21H22v-7.93c0-6.17-7.06-5.94-8.72-2.91l.04-1.68z"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="carousel-dots guides-dots" id="guideDots"></div>

                <div class="guides-trust-bar">
                    <div class="guides-trust-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                        <span>Wilderness & First-Aid Certified</span>
                    </div>
                    <span class="guides-trust-dot">•</span>
                    <div class="guides-trust-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        <span>Native Multilingual Navigators</span>
                    </div>
                    <span class="guides-trust-dot">•</span>
                    <div class="guides-trust-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <span>4.9 / 5 Guest Satisfaction Rating</span>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <section class="testimonials-section" id="testimonials" aria-label="Client Testimonials">
        <div class="container">
            <div class="section-head-split testimonials-header">
                <div class="section-head-content">
                    <span class="section-script">Testimonial</span>
                    <h2 class="section-title">What Client Say About us</h2>
                </div>
                <div class="testimonials-header-meta">
                    <div class="testimonials-score-badge">
                        <svg viewBox="0 0 24 24" fill="#FFB800"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <strong>4.9 / 5.0</strong>
                        <span>1,420+ Verified Traveler Reviews</span>
                    </div>
                    <p class="testimonials-header-desc">
                        Real reflections and candid memories from globetrotters who explored the world with OmTravel.
                    </p>
                </div>
            </div>

            @if ($testimonials->isNotEmpty())
                <div class="testimonials-carousel-wrap">
                    <div class="testimonials-grid" id="testimonialsGrid">
                        @foreach ($testimonials as $testimonial)
                            <article class="testimonial-card {{ $loop->index === 1 ? 'is-center-card' : '' }} {{ $loop->index >= 3 ? 'is-hidden-slide' : '' }}">
                                <span class="testimonial-watermark" aria-hidden="true">&ldquo;</span>

                                <div class="testimonial-top-row">
                                    <div class="testimonial-rating-capsule" aria-label="{{ $testimonial->rating }} out of 5 stars">
                                        <span class="stars-row">
                                            @for ($s = 0; $s < $testimonial->rating; $s++)
                                                <svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                            @endfor
                                        </span>
                                        <span class="testimonial-score-val">{{ number_format((float) $testimonial->rating, 1) }}</span>
                                    </div>

                                    <span class="testimonial-verified-badge">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                                        <span>Verified</span>
                                    </span>
                                </div>

                                <blockquote class="testimonial-quote-wrap">
                                    <p class="testimonial-quote">&ldquo;{{ $testimonial->quote }}&rdquo;</p>
                                </blockquote>

                                <div class="testimonial-footer">
                                    <div class="testimonial-author">
                                        <img src="{{ $testimonial->avatarUrl() }}" alt="{{ $testimonial->client_name }}" class="testimonial-avatar" loading="lazy">
                                        <div class="testimonial-author-meta">
                                            <h3 class="testimonial-name">{{ $testimonial->client_name }}</h3>
                                            <div class="testimonial-role">{{ $testimonial->client_role }}</div>
                                        </div>
                                    </div>
                                    <span class="testimonial-trip-tag">Verified Traveler</span>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="testimonial-nav-bar">
                        <button type="button" class="testimonial-nav-arrow testimonial-nav-prev" id="testimonialPrevBtn" aria-label="Previous testimonial">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        </button>

                        <div class="carousel-dots testimonial-dots-strip" id="testimonialDots"></div>

                        <button type="button" class="testimonial-nav-arrow testimonial-nav-next" id="testimonialNextBtn" aria-label="Next testimonial">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <section class="partners-section" aria-label="Brand Partnerships">
        <div class="container">
            <div class="partners-header">
                <span class="partners-eyebrow">Trusted by Global Expeditions &amp; Luxury Travel Networks</span>
            </div>

            <div class="partners-strip" role="list">
                <div class="partner-brand-item" role="listitem">
                    <div class="partner-icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 20l6-12 4 7 3-5 5 10H3z"/></svg>
                    </div>
                    <span class="partner-brand-name">AlpinePeak</span>
                </div>

                <div class="partner-brand-item" role="listitem">
                    <div class="partner-icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 17l16 0M6 17L12 4l6 13M12 4v13"/></svg>
                    </div>
                    <span class="partner-brand-name">CoralVoyage</span>
                </div>

                <div class="partner-brand-item" role="listitem">
                    <div class="partner-icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><polygon points="15.5 8.5 13 13 8.5 15.5 11 11 15.5 8.5"/></svg>
                    </div>
                    <span class="partner-brand-name">Wanderlust</span>
                </div>

                <div class="partner-brand-item" role="listitem">
                    <div class="partner-icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 21v-9m0 0c-3-4-7-3-8 0 3 0 6 1 8 0zm0 0c3-4 7-3 8 0-3 0-6 1-8 0z"/><path d="M5 21h14"/></svg>
                    </div>
                    <span class="partner-brand-name">PalmIsland</span>
                </div>

                <div class="partner-brand-item" role="listitem">
                    <div class="partner-icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="4"/><path d="M12 3v2m0 14v2M4.9 4.9l1.4 1.4m11.4 11.4l1.4 1.4M3 12h2m14 0h2"/></svg>
                    </div>
                    <span class="partner-brand-name">SunTrail</span>
                </div>

                <div class="partner-brand-item" role="listitem">
                    <div class="partner-icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M22 2L11 13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </div>
                    <span class="partner-brand-name">SkyPass</span>
                </div>

                <div class="partner-brand-item" role="listitem">
                    <div class="partner-icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M2 14c2-2 4-2 6 0s4 2 6 0 4-2 6 0M2 19c2-2 4-2 6 0s4 2 6 0 4-2 6 0"/></svg>
                    </div>
                    <span class="partner-brand-name">Coastline</span>
                </div>

                <div class="partner-brand-item" role="listitem">
                    <div class="partner-icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <span class="partner-brand-name">ExploreCo</span>
                </div>
            </div>
        </div>
    </section>

    <section class="blog-section editorial-magazine-section" id="news-articles" aria-label="News &amp; Articles">
        <div class="container">
            <div class="section-head-split magazine-head-split">
                <div class="section-head">
                    <span class="section-script">Stories &amp; Dispatches</span>
                    <h2 class="section-title">News &amp; Articles From {{ $siteSettings['site_name'] ?? 'OmTravel' }}</h2>
                    <p class="magazine-head-sub">Curated expedition guides, seasonal travel notes, and culture dispatches from our global specialists.</p>
                </div>
                <a href="{{ route('blog.index') }}" class="btn btn-outline-soft magazine-see-all-link">
                    <span>See More Articles</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>

            @if ($blogPosts->isNotEmpty())
                @php
                    $featuredPost = $blogPosts->first();
                    $supportingPosts = $blogPosts->slice(1);
                @endphp

                <div class="magazine-editorial-grid {{ $supportingPosts->isEmpty() ? 'magazine-editorial-grid--single' : '' }}">
                    {{-- Visually Prominent Featured Article --}}
                    <article class="magazine-featured-card blog-card">
                        <a href="{{ route('blog.show', $featuredPost) }}" class="magazine-featured-media blog-thumb" aria-label="{{ $featuredPost->title }}">
                            <img src="{{ $featuredPost->imageUrl() }}" alt="{{ $featuredPost->title }}" loading="lazy">
                            <div class="magazine-featured-badges">
                                <span class="magazine-badge magazine-badge--lead">
                                    <svg viewBox="0 0 24 24" fill="currentColor" width="12" height="12"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    Featured Story
                                </span>
                                @if ($featuredPost->category)
                                    <span class="magazine-badge magazine-badge--category blog-category-pill">{{ $featuredPost->category }}</span>
                                @endif
                            </div>
                        </a>

                        <div class="magazine-featured-body">
                            <div class="magazine-meta-strip blog-meta">
                                <span class="magazine-meta-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="14" height="14"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    <span>{{ ($featuredPost->published_at ?? $featuredPost->created_at)->format('F d, Y') }}</span>
                                </span>
                                <span class="magazine-meta-divider blog-meta-sep"></span>
                                <span class="magazine-meta-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="14" height="14"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <span>{{ $featuredPost->read_time }}</span>
                                </span>
                            </div>

                            <h3 class="magazine-featured-title blog-title">
                                <a href="{{ route('blog.show', $featuredPost) }}">{{ $featuredPost->title }}</a>
                            </h3>

                            @if ($featuredPost->excerpt)
                                <p class="magazine-featured-excerpt blog-excerpt">{{ \Illuminate\Support\Str::limit($featuredPost->excerpt, 160) }}</p>
                            @endif

                            <div class="magazine-featured-action">
                                <a href="{{ route('blog.show', $featuredPost) }}" class="btn btn-outline-soft magazine-cta-btn">
                                    <span>Read Full Story</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>

                    {{-- Supporting Articles Layout --}}
                    @if ($supportingPosts->isNotEmpty())
                        <div class="magazine-supporting-col">
                            @foreach ($supportingPosts as $post)
                                <article class="magazine-dispatch-card blog-card">
                                    <a href="{{ route('blog.show', $post) }}" class="magazine-dispatch-thumb blog-thumb" aria-label="{{ $post->title }}">
                                        <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" loading="lazy">
                                        @if ($post->category)
                                            <span class="magazine-thumb-badge blog-category-pill">{{ $post->category }}</span>
                                        @endif
                                    </a>

                                    <div class="magazine-dispatch-body">
                                        <div class="magazine-meta-strip magazine-meta-strip--sm blog-meta">
                                            <span class="magazine-meta-item">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="13" height="13"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                <span>{{ ($post->published_at ?? $post->created_at)->format('F d, Y') }}</span>
                                            </span>
                                            <span class="magazine-meta-divider blog-meta-sep"></span>
                                            <span class="magazine-meta-item">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="13" height="13"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                <span>{{ $post->read_time }}</span>
                                            </span>
                                        </div>

                                        <h4 class="magazine-dispatch-title blog-title">
                                            <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                                        </h4>

                                        @if ($post->excerpt)
                                            <p class="magazine-dispatch-excerpt blog-excerpt">{{ \Illuminate\Support\Str::limit($post->excerpt, 105) }}</p>
                                        @endif

                                        <div class="magazine-dispatch-action">
                                            <a href="{{ route('blog.show', $post) }}" class="magazine-inline-link">
                                                <span>Read Article</span>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>
@endsection
