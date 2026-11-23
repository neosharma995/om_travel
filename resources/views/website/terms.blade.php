@extends('layouts.website')

@section('title', 'Terms & Conditions — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    {{-- Hero Banner --}}
    <section class="page-banner legal-hero-banner">
        <div class="container">
            <div class="legal-hero-content">
                <span class="legal-eyebrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    Transparency &amp; Legal Standards
                </span>
                <h1 class="page-banner-title">Terms &amp; Conditions</h1>
                <p class="legal-hero-desc">Clear, transparent booking standards, deposit conditions, and mutual obligations designed to protect and elevate your handcrafted travel experience with {{ $siteSettings['site_name'] ?? 'OmTravel' }}.</p>
                <div class="breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span>&rsaquo;</span>
                    <span>Terms &amp; Conditions</span>
                </div>

                <div class="legal-meta-strip">
                    <div class="legal-meta-pill">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Last Updated: January 2026</span>
                    </div>
                    <div class="legal-meta-pill">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span>Version 2.4</span>
                    </div>
                    <div class="legal-meta-pill">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        <span>Applicable Globally &amp; Domestically</span>
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
                            <span>Table of Contents</span>
                        </h3>
                        <nav class="legal-toc-nav">
                            <ul class="legal-toc-list">
                                <li><a href="#acceptance" class="legal-toc-link">1. Agreement to Terms</a></li>
                                <li><a href="#booking-payments" class="legal-toc-link">2. Bookings &amp; Payments</a></li>
                                <li><a href="#cancellations-refunds" class="legal-toc-link">3. Cancellations &amp; Refunds</a></li>
                                <li><a href="#documents-visas" class="legal-toc-link">4. Travel Documents &amp; Visas</a></li>
                                <li><a href="#insurance-health" class="legal-toc-link">5. Travel Insurance &amp; Health</a></li>
                                <li><a href="#itinerary-changes" class="legal-toc-link">6. Changes &amp; Force Majeure</a></li>
                                <li><a href="#traveler-conduct" class="legal-toc-link">7. Traveler Responsibilities</a></li>
                                <li><a href="#liability" class="legal-toc-link">8. Limitation of Liability</a></li>
                                <li><a href="#governing-law" class="legal-toc-link">9. Governing Law &amp; Jurisdiction</a></li>
                                <li><a href="#contact-legal" class="legal-toc-link">10. Contact Information</a></li>
                            </ul>
                        </nav>
                    </div>

                    <div class="legal-support-card">
                        <div class="legal-support-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <h4>Need Booking Assistance?</h4>
                        <p>Our dedicated travel specialists are available 24/7 to clarify itinerary guidelines and custom reservations.</p>
                        <a href="{{ route('contact.create') }}" class="btn btn-cyan btn-sm">Speak With Concierge</a>
                    </div>
                </aside>

                {{-- Right Main Editorial Content --}}
                <article class="legal-content">
                    {{-- Section 1 --}}
                    <section class="legal-section" id="acceptance">
                        <div class="legal-section-header">
                            <span class="legal-section-num">01</span>
                            <h2 class="legal-section-title">Agreement to Terms</h2>
                        </div>
                        <div class="legal-prose">
                            <p>Welcome to <strong>{{ $siteSettings['site_name'] ?? 'OmTravel' }}</strong> ("Company", "we", "our", or "us"). These Terms &amp; Conditions govern your access to our website, digital reservation portal, and all travel arrangements, tour packages, guided expeditions, accommodation reservations, and concierge services curated by us.</p>
                            <p>By browsing our platforms, inquiring about custom journeys, or submitting a formal tour booking, you ("Traveler", "Lead Passenger", or "Customer") confirm that you have read, comprehended, and agreed to be legally bound by these terms. If you are booking on behalf of additional guests or a group, you confirm that you have the authority to accept these conditions on their behalf.</p>
                        </div>
                    </section>

                    {{-- Section 2 --}}
                    <section class="legal-section" id="booking-payments">
                        <div class="legal-section-header">
                            <span class="legal-section-num">02</span>
                            <h2 class="legal-section-title">Booking Reservations &amp; Payment Terms</h2>
                        </div>
                        <div class="legal-prose">
                            <p>All prices quoted on our website and in official travel quotations are denominated in <strong>Indian Rupees (₹)</strong>, unless specifically stated otherwise in writing.</p>
                            <ul class="legal-bullet-list">
                                <li><strong>Reservation Confirmation:</strong> A booking is officially confirmed once our reservations team issues a formal booking reference and invoice following the receipt of the initial deposit.</li>
                                <li><strong>Security Deposit:</strong> A non-refundable booking deposit of 30% of the total tour cost is required at the time of reservation to secure lodge accommodations, private transport, and licensed guide permits.</li>
                                <li><strong>Balance Settlement:</strong> The outstanding balance must be settled in full at least 30 days prior to the scheduled departure date. For reservations made within 30 days of departure, 100% of the total journey cost is payable upon booking.</li>
                                <li><strong>Accepted Payment Methods:</strong> We accept verified bank transfers, major credit cards, UPI, and authorized digital gateways. All transaction charges imposed by banking institutions remain the passenger's responsibility.</li>
                            </ul>

                            <div class="legal-callout">
                                <div class="legal-callout-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                </div>
                                <div>
                                    <strong>Price Guarantee:</strong> Once your deposit is received and confirmed, {{ $siteSettings['site_name'] ?? 'OmTravel' }} guarantees the package price against currency fluctuations, excluding government taxes or statutory tariff increases.
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Section 3 --}}
                    <section class="legal-section" id="cancellations-refunds">
                        <div class="legal-section-header">
                            <span class="legal-section-num">03</span>
                            <h2 class="legal-section-title">Cancellations, Amendments &amp; Refund Schedule</h2>
                        </div>
                        <div class="legal-prose">
                            <p>Should you need to cancel or amend your travel plans, written notice via verified email to <a href="mailto:{{ $siteSettings['contact_email'] ?? 'info@omtravel.com' }}">{{ $siteSettings['contact_email'] ?? 'info@omtravel.com' }}</a> is required. The cancellation date will be determined by the timestamp of written receipt.</p>
                            
                            <div class="legal-table-wrap">
                                <table class="legal-table">
                                    <thead>
                                        <tr>
                                            <th>Notice Period Prior to Departure</th>
                                            <th>Cancellation Fee (Per Person)</th>
                                            <th>Eligible Refund Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>45+ days prior to departure</td>
                                            <td>Deposit only (Administrative fee)</td>
                                            <td>100% of remaining balance</td>
                                        </tr>
                                        <tr>
                                            <td>30 to 44 days prior to departure</td>
                                            <td>50% of total tour package</td>
                                            <td>50% refund of total package</td>
                                        </tr>
                                        <tr>
                                            <td>15 to 29 days prior to departure</td>
                                            <td>75% of total tour package</td>
                                            <td>25% refund of total package</td>
                                        </tr>
                                        <tr>
                                            <td>Fewer than 15 days or No-Show</td>
                                            <td>100% of total tour package</td>
                                            <td>No refund eligible</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p>Non-refundable components such as issued domestic flights, permit passes, and exclusive wildlife permits cannot be refunded regardless of the cancellation date.</p>
                        </div>
                    </section>

                    {{-- Section 4 --}}
                    <section class="legal-section" id="documents-visas">
                        <div class="legal-section-header">
                            <span class="legal-section-num">04</span>
                            <h2 class="legal-section-title">Travel Documents, Passports &amp; Visas</h2>
                        </div>
                        <div class="legal-prose">
                            <p>Travelers hold sole responsibility for obtaining and holding valid travel documentation, including:</p>
                            <ul class="legal-bullet-list">
                                <li>A valid passport with a minimum validity of six (6) months beyond the planned return date.</li>
                                <li>All requisite entry visas, transit permits, e-visas, and government authorizations corresponding to the destinations included in the itinerary.</li>
                                <li>All documentation must accurately match the traveler's full legal name as recorded on official identification.</li>
                            </ul>
                            <p>{{ $siteSettings['site_name'] ?? 'OmTravel' }} provides general visa guidance in good faith, but accepts no liability if a traveler is denied entry or boarding by immigration officials or airlines.</p>
                        </div>
                    </section>

                    {{-- Section 5 --}}
                    <section class="legal-section" id="insurance-health">
                        <div class="legal-section-header">
                            <span class="legal-section-num">05</span>
                            <h2 class="legal-section-title">Travel Insurance &amp; Expedition Health</h2>
                        </div>
                        <div class="legal-prose">
                            <p><strong>Comprehensive travel insurance is mandatory for all guests participating in our global expeditions.</strong> Your policy must provide comprehensive coverage for:</p>
                            <ul class="legal-bullet-list">
                                <li>Emergency medical evacuation, hospitalization, and repatriations.</li>
                                <li>Trip cancellation, curtailment, and flight interruption.</li>
                                <li>Loss, theft, or damage of personal luggage, baggage, and equipment.</li>
                                <li>Adventure sports or high-altitude trekking if specified in your bespoke itinerary.</li>
                            </ul>
                            <p>Travelers must inform us of any pre-existing medical conditions, physical limitations, or special dietary requirements prior to confirming their reservation.</p>
                        </div>
                    </section>

                    {{-- Section 6 --}}
                    <section class="legal-section" id="itinerary-changes">
                        <div class="legal-section-header">
                            <span class="legal-section-num">06</span>
                            <h2 class="legal-section-title">Itinerary Flexibility &amp; Force Majeure</h2>
                        </div>
                        <div class="legal-prose">
                            <p>While {{ $siteSettings['site_name'] ?? 'OmTravel' }} makes every effort to execute itineraries as published, bespoke adventure and international travel require flexibility. We reserve the right to alter routes, accommodation tiers, or scheduled activities should weather, natural events, border closures, or safety considerations necessitate change.</p>
                            <p><strong>Force Majeure:</strong> We are not liable for delays, cancellations, or losses resulting from events beyond our reasonable control, including natural catastrophes, severe weather, war, civil unrest, strikes, government sanctions, pandemics, or airline route cancelations.</p>
                        </div>
                    </section>

                    {{-- Section 7 --}}
                    <section class="legal-section" id="traveler-conduct">
                        <div class="legal-section-header">
                            <span class="legal-section-num">07</span>
                            <h2 class="legal-section-title">Traveler Responsibilities &amp; Code of Conduct</h2>
                        </div>
                        <div class="legal-prose">
                            <p>To preserve group safety and respect native environments and wildlife, travelers agree to follow instructions provided by tour leaders, local certified guides, and lodge staff. We uphold a zero-tolerance policy against disruptive behavior, environmental destruction, or violation of local laws.</p>
                            <p>We reserve the right to terminate the journey of any participant whose conduct endangers others, with no refund or compensation.</p>
                        </div>
                    </section>

                    {{-- Section 8 --}}
                    <section class="legal-section" id="liability">
                        <div class="legal-section-header">
                            <span class="legal-section-num">08</span>
                            <h2 class="legal-section-title">Limitation of Liability</h2>
                        </div>
                        <div class="legal-prose">
                            <p>{{ $siteSettings['site_name'] ?? 'OmTravel' }} acts as an independent travel curator connecting guests with certified third-party service providers (airlines, transport operators, accommodations, and local activity operators). To the maximum extent permitted by law, we shall not be held liable for personal injury, illness, property loss, or consequential damages arising from third-party vendor failures.</p>
                        </div>
                    </section>

                    {{-- Section 9 --}}
                    <section class="legal-section" id="governing-law">
                        <div class="legal-section-header">
                            <span class="legal-section-num">09</span>
                            <h2 class="legal-section-title">Governing Law &amp; Dispute Resolution</h2>
                        </div>
                        <div class="legal-prose">
                            <p>These Terms &amp; Conditions are governed by and construed in accordance with the laws of India. Any unresolved dispute arising out of or in connection with these conditions shall be resolved amicably through good-faith arbitration prior to seeking relief in courts of competent jurisdiction.</p>
                        </div>
                    </section>

                    {{-- Section 10 --}}
                    <section class="legal-section" id="contact-legal">
                        <div class="legal-section-header">
                            <span class="legal-section-num">10</span>
                            <h2 class="legal-section-title">Contact Our Legal &amp; Concierge Team</h2>
                        </div>
                        <div class="legal-prose">
                            <p>If you have any questions, clarifications, or special legal inquiries regarding our terms, please contact our guest compliance team:</p>
                            <div class="legal-contact-box">
                                <div>
                                    <strong>{{ $siteSettings['site_name'] ?? 'OmTravel' }} Guest Operations</strong>
                                    <p>{{ $siteSettings['contact_address'] ?? '789 Inner Lane, California, USA' }}</p>
                                    <p>Email: <a href="mailto:{{ $siteSettings['contact_email'] ?? 'info@omtravel.com' }}">{{ $siteSettings['contact_email'] ?? 'info@omtravel.com' }}</a></p>
                                    <p>Phone: {{ $siteSettings['contact_phone'] ?? '+01 234 567 890' }}</p>
                                </div>
                                <a href="{{ route('contact.create') }}" class="btn btn-cyan">Contact Support</a>
                            </div>
                        </div>
                    </section>
                </article>
            </div>
        </div>
    </section>
@endsection
