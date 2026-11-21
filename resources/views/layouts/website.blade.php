<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', ($siteSettings['site_name'] ?? 'OmTravel').' — '.($siteSettings['site_tagline'] ?? 'Explore World'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600;700&family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/website.css') }}?v={{ filemtime(public_path('css/website.css')) }}">
</head>
<body>
    <div class="top-bar">
            <div class="container-wide top-bar-inner">
                <div class="top-bar-info">
                    <span class="top-bar-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>{{ $siteSettings['topbar_address'] ?? '45 New Eskaton Road, Austria' }}</span>
                    </span>
                    <span class="top-bar-sep"></span>
                    <span class="top-bar-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>{{ $siteSettings['topbar_hours'] ?? 'Sun to Friday: 8.00 am - 7.00 pm' }}</span>
                    </span>
                </div>
                <div class="top-bar-links">
                    <a href="{{ route('contact.create') }}">Support</a>
                    <span class="top-bar-sep"></span>
                    @auth
                        <a href="{{ auth()->user()->dashboardRoute() }}">
                            {{ auth()->user()->isAdmin() ? 'Admin Dashboard' : 'My Dashboard' }}
                        </a>
                        <span class="top-bar-sep"></span>
                        <form action="{{ route('logout') }}" method="POST" class="top-bar-logout-form">
                            @csrf
                            <button type="submit" class="top-bar-logout-btn">Sign Out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}">Sign In</a>
                        <span class="top-bar-sep"></span>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            </div>
        </div>

        <header class="site-header">
            <div class="container-wide navbar-inner">
                <a href="{{ route('home') }}" class="brand-logo">
                    <span class="brand-mark">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                    </span>
                    <span class="brand-text">
                        <span class="brand-name">{{ $siteSettings['site_name'] ?? 'OmTravel' }}</span>
                        <span class="brand-sub">{{ $siteSettings['site_tagline'] ?? 'Explore World' }}</span>
                    </span>
                </a>

                <button type="button" class="nav-toggle" id="navToggle" aria-label="Toggle Menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>

                <nav class="nav-menu" id="navMenu">
                    <ul class="nav-links">
                        <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a></li>
                        <li><a href="{{ route('tours.index') }}" class="{{ request()->routeIs('tours.*') ? 'is-active' : '' }}">Tours</a></li>
                        <li><a href="{{ route('home') }}#destinations" class="{{ request()->routeIs('destinations.*') ? 'is-active' : '' }}">Destinations</a></li>
                        <li><a href="{{ route('home') }}#gallery">Gallery</a></li>
                        <li><a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'is-active' : '' }}">Blog</a></li>
                        <li><a href="{{ route('contact.create') }}" class="{{ request()->routeIs('contact.*') ? 'is-active' : '' }}">Contact Us</a></li>
                    </ul>
                </nav>

                <div class="nav-actions">
                    @auth
                        <a href="{{ auth()->user()->dashboardRoute() }}" class="btn btn-outline-white nav-auth-btn">
                            {{ auth()->user()->isAdmin() ? 'Admin Panel' : 'My Trips' }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-white nav-auth-btn">Sign In</a>
                    @endauth
                    <a href="{{ route('tours.index') }}" class="btn btn-cyan">
                        <span>Book Now</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="site-footer">
            <div class="container footer-grid">
                <div>
                    <a href="{{ route('home') }}" class="brand-logo">
                        <span class="brand-mark">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                        </span>
                        <span class="brand-text">
                            <span class="brand-name footer-brand-name">{{ $siteSettings['site_name'] ?? 'OmTravel' }}</span>
                            <span class="brand-sub footer-brand-sub">{{ $siteSettings['site_tagline'] ?? 'Explore World' }}</span>
                        </span>
                    </a>
                    <p class="footer-about">{{ $siteSettings['footer_about'] ?? '' }}</p>
                    <div class="footer-socials">
                        <a href="#facebook" class="social-circle" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h1.5V2.14c-.326-.043-1.557-.14-2.857-.14C11.928 2 10 3.657 10 6.7v2.8H7v4h3V22h4v-8.5z"/></svg>
                        </a>
                        <a href="#twitter" class="social-circle" aria-label="Twitter">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 5.92a8.24 8.24 0 0 1-2.36.65 4.12 4.12 0 0 0 1.81-2.27 8.22 8.22 0 0 1-2.61 1 4.1 4.1 0 0 0-7 3.74A11.65 11.65 0 0 1 3.39 4.7a4.1 4.1 0 0 0 1.27 5.48 4.07 4.07 0 0 1-1.86-.51v.05a4.11 4.11 0 0 0 3.29 4.02 4.1 4.1 0 0 1-1.85.07 4.11 4.11 0 0 0 3.83 2.85A8.23 8.23 0 0 1 2 18.39a11.62 11.62 0 0 0 6.29 1.84c7.55 0 11.67-6.25 11.67-11.67 0-.18 0-.35-.01-.53A8.35 8.35 0 0 0 22 5.92z"/></svg>
                        </a>
                        <a href="#linkedin" class="social-circle" aria-label="LinkedIn">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.94 5a2 2 0 1 1-4-.002 2 2 0 0 1 4 .002zM7 8.48H3V21h4V8.48zm6.32 0H9.34V21h3.94v-6.57c0-3.66 4.77-4 4.77 0V21H22v-7.93c0-6.17-7.06-5.94-8.72-2.91l.04-1.68z"/></svg>
                        </a>
                        <a href="#instagram" class="social-circle" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="footer-heading">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">&rsaquo; Home</a></li>
                        <li><a href="{{ route('tours.index') }}">&rsaquo; Tour Packages</a></li>
                        <li><a href="{{ route('home') }}#destinations">&rsaquo; Destinations</a></li>
                        <li><a href="{{ route('blog.index') }}">&rsaquo; Blog &amp; Articles</a></li>
                        <li><a href="{{ route('contact.create') }}">&rsaquo; Contact Us</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="footer-heading">Get In Touch</h3>
                    <div class="footer-contact-list">
                        <div class="footer-contact-item">
                            <span class="footer-contact-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </span>
                            <div>
                                <div>{{ $siteSettings['contact_phone'] ?? '+01 234 567 890' }}</div>
                                @if (!empty($siteSettings['contact_phone_alt']))
                                    <div>{{ $siteSettings['contact_phone_alt'] }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="footer-contact-item">
                            <span class="footer-contact-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </span>
                            <div>{{ $siteSettings['contact_email'] ?? 'info@omtravel.com' }}</div>
                        </div>
                        <div class="footer-contact-item">
                            <span class="footer-contact-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            </span>
                            <div>{{ $siteSettings['contact_address'] ?? '789 Inner Lane, California, USA' }}</div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="footer-heading">Instagram Post</h3>
                    <div class="footer-insta-grid">
                        @forelse ($footerGallery as $item)
                            <a href="{{ route('home') }}#gallery" class="footer-insta-thumb">
                                <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" loading="lazy">
                            </a>
                        @empty
                            <a href="{{ route('tours.index') }}" class="footer-insta-thumb">
                                <img src="/images/seed/cat-beach.jpg" alt="Travel highlight">
                            </a>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="container footer-bottom-inner">
                    <div>Copyright {{ date('Y') }} {{ $siteSettings['site_name'] ?? 'OmTravel' }}. All Rights Reserved.</div>
                    <div class="payment-badges">
                        <span>We Accept</span>
                        <span class="payment-pill">VISA</span>
                        <span class="payment-pill">Mastercard</span>
                        <span class="payment-pill">PayPal</span>
                        <span class="payment-pill">Apple Pay</span>
                    </div>
                </div>
            </div>
        </footer>

    <button type="button" class="scroll-top-btn" id="scrollTopBtn" aria-label="Scroll to top">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
    </button>

    <div class="lightbox-modal" id="galleryLightbox" role="dialog" aria-modal="true" aria-label="Gallery preview">
        <div class="lightbox-dialog">
            <button type="button" class="lightbox-close" id="lightboxClose" aria-label="Close preview">&times;</button>
            <div class="lightbox-image-wrap">
                <img id="lightboxImage" src="" alt="Gallery preview">
            </div>
            <div class="lightbox-caption">
                <strong id="lightboxTitle"></strong>
                <span id="lightboxLocation"></span>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/website.js') }}?v={{ filemtime(public_path('js/website.js')) }}"></script>
</body>
</html>
