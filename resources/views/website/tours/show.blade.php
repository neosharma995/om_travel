@extends('layouts.website')

@section('title', $tour->title.' — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    {{-- Tour Package Hero Header --}}
    <section class="tour-detail-hero">
        <div class="container">
            {{-- Top Navigation & Breadcrumbs --}}
            <div class="tour-hero-top-nav">
                <nav class="tour-breadcrumbs" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span class="crumb-sep">&rsaquo;</span>
                    <a href="{{ route('tours.index') }}">Tours</a>
                    @if ($tour->destination)
                        <span class="crumb-sep">&rsaquo;</span>
                        <a href="{{ route('tours.index', ['destination' => $tour->destination->slug]) }}">{{ $tour->destination->name }}</a>
                    @endif
                    <span class="crumb-sep">&rsaquo;</span>
                    <span class="crumb-current">{{ \Illuminate\Support\Str::limit($tour->title, 36) }}</span>
                </nav>

                <a href="{{ route('tours.index') }}" class="tour-back-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="14" height="14">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    <span>All Tour Packages</span>
                </a>
            </div>

            {{-- Tour Badges & Main Title --}}
            <div class="tour-hero-header-content">
                <div class="tour-hero-badges-strip">
                    @if ($tour->category)
                        <span class="tour-badge tour-badge--category">{{ $tour->category->name }}</span>
                    @endif
                    @if ($tour->tourType)
                        <span class="tour-badge tour-badge--type">{{ $tour->tourType->name }}</span>
                    @endif
                    @if ($tour->activity)
                        <span class="tour-badge tour-badge--type">{{ $tour->activity->name }}</span>
                    @endif
                    @if ($tour->location)
                        <span class="tour-badge tour-badge--location">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span>{{ $tour->location }}</span>
                        </span>
                    @endif
                    <span class="tour-badge tour-badge--rating">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="13" height="13">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        <span>{{ number_format((float) $tour->rating, 1) }}</span>
                        <span class="tour-rating-count">({{ $tour->reviews_count }} verified reviews)</span>
                    </span>
                </div>

                <h1 class="tour-detail-heading">{{ $tour->title }}</h1>

                @if ($tour->summary)
                    <p class="tour-detail-summary-lead">{{ $tour->summary }}</p>
                @endif

                {{-- Key Package Highlights Ribbon --}}
                <div class="tour-key-specs-ribbon">
                    <div class="tour-spec-item">
                        <div class="tour-spec-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div class="tour-spec-text">
                            <span class="tour-spec-label">Duration</span>
                            <strong class="tour-spec-value">{{ $tour->displayDuration() }}</strong>
                        </div>
                    </div>

                    <div class="tour-spec-item">
                        <div class="tour-spec-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div class="tour-spec-text">
                            <span class="tour-spec-label">Group Size</span>
                            <strong class="tour-spec-value">Up to 30 Guests</strong>
                        </div>
                    </div>

                    <div class="tour-spec-item">
                        <div class="tour-spec-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <div class="tour-spec-text">
                            <span class="tour-spec-label">Leadership</span>
                            <strong class="tour-spec-value">Certified Expedition Leader</strong>
                        </div>
                    </div>

                    <div class="tour-spec-item">
                        <div class="tour-spec-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </div>
                        <div class="tour-spec-text">
                            <span class="tour-spec-label">Booking</span>
                            <strong class="tour-spec-value">Instant Reservation</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Large Hero Showcase Image & Gallery Frame --}}
            <div class="tour-hero-gallery-wrap">
                <div class="tour-hero-media-card"
                     tabindex="0"
                     role="button"
                     aria-label="View {{ $tour->title }} cover image in high resolution"
                     data-lightbox-src="{{ $tour->imageUrl() }}"
                     data-lightbox-title="{{ $tour->title }}"
                     data-lightbox-location="{{ $tour->location ?? ($tour->destination->name ?? 'Sanctuary Tour') }}">

                    <img src="{{ $tour->imageUrl() }}" alt="{{ $tour->title }}" class="tour-hero-cover-img">
                    <div class="tour-hero-cover-overlay"></div>

                    {{-- Top Overlays --}}
                    <div class="tour-hero-overlay-top">
                        <span class="tour-hero-destination-pill">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span>{{ $tour->destination->name ?? ($tour->location ?? 'Global Journey') }}</span>
                        </span>

                        <span class="tour-hero-zoom-btn" title="View Full Photo">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="14" height="14">
                                <polyline points="15 3 21 3 21 9"/>
                                <polyline points="9 21 3 21 3 15"/>
                                <line x1="21" y1="3" x2="14" y2="10"/>
                                <line x1="3" y1="21" x2="10" y2="14"/>
                            </svg>
                        </span>
                    </div>

                    {{-- Bottom Overlays --}}
                    <div class="tour-hero-overlay-bottom">
                        <div class="tour-hero-caption-note">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                            <span>Curated Field Photography &bull; Click to Expand</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Tour Experience & Reservation Layout --}}
    <section class="page-section tour-detail-section">
        <div class="container">
            <div class="tour-detail-grid-layout">
                {{-- Left Content Column (Overview, Highlights, Inclusions, Itinerary, Essentials) --}}
                <div class="tour-detail-content-area">
                    {{-- 1. Expedition Overview --}}
                    <div class="tour-section-block tour-overview-block">
                        <div class="tour-block-header">
                            <span class="tour-block-eyebrow">The Journey Narrative</span>
                            <h2 class="tour-block-title">Expedition Overview</h2>
                        </div>

                        <div class="tour-description-prose">
                            @php
                                $descParagraphs = array_filter(array_map('trim', preg_split('/\r\n\r\n|\n\n/', $tour->description)));
                            @endphp
                            @if (!empty($descParagraphs))
                                @foreach ($descParagraphs as $descP)
                                    <p class="tour-desc-paragraph">{{ $descP }}</p>
                                @endforeach
                            @else
                                <p class="tour-desc-paragraph">{{ $tour->description }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- 2. Curated Highlights & Inclusions Presentation --}}
                    @if (count($tour->highlightsList()) > 0)
                        <div class="tour-section-block tour-highlights-block">
                            <div class="tour-block-header">
                                <span class="tour-block-eyebrow">Signature Experiences</span>
                                <h3 class="tour-block-title">Key Tour Highlights</h3>
                            </div>

                            <div class="tour-highlights-grid">
                                @foreach ($tour->highlightsList() as $index => $highlight)
                                    <div class="tour-highlight-card">
                                        <div class="tour-highlight-badge">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                        </div>
                                        <div class="tour-highlight-content">
                                            <span class="tour-highlight-step">Highlight 0{{ $index + 1 }}</span>
                                            <h4 class="tour-highlight-title">{{ $highlight }}</h4>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- 3. Comprehensive Inclusions & Exclusions Matrix --}}
                    <div class="tour-section-block tour-inclusions-block">
                        <div class="tour-block-header">
                            <span class="tour-block-eyebrow">Package Scope</span>
                            <h3 class="tour-block-title">What is Included &amp; Excluded</h3>
                        </div>

                        <div class="tour-inclusions-matrix">
                            {{-- Inclusions Column --}}
                            <div class="inclusions-card inclusions-card--included">
                                <div class="inclusions-head">
                                    <div class="inclusions-icon-wrap inclusions-icon-wrap--included">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                    </div>
                                    <h4 class="inclusions-column-title">Included in Your Package</h4>
                                </div>
                                <ul class="inclusions-list">
                                    @if (count($tour->highlightsList()) > 0)
                                        @foreach ($tour->highlightsList() as $item)
                                            <li>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="20 6 9 17 4 12"/></svg>
                                                <span>{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    @endif
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span>Certified professional bilingual expedition leader</span>
                                    </li>
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span>Daily breakfast &amp; welcome regional tasting</span>
                                    </li>
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span>All scheduled transfers &amp; entrance fees</span>
                                    </li>
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span>24/7 dedicated travel concierge emergency support</span>
                                    </li>
                                </ul>
                            </div>

                            {{-- Exclusions Column --}}
                            <div class="inclusions-card inclusions-card--excluded">
                                <div class="inclusions-head">
                                    <div class="inclusions-icon-wrap inclusions-icon-wrap--excluded">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16">
                                            <line x1="18" y1="6" x2="6" y2="18"/>
                                            <line x1="6" y1="6" x2="18" y2="18"/>
                                        </svg>
                                    </div>
                                    <h4 class="inclusions-column-title">Excluded from Package</h4>
                                </div>
                                <ul class="inclusions-list inclusions-list--excluded">
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        <span>International airfare to/from tour starting location</span>
                                    </li>
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        <span>Personal medical &amp; travel cancellation insurance</span>
                                    </li>
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        <span>Optional extracurricular excursions outside schedule</span>
                                    </li>
                                    <li>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        <span>Discretionary gratuities for local drivers and guides</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Well-Organized Itinerary Track --}}
                    <div class="tour-section-block tour-itinerary-block">
                        <div class="tour-block-header">
                            <span class="tour-block-eyebrow">Day-by-Day Flow</span>
                            <h3 class="tour-block-title">Journey Itinerary Timeline</h3>
                        </div>

                        <div class="tour-timeline-track">
                            @php
                                $highlights = $tour->highlightsList();
                                $totalDays = max((int) $tour->duration_days, 1);
                            @endphp

                            @if (!empty($highlights))
                                @foreach ($highlights as $idx => $step)
                                    <div class="timeline-item">
                                        <div class="timeline-marker">
                                            <span class="timeline-dot"></span>
                                        </div>
                                        <div class="timeline-body">
                                            <span class="timeline-day-pill">Stage 0{{ $idx + 1 }}</span>
                                            <h4 class="timeline-title">{{ $step }}</h4>
                                            <p class="timeline-desc">Curated excursion with local certified specialists, private ground transport, and immersive cultural exploration.</p>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="timeline-item">
                                    <div class="timeline-marker">
                                        <span class="timeline-dot"></span>
                                    </div>
                                    <div class="timeline-body">
                                        <span class="timeline-day-pill">Days 01 - 0{{ $totalDays }}</span>
                                        <h4 class="timeline-title">Complete {{ $tour->displayDuration() }} Expedition Program</h4>
                                        <p class="timeline-desc">{{ $tour->summary ?? $tour->description }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 5. Supporting Travel Information Essentials --}}
                    <div class="tour-section-block tour-essentials-block">
                        <div class="tour-block-header">
                            <span class="tour-block-eyebrow">Practical Guidance</span>
                            <h3 class="tour-block-title">Important Traveler Essentials</h3>
                        </div>

                        <div class="tour-essentials-grid">
                            <div class="essential-card">
                                <div class="essential-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                </div>
                                <h4 class="essential-title">Meeting &amp; Pickup</h4>
                                <p class="essential-text">Personal meet-and-greet transfers provided directly upon arrival at destination airport or terminal.</p>
                            </div>

                            <div class="essential-card">
                                <div class="essential-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                </div>
                                <h4 class="essential-title">Activity Level</h4>
                                <p class="essential-text">Moderate leisure activity suitable for active travelers. Scenic walks, guided tours, and relaxing transfers.</p>
                            </div>

                            <div class="essential-card">
                                <div class="essential-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                </div>
                                <h4 class="essential-title">What to Bring</h4>
                                <p class="essential-text">Comfortable footwear, lightweight breathable layers, personal sun protection, and a camera for vistas.</p>
                            </div>

                            <div class="essential-card">
                                <div class="essential-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                                </div>
                                <h4 class="essential-title">Cancellation Policy</h4>
                                <p class="essential-text">100% full refund guarantee when canceled up to 14 days prior to departure date. Zero hidden penalty fees.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Sticky Booking & Enquiry Card --}}
                <aside class="tour-booking-sidebar-wrap">
                    <div class="tour-booking-card" id="tourBookingCard">
                        {{-- Booking Card Price Header --}}
                        <div class="booking-card-header">
                            <span class="booking-card-eyebrow">Reserve Your Passage</span>
                            <div class="booking-price-line">
                                <span class="booking-price-curr">₹</span>
                                <span class="booking-price-amount" id="tourUnitPrice" data-price="{{ (float) $tour->price }}">{{ number_format((float) $tour->price, 2) }}</span>
                                <span class="booking-price-period">/ Person</span>
                            </div>
                            <div class="booking-badge-meta">
                                <span class="booking-duration-badge">{{ $tour->displayDuration() }}</span>
                                <span class="booking-status-badge">Available Now</span>
                            </div>
                        </div>

                        {{-- Estimated Total Ribbon --}}
                        <div class="booking-est-total-strip">
                            <div class="est-total-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                <span>Estimated Total:</span>
                            </div>
                            <strong class="est-total-value" id="liveBookingTotal">₹{{ number_format((float) $tour->price * (int) old('guests', 2), 2) }}</strong>
                        </div>

                        {{-- Alert Notifications --}}
                        @if (session('booking_success'))
                            <div class="alert alert-success booking-success-alert">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <span>{{ session('booking_success') }}</span>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-error booking-error-alert">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>Please review the highlighted booking fields below.</span>
                            </div>
                        @endif

                        {{-- Booking Form --}}
                        <form action="{{ route('tours.book', $tour) }}" method="POST" class="tour-booking-form" id="tourBookingForm">
                            @csrf

                            <div class="form-group">
                                <label for="customer_name" class="form-label">Full Name <span class="req-star">*</span></label>
                                <div class="form-input-icon-wrap">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" class="form-control @error('customer_name') is-invalid @enderror" placeholder="Your full legal name" required>
                                </div>
                                @error('customer_name') <span class="field-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="customer_email" class="form-label">Email Address <span class="req-star">*</span></label>
                                <div class="form-input-icon-wrap">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                    <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}" class="form-control @error('customer_email') is-invalid @enderror" placeholder="name@example.com" required>
                                </div>
                                @error('customer_email') <span class="field-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="customer_phone" class="form-label">Phone Number</label>
                                <div class="form-input-icon-wrap">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" class="form-control @error('customer_phone') is-invalid @enderror" placeholder="+1 (555) 000-0000">
                                </div>
                                @error('customer_phone') <span class="field-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label for="travel_date" class="form-label">Travel Date <span class="req-star">*</span></label>
                                    <div class="form-input-icon-wrap">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        <input type="date" id="travel_date" name="travel_date" value="{{ old('travel_date', now()->addDays(14)->toDateString()) }}" min="{{ now()->toDateString() }}" class="form-control @error('travel_date') is-invalid @enderror" required>
                                    </div>
                                    @error('travel_date') <span class="field-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label for="guests" class="form-label">Guests <span class="req-star">*</span></label>
                                    <div class="form-input-icon-wrap">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        <input type="number" id="guests" name="guests" value="{{ old('guests', 2) }}" min="1" max="30" class="form-control @error('guests') is-invalid @enderror" required>
                                    </div>
                                    @error('guests') <span class="field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="special_requests" class="form-label">Special Requests &amp; Notes</label>
                                <textarea id="special_requests" name="special_requests" class="form-control @error('special_requests') is-invalid @enderror" rows="3" placeholder="Dietary restrictions, flight numbers, room preferences...">{{ old('special_requests') }}</textarea>
                                @error('special_requests') <span class="field-error">{{ $message }}</span> @enderror
                            </div>

                            <button type="submit" class="btn btn-cyan tour-booking-submit-btn">
                                <span>Confirm Booking Request</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="16" height="16"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </button>
                        </form>

                        {{-- Booking Guarantees Footer --}}
                        <div class="booking-guarantees-list">
                            <div class="guarantee-badge-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                <span>Encrypted 256-Bit SSL Booking</span>
                            </div>
                            <div class="guarantee-badge-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/></svg>
                                <span>No Booking or Hidden Cancellation Fees</span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            {{-- 6. More Tours You May Enjoy Section --}}
            @if ($relatedTours->isNotEmpty())
                <div class="tour-related-packages-section">
                    <div class="tour-related-header">
                        <div class="related-copy">
                            <span class="related-eyebrow">Similar Odysseys</span>
                            <h2 class="related-main-heading">More Tours You May Enjoy</h2>
                        </div>
                        <a href="{{ route('tours.index') }}" class="btn btn-outline-soft related-view-link">
                            <span>Browse All Tours</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </a>
                    </div>

                    <div class="tours-grid-3">
                        @foreach ($relatedTours as $related)
                            <article class="tour-card tour-detail-related-card">
                                <a href="{{ route('tours.show', $related) }}" class="tour-card-thumb" aria-label="{{ $related->title }}">
                                    <img src="{{ $related->imageUrl() }}" alt="{{ $related->title }}" loading="lazy">
                                    @if ($related->category)
                                        <span class="tour-card-badge">{{ $related->category->name }}</span>
                                    @endif
                                </a>

                                <div class="tour-card-body">
                                    <div class="tour-card-meta">
                                        @if ($related->location)
                                            <span class="tour-card-location">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                                <span>{{ $related->location }}</span>
                                            </span>
                                        @endif
                                        <span class="tour-card-rating">
                                            <svg viewBox="0 0 24 24" fill="currentColor" width="12" height="12"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                            <span>{{ number_format((float) $related->rating, 1) }}</span>
                                        </span>
                                    </div>

                                    <h3 class="tour-card-title">
                                        <a href="{{ route('tours.show', $related) }}">{{ $related->title }}</a>
                                    </h3>

                                    <div class="tour-price-line">
                                        <span class="price-val">₹{{ number_format((float) $related->price, 2) }}</span>
                                        <span class="price-unit">/ Person</span>
                                    </div>

                                    <div class="tour-card-footer">
                                        <span class="tour-duration">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            <span>{{ $related->displayDuration() }}</span>
                                        </span>
                                        <a href="{{ route('tours.show', $related) }}" class="btn btn-outline-soft tour-card-btn">
                                            <span>View Tour</span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
