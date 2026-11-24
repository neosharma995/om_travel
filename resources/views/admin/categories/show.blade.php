@extends('layouts.admin')

@section('title', 'Category Details')

@section('content')
    <div class="detail-hero-card">
        <div class="detail-hero-top">
            <div class="detail-hero-main">
                <img src="{{ $category->imageUrl() }}" alt="{{ $category->name }}" class="detail-hero-thumb">
                <div class="detail-hero-info">
                    <div class="detail-badges">
                        <span class="status-pill {{ $category->is_active ? 'is-active' : 'is-inactive' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="detail-badge-neutral">
                            {{ $category->parent ? 'Subcategory of ' . $category->parent->name : 'Top-Level Category' }}
                        </span>
                    </div>
                    <h2 class="detail-title">{{ $category->name }}</h2>
                    <p class="detail-subtitle">
                        {{ $category->tours_count }} {{ Str::plural('tour package', $category->tours_count) }} &bull;
                        {{ $category->children->count() }} {{ Str::plural('subcategory', $category->children->count()) }}
                    </p>
                </div>
            </div>

            <div class="detail-hero-actions">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-light btn-sm">&larr; Back</a>
                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary btn-sm">Edit Category</a>
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                    onsubmit="return confirm('Delete this category?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <div class="detail-layout-grid">
        <div class="detail-main-col">
            @if ($category->children->isNotEmpty())
                <section class="panel-card">
                    <div class="panel-header">
                        <h3 class="panel-title">Subcategories ({{ $category->children->count() }})</h3>
                    </div>
                    <div class="detail-section-pad">
                        <div class="detail-chip-list">
                            @foreach ($category->children as $child)
                                <a href="{{ route('admin.categories.show', $child) }}" class="detail-chip-link">
                                    <span>{{ $child->name }}</span>
                                    <span class="status-pill {{ $child->is_active ? 'is-active' : 'is-inactive' }}">
                                        {{ $child->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            <section class="panel-card">
                <div class="panel-header">
                    <h3 class="panel-title">Assigned Tour Packages ({{ $category->tours_count }})</h3>
                </div>

                @if ($tours->isEmpty())
                    <p class="detail-empty-state">No tour packages are directly assigned to this category yet.</p>
                @else
                    <div class="table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th>Destination</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tours as $tour)
                                    <tr>
                                        <td>
                                            <strong>{{ $tour->title }}</strong>
                                            <div class="cell-sub">{{ $tour->tourType?->name ?? 'Standard' }}</div>
                                        </td>
                                        <td>{{ $tour->destination?->name ?? 'Unassigned' }}</td>
                                        <td>&#8377;{{ number_format((float) $tour->price, 2) }}</td>
                                        <td>
                                            <span class="status-pill {{ $tour->is_active ? 'is-active' : 'is-inactive' }}">
                                                {{ $tour->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.tours.show', $tour) }}" class="btn btn-view btn-sm">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        <aside class="panel-card detail-sidebar-card">
            <div class="panel-header">
                <h3 class="panel-title">Category Metadata</h3>
            </div>
            <div class="detail-meta-list">
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Slug</span>
                    <span class="detail-meta-value">{{ $category->slug }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Parent Category</span>
                    <span class="detail-meta-value">
                        @if ($category->parent)
                            <a href="{{ route('admin.categories.show', $category->parent) }}">
                                {{ $category->parent->name }}
                            </a>
                        @else
                            Top-level Category
                        @endif
                    </span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Subcategories</span>
                    <span class="detail-meta-value">{{ $category->children->count() }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Direct Tours</span>
                    <span class="detail-meta-value">{{ $category->tours_count }}</span>
                </div>
                <div class="detail-meta-row">
                    <span class="detail-meta-label">Sort Order</span>
                    <span class="detail-meta-value">{{ $category->sort_order }}</span>
                </div>
            </div>
        </aside>
    </div>
@endsection
