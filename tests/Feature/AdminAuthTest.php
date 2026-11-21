<?php

use App\Models\User;

test('guest accessing admin or customer dashboard is redirected to unified login', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));

    $this->get(route('customer.dashboard'))
        ->assertRedirect(route('login'));

    $this->get(route('admin.login'))
        ->assertRedirect(route('login'));
});

test('authenticated customer receives forbidden when accessing admin dashboard', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('admin user signs in through unified login and is routed to admin dashboard', function () {
    $admin = User::factory()->admin()->create([
        'email' => 'admin@omtravel.com',
        'password' => 'secret123',
    ]);

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Sign In to OmTravel');

    $loginResponse = $this->post(route('login.submit'), [
        'email' => 'admin@omtravel.com',
        'password' => 'secret123',
    ]);

    $loginResponse->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin);

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard Overview')
        ->assertSee('css/admin.css')
        ->assertDontSee('tailwindcss');

    $this->post(route('logout'))
        ->assertRedirect(route('login'));
    $this->assertGuest();
});

test('customer user signs in through the same unified login and is routed to customer dashboard', function () {
    $customer = User::factory()->create([
        'email' => 'customer@omtravel.com',
        'password' => 'secret123',
        'is_admin' => false,
    ]);

    $response = $this->post(route('login.submit'), [
        'email' => 'customer@omtravel.com',
        'password' => 'secret123',
    ]);

    $response->assertRedirect(route('customer.dashboard'));
    $this->assertAuthenticatedAs($customer);

    $this->get(route('customer.dashboard'))
        ->assertOk()
        ->assertSee('Traveller Dashboard')
        ->assertDontSee('tailwindcss');
});

test('already signed in users visiting login are redirected to their role dashboard', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create(['is_admin' => false]);

    $this->actingAs($admin)
        ->get(route('login'))
        ->assertRedirect(route('admin.dashboard'));

    $this->actingAs($customer)
        ->get(route('login'))
        ->assertRedirect(route('customer.dashboard'));
});

test('invalid password fails unified login with validation error', function () {
    User::factory()->admin()->create([
        'email' => 'admin@omtravel.com',
        'password' => 'correct-pass',
    ]);

    $response = $this->from(route('login'))->post(route('login.submit'), [
        'email' => 'admin@omtravel.com',
        'password' => 'wrong-pass',
    ]);

    $response->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('auth pages include website header, footer, and demo credentials on login', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('site-header')
        ->assertSee('site-footer')
        ->assertSee('customer@omtravel.com')
        ->assertSee('admin@omtravel.com')
        ->assertSee('password');

    $this->get(route('register'))
        ->assertOk()
        ->assertSee('site-header')
        ->assertSee('site-footer')
        ->assertSee('Create Traveller Account');
});
