<?php

use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\GalleryItem;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\TourGuide;
use App\Models\TourType;

test('quality audit: homepage preserves protected components and renders all redesigned sections', function () {
    // Ensure site settings and seed data
    SiteSetting::setMany([
        'hero_title' => 'Natural Wonder of the world',
        'topbar_address' => '45 New Eskaton Road, Austria',
    ]);

    $beachCategory = TourCategory::factory()->create(['name' => 'Beach', 'slug' => 'beach']);
    $destination = Destination::factory()->create(['name' => 'Greece', 'slug' => 'greece', 'is_popular' => true]);
    $tour = Tour::factory()->create([
        'destination_id' => $destination->id,
        'tour_category_id' => $beachCategory->id,
        'title' => 'Greece Tour Package',
        'is_featured' => true,
        'is_active' => true,
    ]);

    $gallery = GalleryItem::factory()->create([
        'title' => 'Santorini Sunset Cliffs',
        'location' => 'Oia, Greece',
        'is_active' => true,
    ]);

    $guide = TourGuide::factory()->create([
        'name' => 'Jane Cooper',
        'role' => 'Tourist Guide',
        'is_active' => true,
    ]);

    $testimonial = Testimonial::factory()->create([
        'client_name' => 'Maria Doe',
        'is_active' => true,
    ]);

    $post = BlogPost::factory()->create([
        'title' => '10 Reason Why You Should Visit New Maldives',
        'is_published' => true,
    ]);

    $response = $this->get(route('home'));

    $response->assertOk()
        // 1. Protected Header & Footer
        ->assertSee('45 New Eskaton Road, Austria')
        ->assertSee('Sun to Friday: 8.00 am - 7.00 pm')
        ->assertSee('All Rights Reserved')
        // 2. Protected Hero Slider & Hero Search
        ->assertSee('heroSlider')
        ->assertSee('heroSearchForm')
        ->assertSee('Natural Wonder of the world')
        // 3. Redesigned Popular Packages Section
        ->assertSee('Greece Tour Package')
        // 4. Redesigned Recent Gallery
        ->assertSee('Santorini Sunset Cliffs')
        ->assertSee('Oia, Greece')
        // 5. Redesigned Statistics
        ->assertSee('12')
        ->assertSee('Years Experience')
        ->assertSee('97%')
        ->assertSee('Retention Rate')
        ->assertSee('8k')
        ->assertSee('Tour Completed')
        ->assertSee('19k')
        ->assertSee('Happy Travellers')
        // 6. Redesigned Tour Guides
        ->assertSee('Jane Cooper')
        ->assertSee('Tourist Guide')
        // 7. Redesigned Testimonials
        ->assertSee('What Client Say About us')
        ->assertSee('Maria Doe')
        // 8. Redesigned Brand Partners
        ->assertSee('AlpinePeak')
        ->assertSee('CoralVoyage')
        ->assertSee('Wanderlust')
        ->assertSee('PalmIsland')
        ->assertSee('SunTrail')
        ->assertSee('SkyPass')
        ->assertSee('Coastline')
        ->assertSee('ExploreCo')
        // 9. Redesigned News & Articles
        ->assertSee('News &amp; Articles', false)
        ->assertSee('10 Reason Why You Should Visit New Maldives');
});

