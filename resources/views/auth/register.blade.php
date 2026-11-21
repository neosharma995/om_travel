@extends('layouts.website')

@section('title', 'Create Account — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    <section class="auth-section">
        <div class="container">
            <div class="auth-grid">
                <div class="auth-info-panel">
                    <div>
                        <span class="auth-eyebrow">Start Your Journey</span>
                        <h2 class="auth-panel-title">Plan &amp; Track Every Trip</h2>
                        <p class="auth-panel-text">
                            Create a free traveller account to organize upcoming vacations, booking statuses, and custom tour inquiries in one portal.
                        </p>
                    </div>

                    <ul class="auth-perks-list">
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>View upcoming tour reservations and booking status updates</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Cancel pending booking requests whenever plans change</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Track replies to your custom itinerary and destination inquiries</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Manage your traveller profile details in one place</span>
                        </li>
                    </ul>

                    <div class="auth-demo-note">
                        Want to preview right away? View instant Customer &amp; Admin demo credentials on the
                        <a href="{{ route('login') }}">Sign In page &rarr;</a>
                    </div>
                </div>

                <div class="auth-form-card">
                    <div class="auth-form-head">
                        <span class="auth-icon-badge" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                        </span>
                        <h1 class="auth-title">Create Traveller Account</h1>
                        <p class="auth-sub">Track bookings, manage upcoming trips, and view travel inquiries in one place.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-error">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('register.submit') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" autocomplete="name" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" autocomplete="email" required>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password * (min 8 characters)</label>
                            <input type="password" id="password" name="password" class="form-control" autocomplete="new-password" required>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Confirm Password *</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password" required>
                        </div>

                        <button type="submit" class="btn btn-cyan auth-submit-btn">Create Account</button>
                    </form>

                    <div class="auth-footer-links">
                        <div>
                            Already have an account?
                            <a href="{{ route('login') }}" class="auth-link">Sign In</a>
                        </div>
                        <div>
                            <a href="{{ route('home') }}" class="auth-link">&larr; Back to Website</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
