<?php

use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\GalleryItem;
use App\Models\Inquiry;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\TourGuide;

test('homepage renders all travel sections with normal css only', function () {
    SiteSetting::setMany(['hero_title' => 'Natural Wonder of the world']);
    $category = TourCategory::factory()->create(['name' => 'Cruises']);
    $destination = Destination::factory()->create(['name' => 'Maldives', 'is_popular' => true]);
    $tour = Tour::factory()->create([
        'destination_id' => $destination->id,
        'tour_category_id' => $category->id,
        'title' => 'Maldives Lagoon Retreat',
        'is_featured' => true,
        'is_active' => true,
    ]);
    $gallery = GalleryItem::factory()->create(['title' => 'Lagoon Sunset']);
    $guide = TourGuide::factory()->create(['name' => 'Jane Cooper']);
    $testimonial = Testimonial::factory()->create(['client_name' => 'Maria Doe']);
    $post = BlogPost::factory()->create(['title' => '10 Reason Why You Should Visit New Maldives']);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Natural Wonder of the world')
        ->assertSee($category->name)
        ->assertSee($destination->name)
        ->assertSee($tour->title)
        ->assertSee($gallery->title)
        ->assertSee($guide->name)
        ->assertSee($testimonial->client_name)
        ->assertSee($post->title)
        ->assertSee('css/website.css')
        ->assertDontSee('tailwindcss')
        ->assertDontSee('bootstrap');
});

test('tours index filters by destination, category, duration, max price, and search', function () {
    $greece = Destination::factory()->create(['name' => 'Greece', 'slug' => 'greece']);
    $italy = Destination::factory()->create(['name' => 'Italy', 'slug' => 'italy']);
    $beach = TourCategory::factory()->create(['name' => 'Beach', 'slug' => 'beach']);
    $cruise = TourCategory::factory()->create(['name' => 'Cruises', 'slug' => 'cruises']);

    $matching = Tour::factory()->create([
        'destination_id' => $greece->id,
        'tour_category_id' => $beach->id,
        'title' => 'Santorini Sun Escape',
        'price' => 850.00,
        'duration_days' => 5,
        'is_active' => true,
    ]);

    $other = Tour::factory()->create([
        'destination_id' => $italy->id,
        'tour_category_id' => $cruise->id,
        'title' => 'Amalfi Grand Voyage',
        'price' => 1800.00,
        'duration_days' => 9,
        'is_active' => true,
    ]);

    $response = $this->get(route('tours.index', [
        'destination' => 'greece',
        'category' => 'beach',
        'duration' => 'medium',
        'max_price' => 1000,
        'search' => 'Santorini',
    ]));

    $response->assertOk()
        ->assertSee($matching->title)
        ->assertDontSee($other->title);
});

test('tour detail page shows active tour and returns not found for inactive tour', function () {
    $activeTour = Tour::factory()->create(['title' => 'Active Alpine Tour', 'is_active' => true]);
    $inactiveTour = Tour::factory()->create(['title' => 'Hidden Tour', 'is_active' => false]);

    $this->get(route('tours.show', $activeTour))
        ->assertOk()
        ->assertSee($activeTour->title);

    $this->get(route('tours.show', $inactiveTour))
        ->assertNotFound();
});

test('guest can submit a tour booking and total price is calculated accurately', function () {
    $tour = Tour::factory()->create(['price' => 450.00, 'is_active' => true]);

    $response = $this->post(route('tours.book', $tour), [
        'customer_name' => 'Alice Morgan',
        'customer_email' => 'alice@example.com',
        'customer_phone' => '+1 555 201 8899',
        'travel_date' => now()->addDays(15)->toDateString(),
        'guests' => 3,
        'special_requests' => 'Window seats please.',
    ]);

    $response->assertRedirect(route('tours.show', $tour))
        ->assertSessionHas('booking_success');

    $this->assertDatabaseHas('bookings', [
        'tour_id' => $tour->id,
        'customer_name' => 'Alice Morgan',
        'customer_email' => 'alice@example.com',
        'guests' => 3,
        'total_price' => 1350.00,
        'status' => 'pending',
    ]);
});

test('tour booking rejects invalid input without saving a record', function () {
    $tour = Tour::factory()->create(['is_active' => true]);

    $response = $this->from(route('tours.show', $tour))->post(route('tours.book', $tour), [
        'customer_name' => '',
        'customer_email' => 'not-an-email',
        'travel_date' => now()->subDay()->toDateString(),
        'guests' => 0,
    ]);

    $response->assertRedirect(route('tours.show', $tour))
        ->assertSessionHasErrors(['customer_name', 'customer_email', 'travel_date', 'guests']);

    expect(Booking::query()->count())->toBe(0);
});

test('destination detail page renders active destination and blocks inactive one', function () {
    $active = Destination::factory()->create(['name' => 'Switzerland', 'is_active' => true]);
    $inactive = Destination::factory()->create(['name' => 'Draftland', 'is_active' => false]);
    $tour = Tour::factory()->create(['destination_id' => $active->id, 'title' => 'Swiss Alps Pass', 'is_active' => true]);

    $this->get(route('destinations.show', $active))
        ->assertOk()
        ->assertSee($active->name)
        ->assertSee($tour->title);

    $this->get(route('destinations.show', $inactive))
        ->assertNotFound();
});

test('blog pages display published articles and hide unpublished ones', function () {
    $published = BlogPost::factory()->create(['title' => 'Published Island Guide', 'is_published' => true]);
    $draft = BlogPost::factory()->create(['title' => 'Secret Draft Article', 'is_published' => false]);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSee($published->title)
        ->assertDontSee($draft->title);

    $this->get(route('blog.show', $published))
        ->assertOk()
        ->assertSee($published->title);

    $this->get(route('blog.show', $draft))
        ->assertNotFound();
});

test('contact page renders and stores valid inquiry while rejecting invalid payloads', function () {
    $this->get(route('contact.create'))->assertOk();

    $invalid = $this->from(route('contact.create'))->post(route('contact.store'), [
        'name' => '',
        'email' => 'bad',
        'subject' => '',
        'message' => '',
    ]);

    $invalid->assertRedirect(route('contact.create'))
        ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    expect(Inquiry::query()->count())->toBe(0);

    $valid = $this->post(route('contact.store'), [
        'name' => 'Jordan Lee',
        'email' => 'jordan@example.com',
        'phone' => '+1 555 0100',
        'subject' => 'Group Tour Question',
        'message' => 'Can we customize a 6-day itinerary for eight people?',
    ]);

    $valid->assertRedirect(route('contact.create'))
        ->assertSessionHas('contact_success');

    $this->assertDatabaseHas('inquiries', [
        'email' => 'jordan@example.com',
        'subject' => 'Group Tour Question',
        'status' => 'new',
    ]);
});
