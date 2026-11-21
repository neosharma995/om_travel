<?php

use App\Models\Booking;
use App\Models\Inquiry;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('visitor can register as a customer and is redirected to customer dashboard without admin rights', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee('Create Traveller Account');

    $response = $this->post(route('register.submit'), [
        'name' => 'Maya Lin',
        'email' => 'maya.lin@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('customer.dashboard'));

    $user = User::query()->where('email', 'maya.lin@example.com')->firstOrFail();
    expect($user->isAdmin())->toBeFalse();
    $this->assertAuthenticatedAs($user);
});

test('admin visiting customer dashboard is redirected to admin dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('customer.dashboard'))
        ->assertRedirect(route('admin.dashboard'));
});

test('logged-in customer booking attaches user_id and displays only own bookings and inquiries', function () {
    $customer = User::factory()->create(['email' => 'elena@example.com']);
    $otherCustomer = User::factory()->create(['email' => 'other@example.com']);

    $tour = Tour::factory()->create(['title' => 'Greece Sunset Sail', 'price' => 500.00, 'is_active' => true]);
    $otherTour = Tour::factory()->create(['title' => 'Secret Other Trip', 'is_active' => true]);

    Booking::factory()->create([
        'tour_id' => $otherTour->id,
        'user_id' => $otherCustomer->id,
        'customer_email' => 'other@example.com',
    ]);

    Inquiry::factory()->create([
        'email' => 'elena@example.com',
        'subject' => 'My Private Inquiry',
    ]);
    Inquiry::factory()->create([
        'email' => 'other@example.com',
        'subject' => 'Someone Else Inquiry',
    ]);

    $this->actingAs($customer)
        ->post(route('tours.book', $tour), [
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'travel_date' => now()->addDays(20)->toDateString(),
            'guests' => 2,
        ])
        ->assertRedirect(route('tours.show', $tour));

    $this->assertDatabaseHas('bookings', [
        'tour_id' => $tour->id,
        'user_id' => $customer->id,
        'total_price' => 1000.00,
    ]);

    $this->actingAs($customer)
        ->get(route('customer.bookings.index'))
        ->assertOk()
        ->assertSee('Greece Sunset Sail')
        ->assertDontSee('Secret Other Trip');

    $this->actingAs($customer)
        ->get(route('customer.inquiries.index'))
        ->assertOk()
        ->assertSee('My Private Inquiry')
        ->assertDontSee('Someone Else Inquiry');
});

test('customer can cancel own pending booking, cannot cancel confirmed booking, and cannot cancel another users booking', function () {
    $customer = User::factory()->create(['email' => 'elena@example.com']);
    $otherCustomer = User::factory()->create(['email' => 'other@example.com']);

    $pendingBooking = Booking::factory()->create([
        'user_id' => $customer->id,
        'customer_email' => $customer->email,
        'status' => 'pending',
    ]);

    $confirmedBooking = Booking::factory()->create([
        'user_id' => $customer->id,
        'customer_email' => $customer->email,
        'status' => 'confirmed',
    ]);

    $otherBooking = Booking::factory()->create([
        'user_id' => $otherCustomer->id,
        'customer_email' => $otherCustomer->email,
        'status' => 'pending',
    ]);

    $this->actingAs($customer)
        ->post(route('customer.bookings.cancel', $pendingBooking))
        ->assertRedirect();
    expect($pendingBooking->fresh()->status)->toBe('cancelled');

    $this->actingAs($customer)
        ->post(route('customer.bookings.cancel', $confirmedBooking))
        ->assertRedirect()
        ->assertSessionHas('error');
    expect($confirmedBooking->fresh()->status)->toBe('confirmed');

    $this->actingAs($customer)
        ->post(route('customer.bookings.cancel', $otherBooking))
        ->assertForbidden();
    expect($otherBooking->fresh()->status)->toBe('pending');
});

test('customer can update profile and password with current password validation', function () {
    $customer = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'password' => 'old-password',
    ]);

    $this->actingAs($customer)
        ->from(route('customer.profile.edit'))
        ->put(route('customer.profile.update'), [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'current_password' => 'wrong-password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ])
        ->assertRedirect(route('customer.profile.edit'))
        ->assertSessionHasErrors('current_password');

    $this->actingAs($customer)
        ->put(route('customer.profile.update'), [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'current_password' => 'old-password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ])
        ->assertRedirect(route('customer.profile.edit'))
        ->assertSessionHas('success');

    $customer->refresh();
    expect($customer->name)->toBe('New Name')
        ->and($customer->email)->toBe('new@example.com')
        ->and(Hash::check('new-password123', $customer->password))->toBeTrue();
});
