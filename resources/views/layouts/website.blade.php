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
                <div class="top-bar-socials">
                    <a href="#facebook" class="top-social-circle" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h1.5V2.14c-.326-.043-1.557-.14-2.857-.14C11.928 2 10 3.657 10 6.7v2.8H7v4h3V22h4v-8.5z"/></svg>
                    </a>
                    <a href="#instagram" class="top-social-circle" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg>
                    </a>
                    <a href="#youtube" class="top-social-circle" aria-label="YouTube">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21.58 7.19a2.5 2.5 0 0 0-1.76-1.77C18.26 5 12 5 12 5s-6.26 0-7.82.42A2.5 2.5 0 0 0 2.42 7.2 26.24 26.24 0 0 0 2 12a26.24 26.24 0 0 0 .42 4.81 2.5 2.5 0 0 0 1.76 1.77C5.74 19 12 19 12 19s6.26 0 7.82-.42a2.5 2.5 0 0 0 1.76-1.77A26.24 26.24 0 0 0 22 12a26.24 26.24 0 0 0-.42-4.81zM10 15V9l5.2 3-5.2 3z"/></svg>
                    </a>
                    <a href="#twitter" class="top-social-circle" aria-label="X">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <header class="site-header" id="siteHeader">
        <div class="container-wide">
            <div class="navbar-inner">
                <a href="{{ route('home') }}" class="brand-logo" aria-label="{{ $siteSettings['site_name'] ?? 'OmTravel' }} Home">
                    <span class="brand-mark">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                    </span>
                    <span class="brand-text">
                        <span class="brand-name">{{ $siteSettings['site_name'] ?? 'OmTravel' }}</span>
                        <span class="brand-sub">{{ $siteSettings['site_tagline'] ?? 'Explore World' }}</span>
                    </span>
                </a>

                <button type="button" class="nav-toggle" id="navToggle" aria-label="Toggle Menu" aria-expanded="false" aria-controls="navMenu">
                    <span class="nav-toggle-bar"></span>
                    <span class="nav-toggle-bar"></span>
                    <span class="nav-toggle-bar"></span>
                </button>

                <nav class="nav-menu" id="navMenu">
                    <ul class="nav-links">
                        <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a></li>
                        <li><a href="{{ route('tours.index') }}" class="{{ request()->routeIs('tours.*') ? 'is-active' : '' }}">Tours</a></li>
                        <li><a href="{{ route('destinations.index') }}" class="{{ request()->routeIs('destinations.*') ? 'is-active' : '' }}">Destinations</a></li>
                        <li><a href="{{ route('gallery.index') }}" class="{{ request()->routeIs('gallery.*') ? 'is-active' : '' }}">Gallery</a></li>
                        <li><a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'is-active' : '' }}">Blog</a></li>
                        <li><a href="{{ route('contact.create') }}" class="{{ request()->routeIs('contact.*') ? 'is-active' : '' }}">Contact Us</a></li>
                    </ul>

                    <div class="nav-mobile-footer">
                        <div class="nav-mobile-info">
                            <span class="nav-mobile-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span>{{ $siteSettings['topbar_address'] ?? '45 New Eskaton Road, Austria' }}</span>
                            </span>
                            <span class="nav-mobile-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span>{{ $siteSettings['topbar_hours'] ?? 'Sun to Friday: 8.00 am - 7.00 pm' }}</span>
                            </span>
                        </div>
                        <div class="nav-mobile-actions">
                            @auth
                                <a href="{{ auth()->user()->dashboardRoute() }}" class="btn btn-outline-white nav-auth-btn">
                                    {{ auth()->user()->isAdmin() ? 'Admin Panel' : 'My Trips' }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-white nav-auth-btn">Sign In</a>
                                <a href="{{ route('register') }}" class="btn btn-outline-white nav-auth-btn">Register</a>
                            @endauth
                            <a href="{{ route('tours.index') }}" class="btn btn-cyan nav-book-btn">
                                <span>Book Now</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </a>
                        </div>
                    </div>
                </nav>

                <div class="nav-actions">
                    <a href="{{ route('tours.index') }}" class="nav-search-btn" aria-label="Search tours">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </a>
                    @auth
                        <a href="{{ auth()->user()->dashboardRoute() }}" class="btn btn-outline-white nav-auth-btn">
                            {{ auth()->user()->isAdmin() ? 'Admin Panel' : 'My Trips' }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-white nav-auth-btn">Sign In</a>
                    @endauth
                    <a href="{{ route('tours.index') }}" class="btn btn-cyan nav-book-btn">
                        <span>Book Now</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </header>

        <main>
            @yield('content')
        </main>

        <footer class="site-footer">
            <div class="container footer-grid">
                {{-- Col 1: Brand & Socials --}}
                <div class="footer-col footer-col-brand">
                    <a href="{{ route('home') }}" class="brand-logo" aria-label="{{ $siteSettings['site_name'] ?? 'OmTravel' }} Home">
                        <span class="brand-mark">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                        </span>
                        <span class="brand-text">
                            <span class="brand-name footer-brand-name">{{ $siteSettings['site_name'] ?? 'OmTravel' }}</span>
                            <span class="brand-sub footer-brand-sub">{{ $siteSettings['site_tagline'] ?? 'Explore World' }}</span>
                        </span>
                    </a>
                    <p class="footer-about">
                        {{ !empty($siteSettings['footer_about']) ? $siteSettings['footer_about'] : 'Discover the world with OmTravel – your trusted partner for unforgettable travel experiences.' }}
                    </p>
                    <div class="footer-socials">
                        <a href="#facebook" class="social-circle" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h1.5V2.14c-.326-.043-1.557-.14-2.857-.14C11.928 2 10 3.657 10 6.7v2.8H7v4h3V22h4v-8.5z"/></svg>
                        </a>
                        <a href="#instagram" class="social-circle" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg>
                        </a>
                        <a href="#youtube" class="social-circle" aria-label="YouTube">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21.58 7.19a2.5 2.5 0 0 0-1.76-1.77C18.26 5 12 5 12 5s-6.26 0-7.82.42A2.5 2.5 0 0 0 2.42 7.2 26.24 26.24 0 0 0 2 12a26.24 26.24 0 0 0 .42 4.81 2.5 2.5 0 0 0 1.76 1.77C5.74 19 12 19 12 19s6.26 0 7.82-.42a2.5 2.5 0 0 0 1.76-1.77A26.24 26.24 0 0 0 22 12a26.24 26.24 0 0 0-.42-4.81zM10 15V9l5.2 3-5.2 3z"/></svg>
                        </a>
                        <a href="#twitter" class="social-circle" aria-label="X">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Col 2: Quick Links --}}
                <div class="footer-col">
                    <h3 class="footer-heading">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('tours.index') }}">Tours</a></li>
                        <li><a href="{{ route('destinations.index') }}">Destinations</a></li>
                        <li><a href="{{ route('gallery.index') }}">Gallery</a></li>
                        <li><a href="{{ route('blog.index') }}">Blog</a></li>
                    </ul>
                </div>

                {{-- Col 3: Support --}}
                <div class="footer-col">
                    <h3 class="footer-heading">Support</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}#faq">FAQs</a></li>
                        <li><a href="{{ route('contact.create') }}#terms">Terms &amp; Conditions</a></li>
                        <li><a href="{{ route('contact.create') }}#privacy">Privacy Policy</a></li>
                        <li><a href="{{ route('contact.create') }}">Contact Us</a></li>
                    </ul>
                </div>

                {{-- Col 4: Contact Info --}}
                <div class="footer-col footer-col-contact">
                    <h3 class="footer-heading">Contact Info</h3>
                    <div class="footer-contact-list">
                        <div class="footer-contact-item">
                            <span class="footer-contact-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            </span>
                            <span>{{ $siteSettings['topbar_address'] ?? '45 New Eskaton Road, Austria' }}</span>
                        </div>
                        <div class="footer-contact-item">
                            <span class="footer-contact-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </span>
                            <span>{{ $siteSettings['contact_phone'] ?? '+43 123 456 789' }}</span>
                        </div>
                        <div class="footer-contact-item">
                            <span class="footer-contact-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </span>
                            <span>{{ $siteSettings['contact_email'] ?? 'info@omtravel.com' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Col 5: Subscribe Newsletter --}}
                <div class="footer-col footer-col-newsletter">
                    <h3 class="footer-heading">Subscribe Newsletter</h3>
                    <p class="footer-newsletter-desc">Get the latest travel deals &amp; updates.</p>
                    <form action="{{ route('contact.store') }}" method="POST" class="footer-newsletter-form" id="newsletterForm">
                        @csrf
                        <input type="hidden" name="name" value="Newsletter Subscriber">
                        <input type="hidden" name="message" value="Subscribed to newsletter updates">
                        <div class="newsletter-input-wrap">
                            <input type="email" name="email" class="newsletter-input" placeholder="Your Email Address" required>
                            <button type="submit" class="newsletter-submit-btn" aria-label="Subscribe to newsletter">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="container footer-bottom-inner">
                    <div class="footer-copyright">
                        &copy; {{ date('Y') }} {{ $siteSettings['site_name'] ?? 'OmTravel' }}. All Rights Reserved.
                    </div>
                    <div class="footer-credit">
                        Designed with <span class="heart-icon">&hearts;</span> for Travelers
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
