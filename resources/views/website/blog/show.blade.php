@extends('layouts.website')

@section('title', $post->title.' — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    <section class="page-banner">
        <div class="container">
            <h1 class="page-banner-title">{{ $post->title }}</h1>
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&rsaquo;</span>
                <a href="{{ route('blog.index') }}">Blog</a>
                <span>&rsaquo;</span>
                <span>Article</span>
            </div>
        </div>
    </section>

    <section class="page-section">
        <div class="container detail-layout">
            <article>
                <div class="detail-cover">
                    <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}">
                </div>
                <div class="detail-meta-strip">
                    <span class="meta-pill">{{ $post->category }}</span>
                    <span class="meta-pill">{{ ($post->published_at ?? $post->created_at)->format('F d, Y') }}</span>
                    <span class="meta-pill">{{ $post->read_time }}</span>
                </div>
                <div style="white-space: pre-line; font-size: 16px; line-height: 1.8;">{{ $post->content }}</div>
            </article>

            <aside class="sidebar-card">
                <h3 style="margin-bottom: 18px;">Recent Articles</h3>
                @forelse ($recentPosts as $recent)
                    <div style="padding: 12px 0; border-bottom: 1px solid var(--border-light);">
                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">
                            {{ ($recent->published_at ?? $recent->created_at)->format('M d, Y') }}
                        </div>
                        <a href="{{ route('blog.show', $recent) }}" style="font-weight: 700; color: var(--primary-dark);">
                            {{ $recent->title }}
                        </a>
                    </div>
                @empty
                    <p>No additional articles available.</p>
                @endforelse
            </aside>
        </div>
    </section>
@endsection
