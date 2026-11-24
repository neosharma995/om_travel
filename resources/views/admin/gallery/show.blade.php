@extends('layouts.admin')

@section('title', 'Gallery Item Details')

@section('content')
    <div class="detail-hero-card">
        <div class="detail-hero-top">
            <div class="detail-hero-main">
                <div class="detail-hero-info">
                    <div class="detail-badges">
                        <span class="status-pill {{ $item->is_active ? 'is-active' : 'is-inactive' }}">
                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if ($item->location)
                            <span class="detail-badge-neutral">{{ $item->location }}</span>
                        @endif
                    </div>
                    <h2 class="detail-title">{{ $item->title }}</h2>
                    <p class="detail-subtitle">Recent gallery showcase item</p>
                </div>
            </div>

            <div class="detail-hero-actions">
                <a href="{{ route('admin.gallery.index') }}" class="btn btn-light btn-sm">&larr; Back</a>
                <a href="{{ route('admin.gallery.edit', $item) }}" class="btn btn-primary btn-sm">Edit Item</a>
                <form method="POST" action="{{ route('admin.gallery.destroy', $item) }}"
                    onsubmit="return confirm('Delete this gallery item?');">
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
                    <h3 class="panel-title">Photo Preview</h3>
                </div>
                <div class="detail-section-pad">
                    <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" class="admin-show-image">
                </div>
            </section>
        </div>

        <aside class="panel-card detail-sidebar-card">
            <div class="panel-header">
                <h3 class="panel-title">Gallery Metadata</h3>
            </div>
            <div class="detail-meta-list">
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Location</span>
                    <span class="detail-meta-value">{{ $item->location ?: '—' }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Sort Order</span>
                    <span class="detail-meta-value">{{ $item->sort_order }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Created</span>
                    <span class="detail-meta-value">{{ $item->created_at?->format('M d, Y') }}</span>
                </div>
            </div>
        </aside>
    </div>
@endsection