test('quality audit: all inner pages render with expected editorial design, dynamic content, and working routes', function () {
    $category = TourCategory::factory()->create(['name' => 'Hiking', 'slug' => 'hiking']);
    $tourType = TourType::factory()->create(['name' => 'Alpine Expedition', 'slug' => 'alpine-expedition']);
    $destination = Destination::factory()->create([
        'name' => 'Switzerland',
        'slug' => 'switzerland',
        'country' => 'Switzerland',
        'description' => 'Snow-capped peaks and pristine mountain passes.',
        'is_popular' => true,
        'is_active' => true,
    ]);

    $tour = Tour::factory()->create([
        'destination_id' => $destination->id,
        'tour_category_id' => $category->id,
        'tour_type_id' => $tourType->id,
        'title' => 'Switzerland Alpine Escape',
        'location' => 'Zermatt, Switzerland',
        'price' => 1150.00,
        'duration_days' => 6,
        'duration_label' => '6 Days',
        'rating' => 4.8,
        'reviews_count' => 48,
        'summary' => 'Traverse crystal alpine lakes and scenic Swiss railways.',
        'description' => 'Experience Switzerland highest peaks with certified leaders.',
        'highlights' => "Glacier viewpoint ascent\nScenic rail pass",
        'is_active' => true,
    ]);

    $galleryItem = GalleryItem::factory()->create([
        'title' => 'Snowy Alpine Ridge Trail',
        'location' => 'Zermatt, Switzerland',
        'is_active' => true,
    ]);

    $post = BlogPost::factory()->create([
        'title' => 'Alpine Rail Journeys and Mountain Horizons',
        'category' => 'Adventure',
        'excerpt' => 'Exploring Swiss glaciers by rail and foot.',
        'content' => "High above the tree line, crisp air welcomes adventurers.\n\nEvery turn on the mountain railway unveils new vistas.",
        'is_published' => true,
    ]);

    // 1. Tours index page (/tours)
    $this->get(route('tours.index'))
        ->assertOk()
        ->assertSee('Handcrafted Tour Packages')
        ->assertSee($tour->title)
        ->assertSee('toursFilterSidebar')
        ->assertSee('toursFilterToggle');

    // 2. Destinations index page (/destinations)
    $this->get(route('destinations.index'))
        ->assertOk()
        ->assertSee('World Destinations &amp; Odysseys', false)
        ->assertSee($destination->name)
        ->assertSee(route('destinations.show', $destination));

    // 3. Standalone Gallery page (/gallery)
    $this->get(route('gallery.index'))
        ->assertOk()
        ->assertSee('Moments &amp; Horizons Across The Globe', false)
        ->assertSee($galleryItem->title)
        ->assertSee($galleryItem->location)
        ->assertSee('data-lightbox-src="'.$galleryItem->imageUrl().'"', false);

    // 4. Blog index page (/blog)
    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSee('Travel Blog &amp; Articles', false)
        ->assertSee($post->title)
        ->assertSee($post->category)
        ->assertSee(route('blog.show', $post));

    // 5. Blog detail page (/blog/{slug})
    $this->get(route('blog.show', $post))
        ->assertOk()
        ->assertSee($post->title)
        ->assertSee($post->excerpt)
        ->assertSee('Exploring Swiss glaciers by rail and foot')
        ->assertSee('Back to All Articles');

    // 6. Tour package detail page (/tours/{slug})
    $this->get(route('tours.show', $tour))
        ->assertOk()
        ->assertSee($tour->title)
        ->assertSee($tour->summary)
        ->assertSee($tour->location)
        ->assertSee($category->name)
        ->assertSee($tourType->name)
        ->assertSee('Expedition Overview')
        ->assertSee('What is Included &amp; Excluded', false)
        ->assertSee('Glacier viewpoint ascent')
        ->assertSee('Reserve Your Passage')
        ->assertSee('Confirm Booking Request');
});

test('quality audit: booking form validation and submission integrity', function () {
    $tour = Tour::factory()->create([
        'title' => 'Maldives Lagoon Retreat',
        'price' => 850.00,
        'is_active' => true,
    ]);

    // 1. Invalid payload rejected
    $invalid = $this->from(route('tours.show', $tour))->post(route('tours.book', $tour), [
        'customer_name' => '',
        'customer_email' => 'bad-email',
        'travel_date' => now()->subDays(5)->toDateString(),
        'guests' => 0,
    ]);
    $invalid->assertRedirect(route('tours.show', $tour))
        ->assertSessionHasErrors(['customer_name', 'customer_email', 'travel_date', 'guests']);

    // 2. Valid booking created
    $valid = $this->post(route('tours.book', $tour), [
        'customer_name' => 'Alexander Wright',
        'customer_email' => 'alex@example.com',
        'customer_phone' => '+44 7700 900123',
        'travel_date' => now()->addDays(30)->toDateString(),
        'guests' => 2,
        'special_requests' => 'Overwater villa preference please.',
    ]);

    $valid->assertRedirect(route('tours.show', $tour))
        ->assertSessionHas('booking_success');

    $this->assertDatabaseHas('bookings', [
        'tour_id' => $tour->id,
        'customer_name' => 'Alexander Wright',
        'customer_email' => 'alex@example.com',
        'guests' => 2,
        'total_price' => 1700.00,
        'status' => 'pending',
    ]);
});
