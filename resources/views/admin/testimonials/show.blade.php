@extends('layouts.admin')

@section('title', 'Testimonial Details')

@section('content')
    <div class="detail-hero-card">
        <div class="detail-hero-top">
            <div class="detail-hero-main">
                <img src="{{ $testimonial->avatarUrl() }}" alt="{{ $testimonial->client_name }}" class="detail-hero-avatar">
                <div class="detail-hero-info">
                    <div class="detail-badges">
                        <span class="status-pill {{ $testimonial->is_active ? 'is-active' : 'is-inactive' }}">
                            {{ $testimonial->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if ($testimonial->is_featured)
                            <span class="status-pill is-featured">Featured</span>
                        @endif
                        <span class="detail-badge-neutral">{{ $testimonial->rating }} / 5 Rating</span>
                    </div>
                    <h2 class="detail-title">{{ $testimonial->client_name }}</h2>
                    <p class="detail-subtitle">{{ $testimonial->client_role ?: 'Verified Traveler' }}</p>
                </div>
            </div>

            <div class="detail-hero-actions">
                <a href="{{ route('admin.testimonials.index') }}" class="btn btn-light btn-sm">&larr; Back</a>
                <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-primary btn-sm">Edit Testimonial</a>
                <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}"
                    onsubmit="return confirm('Delete this testimonial?');">
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
                    <h3 class="panel-title">Client Quote</h3>
                </div>
                <div class="detail-section-pad">
                    <blockquote class="detail-quote-box">{{ $testimonial->quote }}</blockquote>
                </div>
            </section>
        </div>

        <aside class="panel-card detail-sidebar-card">
            <div class="panel-header">
                <h3 class="panel-title">Testimonial Metadata</h3>
            </div>
            <div class="detail-meta-list">
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Client Role</span>
                    <span class="detail-meta-value">{{ $testimonial->client_role ?: '—' }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Rating</span>
                    <span class="detail-meta-value">{{ $testimonial->rating }} / 5</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Featured</span>
                    <span class="detail-meta-value">{{ $testimonial->is_featured ? 'Yes' : 'No' }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Sort Order</span>
                    <span class="detail-meta-value">{{ $testimonial->sort_order }}</span>
                </div>
            </div>
        </aside>
    </div>
@endsection
