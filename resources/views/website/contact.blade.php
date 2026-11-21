@extends('layouts.website')

@section('title', 'Contact Us — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    <section class="page-banner">
        <div class="container">
            <h1 class="page-banner-title">Contact Us</h1>
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&rsaquo;</span>
                <span>Contact</span>
            </div>
        </div>
    </section>

    <section class="page-section">
        <div class="container contact-grid">
            <div class="contact-cards">
                <div>
                    <span class="section-script">Get In Touch</span>
                    <h2 class="section-title" style="margin-bottom: 12px;">Let's Plan Your Next Journey</h2>
                    <p>Have questions about custom packages, private group departures, or family tours? Send us a message anytime.</p>
                </div>

                <div class="contact-info-card">
                    <span class="footer-contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </span>
                    <div>
                        <h4>Phone</h4>
                        <div>{{ $siteSettings['contact_phone'] ?? '+01 234 567 890' }}</div>
                        @if (!empty($siteSettings['contact_phone_alt']))
                            <div>{{ $siteSettings['contact_phone_alt'] }}</div>
                        @endif
                    </div>
                </div>

                <div class="contact-info-card">
                    <span class="footer-contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </span>
                    <div>
                        <h4>Email</h4>
                        <div>{{ $siteSettings['contact_email'] ?? 'info@omtravel.com' }}</div>
                    </div>
                </div>

                <div class="contact-info-card">
                    <span class="footer-contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </span>
                    <div>
                        <h4>Office Address</h4>
                        <div>{{ $siteSettings['contact_address'] ?? '789 Inner Lane, California, USA' }}</div>
                    </div>
                </div>
            </div>

            <div class="contact-form-card">
                <h3 style="margin-bottom: 18px;">Send Us a Message</h3>

                @if (session('contact_success'))
                    <div class="alert alert-success">{{ session('contact_success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">Please fix the errors below before submitting.</div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="name" class="form-label">Your Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', auth()->user()?->name) }}" class="form-control" required>
                            @error('name') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" id="email" name="email" value="{{ old('email', auth()->user()?->email) }}" class="form-control" required>
                            @error('email') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="form-control">
                            @error('phone') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="subject" class="form-label">Subject *</label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" class="form-control" required>
                            @error('subject') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message" class="form-label">Your Message *</label>
                        <textarea id="message" name="message" class="form-control" required>{{ old('message') }}</textarea>
                        @error('message') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn btn-cyan">Send Inquiry</button>
                </form>
            </div>
        </div>
    </section>
@endsection
