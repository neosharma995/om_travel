@extends('layouts.website')

@section('title', $post->title.' — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    {{-- Editorial Article Header & Hero Banner --}}
    <section class="article-header-section">
        <div class="container">
            <div class="article-header-inner">
                {{-- Breadcrumbs Navigation --}}
                <nav class="article-breadcrumbs" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span class="crumb-sep">&rsaquo;</span>
                    <a href="{{ route('blog.index') }}">Blog</a>
                    @if ($post->category)
                        <span class="crumb-sep">&rsaquo;</span>
                        <a href="{{ route('blog.index', ['category' => $post->category]) }}">{{ $post->category }}</a>
                    @endif
                    <span class="crumb-sep">&rsaquo;</span>
                    <span class="crumb-current">{{ \Illuminate\Support\Str::limit($post->title, 40) }}</span>
                </nav>

                {{-- Category Pill --}}
                @if ($post->category)
                    <div class="article-category-wrap">
                        <a href="{{ route('blog.index', ['category' => $post->category]) }}" class="article-category-pill">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                <line x1="7" y1="7" x2="7.01" y2="7"/>
                            </svg>
                            <span>{{ $post->category }}</span>
                        </a>
                    </div>
                @endif

                {{-- Headline --}}
                <h1 class="article-main-title">{{ $post->title }}</h1>

                {{-- Editorial Standfirst / Introduction --}}
                @if ($post->excerpt)
                    <p class="article-standfirst-desc">{{ $post->excerpt }}</p>
                @endif

                {{-- Publication Metadata Strip --}}
                <div class="article-meta-toolbar">
                    <div class="article-author-block">
                        <div class="article-author-avatar" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div class="article-author-text">
                            <span class="article-author-name">{{ $siteSettings['site_name'] ?? 'OmTravel' }} Editorial</span>
                            <span class="article-author-byline">Field Correspondent</span>
                        </div>
                    </div>

                    <div class="article-meta-stats">
                        <span class="article-meta-stat">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="14" height="14">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <span>{{ ($post->published_at ?? $post->created_at)->format('F d, Y') }}</span>
                        </span>
                        <span class="article-meta-dot">&bull;</span>
                        <span class="article-meta-stat">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="14" height="14">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <span>{{ $post->read_time }}</span>
                        </span>
                    </div>
                </div>

                {{-- Large Dramatic Cover Media --}}
                <div class="article-hero-media">
                    <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="article-hero-img">
                    <div class="article-hero-overlay"></div>
                    <div class="article-hero-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                            <circle cx="12" cy="12" r="10"/>
                            <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>
                        </svg>
                        <span>Field Archive &bull; {{ $post->category ?? 'Sanctuary Guide' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Article Reading Experience --}}
    <section class="article-reading-section">
        <div class="container">
            <div class="article-reading-layout">
                {{-- Primary Editorial Content Column --}}
                <main class="article-prose-column">
                    @php
                        // Split content by paragraph boundaries for elegant typography
                        $paragraphs = array_filter(array_map('trim', preg_split('/\r\n\r\n|\n\n/', $post->content)));
                    @endphp

                    <div class="article-body-prose">
                        @foreach ($paragraphs as $index => $paragraph)
                            <p class="article-paragraph {{ $index === 0 ? 'article-paragraph--lead' : '' }}">{{ $paragraph }}</p>
                        @endforeach
                    </div>

                    {{-- Photographic Vignette Quote Break --}}
                    <div class="article-editorial-quote">
                        <svg class="quote-icon" viewBox="0 0 24 24" fill="currentColor" width="28" height="28" aria-hidden="true">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                        </svg>
                        <blockquote>Travel is more than the seeing of sights; it is a profound transformation in how the ideas of living endure in the mind.</blockquote>
                        <cite>&mdash; OmTravel Expedition Dispatch Bureau</cite>
                    </div>

                    {{-- Article Footer Tags & Actions --}}
                    <div class="article-footer-tags">
                        <div class="article-tags-left">
                            <span class="article-tag-label">Filed Under:</span>
                            @if ($post->category)
                                <a href="{{ route('blog.index', ['category' => $post->category]) }}" class="article-tag-link">
                                    #{{ $post->category }}
                                </a>
                            @endif
                            <span class="article-tag-link">#GlobalExploration</span>
                            <span class="article-tag-link">#TravelNotes</span>
                        </div>

                        <div class="article-share-actions">
                            <a href="{{ route('blog.index') }}" class="btn btn-outline-soft article-return-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="14" height="14">
                                    <line x1="19" y1="12" x2="5" y2="12"/>
                                    <polyline points="12 19 5 12 12 5"/>
                                </svg>
                                <span>Back to All Articles</span>
                            </a>
                        </div>
                    </div>

                    {{-- Next / Previous Editorial Navigation --}}
                    @if (isset($prevPost) || isset($nextPost))
                        <nav class="article-sequence-nav" aria-label="Article sequence">
                            @if ($prevPost)
                                <a href="{{ route('blog.show', $prevPost) }}" class="article-nav-card article-nav-card--prev">
                                    <span class="article-nav-direction">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="13" height="13"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                                        <span>Previous Dispatch</span>
                                    </span>
                                    <strong class="article-nav-title">{{ $prevPost->title }}</strong>
                                </a>
                            @else
                                <div class="article-nav-card article-nav-card--empty"></div>
                            @endif

                            @if ($nextPost)
                                <a href="{{ route('blog.show', $nextPost) }}" class="article-nav-card article-nav-card--next">
                                    <span class="article-nav-direction">
                                        <span>Next Dispatch</span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="13" height="13"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                    </span>
                                    <strong class="article-nav-title">{{ $nextPost->title }}</strong>
                                </a>
                            @endif
                        </nav>
                    @endif
                </main>

                {{-- Elegant Sticky Sidebar with Quick Information & Author Vignette --}}
                <aside class="article-sidebar-column">
                    <div class="article-sidebar-sticky">
                        {{-- Dispatch Dossier Card --}}
                        <div class="article-sidebar-dossier">
                            <span class="dossier-eyebrow">Dispatch Dossier</span>
                            <h4 class="dossier-heading">Article Highlights</h4>
                            <ul class="dossier-list">
                                <li>
                                    <span class="dossier-item-label">Topic</span>
                                    <span class="dossier-item-val">{{ $post->category ?? 'Sanctuary Exploration' }}</span>
                                </li>
                                <li>
                                    <span class="dossier-item-label">Published</span>
                                    <span class="dossier-item-val">{{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}</span>
                                </li>
                                <li>
                                    <span class="dossier-item-label">Reading Time</span>
                                    <span class="dossier-item-val">{{ $post->read_time }}</span>
                                </li>
                                <li>
                                    <span class="dossier-item-label">Editorial Desk</span>
                                    <span class="dossier-item-val">Resident Specialists</span>
                                </li>
                            </ul>
                        </div>

                        {{-- Travel Booking Teaser Card --}}
                        <div class="article-sidebar-cta">
                            <span class="sidebar-cta-eyebrow">Plan Your Own Odyssey</span>
                            <h4 class="sidebar-cta-title">Turn This Inspiration Into Reality</h4>
                            <p class="sidebar-cta-desc">Discover our handcrafted packages and private journeys matching these serene horizons.</p>
                            <a href="{{ route('tours.index') }}" class="btn btn-cyan sidebar-cta-btn">
                                <span>Explore Tour Packages</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="14" height="14"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- Related Articles Section --}}
    @if ($recentPosts->isNotEmpty())
        <section class="article-related-section">
            <div class="container">
                <div class="article-related-header">
                    <div class="related-header-copy">
                        <span class="related-script">Further Reading</span>
                        <h2 class="related-title">Related Stories &amp; Further Reading</h2>
                    </div>
                    <a href="{{ route('blog.index') }}" class="btn btn-outline-soft related-view-all">
                        <span>All Dispatches</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>

                <div class="article-related-grid">
                    @foreach ($recentPosts as $recent)
                        <article class="article-related-card">
                            <a href="{{ route('blog.show', $recent) }}" class="related-card-media" aria-label="{{ $recent->title }}">
                                <img src="{{ $recent->imageUrl() }}" alt="{{ $recent->title }}" loading="lazy" class="related-card-img">
                                @if ($recent->category)
                                    <span class="related-card-category">{{ $recent->category }}</span>
                                @endif
                            </a>

                            <div class="related-card-body">
                                <div class="related-card-meta">
                                    <span class="related-meta-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="12" height="12"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        <span>{{ ($recent->published_at ?? $recent->created_at)->format('M d, Y') }}</span>
                                    </span>
                                    <span class="related-meta-sep">&bull;</span>
                                    <span class="related-meta-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="12" height="12"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <span>{{ $recent->read_time }}</span>
                                    </span>
                                </div>

                                <h3 class="related-card-title">
                                    <a href="{{ route('blog.show', $recent) }}">{{ $recent->title }}</a>
                                </h3>

                                @if ($recent->excerpt)
                                    <p class="related-card-excerpt">{{ \Illuminate\Support\Str::limit($recent->excerpt, 100) }}</p>
                                @endif

                                <div class="related-card-footer">
                                    <a href="{{ route('blog.show', $recent) }}" class="related-read-link">
                                        <span>Read Story</span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="13" height="13"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
