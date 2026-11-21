@extends('layouts.customer')

@section('title', 'My Profile')
@section('page_title', 'Profile & Security')

@section('content')
    <div class="panel-card" style="max-width: 780px;">
        <div class="panel-header">
            <h2 class="panel-title">Account Details</h2>
        </div>

        <form action="{{ route('customer.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label for="name" class="form-label">Full Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                    @error('name') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group form-span-2">
                    <label for="current_password" class="form-label">Current Password (required only when changing password)</label>
                    <input type="password" id="current_password" name="current_password" class="form-control">
                    @error('current_password') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" id="password" name="password" class="form-control">
                    @error('password') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
@endsection
