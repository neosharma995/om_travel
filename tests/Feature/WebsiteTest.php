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
use App\Models\TourType;

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

test('tours index filters by tour type and tour detail page displays tour type', function () {
    $luxuryType = TourType::factory()->create(['name' => 'Luxury Escape', 'slug' => 'luxury-escape']);
    $trekType = TourType::factory()->create(['name' => 'Adventure Trek', 'slug' => 'adventure-trek']);

    $luxuryTour = Tour::factory()->create([
        'tour_type_id' => $luxuryType->id,
        'title' => 'Amalfi Private Retreat',
        'is_featured' => true,
        'is_active' => true,
    ]);

    $trekTour = Tour::factory()->create([
        'tour_type_id' => $trekType->id,
        'title' => 'Dolomites Summit Circuit',
        'is_featured' => false,
        'is_active' => true,
    ]);

    $this->get(route('tours.index', ['tour_type' => 'luxury-escape']))
        ->assertOk()
        ->assertSee('Luxury Escape')
        ->assertSee($luxuryTour->title)
        ->assertDontSee($trekTour->title);

    $this->get(route('tours.show', $luxuryTour))
        ->assertOk()
        ->assertSee('Luxury Escape');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Luxury Escape');
});

test('tours index renders discovery layout, all 6 filter inputs, and booking links', function () {
    $category = TourCategory::factory()->create(['name' => 'Snorkeling', 'slug' => 'snorkeling']);
    $destination = Destination::factory()->create(['name' => 'Thailand', 'slug' => 'thailand']);
    $tourType = TourType::factory()->create(['name' => 'Adventure', 'slug' => 'adventure']);
    $tour = Tour::factory()->create([
        'destination_id' => $destination->id,
        'tour_category_id' => $category->id,
        'tour_type_id' => $tourType->id,
        'title' => 'Phuket Island Expedition',
        'price' => 750.00,
        'duration_days' => 4,
        'is_active' => true,
    ]);

    $response = $this->get(route('tours.index'));

    $response->assertOk()
        ->assertSee('Handcrafted Tour Packages')
        ->assertSee('name="search"', false)
        ->assertSee('name="destination"', false)
        ->assertSee('name="category"', false)
        ->assertSee('name="tour_type"', false)
        ->assertSee('name="duration"', false)
        ->assertSee('name="max_price"', false)
        ->assertSee($tour->title)
        ->assertSee(route('tours.show', $tour))
        ->assertSee('Book Now');
});

test('tours index filter options filter accurately and render active badges or empty state', function () {
    $swiss = Destination::factory()->create(['name' => 'Switzerland', 'slug' => 'switzerland']);
    $japan = Destination::factory()->create(['name' => 'Japan', 'slug' => 'japan']);
    $hiking = TourCategory::factory()->create(['name' => 'Hiking', 'slug' => 'hiking']);
    $cruise = TourCategory::factory()->create(['name' => 'Cruises', 'slug' => 'cruises']);
    $solo = TourType::factory()->create(['name' => 'Solo Expeditions', 'slug' => 'solo-expeditions']);
    $family = TourType::factory()->create(['name' => 'Family Holiday', 'slug' => 'family-holiday']);

    $swissTour = Tour::factory()->create([
        'destination_id' => $swiss->id,
        'tour_category_id' => $hiking->id,
        'tour_type_id' => $solo->id,
        'title' => 'Matterhorn Summit Trek',
        'price' => 1200.00,
        'duration_days' => 8,
        'is_active' => true,
    ]);

    $japanTour = Tour::factory()->create([
        'destination_id' => $japan->id,
        'tour_category_id' => $cruise->id,
        'tour_type_id' => $family->id,
        'title' => 'Tokyo Bay Cruise Escape',
        'price' => 450.00,
        'duration_days' => 2,
        'is_active' => true,
    ]);

    // 1. Destination filter
    $this->get(route('tours.index', ['destination' => 'switzerland']))
        ->assertOk()
        ->assertSee($swissTour->title)
        ->assertDontSee($japanTour->title)
        ->assertSee('Active Filters');

    // 2. Activity / Category filter
    $this->get(route('tours.index', ['category' => 'cruises']))
        ->assertOk()
        ->assertSee($japanTour->title)
        ->assertDontSee($swissTour->title);

    // 3. Tour Type filter
    $this->get(route('tours.index', ['tour_type' => 'solo-expeditions']))
        ->assertOk()
        ->assertSee($swissTour->title)
        ->assertDontSee($japanTour->title);

    // 4. Duration filter (short <= 3, long >= 7)
    $this->get(route('tours.index', ['duration' => 'short']))
        ->assertOk()
        ->assertSee($japanTour->title)
        ->assertDontSee($swissTour->title);

    $this->get(route('tours.index', ['duration' => 'long']))
        ->assertOk()
        ->assertSee($swissTour->title)
        ->assertDontSee($japanTour->title);

    // 5. Max Price budget filter
    $this->get(route('tours.index', ['max_price' => 500]))
        ->assertOk()
        ->assertSee($japanTour->title)
        ->assertDontSee($swissTour->title);

    // 6. Search keyword
    $this->get(route('tours.index', ['search' => 'Matterhorn']))
        ->assertOk()
        ->assertSee($swissTour->title)
        ->assertDontSee($japanTour->title);

    // 7. Empty state when no tour matches
    $this->get(route('tours.index', ['search' => 'NonExistentExpeditionTitle']))
        ->assertOk()
        ->assertSee('No tour packages matched your filters')
        ->assertSee('Reset All Filters');
});

