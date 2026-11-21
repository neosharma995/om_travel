@extends('layouts.website')

@section('title', $tour->title.' — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    <section class="page-banner">
        <div class="container">
            <h1 class="page-banner-title">{{ $tour->title }}</h1>
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&rsaquo;</span>
                <a href="{{ route('tours.index') }}">Tours</a>
                <span>&rsaquo;</span>
                <span>{{ $tour->title }}</span>
            </div>
        </div>
    </section>

    <section class="page-section">
        <div class="container">
            <div class="detail-layout">
                <div>
                    <div class="detail-cover">
                        <img src="{{ $tour->imageUrl() }}" alt="{{ $tour->title }}">
                    </div>

                    <div class="detail-meta-strip">
                        @if ($tour->location)
                            <span class="meta-pill">{{ $tour->location }}</span>
                        @endif
                        <span class="meta-pill">{{ $tour->displayDuration() }}</span>
                        <span class="meta-pill">{{ number_format((float) $tour->rating, 1) }} Rating ({{ $tour->reviews_count }} reviews)</span>
                        @if ($tour->category)
                            <span class="meta-pill">{{ $tour->category->name }}</span>
                        @endif
                    </div>

                    <h2>Overview</h2>
                    @if ($tour->summary)
                        <p><strong>{{ $tour->summary }}</strong></p>
                    @endif
                    <div style="white-space: pre-line; margin-bottom: 28px;">{{ $tour->description }}</div>

                    @if (count($tour->highlightsList()) > 0)
                        <h3>Tour Highlights</h3>
                        <ul class="highlights-list">
                            @foreach ($tour->highlightsList() as $highlight)
                                <li>{{ $highlight }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <aside class="sidebar-card">
                    <div class="tour-price-line" style="font-size: 28px;">
                        ${{ number_format((float) $tour->price, 2) }} <span>/Person</span>
                    </div>
                    <p style="margin-bottom: 18px;">Reserve your spot instantly. No hidden fees.</p>

                    @if (session('booking_success'))
                        <div class="alert alert-success">{{ session('booking_success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-error">Please review the highlighted booking fields below.</div>
                    @endif

                    <form action="{{ route('tours.book', $tour) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="customer_name" class="form-label">Full Name *</label>
                            <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" class="form-control" required>
                            @error('customer_name') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="customer_email" class="form-label">Email Address *</label>
                            <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}" class="form-control" required>
                            @error('customer_email') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="customer_phone" class="form-label">Phone Number</label>
                            <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" class="form-control">
                            @error('customer_phone') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label for="travel_date" class="form-label">Travel Date *</label>
                                <input type="date" id="travel_date" name="travel_date" value="{{ old('travel_date', now()->addDays(14)->toDateString()) }}" min="{{ now()->toDateString() }}" class="form-control" required>
                                @error('travel_date') <span class="field-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="guests" class="form-label">Guests *</label>
                                <input type="number" id="guests" name="guests" value="{{ old('guests', 2) }}" min="1" max="30" class="form-control" required>
                                @error('guests') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="special_requests" class="form-label">Special Requests</label>
                            <textarea id="special_requests" name="special_requests" class="form-control" placeholder="Dietary preferences, pickup notes...">{{ old('special_requests') }}</textarea>
                            @error('special_requests') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn btn-cyan" style="width: 100%;">Confirm Booking Request</button>
                    </form>
                </aside>
            </div>

            @if ($relatedTours->isNotEmpty())
                <div style="margin-top: 64px;">
                    <h2 style="margin-bottom: 24px;">More Tours You May Enjoy</h2>
                    <div class="tours-grid-3">
                        @foreach ($relatedTours as $related)
                            <article class="tour-card">
                                <a href="{{ route('tours.show', $related) }}" class="tour-card-thumb">
                                    <img src="{{ $related->imageUrl() }}" alt="{{ $related->title }}" loading="lazy">
                                </a>
                                <div class="tour-card-body">
                                    <h3 class="tour-card-title">
                                        <a href="{{ route('tours.show', $related) }}">{{ $related->title }}</a>
                                    </h3>
                                    <div class="tour-price-line">
                                        ${{ number_format((float) $related->price, 2) }} <span>/Person</span>
                                    </div>
                                    <div class="tour-card-footer">
                                        <span class="tour-duration">{{ $related->displayDuration() }}</span>
                                        <a href="{{ route('tours.show', $related) }}" class="btn btn-outline-soft">View Tour</a>
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
