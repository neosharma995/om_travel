@extends('layouts.admin')

@section('title', 'Blog Article Details')

@section('content')
    <div class="detail-hero-card">
        <div class="detail-hero-top">
            <div class="detail-hero-main">
                <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="detail-hero-thumb">
                <div class="detail-hero-info">
                    <div class="detail-badges">
                        <span class="status-pill {{ $post->is_published ? 'is-active' : 'is-inactive' }}">
                            {{ $post->is_published ? 'Published' : 'Draft' }}
                        </span>
                        @if ($post->category)
                            <span class="detail-badge-neutral">{{ $post->category }}</span>
                        @endif
                    </div>
                    <h2 class="detail-title">{{ $post->title }}</h2>
                    <p class="detail-subtitle">
                        {{ $post->read_time ?: '—' }} &bull;
                        {{ $post->published_at?->format('M d, Y') ?? 'Not published yet' }}
                    </p>
                </div>
            </div>

            <div class="detail-hero-actions">
                <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-light btn-sm">&larr; Back</a>
                <a href="{{ route('admin.blog-posts.edit', $post) }}" class="btn btn-primary btn-sm">Edit Article</a>
                <form method="POST" action="{{ route('admin.blog-posts.destroy', $post) }}"
                    onsubmit="return confirm('Delete this blog article?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <div class="detail-layout-grid">
        <div class="detail-main-col">
            <section class="panel-card">
                <div class="panel-header">
                    <h3 class="panel-title">Article Content</h3>
                </div>
                <div class="detail-section-pad">
                    @if ($post->excerpt)
                        <div class="detail-block">
                            <h4 class="detail-block-title">Excerpt</h4>
                            <p class="detail-lead">{{ $post->excerpt }}</p>
                        </div>
                    @endif

                    <div class="detail-block">
                        <h4 class="detail-block-title">Full Article</h4>
                        @if ($post->content)
                            <div class="admin-detail-prose">{{ $post->content }}</div>
                        @else
                            <p class="detail-subtitle">No article content provided.</p>
                        @endif
                    </div>
                </div>
            </section>
        </div>

        <aside class="panel-card detail-sidebar-card">
            <div class="panel-header">
                <h3 class="panel-title">Article Metadata</h3>
            </div>
            <div class="detail-meta-list">
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Slug</span>
                    <span class="detail-meta-value">{{ $post->slug }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Category</span>
                    <span class="detail-meta-value">{{ $post->category ?: 'General' }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Read Time</span>
                    <span class="detail-meta-value">{{ $post->read_time ?: '—' }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Published At</span>
                    <span class="detail-meta-value">{{ $post->published_at?->format('M d, Y h:i A') ?? 'Not scheduled' }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Last Updated</span>
                    <span class="detail-meta-value">{{ $post->updated_at?->format('M d, Y') }}</span>
                </div>
            </div>
        </aside>
    </div>
@endsection
