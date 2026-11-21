@extends('layouts.website')

@section('title', $destination->name.' — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    <section class="page-banner">
        <div class="container">
            <h1 class="page-banner-title">{{ $destination->name }}</h1>
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&rsaquo;</span>
                <span>{{ $destination->name }}</span>
            </div>
        </div>
    </section>

    <section class="page-section">
        <div class="container">
            <div class="detail-layout" style="margin-bottom: 48px;">
                <div class="detail-cover" style="margin-bottom: 0; height: 340px;">
                    <img src="{{ $destination->imageUrl() }}" alt="{{ $destination->name }}">
                </div>
                <div class="sidebar-card" style="position: static;">
                    <span class="section-script">Destination Guide</span>
                    <h2 style="margin-bottom: 12px;">Explore {{ $destination->name }}</h2>
                    @if ($destination->country)
                        <div class="detail-meta-strip">
                            <span class="meta-pill">{{ $destination->country }}</span>
                            <span class="meta-pill">{{ $destination->displayListingsCount() }}</span>
                        </div>
                    @endif
                    <p>{{ $destination->description }}</p>
                    <a href="{{ route('contact.create') }}" class="btn btn-cyan">Request Custom Itinerary</a>
                </div>
            </div>

            <h2 style="margin-bottom: 24px;">Available Tours in {{ $destination->name }}</h2>

            @if ($tours->isEmpty())
                <div class="empty-state">
                    <h3>No active packages in {{ $destination->name }} yet.</h3>
                    <p>Browse our full collection of curated global tours or ask for a tailored itinerary.</p>
                    <a href="{{ route('tours.index') }}" class="btn btn-cyan">Browse All Tours</a>
                </div>
            @else
                <div class="tours-grid-3">
                    @foreach ($tours as $tour)
                        <article class="tour-card">
                            <a href="{{ route('tours.show', $tour) }}" class="tour-card-thumb">
                                <img src="{{ $tour->imageUrl() }}" alt="{{ $tour->title }}" loading="lazy">
                            </a>
                            <div class="tour-card-body">
                                <h3 class="tour-card-title">
                                    <a href="{{ route('tours.show', $tour) }}">{{ $tour->title }}</a>
                                </h3>
                                <div class="tour-price-line">
                                    ${{ number_format((float) $tour->price, 2) }} <span>/Person</span>
                                </div>
                                <div class="tour-card-footer">
                                    <span class="tour-duration">{{ $tour->displayDuration() }}</span>
                                    <a href="{{ route('tours.show', $tour) }}" class="btn btn-outline-soft">Book Now</a>
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
