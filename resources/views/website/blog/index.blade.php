@extends('layouts.website')

@section('title', 'Travel Blog & Articles — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    {{-- Editorial Hero Banner --}}
    <section class="page-banner blog-hero-banner">
        <div class="container">
            <div class="blog-banner-content">
                <span class="blog-banner-eyebrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                    Stories &amp; Field Chronicles
                </span>
                <h1 class="page-banner-title">Travel Blog &amp; Articles</h1>
                <p class="blog-banner-desc">Curated expedition guides, seasonal travel tips, cultural chronicles, and visual stories from our resident globetrotters and expedition leaders.</p>
                <div class="breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span>&rsaquo;</span>
                    <span>Blog</span>
                </div>

                <div class="blog-banner-hallmarks">
                    <div class="blog-hallmark-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <span>Curated Expedition Guides</span>
                    </div>
                    <div class="blog-hallmark-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Authentic Field Notes</span>
                    </div>
                    <div class="blog-hallmark-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>Global Sanctuaries</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Blog Magazine Section --}}
    <section class="page-section blog-magazine-section">
        <div class="container">
            {{-- Category Filter & Editorial Navigation Toolbar --}}
            <div class="blog-nav-toolbar">
                <div class="blog-category-tabs" role="tablist" aria-label="Article categories">
                    <a href="{{ route('blog.index') }}"
                       class="blog-filter-pill {{ empty($selectedCategory) || strtolower($selectedCategory) === 'all' ? 'is-active' : '' }}"
                       role="tab"
                       aria-selected="{{ empty($selectedCategory) || strtolower($selectedCategory) === 'all' ? 'true' : 'false' }}">
                        <span>All Articles</span>
                        <span class="blog-pill-count">{{ $posts->total() }}</span>
                    </a>

                    @if (isset($categories) && count($categories) > 0)
                        @foreach ($categories as $cat)
                            @php
                                $isActiveCat = strtolower($selectedCategory) === strtolower($cat);
                            @endphp
                            <a href="{{ route('blog.index', ['category' => $cat]) }}"
                               class="blog-filter-pill {{ $isActiveCat ? 'is-active' : '' }}"
                               role="tab"
                               aria-selected="{{ $isActiveCat ? 'true' : 'false' }}">
                                <span>{{ $cat }}</span>
                            </a>
                        @endforeach
                    @endif
                </div>

                <div class="blog-nav-meta">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Curated Weekly by OmTravel Editorial</span>
                </div>
            </div>

            @if ($posts->isEmpty())
                <div class="empty-state blog-empty-state">
                    <div class="empty-state-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="48" height="48">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                    </div>
                    <h3>No blog articles published yet.</h3>
                    <p>Check back soon for inspiring travel guides, destination essays, and seasonal tips.</p>
                    @if (!empty($selectedCategory))
                        <a href="{{ route('blog.index') }}" class="btn btn-cyan">
                            <span>View All Articles</span>
                        </a>
                    @endif
                </div>
            @else
                @php
                    $isFirstPage = ($posts->currentPage() === 1);
                    $leadPost = $isFirstPage ? $posts->first() : null;
                    $otherPosts = $isFirstPage ? $posts->slice(1) : $posts;
                    $twinFeatures = $otherPosts->take(2);
                    $standardPosts = $otherPosts->slice(2);
                @endphp

                {{-- 1. Grand Editorial Cover Story (Page 1 Lead Feature) --}}
                @if ($leadPost)
                    <article class="blog-lead-story">
                        <a href="{{ route('blog.show', $leadPost) }}" class="blog-lead-media" aria-label="{{ $leadPost->title }}">
                            <img src="{{ $leadPost->imageUrl() }}" alt="{{ $leadPost->title }}" loading="lazy" class="blog-lead-img">
                            <div class="blog-lead-overlay"></div>

                            <div class="blog-lead-badges">
                                <span class="blog-badge blog-badge--lead">
                                    <svg viewBox="0 0 24 24" fill="currentColor" width="12" height="12"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    Cover Story
                                </span>
                                @if ($leadPost->category)
                                    <span class="blog-badge blog-badge--category">{{ $leadPost->category }}</span>
                                @endif
                            </div>
                        </a>

                        <div class="blog-lead-content">
                            <div class="blog-lead-header">
                                <span class="blog-edition-tag">Editorial Feature &bull; Vol. {{ date('Y') }}</span>
                                <div class="blog-meta-strip">
                                    <span class="blog-meta-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="14" height="14"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        <span>{{ ($leadPost->published_at ?? $leadPost->created_at)->format('F d, Y') }}</span>
                                    </span>
                                    <span class="blog-meta-sep"></span>
                                    <span class="blog-meta-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="14" height="14"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <span>{{ $leadPost->read_time }}</span>
                                    </span>
                                </div>
                            </div>

                            <h2 class="blog-lead-title">
                                <a href="{{ route('blog.show', $leadPost) }}">{{ $leadPost->title }}</a>
                            </h2>

                            @if ($leadPost->excerpt)
                                <p class="blog-lead-excerpt">{{ $leadPost->excerpt }}</p>
                            @endif

                            <div class="blog-lead-action">
                                <a href="{{ route('blog.show', $leadPost) }}" class="btn btn-cyan blog-lead-cta">
                                    <span>Read Cover Story</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="15" height="15"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                                <span class="blog-author-note">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    By OmTravel Specialists
                                </span>
                            </div>
                        </div>
                    </article>
                @endif

                {{-- 2. Remaining Articles: Creative Multi-Cadence Magazine Layout --}}
                @if ($otherPosts->isNotEmpty())
                    <div class="blog-archive-header">
                        <div class="blog-archive-label">
                            <span class="blog-archive-script">Latest Dispatches</span>
                            <h3 class="blog-archive-title">Recent Travel Stories &amp; Field Guides</h3>
                        </div>
                        <div class="blog-archive-rule"></div>
                    </div>

                    {{-- Twin Secondary Features (Side-by-Side 2-Column Spread) --}}
                    @if ($twinFeatures->isNotEmpty())
                        <div class="blog-twin-grid">
                            @foreach ($twinFeatures as $post)
                                <article class="blog-twin-card">
                                    <a href="{{ route('blog.show', $post) }}" class="blog-twin-media" aria-label="{{ $post->title }}">
                                        <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" loading="lazy" class="blog-twin-img">
                                        <div class="blog-twin-overlay"></div>
                                        @if ($post->category)
                                            <span class="blog-twin-category">{{ $post->category }}</span>
                                        @endif
                                    </a>

                                    <div class="blog-twin-body">
                                        <div class="blog-meta-strip">
                                            <span class="blog-meta-item">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="13" height="13"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                <span>{{ ($post->published_at ?? $post->created_at)->format('F d, Y') }}</span>
                                            </span>
                                            <span class="blog-meta-sep"></span>
                                            <span class="blog-meta-item">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="13" height="13"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                <span>{{ $post->read_time }}</span>
                                            </span>
                                        </div>

                                        <h3 class="blog-twin-title">
                                            <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                                        </h3>

                                        @if ($post->excerpt)
                                            <p class="blog-twin-excerpt">{{ \Illuminate\Support\Str::limit($post->excerpt, 150) }}</p>
                                        @endif

                                        <div class="blog-card-action-bar">
                                            <a href="{{ route('blog.show', $post) }}" class="blog-inline-link">
                                                <span>Read Article</span>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="14" height="14"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif

                    {{-- Standard Supporting Catalog Grid (for 3rd, 4th, 5th+ posts) --}}
                    @if ($standardPosts->isNotEmpty())
                        <div class="blog-catalog-grid">
                            @foreach ($standardPosts as $post)
                                <article class="blog-catalog-card">
                                    <a href="{{ route('blog.show', $post) }}" class="blog-catalog-media" aria-label="{{ $post->title }}">
                                        <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" loading="lazy" class="blog-catalog-img">
                                        @if ($post->category)
                                            <span class="blog-catalog-category">{{ $post->category }}</span>
                                        @endif
                                    </a>

                                    <div class="blog-catalog-body">
                                        <div class="blog-meta-strip blog-meta-strip--sm">
                                            <span class="blog-meta-item">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="12" height="12"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                <span>{{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}</span>
                                            </span>
                                            <span class="blog-meta-sep"></span>
                                            <span class="blog-meta-item">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="12" height="12"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                <span>{{ $post->read_time }}</span>
                                            </span>
                                        </div>

                                        <h4 class="blog-catalog-title">
                                            <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                                        </h4>

                                        @if ($post->excerpt)
                                            <p class="blog-catalog-excerpt">{{ \Illuminate\Support\Str::limit($post->excerpt, 110) }}</p>
                                        @endif

                                        <div class="blog-catalog-action">
                                            <a href="{{ route('blog.show', $post) }}" class="blog-inline-link">
                                                <span>Read Story</span>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="13" height="13"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                @endif

                {{-- Editorial Newsletter Callout Strip --}}
                <div class="blog-reader-callout">
                    <div class="blog-callout-copy">
                        <span class="blog-callout-eyebrow">The Expedition Dispatch</span>
                        <h3 class="blog-callout-heading">Receive Curated Travel Intelligence Weekly</h3>
                        <p class="blog-callout-text">Join thousands of discerning travelers receiving secret atoll itineraries, seasonal flight insights, and photography stories directly in their inbox.</p>
                    </div>

                    <form action="{{ route('contact.store') }}" method="POST" class="blog-callout-form">
                        @csrf
                        <input type="hidden" name="name" value="Travel Blog Subscriber">
                        <input type="hidden" name="message" value="Subscribed to The Expedition Dispatch newsletter from the blog page">
                        <div class="blog-callout-input-wrap">
                            <input type="email" name="email" class="blog-callout-input" placeholder="Enter your email address..." required>
                            <button type="submit" class="btn btn-cyan blog-callout-submit">
                                <span>Subscribe</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="14" height="14"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Sophisticated Pagination Bar --}}
                <div class="blog-pagination-wrapper">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