test('destinations index renders editorial discovery layout, search and popular filters, and detail links', function () {
    $maldives = Destination::factory()->create([
        'name' => 'Maldives',
        'slug' => 'maldives',
        'country' => 'Maldives',
        'description' => 'Crystal turquoise atolls and overwater villas.',
        'is_popular' => true,
        'is_active' => true,
    ]);

    $greece = Destination::factory()->create([
        'name' => 'Greece',
        'slug' => 'greece',
        'country' => 'Greece',
        'description' => 'Aegean coastal journeys and ancient ruins.',
        'is_popular' => false,
        'is_active' => true,
    ]);

    // 1. Destinations index renders successfully
    $response = $this->get(route('destinations.index'));

    $response->assertOk()
        ->assertSee('World Destinations')
        ->assertSee($maldives->name)
        ->assertSee($greece->name)
        ->assertSee(route('destinations.show', $maldives))
        ->assertSee(route('destinations.show', $greece));

    // 2. Search keyword filter
    $this->get(route('destinations.index', ['search' => 'Maldives']))
        ->assertOk()
        ->assertSee($maldives->name)
        ->assertDontSee($greece->name);

    // 3. Popular tab filter
    $this->get(route('destinations.index', ['filter' => 'popular']))
        ->assertOk()
        ->assertSee($maldives->name)
        ->assertDontSee($greece->name);

    // 4. Empty state when no destination matches
    $this->get(route('destinations.index', ['search' => 'UnknownPlaceX99']))
        ->assertOk()
        ->assertSee('No destinations matched your search')
        ->assertSee('View All Destinations');
});

