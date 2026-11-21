<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'My Dashboard') — {{ $siteSettings['site_name'] ?? 'OmTravel' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
</head>
<body>
    <div class="admin-shell customer-portal">
        <aside class="admin-sidebar customer-sidebar" id="adminSidebar">
            <a href="{{ route('customer.dashboard') }}" class="admin-brand">
                <span class="admin-brand-mark">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                </span>
                <div>
                    <div class="admin-brand-title">{{ $siteSettings['site_name'] ?? 'OmTravel' }}</div>
                    <div class="admin-brand-sub">Traveller Portal</div>
                </div>
            </a>

            <nav class="admin-nav">
                <div class="admin-nav-group">My Account</div>
                <a href="{{ route('customer.dashboard') }}" class="admin-nav-link {{ request()->routeIs('customer.dashboard') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <span>Overview</span>
                </a>
                <a href="{{ route('customer.bookings.index') }}" class="admin-nav-link {{ request()->routeIs('customer.bookings.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span>My Bookings</span>
                </a>
                <a href="{{ route('customer.inquiries.index') }}" class="admin-nav-link {{ request()->routeIs('customer.inquiries.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <span>My Inquiries</span>
                </a>
                <a href="{{ route('customer.profile.edit') }}" class="admin-nav-link {{ request()->routeIs('customer.profile.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>My Profile</span>
                </a>

                <div class="admin-nav-group">Explore</div>
                <a href="{{ route('tours.index') }}" class="admin-nav-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <span>Browse Tours</span>
                </a>
                <a href="{{ route('contact.create') }}" class="admin-nav-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <span>Contact Support</span>
                </a>
            </nav>

            <div class="admin-sidebar-footer">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-ghost-light">Sign Out</button>
                </form>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar-left">
                    <button type="button" class="admin-menu-btn" id="adminMenuBtn" aria-label="Toggle Menu">&#9776;</button>
                    <h1 class="admin-page-title">@yield('page_title', 'My Travel Dashboard')</h1>
                </div>
                <div class="admin-topbar-right">
                    <span class="admin-user-chip">{{ auth()->user()?->name }}</span>
                    <a href="{{ route('tours.index') }}" class="btn btn-primary btn-sm">Book New Tour</a>
                    <a href="{{ route('home') }}" class="btn btn-outline btn-sm">Website &#8599;</a>
                </div>
            </header>

            <div class="admin-content">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <strong>Please correct the errors below:</strong>
                        <ul style="margin: 6px 0 0 18px; padding: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('js/admin.js') }}?v={{ filemtime(public_path('js/admin.js')) }}"></script>
</body>
</html>
