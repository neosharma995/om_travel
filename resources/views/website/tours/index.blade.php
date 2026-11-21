@extends('layouts.website')

@section('title', 'Tour Packages — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    <section class="page-banner">
        <div class="container">
            <h1 class="page-banner-title">Tour Packages</h1>
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&rsaquo;</span>
                <span>Tours</span>
            </div>
        </div>
    </section>

    <section class="page-section">
        <div class="container">
            <div class="filter-panel">
                <form action="{{ route('tours.index') }}" method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="filter-search" class="form-label">Search</label>
                        <input type="text" id="filter-search" name="search" value="{{ $filters['search'] }}" class="form-control" placeholder="Destination or title">
                    </div>

                    <div class="form-group">
                        <label for="filter-destination" class="form-label">Destination</label>
                        <select id="filter-destination" name="destination" class="form-control">
                            <option value="">All Destinations</option>
                            @foreach ($destinations as $dest)
                                <option value="{{ $dest->slug }}" @selected($filters['destination'] === $dest->slug)>{{ $dest->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filter-category" class="form-label">Activity</label>
                        <select id="filter-category" name="category" class="form-control">
                            <option value="">All Categories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->slug }}" @selected($filters['category'] === $cat->slug)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filter-duration" class="form-label">Duration</label>
                        <select id="filter-duration" name="duration" class="form-control">
                            <option value="">Any Duration</option>
                            <option value="short" @selected($filters['duration'] === 'short')>1 - 3 Days</option>
                            <option value="medium" @selected($filters['duration'] === 'medium')>4 - 6 Days</option>
                            <option value="long" @selected($filters['duration'] === 'long')>7+ Days</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filter-price" class="form-label">Max Budget ($)</label>
                        <input type="number" id="filter-price" name="max_price" value="{{ $filters['max_price'] }}" min="0" step="10" class="form-control" placeholder="e.g. 1200">
                    </div>

                    <button type="submit" class="btn btn-cyan">Filter Tours</button>
                </form>
            </div>

            @if ($tours->isEmpty())
                <div class="empty-state">
                    <h3>No tour packages matched your filters.</h3>
                    <p>Try clearing some filters or browse all curated journeys.</p>
                    <a href="{{ route('tours.index') }}" class="btn btn-cyan">Reset Filters</a>
                </div>
            @else
                <div class="tours-grid-3">
                    @foreach ($tours as $tour)
                        <article class="tour-card">
                            <a href="{{ route('tours.show', $tour) }}" class="tour-card-thumb">
                                <img src="{{ $tour->imageUrl() }}" alt="{{ $tour->title }}" loading="lazy">
                                @if ($tour->destination)
                                    <span class="tour-card-badge">{{ $tour->destination->name }}</span>
                                @endif
                            </a>
                            <div class="tour-card-body">
                                <h2 class="tour-card-title">
                                    <a href="{{ route('tours.show', $tour) }}">{{ $tour->title }}</a>
                                </h2>
                                <div class="tour-rating">
                                    <span class="stars-row">
                                        @for ($i = 0; $i < 5; $i++)
                                            <svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        @endfor
                                    </span>
                                    <span>({{ number_format((float) $tour->rating, 1) }} Rating)</span>
                                </div>
                                @if ($tour->summary)
                                    <p>{{ $tour->summary }}</p>
                                @endif
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

                {{ $tours->links() }}
            @endif
        </div>
    </section>
@endsection