test('gallery page renders editorial photography layout, region filters, and lightbox metadata', function () {
    $maldivesPhoto = GalleryItem::factory()->create([
        'title' => 'Overwater Lagoon Villas',
        'location' => 'Baa Atoll, Maldives',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $swissPhoto = GalleryItem::factory()->create([
        'title' => 'Snowy Alpine Ridge Trail',
        'location' => 'Zermatt, Switzerland',
        'sort_order' => 2,
        'is_active' => true,
    ]);

    // 1. Standalone gallery page renders successfully
    $response = $this->get(route('gallery.index'));

    $response->assertOk()
        ->assertSee('Moments &amp; Horizons Across The Globe', false)
        ->assertSee('Curated Visual Chronicles')
        ->assertSee($maldivesPhoto->title)
        ->assertSee($maldivesPhoto->location)
        ->assertSee($swissPhoto->title)
        ->assertSee($swissPhoto->location)
        ->assertSee('data-lightbox-src="'.$maldivesPhoto->imageUrl().'"', false)
        ->assertSee('data-lightbox-title="'.$maldivesPhoto->title.'"', false)
        ->assertSee('data-lightbox-location="'.$maldivesPhoto->location.'"', false)
        ->assertSee('All Captures');

    // 2. Region / Category filter
    $filteredResponse = $this->get(route('gallery.index', ['category' => 'Maldives']));
    $filteredResponse->assertOk()
        ->assertSee($maldivesPhoto->title)
        ->assertDontSee($swissPhoto->title);

    // 3. Empty state for non-matching category
    $emptyResponse = $this->get(route('gallery.index', ['category' => 'Antarctica']));
    $emptyResponse->assertOk()
        ->assertSee('No photographs in this collection')
        ->assertSee('View All Captures');
});

test('blog listing renders editorial magazine spread, category filters, and metadata', function () {
    $adventurePost = BlogPost::factory()->create([
        'title' => 'The 7 Amazing Destinations for Adventure Seekers',
        'category' => 'Adventure',
        'read_time' => '7 Min Read',
        'published_at' => now()->subDay(),
        'is_published' => true,
    ]);

    $japanPost = BlogPost::factory()->create([
        'title' => 'The Best Time to Visit Japan & Island Resorts',
        'category' => 'Seasonal Tips',
        'read_time' => '5 Min Read',
        'published_at' => now()->subDays(2),
        'is_published' => true,
    ]);

    // 1. Blog index page renders magazine layout
    $response = $this->get(route('blog.index'));

    $response->assertOk()
        ->assertSee('Travel Blog &amp; Articles', false)
        ->assertSee('Stories &amp; Field Chronicles', false)
        ->assertSee('Cover Story')
        ->assertSee($adventurePost->title)
        ->assertSee($adventurePost->read_time)
        ->assertSee($adventurePost->category)
        ->assertSee($japanPost->title)
        ->assertSee($japanPost->read_time)
        ->assertSee($japanPost->category)
        ->assertSee(route('blog.show', $adventurePost))
        ->assertSee(route('blog.show', $japanPost))
        ->assertSee('All Articles');

    // 2. Category filtering
    $filtered = $this->get(route('blog.index', ['category' => 'Adventure']));
    $filtered->assertOk()
        ->assertSee($adventurePost->title)
        ->assertDontSee($japanPost->title);

    // 3. Empty state for non-existent category
    $empty = $this->get(route('blog.index', ['category' => 'DesertSafari']));
    $empty->assertOk()
        ->assertSee('No blog articles published yet.')
        ->assertSee('View All Articles');
});

test('blog article detail page renders editorial reading layout, standfirst, hero cover, and related articles', function () {
    $mainArticle = BlogPost::factory()->create([
        'title' => 'The Art of Slow Travel Across Forgotten Atolls',
        'category' => 'Island Guide',
        'read_time' => '8 Min Read',
        'excerpt' => 'An intimate field journal exploring quiet coral sanctuaries and dawn passages across the Indian Ocean.',
        'content' => "The tide turns silently against the wooden stilts of the lagoon house.\n\nEvery journey here begins not with a plan, but with the quiet realization that time moves differently on water.\n\nFrom early dawn swims along the reef crest to starlit evening discussions, each hour invites reflection.",
        'is_published' => true,
    ]);

    $relatedArticle = BlogPost::factory()->create([
        'title' => 'High Alpine Ridge Treks in Switzerland',
        'category' => 'Alpine Journeys',
        'read_time' => '6 Min Read',
        'is_published' => true,
    ]);

    $response = $this->get(route('blog.show', $mainArticle));

    $response->assertOk()
        ->assertSee($mainArticle->title)
        ->assertSee($mainArticle->excerpt)
        ->assertSee($mainArticle->category)
        ->assertSee($mainArticle->read_time)
        ->assertSee($mainArticle->imageUrl())
        ->assertSee('The tide turns silently against the wooden stilts')
        ->assertSee('Back to All Articles')
        ->assertSee('Related Stories &amp; Further Reading', false)
        ->assertSee($relatedArticle->title);
});

test('tour package detail page renders destination hero, itinerary milestones, inclusions, and refined booking form', function () {
    $category = TourCategory::factory()->create(['name' => 'Island Cruising']);
    $type = TourType::factory()->create(['name' => 'Signature Voyage']);
    $destination = Destination::factory()->create(['name' => 'Cyclades', 'slug' => 'cyclades']);

    $tour = Tour::factory()->create([
        'destination_id' => $destination->id,
        'tour_category_id' => $category->id,
        'tour_type_id' => $type->id,
        'title' => 'Santorini & Mykonos Grand Odyssey',
        'location' => 'Cyclades Islands, Greece',
        'price' => 1250.00,
        'duration_days' => 8,
        'duration_label' => '8 Days',
        'rating' => 4.9,
        'reviews_count' => 82,
        'summary' => 'Sail through sunlit azure calderas, explore ancient whitewashed cliffside towns, and relax in luxury suites.',
        'description' => "An extraordinary eight-day passage across the Aegean.\n\nExperience private catamaran sailing, guided historical explorations, and sunset dinners overlooking the caldera.",
        'highlights' => "Private caldera sunset catamaran sail\nGuided heritage walk of Akrotiri excavations\nBoutique cliffside suites in Oia",
        'is_active' => true,
    ]);

    $relatedTour = Tour::factory()->create([
        'destination_id' => $destination->id,
        'title' => 'Crete Coastal Discovery',
        'price' => 950.00,
        'is_active' => true,
    ]);

    // 1. Tour detail page renders all sections
    $response = $this->get(route('tours.show', $tour));

    $response->assertOk()
        ->assertSee($tour->title)
        ->assertSee($tour->summary)
        ->assertSee($tour->location)
        ->assertSee($category->name)
        ->assertSee($type->name)
        ->assertSee($tour->displayDuration())
        ->assertSee('Expedition Overview')
        ->assertSee('Key Tour Highlights')
        ->assertSee('What is Included &amp; Excluded', false)
        ->assertSee('Included in Your Package')
        ->assertSee('Excluded from Package')
        ->assertSee('Journey Itinerary Timeline')
        ->assertSee('Important Traveler Essentials')
        ->assertSee('Private caldera sunset catamaran sail')
        ->assertSee('Reserve Your Passage')
        ->assertSee('Confirm Booking Request')
        ->assertSee($relatedTour->title);

    // 2. Booking submission succeeds
    $bookingResponse = $this->post(route('tours.book', $tour), [
        'customer_name' => 'Elena Rostova',
        'customer_email' => 'elena@example.com',
        'customer_phone' => '+30 210 1234567',
        'travel_date' => now()->addDays(20)->toDateString(),
        'guests' => 2,
        'special_requests' => 'Sea view room preference.',
    ]);

    $bookingResponse->assertRedirect(route('tours.show', $tour))
        ->assertSessionHas('booking_success');

    $this->assertDatabaseHas('bookings', [
        'tour_id' => $tour->id,
        'customer_name' => 'Elena Rostova',
        'customer_email' => 'elena@example.com',
        'guests' => 2,
        'total_price' => 2500.00,
    ]);
});

test('terms and conditions page renders successfully with transparent booking standards', function () {
    $this->get(route('terms'))
        ->assertOk()
        ->assertSee('Terms &amp; Conditions', false)
        ->assertSee('Agreement to Terms')
        ->assertSee('Indian Rupees (₹)')
        ->assertSee('Cancellations, Amendments &amp; Refund Schedule', false)
        ->assertSee('Limitation of Liability');

    $this->get('/terms-and-conditions')
        ->assertRedirect(route('terms'));
});

test('privacy policy page renders successfully with data protection standards', function () {
    $this->get(route('privacy'))
        ->assertOk()
        ->assertSee('Privacy Policy')
        ->assertSee('Our Commitment to Traveler Privacy')
        ->assertSee('Categories of Information We Collect')
        ->assertSee('Data Security &amp; Storage Protocols', false)
        ->assertSee('Contacting Our Data Protection Officer');

    $this->get('/privacy-policy')
        ->assertRedirect(route('privacy'));
});

test('footer includes working links to terms and privacy policy', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('terms'))
        ->assertSee(route('privacy'));
});
