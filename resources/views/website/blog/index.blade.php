@extends('layouts.website')

@section('title', 'Travel Blog & Articles — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    <section class="page-banner">
        <div class="container">
            <h1 class="page-banner-title">Travel Blog &amp; Articles</h1>
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&rsaquo;</span>
                <span>Blog</span>
            </div>
        </div>
    </section>

    <section class="page-section">
        <div class="container">
            @if ($posts->isEmpty())
                <div class="empty-state">
                    <h3>No blog articles published yet.</h3>
                    <p>Check back soon for inspiring travel guides and seasonal tips.</p>
                </div>
            @else
                <div class="blog-grid">
                    @foreach ($posts as $post)
                        <article class="blog-card blog-card-surface">
                            <a href="{{ route('blog.show', $post) }}" class="blog-thumb">
                                <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" loading="lazy">
                            </a>
                            <div class="blog-meta">
                                <span>{{ ($post->published_at ?? $post->created_at)->format('F d, Y') }}</span>
                                <span class="blog-meta-sep"></span>
                                <span>{{ $post->read_time }}</span>
                            </div>
                            <h2 class="blog-title">
                                <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                            </h2>
                            @if ($post->excerpt)
                                <p class="blog-excerpt">{{ $post->excerpt }}</p>
                            @endif
                            <div style="margin-top: auto;">
                                <a href="{{ route('blog.show', $post) }}" class="btn btn-outline-soft">Read More</a>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{ $posts->links() }}
            @endif
        </div>
    </section>
@endsection
