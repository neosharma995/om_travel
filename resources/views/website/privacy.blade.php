@extends('layouts.website')

@section('title', 'Privacy Policy — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    {{-- Hero Banner --}}
    <section class="page-banner legal-hero-banner">
        <div class="container">
            <div class="legal-hero-content">
                <span class="legal-eyebrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Data Protection &amp; Confidentiality
                </span>
                <h1 class="page-banner-title">Privacy Policy</h1>
                <p class="legal-hero-desc">Your privacy is fundamental to our relationship. Learn how {{ $siteSettings['site_name'] ?? 'OmTravel' }} collects, safeguards, and ethically uses your personal information across your journeys.</p>
                <div class="breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span>&rsaquo;</span>
                    <span>Privacy Policy</span>
                </div>

                <div class="legal-meta-strip">
                    <div class="legal-meta-pill">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Last Updated: January 2026</span>
                    </div>
                    <div class="legal-meta-pill">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span>GDPR &amp; DPDP Compliant</span>
                    </div>
                    <div class="legal-meta-pill">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span>256-Bit SSL Encrypted</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Document Layout --}}
    <section class="page-section legal-page-section">
        <div class="container">
            <div class="legal-layout">
                {{-- Left Sticky Sidebar / Table of Contents --}}
                <aside class="legal-sidebar">
                    <div class="legal-toc-card">
                        <h3 class="legal-toc-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                            <span>Privacy Topics</span>
                        </h3>
                        <nav class="legal-toc-nav">
                            <ul class="legal-toc-list">
                                <li><a href="#overview" class="legal-toc-link">1. Privacy Commitment</a></li>
                                <li><a href="#information-collected" class="legal-toc-link">2. Information We Collect</a></li>
                                <li><a href="#how-we-use" class="legal-toc-link">3. How We Use Data</a></li>
                                <li><a href="#partner-sharing" class="legal-toc-link">4. Sharing with Partners</a></li>
                                <li><a href="#cookies" class="legal-toc-link">5. Cookies &amp; Tracking</a></li>
                                <li><a href="#data-security" class="legal-toc-link">6. Data Security &amp; Storage</a></li>
                                <li><a href="#international-transfers" class="legal-toc-link">7. International Transfers</a></li>
                                <li><a href="#privacy-rights" class="legal-toc-link">8. Your Privacy Rights</a></li>
                                <li><a href="#data-retention" class="legal-toc-link">9. Data Retention Policy</a></li>
                                <li><a href="#contact-dpo" class="legal-toc-link">10. Contacting DPO</a></li>
                            </ul>
                        </nav>
                    </div>

                    <div class="legal-support-card">
                        <div class="legal-support-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <h4>Privacy &amp; Data Inquiries</h4>
                        <p>You have full ownership of your data. Contact our Data Protection Officer for subject access requests or corrections.</p>
                        <a href="{{ route('contact.create') }}" class="btn btn-cyan btn-sm">Submit Privacy Request</a>
                    </div>
                </aside>

                {{-- Right Main Editorial Content --}}
                <article class="legal-content">
                    {{-- Section 1 --}}
                    <section class="legal-section" id="overview">
                        <div class="legal-section-header">
                            <span class="legal-section-num">01</span>
                            <h2 class="legal-section-title">Our Commitment to Traveler Privacy</h2>
                        </div>
                        <div class="legal-prose">
                            <p>At <strong>{{ $siteSettings['site_name'] ?? 'OmTravel' }}</strong> ("Company", "we", "our", or "us"), we hold your trust in the highest regard. Crafting bespoke journeys requires sharing personal travel preferences, passport documentation, and payment details. This Privacy Policy outlines our transparent standards for collecting, holding, using, and safeguarding that data.</p>
                            <p>We strictly abide by applicable data protection laws, including the Digital Personal Data Protection Act (DPDP), the General Data Protection Regulation (GDPR), and international travel data security protocols.</p>
                        </div>
                    </section>

                    {{-- Section 2 --}}
                    <section class="legal-section" id="information-collected">
                        <div class="legal-section-header">
                            <span class="legal-section-num">02</span>
                            <h2 class="legal-section-title">Categories of Information We Collect</h2>
                        </div>
                        <div class="legal-prose">
                            <p>We only collect information necessary to arrange your reservations, provide concierge care, and personalize your experience:</p>
                            <ul class="legal-bullet-list">
                                <li><strong>Identity &amp; Contact Details:</strong> Full name, email address, telephone number, residential address, emergency contact information, and date of birth.</li>
                                <li><strong>Travel &amp; Identification Documents:</strong> Passport numbers, nationality, issuance/expiry dates, visa permits, and frequent flyer details required by airlines and immigration authorities.</li>
                                <li><strong>Booking &amp; Transaction Details:</strong> Records of packages booked, dates of travel, payment amounts (in ₹ Indian Rupees), transaction receipts, and customer service correspondence.</li>
                                <li><strong>Special Care &amp; Health Data:</strong> Dietary requirements, medical limitations, or mobility needs shared voluntarily to ensure appropriate lodge and expedition pacing.</li>
                                <li><strong>Technical &amp; Usage Information:</strong> IP address, device type, browser metadata, and navigation interactions gathered through cookies when exploring our website.</li>
                            </ul>
                        </div>
                    </section>

                    {{-- Section 3 --}}
                    <section class="legal-section" id="how-we-use">
                        <div class="legal-section-header">
                            <span class="legal-section-num">03</span>
                            <h2 class="legal-section-title">Purposes of Data Processing</h2>
                        </div>
                        <div class="legal-prose">
                            <p>We process your personal information strictly for legitimate travel-related purposes:</p>
                            <ul class="legal-bullet-list">
                                <li>To fulfill and manage tour package reservations, hotel stays, transport logistics, and guide appointments.</li>
                                <li>To communicate essential updates, flight schedules, emergency travel advisories, and itinerary changes.</li>
                                <li>To process financial transactions and issue verified booking invoices and vouchers.</li>
                                <li>To respond to your inquiries submitted via our contact forms, AI trip planner, or live concierge chats.</li>
                                <li>To deliver curated travel newsletters and seasonal packages, only when you have provided explicit opt-in consent.</li>
                            </ul>

                            <div class="legal-callout">
                                <div class="legal-callout-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                </div>
                                <div>
                                    <strong>No Data Selling:</strong> {{ $siteSettings['site_name'] ?? 'OmTravel' }} never sells, rents, or monetizes traveler data to third-party marketing brokers or advertisers.
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Section 4 --}}
                    <section class="legal-section" id="partner-sharing">
                        <div class="legal-section-header">
                            <span class="legal-section-num">04</span>
                            <h2 class="legal-section-title">Information Sharing with Verified Partners</h2>
                        </div>
                        <div class="legal-prose">
                            <p>To execute your itinerary seamlessly, we share relevant details with vetted service providers:</p>
                            <ul class="legal-bullet-list">
                                <li><strong>Accommodations &amp; Lodges:</strong> Guest names, check-in dates, and specific room preferences.</li>
                                <li><strong>Licensed Expedition Guides &amp; Drivers:</strong> Names and pickup logistics to ensure safe transfers and guided excursions.</li>
                                <li><strong>Airlines &amp; Transport Carriers:</strong> Full legal names and passport credentials necessary for flight ticketing and transit manifests.</li>
                                <li><strong>Government &amp; National Park Authorities:</strong> Identity details required to issue restricted wildlife permits and national park passes.</li>
                            </ul>
                            <p>All partners are contractually required to safeguard your data and are prohibited from utilizing it for unauthorized purposes.</p>
                        </div>
                    </section>

                    {{-- Section 5 --}}
                    <section class="legal-section" id="cookies">
                        <div class="legal-section-header">
                            <span class="legal-section-num">05</span>
                            <h2 class="legal-section-title">Cookies &amp; Digital Preferences</h2>
                        </div>
                        <div class="legal-prose">
                            <p>Our website utilizes standard session and security cookies to remember your browsing preferences, active filter selections, and authenticated account sessions. You can configure your web browser to disable or reject cookies at any time, though certain features of our booking platform may function with limited convenience.</p>
                        </div>
                    </section>

                    {{-- Section 6 --}}
                    <section class="legal-section" id="data-security">
                        <div class="legal-section-header">
                            <span class="legal-section-num">06</span>
                            <h2 class="legal-section-title">Data Security &amp; Storage Protocols</h2>
                        </div>
                        <div class="legal-prose">
                            <p>We deploy robust organizational, administrative, and technological security controls to safeguard your data against accidental loss, unauthorized access, alteration, or disclosure. These controls include:</p>
                            <ul class="legal-bullet-list">
                                <li>End-to-end 256-bit SSL encryption across all digital interactions and forms.</li>
                                <li>Role-based access permissions restricting sensitive traveler data strictly to authorized concierge staff.</li>
                                <li>Periodic vulnerability scanning, firewall protections, and secure cloud infrastructure.</li>
                            </ul>
                        </div>
                    </section>

                    {{-- Section 7 --}}
                    <section class="legal-section" id="international-transfers">
                        <div class="legal-section-header">
                            <span class="legal-section-num">07</span>
                            <h2 class="legal-section-title">International Data Transfers</h2>
                        </div>
                        <div class="legal-prose">
                            <p>Given the global nature of our travel offerings, your personal data may be transferred to and processed by partner lodges and expedition leaders outside your country of residence. In all cases, we verify that appropriate data protection measures and contractual safeguards are maintained.</p>
                        </div>
                    </section>

                    {{-- Section 8 --}}
                    <section class="legal-section" id="privacy-rights">
                        <div class="legal-section-header">
                            <span class="legal-section-num">08</span>
                            <h2 class="legal-section-title">Your Privacy Rights &amp; Choices</h2>
                        </div>
                        <div class="legal-prose">
                            <p>Under applicable data privacy regulations, you enjoy comprehensive rights regarding your personal information:</p>
                            <ul class="legal-bullet-list">
                                <li><strong>Right to Access:</strong> Request a copy of all personal records we hold concerning you.</li>
                                <li><strong>Right to Rectification:</strong> Update or correct inaccurate or incomplete travel records.</li>
                                <li><strong>Right to Erasure:</strong> Request the deletion of your personal profile, subject to statutory tax and legal retention requirements.</li>
                                <li><strong>Right to Withdraw Consent:</strong> Opt out of marketing correspondence or promotional newsletters instantly via one-click unsubscribe links.</li>
                            </ul>
                        </div>
                    </section>

                    {{-- Section 9 --}}
                    <section class="legal-section" id="data-retention">
                        <div class="legal-section-header">
                            <span class="legal-section-num">09</span>
                            <h2 class="legal-section-title">Data Retention Schedules</h2>
                        </div>
                        <div class="legal-prose">
                            <p>We retain personal information only for the duration required to satisfy the travel reservation, resolve customer inquiries, or comply with applicable tax, accounting, and legal recordkeeping regulations (typically 7 years following journey completion).</p>
                        </div>
                    </section>

                    {{-- Section 10 --}}
                    <section class="legal-section" id="contact-dpo">
                        <div class="legal-section-header">
                            <span class="legal-section-num">10</span>
                            <h2 class="legal-section-title">Contacting Our Data Protection Officer</h2>
                        </div>
                        <div class="legal-prose">
                            <p>To exercise any of your privacy rights, file a data access inquiry, or share feedback, please reach out directly to our Data Protection Officer:</p>
                            <div class="legal-contact-box">
                                <div>
                                    <strong>{{ $siteSettings['site_name'] ?? 'OmTravel' }} Privacy &amp; Compliance Office</strong>
                                    <p>{{ $siteSettings['contact_address'] ?? '789 Inner Lane, California, USA' }}</p>
                                    <p>Email: <a href="mailto:{{ $siteSettings['contact_email'] ?? 'info@omtravel.com' }}">{{ $siteSettings['contact_email'] ?? 'info@omtravel.com' }}</a></p>
                                    <p>Phone: {{ $siteSettings['contact_phone'] ?? '+01 234 567 890' }}</p>
                                </div>
                                <a href="{{ route('contact.create') }}" class="btn btn-cyan">Contact DPO</a>
                            </div>
                        </div>
                    </section>
                </article>
            </div>
        </div>
    </section>
@endsection
