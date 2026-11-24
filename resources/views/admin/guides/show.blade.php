@extends('layouts.admin')

@section('title', 'Tour Guide Details')

@section('content')
    <div class="detail-hero-card">
        <div class="detail-hero-top">
            <div class="detail-hero-main">
                <img src="{{ $guide->photoUrl() }}" alt="{{ $guide->name }}" class="detail-hero-avatar">
                <div class="detail-hero-info">
                    <div class="detail-badges">
                        <span class="status-pill {{ $guide->is_active ? 'is-active' : 'is-inactive' }}">
                            {{ $guide->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if ($guide->role)
                            <span class="detail-badge-neutral">{{ $guide->role }}</span>
                        @endif
                    </div>
                    <h2 class="detail-title">{{ $guide->name }}</h2>
                    <p class="detail-subtitle">{{ $guide->role ?: 'Tour Guide' }}</p>
                </div>
            </div>

            <div class="detail-hero-actions">
                <a href="{{ route('admin.guides.index') }}" class="btn btn-light btn-sm">&larr; Back</a>
                <a href="{{ route('admin.guides.edit', $guide) }}" class="btn btn-primary btn-sm">Edit Guide</a>
                <form method="POST" action="{{ route('admin.guides.destroy', $guide) }}"
                    onsubmit="return confirm('Delete this tour guide?');">
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
                    <h3 class="panel-title">Guide Biography</h3>
                </div>
                <div class="detail-section-pad">
                    @if ($guide->bio)
                        <div class="admin-detail-prose">{{ $guide->bio }}</div>
                    @else
                        <p class="detail-subtitle">No biography provided.</p>
                    @endif
                </div>
            </section>
        </div>

        <aside class="panel-card detail-sidebar-card">
            <div class="panel-header">
                <h3 class="panel-title">Profile &amp; Socials</h3>
            </div>
            <div class="detail-meta-list">
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Role</span>
                    <span class="detail-meta-value">{{ $guide->role ?: 'Tour Guide' }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Facebook</span>
                    <span class="detail-meta-value">{{ $guide->facebook_url ?: '—' }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Twitter</span>
                    <span class="detail-meta-value">{{ $guide->twitter_url ?: '—' }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Instagram</span>
                    <span class="detail-meta-value">{{ $guide->instagram_url ?: '—' }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">LinkedIn</span>
                    <span class="detail-meta-value">{{ $guide->linkedin_url ?: '—' }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Sort Order</span>
                    <span class="detail-meta-value">{{ $guide->sort_order }}</span>
                </div>
            </div>
        </aside>
    </div>
@endsection
