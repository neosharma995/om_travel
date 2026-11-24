@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Overview')

@section('content')
    <style>
        /* Modern Creative Dashboard Design */
        .dash-hero-banner {
            position: relative;
            background: linear-gradient(135deg, #0A2B33 0%, #113D48 45%, #165666 80%, #1CA8CB 100%);
            border-radius: 20px;
            padding: 30px 34px;
            margin-bottom: 28px;
            color: #FFFFFF;
            overflow: hidden;
            box-shadow: 0 10px 30px -10px rgba(17, 61, 72, 0.35);
        }

        .dash-hero-banner::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(28, 168, 203, 0.3) 0%, rgba(28, 168, 203, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .dash-hero-banner::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: 25%;
            width: 320px;
            height: 200px;
            background: radial-gradient(ellipse, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .dash-hero-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            flex-wrap: wrap;
        }

        .dash-hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 5px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.3px;
            color: #E6FAFD;
            margin-bottom: 12px;
        }

        .dash-pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #34D399;
            box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7);
            animation: dashPulse 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
        }

        @keyframes dashPulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.6);
            }
            50% {
                box-shadow: 0 0 0 8px rgba(52, 211, 153, 0);
            }
        }

        .dash-hero-title {
            font-family: var(--font-heading);
            font-size: 26px;
            font-weight: 800;
            color: #FFFFFF;
            margin: 0 0 6px 0;
            line-height: 1.25;
        }

        .dash-hero-desc {
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.82);
            margin: 0;
            max-width: 580px;
            line-height: 1.5;
        }

        .dash-hero-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .dash-hero-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .dash-hero-btn-primary {
            background-color: #FFFFFF;
            color: var(--admin-sidebar);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        }

        .dash-hero-btn-primary:hover {
            background-color: #E6FAFD;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
        }

        .dash-hero-btn-glass {
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #FFFFFF;
            backdrop-filter: blur(6px);
        }

        .dash-hero-btn-glass:hover {
            background: rgba(255, 255, 255, 0.22);
            color: #FFFFFF;
            transform: translateY(-2px);
        }

        /* Creative KPI Cards Grid */
        .dash-kpi-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }

        .dash-card {
            background: #FFFFFF;
            border: 1px solid var(--admin-border);
            border-radius: 16px;
            padding: 20px;
            position: relative;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .dash-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 28px -8px rgba(17, 61, 72, 0.12);
            border-color: #BCDDE6;
        }

        .dash-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--card-accent, var(--admin-accent));
            border-radius: 16px 16px 0 0;
        }

        .dash-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .dash-card-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--card-icon-bg, #E9F6F9);
            color: var(--card-accent, var(--admin-accent));
        }

        .dash-card-icon svg {
            width: 22px;
            height: 22px;
        }

        .dash-card-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 999px;
            background: var(--card-icon-bg, #E9F6F9);
            color: var(--card-accent, var(--admin-accent));
        }

        .dash-card-label {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--admin-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .dash-card-val {
            font-family: var(--font-heading);
            font-size: 30px;
            font-weight: 800;
            color: var(--admin-sidebar);
            line-height: 1.1;
            margin-bottom: 6px;
        }

        .dash-card-sub {
            font-size: 12px;
            color: var(--admin-muted);
            line-height: 1.4;
            margin-bottom: 14px;
            min-height: 34px;
        }

        .dash-card-footer {
            padding-top: 10px;
            border-top: 1px solid #EDF4F7;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 700;
            color: var(--card-accent, var(--admin-accent-dark));
        }

        .dash-card-footer:hover {
            color: var(--admin-sidebar);
        }

        /* Quick Action Shortcuts Launcher */
        .dash-shortcuts-bar {
            background: #FFFFFF;
            border: 1px solid var(--admin-border);
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .dash-shortcuts-label {
            font-size: 13px;
            font-weight: 800;
            color: var(--admin-sidebar);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dash-shortcuts-items {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .dash-shortcut-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            background: #F4F8FA;
            border: 1px solid var(--admin-border);
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--admin-sidebar);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .dash-shortcut-chip:hover {
            background: #E9F6F9;
            border-color: var(--admin-accent);
            color: var(--admin-accent-dark);
            transform: translateY(-2px);
        }

        /* User Avatar Initials Badge */
        .user-avatar-initials {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #113D48 0%, #1CA8CB 100%);
            color: #FFFFFF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11.5px;
            font-weight: 800;
            flex-shrink: 0;
        }

        @media (max-width: 1200px) {
            .dash-kpi-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .dash-kpi-grid {
                grid-template-columns: 1fr;
            }
            .dash-hero-banner {
                padding: 22px 20px;
            }
            .dash-hero-title {
                font-size: 22px;
            }
        }
    </style>

    {{-- Grand Executive Welcome Banner --}}
    <div class="dash-hero-banner">
        <div class="dash-hero-content">
            <div>
                <div class="dash-hero-pill">
                    <span class="dash-pulse-dot"></span>
                    <span>Himachal Pradesh Operations &bull; Live Console</span>
                </div>
                <h1 class="dash-hero-title">Namaste, {{ auth()->user()->name ?? 'Admin' }} 👋</h1>
                <p class="dash-hero-desc">
                    Real-time operational summary of OmTravel's mountain expeditions, traveler bookings, customer messages, and content archive.
                </p>
                <div style="display: flex; gap: 16px; margin-top: 14px; font-size: 12.5px; color: rgba(255,255,255,0.9); flex-wrap: wrap;">
                    <span>📅 {{ now()->format('l, d F Y') }}</span>
                    <span>&bull;</span>
                    <span>🏔️ <strong>{{ $stats['activeToursCount'] ?? $stats['toursCount'] }}</strong> Active Expeditions</span>
                    <span>&bull;</span>
                    <span>💰 <strong>₹{{ number_format((float) ($stats['totalRevenue'] ?? 0), 2) }}</strong> Confirmed Volume</span>
                </div>
            </div>
            <div class="dash-hero-actions">
                <a href="{{ route('admin.tours.create') }}" class="dash-hero-btn dash-hero-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="15" height="15"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span>Add Tour</span>
                </a>
                <a href="{{ route('admin.trash.index') }}" class="dash-hero-btn dash-hero-btn-glass" title="View Trashed Items">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    <span>Trash Bin ({{ $stats['trashedCount'] }})</span>
                </a>
                <a href="{{ route('admin.settings.edit') }}" class="dash-hero-btn dash-hero-btn-glass" title="Website Settings">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    <span>Settings</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Creative 5-Card KPI Grid --}}
    <div class="dash-kpi-grid">
        {{-- Card 1: Tour Packages --}}
        <div class="dash-card" style="--card-accent: #0284C7; --card-icon-bg: #E0F2FE;">
            <div>
                <div class="dash-card-header">
                    <span class="dash-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </span>
                    <span class="dash-card-badge">Expeditions</span>
                </div>
                <div class="dash-card-label">Tour Packages</div>
                <div class="dash-card-val">{{ $stats['toursCount'] }}</div>
                <div class="dash-card-sub">
                    {{ $stats['categoriesCount'] }} Categories &bull; {{ $stats['destinationsCount'] }} Destinations ({{ $stats['popularDestinationsCount'] ?? 0 }} Popular)
                </div>
            </div>
            <a href="{{ route('admin.tours.index') }}" class="dash-card-footer">
                <span>View All Tours</span>
                <span>&rarr;</span>
            </a>
        </div>

        {{-- Card 2: Bookings --}}
        <div class="dash-card" style="--card-accent: #059669; --card-icon-bg: #D1FAE5;">
            <div>
                <div class="dash-card-header">
                    <span class="dash-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </span>
                    @if ($stats['pendingBookingsCount'] > 0)
                        <span class="dash-card-badge" style="background: #FEF3C7; color: #B45309;">
                            {{ $stats['pendingBookingsCount'] }} Pending
                        </span>
                    @else
                        <span class="dash-card-badge">Reservations</span>
                    @endif
                </div>
                <div class="dash-card-label">Tour Bookings</div>
                <div class="dash-card-val">{{ $stats['bookingsCount'] }}</div>
                <div class="dash-card-sub">
                    {{ $stats['pendingBookingsCount'] }} Pending Confirmation &bull; ₹{{ number_format((float) ($stats['totalRevenue'] ?? 0), 0) }} Volume
                </div>
            </div>
            <a href="{{ route('admin.bookings.index') }}" class="dash-card-footer">
                <span>Manage Reservations</span>
                <span>&rarr;</span>
            </a>
        </div>

        {{-- Card 3: Contact Inquiries --}}
        <div class="dash-card" style="--card-accent: #D97706; --card-icon-bg: #FEF3C7;">
            <div>
                <div class="dash-card-header">
                    <span class="dash-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </span>
                    @if ($stats['newInquiriesCount'] > 0)
                        <span class="dash-card-badge" style="background: #FEE2E2; color: #DC2626;">
                            {{ $stats['newInquiriesCount'] }} New
                        </span>
                    @else
                        <span class="dash-card-badge">Inquiries</span>
                    @endif
                </div>
                <div class="dash-card-label">Contact Inquiries</div>
                <div class="dash-card-val">{{ $stats['inquiriesCount'] }}</div>
                <div class="dash-card-sub">
                    {{ $stats['newInquiriesCount'] }} Unread Messages &bull; Verified Inbound Leads
                </div>
            </div>
            <a href="{{ route('admin.inquiries.index') }}" class="dash-card-footer">
                <span>View Messages</span>
                <span>&rarr;</span>
            </a>
        </div>

        {{-- Card 4: Content Items --}}
        <div class="dash-card" style="--card-accent: #6366F1; --card-icon-bg: #EEF2FF;">
            <div>
                <div class="dash-card-header">
                    <span class="dash-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </span>
                    <span class="dash-card-badge">Media Hub</span>
                </div>
                <div class="dash-card-label">Content Items</div>
                <div class="dash-card-val">{{ $stats['galleryCount'] + $stats['guidesCount'] + $stats['blogPostsCount'] }}</div>
                <div class="dash-card-sub">
                    {{ $stats['guidesCount'] }} Guides &bull; {{ $stats['blogPostsCount'] }} Articles &bull; {{ $stats['galleryCount'] }} Photos
                </div>
            </div>
            <a href="{{ route('admin.blog-posts.index') }}" class="dash-card-footer">
                <span>Browse Content</span>
                <span>&rarr;</span>
            </a>
        </div>

        {{-- Card 5: Trash Bin --}}
        <div class="dash-card" style="--card-accent: #E11D48; --card-icon-bg: #FFE4E6;">
            <div>
                <div class="dash-card-header">
                    <span class="dash-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </span>
                    <span class="dash-card-badge" style="background: #FFE4E6; color: #BE123C;">Recovery</span>
                </div>
                <div class="dash-card-label">Trash Bin</div>
                <div class="dash-card-val">{{ $stats['trashedCount'] }}</div>
                <div class="dash-card-sub">
                    Safely archived items ready for 1-click restore or permanent purge
                </div>
            </div>
            <a href="{{ route('admin.trash.index') }}" class="dash-card-footer" style="color: #BE123C;">
                <span>Manage Trash &rarr;</span>
                <span>🗑️</span>
            </a>
        </div>
    </div>

    {{-- Quick Shortcuts Launcher --}}
    <div class="dash-shortcuts-bar">
        <div class="dash-shortcuts-label">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            <span>Quick Expeditions Launcher:</span>
        </div>
        <div class="dash-shortcuts-items">
            <a href="{{ route('admin.tours.create') }}" class="dash-shortcut-chip">
                <span>+</span> Tour Package
            </a>
            <a href="{{ route('admin.destinations.create') }}" class="dash-shortcut-chip">
                <span>+</span> Destination
            </a>
            <a href="{{ route('admin.categories.create') }}" class="dash-shortcut-chip">
                <span>+</span> Tour Category
            </a>
            <a href="{{ route('admin.activities.create') }}" class="dash-shortcut-chip">
                <span>+</span> Activity
            </a>
            <a href="{{ route('admin.blog-posts.create') }}" class="dash-shortcut-chip">
                <span>+</span> Article
            </a>
            <a href="{{ route('admin.guides.create') }}" class="dash-shortcut-chip">
                <span>+</span> Tour Guide
            </a>
            <a href="{{ route('admin.gallery.create') }}" class="dash-shortcut-chip">
                <span>+</span> Gallery Photo
            </a>
        </div>
    </div>

    {{-- Main Two-Column Management Panels (Bookings & Inquiries with Bulk Actions & Pagination) --}}
    <div class="two-col-grid" style="margin-bottom: 24px;">
        {{-- Bookings Panel --}}
        <div class="panel-card" style="margin-bottom: 0;">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Recent Bookings</h2>
                    <div style="font-size: 12.5px; color: var(--admin-muted); margin-top: 3px;">
                        Manage reservations, confirmations &amp; cancellations
                    </div>
                </div>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline btn-sm">All Bookings</a>
                </div>
            </div>

            {{-- Status Filter Tabs --}}
            <div style="display: flex; gap: 6px; margin-bottom: 14px; flex-wrap: wrap;">
                <a href="{{ route('admin.dashboard', array_merge(request()->except('booking_status', 'bookings_page'), ['booking_status' => ''])) }}" 
                   class="btn btn-sm {{ empty($bookingStatus) ? 'btn-primary' : 'btn-outline' }}">
                    All ({{ $stats['bookingsCount'] }})
                </a>
                <a href="{{ route('admin.dashboard', array_merge(request()->except('booking_status', 'bookings_page'), ['booking_status' => 'pending'])) }}" 
                   class="btn btn-sm {{ $bookingStatus === 'pending' ? 'btn-primary' : 'btn-outline' }}">
                    Pending ({{ $stats['pendingBookingsCount'] }})
                </a>
                <a href="{{ route('admin.dashboard', array_merge(request()->except('booking_status', 'bookings_page'), ['booking_status' => 'confirmed'])) }}" 
                   class="btn btn-sm {{ $bookingStatus === 'confirmed' ? 'btn-primary' : 'btn-outline' }}">
                    Confirmed
                </a>
                <a href="{{ route('admin.dashboard', array_merge(request()->except('booking_status', 'bookings_page'), ['booking_status' => 'cancelled'])) }}" 
                   class="btn btn-sm {{ $bookingStatus === 'cancelled' ? 'btn-primary' : 'btn-outline' }}">
                    Cancelled
                </a>
            </div>

            {{-- Standalone Bulk Form --}}
            <form action="{{ route('admin.bookings.bulk') }}" method="POST" id="dashboardBookingsForm">
                @csrf
                <div class="bulk-toolbar" id="dashBookingsBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 10px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 8px 14px; border-radius: 8px; margin-bottom: 12px;">
                    <span style="font-weight: 700; font-size: 13px; color: var(--admin-sidebar);">
                        <span id="dashBookingsCount">0</span> bookings selected
                    </span>
                    <div style="display: flex; gap: 6px;">
                        <button type="submit" name="action" value="confirmed" class="btn btn-outline btn-sm" title="Mark selected as confirmed">Confirm</button>
                        <button type="submit" name="action" value="cancelled" class="btn btn-outline btn-sm" title="Mark selected as cancelled">Cancel</button>
                        <button type="submit" name="action" value="trash" class="btn btn-danger btn-sm" onclick="return confirm('Move selected bookings to trash bin?')" title="Move to trash bin">Trash</button>
                    </div>
                </div>
            </form>

            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 36px; text-align: center;">
                                <input type="checkbox" id="selectAllDashBookings" title="Select all on this page">
                            </th>
                            <th>Traveller</th>
                            <th>Tour</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentBookings as $booking)
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="ids[]" value="{{ $booking->id }}" form="dashboardBookingsForm" class="dash-booking-cb">
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 9px;">
                                        <span class="user-avatar-initials">
                                            {{ strtoupper(substr($booking->customer_name, 0, 1)) }}
                                        </span>
                                        <div>
                                            <a href="{{ route('admin.bookings.show', $booking) }}" style="font-weight: 700; color: var(--admin-sidebar);">{{ $booking->customer_name }}</a>
                                            <div style="font-size: 11.5px; color: var(--admin-muted);">{{ $booking->travel_date->format('M d, Y') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;">{{ $booking->tour?->title ?? 'Deleted Tour' }}</div>
                                    <div style="font-size: 11px; color: var(--admin-muted);">{{ $booking->guests }} Guest(s)</div>
                                </td>
                                <td style="font-weight: 700;">₹{{ number_format((float) $booking->total_price, 2) }}</td>
                                <td>
                                    <span class="badge {{ $booking->status === 'confirmed' ? 'badge-success' : ($booking->status === 'cancelled' ? 'badge-danger' : 'badge-warning') }}">
                                        {{ $booking->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align: center; color: var(--admin-muted); padding: 28px;">No bookings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 14px;">
                {{ $recentBookings->links() }}
            </div>
        </div>

        {{-- Inquiries Panel --}}
        <div class="panel-card" style="margin-bottom: 0;">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Recent Inquiries</h2>
                    <div style="font-size: 12.5px; color: var(--admin-muted); margin-top: 3px;">
                        Manage customer questions &amp; message responses
                    </div>
                </div>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <a href="{{ route('admin.inquiries.index') }}" class="btn btn-outline btn-sm">All Inquiries</a>
                </div>
            </div>

            {{-- Status Filter Tabs --}}
            <div style="display: flex; gap: 6px; margin-bottom: 14px; flex-wrap: wrap;">
                <a href="{{ route('admin.dashboard', array_merge(request()->except('inquiry_status', 'inquiries_page'), ['inquiry_status' => ''])) }}" 
                   class="btn btn-sm {{ empty($inquiryStatus) ? 'btn-primary' : 'btn-outline' }}">
                    All ({{ $stats['inquiriesCount'] }})
                </a>
                <a href="{{ route('admin.dashboard', array_merge(request()->except('inquiry_status', 'inquiries_page'), ['inquiry_status' => 'new'])) }}" 
                   class="btn btn-sm {{ $inquiryStatus === 'new' ? 'btn-primary' : 'btn-outline' }}">
                    New ({{ $stats['newInquiriesCount'] }})
                </a>
                <a href="{{ route('admin.dashboard', array_merge(request()->except('inquiry_status', 'inquiries_page'), ['inquiry_status' => 'replied'])) }}" 
                   class="btn btn-sm {{ $inquiryStatus === 'replied' ? 'btn-primary' : 'btn-outline' }}">
                    Replied
                </a>
            </div>

            {{-- Standalone Bulk Form --}}
            <form action="{{ route('admin.inquiries.bulk') }}" method="POST" id="dashboardInquiriesForm">
                @csrf
                <div class="bulk-toolbar" id="dashInquiriesBulkBar" style="display: none; align-items: center; justify-content: space-between; gap: 10px; background: #E9F6F9; border: 1px solid #C4E7F0; padding: 8px 14px; border-radius: 8px; margin-bottom: 12px;">
                    <span style="font-weight: 700; font-size: 13px; color: var(--admin-sidebar);">
                        <span id="dashInquiriesCount">0</span> inquiries selected
                    </span>
                    <div style="display: flex; gap: 6px;">
                        <button type="submit" name="action" value="replied" class="btn btn-outline btn-sm" title="Mark selected as replied">Mark Replied</button>
                        <button type="submit" name="action" value="closed" class="btn btn-outline btn-sm" title="Mark selected as closed">Close</button>
                        <button type="submit" name="action" value="trash" class="btn btn-danger btn-sm" onclick="return confirm('Move selected inquiries to trash bin?')" title="Move to trash bin">Trash</button>
                    </div>
                </div>
            </form>

            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 36px; text-align: center;">
                                <input type="checkbox" id="selectAllDashInquiries" title="Select all on this page">
                            </th>
                            <th>Sender</th>
                            <th>Subject</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentInquiries as $inquiry)
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="ids[]" value="{{ $inquiry->id }}" form="dashboardInquiriesForm" class="dash-inquiry-cb">
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 9px;">
                                        <span class="user-avatar-initials" style="background: linear-gradient(135deg, #D97706 0%, #F59E0B 100%);">
                                            {{ strtoupper(substr($inquiry->name, 0, 1)) }}
                                        </span>
                                        <div>
                                            <a href="{{ route('admin.inquiries.show', $inquiry) }}" style="font-weight: 700; color: var(--admin-sidebar);">{{ $inquiry->name }}</a>
                                            <div style="font-size: 11.5px; color: var(--admin-muted);">{{ $inquiry->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="max-width: 220px;">
                                    <div style="font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $inquiry->subject }}</div>
                                    <div style="font-size: 11px; color: var(--admin-muted);">{{ $inquiry->created_at->format('M d, Y') }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $inquiry->status === 'replied' ? 'badge-success' : ($inquiry->status === 'new' ? 'badge-warning' : 'badge-neutral') }}">
                                        {{ $inquiry->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="text-align: center; color: var(--admin-muted); padding: 28px;">No inquiries found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 14px;">
                {{ $recentInquiries->links() }}
            </div>
        </div>
    </div>


    {{-- Interactive Bulk Selection Script for Dashboard --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function bindBulkHandler(selectAllId, checkboxClass, barId, countId) {
                const selectAll = document.getElementById(selectAllId);
                const checkboxes = Array.from(document.querySelectorAll('.' + checkboxClass));
                const bar = document.getElementById(barId);
                const count = document.getElementById(countId);

                if (!bar || !count) return;

                function sync() {
                    const checked = checkboxes.filter(cb => cb.checked);
                    if (checked.length > 0) {
                        bar.style.display = 'flex';
                        count.textContent = checked.length;
                    } else {
                        bar.style.display = 'none';
                    }
                    if (selectAll) {
                        selectAll.checked = checkboxes.length > 0 && checked.length === checkboxes.length;
                        selectAll.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
                    }
                }

                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        checkboxes.forEach(cb => { cb.checked = selectAll.checked; });
                        sync();
                    });
                }

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', sync);
                });
            }

            bindBulkHandler('selectAllDashBookings', 'dash-booking-cb', 'dashBookingsBulkBar', 'dashBookingsCount');
            bindBulkHandler('selectAllDashInquiries', 'dash-inquiry-cb', 'dashInquiriesBulkBar', 'dashInquiriesCount');
            bindBulkHandler('selectAllDashTrash', 'dash-trash-cb', 'dashTrashBulkBar', 'dashTrashCount');
        });
    </script>
@endsection
