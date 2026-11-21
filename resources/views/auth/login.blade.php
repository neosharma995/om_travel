@extends('layouts.website')

@section('title', 'Sign In — '.($siteSettings['site_name'] ?? 'OmTravel'))

@section('content')
    <section class="auth-section">
        <div class="container">
            <div class="auth-grid">
                <div class="auth-info-panel">
                    <div>
                        <span class="auth-eyebrow">Welcome Back</span>
                        <h2 class="auth-panel-title">One Sign-In for Travellers &amp; Admins</h2>
                        <p class="auth-panel-text">
                            Sign in once and we will automatically route you to your Customer Dashboard or the Admin Panel based on your account role.
                        </p>
                    </div>

                    <div class="demo-creds-stack">
                        <div class="demo-cred-card">
                            <div class="demo-cred-head">
                                <strong>Customer Account</strong>
                                <span class="demo-role-badge">Customer Dashboard</span>
                            </div>
                            <div class="demo-cred-lines">
                                <div class="demo-cred-row">
                                    <span>Email:</span>
                                    <code class="demo-cred-code">customer@omtravel.com</code>
                                </div>
                                <div class="demo-cred-row">
                                    <span>Password:</span>
                                    <code class="demo-cred-code">password</code>
                                </div>
                            </div>
                            <div class="demo-cred-actions">
                                <button type="button" class="demo-fill-btn" data-demo-email="customer@omtravel.com" data-demo-password="password">Fill Form</button>
                                <button type="button" class="demo-login-btn" data-demo-email="customer@omtravel.com" data-demo-password="password" data-demo-submit="true">Quick Login</button>
                            </div>
                        </div>

                        <div class="demo-cred-card">
                            <div class="demo-cred-head">
                                <strong>Admin Account</strong>
                                <span class="demo-role-badge demo-role-badge-admin">Admin Panel</span>
                            </div>
                            <div class="demo-cred-lines">
                                <div class="demo-cred-row">
                                    <span>Email:</span>
                                    <code class="demo-cred-code">admin@omtravel.com</code>
                                </div>
                                <div class="demo-cred-row">
                                    <span>Password:</span>
                                    <code class="demo-cred-code">password</code>
                                </div>
                            </div>
                            <div class="demo-cred-actions">
                                <button type="button" class="demo-fill-btn" data-demo-email="admin@omtravel.com" data-demo-password="password">Fill Form</button>
                                <button type="button" class="demo-login-btn" data-demo-email="admin@omtravel.com" data-demo-password="password" data-demo-submit="true">Quick Login</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="auth-form-card">
                    <div class="auth-form-head">
                        <span class="auth-icon-badge" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
                        </span>
                        <h1 class="auth-title">Sign In to OmTravel</h1>
                        <p class="auth-sub">Enter your email and password, or click Quick Login on a demo card to preview either portal.</p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-error">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form id="loginForm" action="{{ route('login.submit') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" autocomplete="email" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control" autocomplete="current-password" required>
                        </div>

                        <div class="form-group">
                            <label class="auth-checkbox-row">
                                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                                <span>Remember me</span>
                            </label>
                        </div>

                        <button type="submit" id="loginSubmitBtn" class="btn btn-cyan auth-submit-btn">Sign In</button>
                    </form>

                    <div class="auth-footer-links">
                        <div>
                            New to OmTravel?
                            <a href="{{ route('register') }}" class="auth-link">Create Customer Account</a>
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
