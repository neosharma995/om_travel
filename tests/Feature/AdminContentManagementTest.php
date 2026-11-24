<?php

use App\Models\Activity;
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
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->actingAs($this->admin);
});

test('admin can manage tour categories', function () {
    $createResponse = $this->post(route('admin.categories.store'), [
        'name' => 'Safari Tours',
        'sort_order' => 2,
        'is_active' => '1',
    ]);

    $createResponse->assertRedirect(route('admin.categories.index'));
    $category = TourCategory::query()->where('name', 'Safari Tours')->firstOrFail();

    $this->put(route('admin.categories.update', $category), [
        'name' => 'Luxury Safaris',
        'sort_order' => 4,
    ])->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseHas('tour_categories', [
        'id' => $category->id,
        'name' => 'Luxury Safaris',
        'is_active' => false,
    ]);

    $this->delete(route('admin.categories.destroy', $category))
        ->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseMissing('tour_categories', ['id' => $category->id]);
});

test('admin can manage destinations', function () {
    $this->post(route('admin.destinations.store'), [
        'name' => 'Japan',
        'country' => 'Japan',
        'listings_label' => '14 Listing',
        'is_popular' => '1',
        'is_active' => '1',
    ])->assertRedirect(route('admin.destinations.index'));

    $destination = Destination::query()->where('name', 'Japan')->firstOrFail();

    $this->put(route('admin.destinations.update', $destination), [
        'name' => 'Kyoto & Tokyo',
        'country' => 'Japan',
        'listings_label' => '16 Listing',
        'is_popular' => '1',
        'is_active' => '1',
    ])->assertRedirect(route('admin.destinations.index'));

    $this->assertDatabaseHas('destinations', [
        'id' => $destination->id,
        'name' => 'Kyoto & Tokyo',
    ]);

    $this->delete(route('admin.destinations.destroy', $destination))
        ->assertRedirect(route('admin.destinations.index'));

    $this->assertDatabaseMissing('destinations', ['id' => $destination->id]);
});

test('admin can create, upload image, update, and delete tours', function () {
    Storage::fake('public');
    $destination = Destination::factory()->create();
    $category = TourCategory::factory()->create();
    $photo = UploadedFile::fake()->image('santorini.jpg', 800, 600);

    $this->post(route('admin.tours.store'), [
        'destination_id' => $destination->id,
        'tour_category_id' => $category->id,
        'title' => 'Aegean Island Odyssey',
        'location' => 'Santorini, Greece',
        'price' => 1120.00,
        'duration_days' => 6,
        'duration_label' => '6 Days',
        'rating' => 4.9,
        'reviews_count' => 30,
        'summary' => 'Sunlit sailing tour.',
        'image_file' => $photo,
        'is_featured' => '1',
        'is_active' => '1',
    ])->assertRedirect(route('admin.tours.index'));

    $tour = Tour::query()->where('title', 'Aegean Island Odyssey')->firstOrFail();
    Storage::disk('public')->assertExists($tour->image);

    $this->put(route('admin.tours.update', $tour), [
        'destination_id' => $destination->id,
        'tour_category_id' => $category->id,
        'title' => 'Aegean Deluxe Sail',
        'price' => 1290.00,
        'duration_days' => 7,
        'rating' => 5.0,
        'image' => $tour->image,
        'is_active' => '1',
    ])->assertRedirect(route('admin.tours.index'));

    $this->assertDatabaseHas('tours', [
        'id' => $tour->id,
        'title' => 'Aegean Deluxe Sail',
        'price' => 1290.00,
    ]);

    $oldImage = $tour->image;
    $this->delete(route('admin.tours.destroy', $tour))
        ->assertRedirect(route('admin.tours.index'));

    $this->assertDatabaseMissing('tours', ['id' => $tour->id]);
    Storage::disk('public')->assertMissing($oldImage);
});

test('admin tour validation prevents storing invalid tour data', function () {
    $response = $this->from(route('admin.tours.create'))->post(route('admin.tours.store'), [
        'title' => '',
        'price' => -50,
        'duration_days' => 0,
        'rating' => 9,
    ]);

    $response->assertRedirect(route('admin.tours.create'))
        ->assertSessionHasErrors(['title', 'price', 'duration_days', 'rating']);

    expect(Tour::query()->count())->toBe(0);
});

test('admin can manage gallery items, tour guides, testimonials, and blog posts', function () {
    Storage::fake('public');

    $this->post(route('admin.gallery.store'), [
        'title' => 'Alpine Morning',
        'location' => 'Zermatt',
        'image_file' => UploadedFile::fake()->image('alpine.jpg'),
        'is_active' => '1',
    ])->assertRedirect(route('admin.gallery.index'));
    $gallery = GalleryItem::query()->firstOrFail();
    $this->put(route('admin.gallery.update', $gallery), [
        'title' => 'Alpine Noon',
        'image' => $gallery->image,
        'is_active' => '1',
    ])->assertRedirect(route('admin.gallery.index'));
    $this->delete(route('admin.gallery.destroy', $gallery))->assertRedirect(route('admin.gallery.index'));

    $this->post(route('admin.guides.store'), [
        'name' => 'Liam Brooks',
        'role' => 'Ocean Guide',
        'is_active' => '1',
    ])->assertRedirect(route('admin.guides.index'));
    $guide = TourGuide::query()->firstOrFail();
    $this->put(route('admin.guides.update', $guide), [
        'name' => 'Liam K. Brooks',
        'role' => 'Lead Ocean Guide',
        'is_active' => '1',
    ])->assertRedirect(route('admin.guides.index'));
    $this->delete(route('admin.guides.destroy', $guide))->assertRedirect(route('admin.guides.index'));

    $this->post(route('admin.testimonials.store'), [
        'client_name' => 'Noah Reed',
        'client_role' => 'Traveller',
        'rating' => 5,
        'quote' => 'Incredible service throughout.',
        'is_active' => '1',
    ])->assertRedirect(route('admin.testimonials.index'));
    $testimonial = Testimonial::query()->firstOrFail();
    $this->put(route('admin.testimonials.update', $testimonial), [
        'client_name' => 'Noah Reed',
        'client_role' => 'Explorer',
        'rating' => 5,
        'quote' => 'Updated review quote.',
        'is_active' => '1',
    ])->assertRedirect(route('admin.testimonials.index'));
    $this->delete(route('admin.testimonials.destroy', $testimonial))->assertRedirect(route('admin.testimonials.index'));

    $this->post(route('admin.blog-posts.store'), [
        'title' => 'Packing Light for Island Hopping',
        'category' => 'Tips',
        'read_time' => '4 Min Read',
        'content' => 'Full travel packing guide.',
        'is_published' => '1',
    ])->assertRedirect(route('admin.blog-posts.index'));
    $post = BlogPost::query()->firstOrFail();
    $this->put(route('admin.blog-posts.update', $post), [
        'title' => 'Packing Smart for Islands',
        'category' => 'Tips',
        'read_time' => '5 Min Read',
        'content' => 'Updated guide text.',
        'is_published' => '1',
    ])->assertRedirect(route('admin.blog-posts.index'));
    $this->delete(route('admin.blog-posts.destroy', $post))->assertRedirect(route('admin.blog-posts.index'));

    expect(GalleryItem::query()->count())->toBe(0)
        ->and(TourGuide::query()->count())->toBe(0)
        ->and(Testimonial::query()->count())->toBe(0)
        ->and(BlogPost::query()->count())->toBe(0);
});

test('admin can manage bookings and inquiries', function () {
    $booking = Booking::factory()->create(['status' => 'pending']);
    $inquiry = Inquiry::factory()->create(['status' => 'new']);

    $this->get(route('admin.bookings.show', $booking))->assertOk();
    $this->put(route('admin.bookings.update', $booking), [
        'status' => 'confirmed',
        'admin_notes' => 'Confirmed with hotel.',
    ])->assertRedirect(route('admin.bookings.show', $booking));

    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'confirmed',
        'admin_notes' => 'Confirmed with hotel.',
    ]);

    $this->delete(route('admin.bookings.destroy', $booking))->assertRedirect(route('admin.bookings.index'));
    $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);

    $this->get(route('admin.inquiries.show', $inquiry))->assertOk();
    $this->put(route('admin.inquiries.update', $inquiry), [
        'status' => 'replied',
    ])->assertRedirect(route('admin.inquiries.show', $inquiry));

    $this->assertDatabaseHas('inquiries', [
        'id' => $inquiry->id,
        'status' => 'replied',
    ]);

    $this->delete(route('admin.inquiries.destroy', $inquiry))->assertRedirect(route('admin.inquiries.index'));
    $this->assertDatabaseMissing('inquiries', ['id' => $inquiry->id]);
});

test('admin can update site settings and hero image and see changes on homepage', function () {
    Storage::fake('public');
    $heroUpload = UploadedFile::fake()->image('new-hero.jpg', 1440, 720);

    $payload = array_merge(SiteSetting::defaults(), [
        'hero_title' => 'Explore Untouched Horizons',
        'stat_1_value' => '15',
        'hero_image_file' => $heroUpload,
    ]);

    $this->put(route('admin.settings.update'), $payload)
        ->assertRedirect(route('admin.settings.edit'));

    $storedHero = SiteSetting::query()->where('key', 'hero_image')->value('value');
    Storage::disk('public')->assertExists($storedHero);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Explore Untouched Horizons')
        ->assertSee('15');
});

test('admin can manage tour types and assign them to tour packages', function () {
    $this->post(route('admin.tour-types.store'), [
        'name' => 'Luxury Cruises',
        'description' => 'Private yachts and ocean liners.',
        'sort_order' => 1,
        'is_active' => '1',
    ])->assertRedirect(route('admin.tour-types.index'));

    $tourType = TourType::query()->where('name', 'Luxury Cruises')->firstOrFail();

    $this->get(route('admin.tour-types.index'))
        ->assertOk()
        ->assertSee('Luxury Cruises');

    $destination = Destination::factory()->create();
    $category = TourCategory::factory()->create();

    $this->post(route('admin.tours.store'), [
        'destination_id' => $destination->id,
        'tour_category_id' => $category->id,
        'tour_type_id' => $tourType->id,
        'title' => 'Mediterranean Yacht Journey',
        'price' => 1450.00,
        'duration_days' => 5,
        'rating' => 4.9,
        'is_active' => '1',
    ])->assertRedirect(route('admin.tours.index'));

    $this->assertDatabaseHas('tours', [
        'title' => 'Mediterranean Yacht Journey',
        'tour_type_id' => $tourType->id,
    ]);

    $this->put(route('admin.tour-types.update', $tourType), [
        'name' => 'Signature Voyages',
        'description' => 'Curated ocean cruises.',
        'sort_order' => 3,
    ])->assertRedirect(route('admin.tour-types.index'));

    $this->assertDatabaseHas('tour_types', [
        'id' => $tourType->id,
        'name' => 'Signature Voyages',
        'is_active' => false,
    ]);

    $this->delete(route('admin.tour-types.destroy', $tourType))
        ->assertRedirect(route('admin.tour-types.index'));

    $this->assertDatabaseMissing('tour_types', ['id' => $tourType->id]);
});

test('admin can manage activities with image upload and assign them to tour packages', function () {
    Storage::fake('public');
    $photo = UploadedFile::fake()->image('kayaking.jpg', 800, 600);

    $this->post(route('admin.activities.store'), [
        'name' => 'Lagoon Kayaking',
        'description' => 'Guided sea kayaking in emerald coves.',
        'image_file' => $photo,
        'sort_order' => 1,
        'is_active' => '1',
    ])->assertRedirect(route('admin.activities.index'));

    $activity = Activity::query()->where('name', 'Lagoon Kayaking')->firstOrFail();
    Storage::disk('public')->assertExists($activity->image);

    $this->get(route('admin.activities.index'))
        ->assertOk()
        ->assertSee('Lagoon Kayaking');

    $destination = Destination::factory()->create();
    $category = TourCategory::factory()->create();

    $this->post(route('admin.tours.store'), [
        'destination_id' => $destination->id,
        'tour_category_id' => $category->id,
        'activity_id' => $activity->id,
        'title' => 'Krabi Kayak Expedition',
        'price' => 720.00,
        'duration_days' => 4,
        'rating' => 4.8,
        'is_active' => '1',
    ])->assertRedirect(route('admin.tours.index'));

    $this->assertDatabaseHas('tours', [
        'title' => 'Krabi Kayak Expedition',
        'activity_id' => $activity->id,
    ]);

    $this->put(route('admin.activities.update', $activity), [
        'name' => 'Open Sea Kayaking',
        'description' => 'Coastal paddling routes.',
        'image' => $activity->image,
        'sort_order' => 2,
    ])->assertRedirect(route('admin.activities.index'));

    $this->assertDatabaseHas('activities', [
        'id' => $activity->id,
        'name' => 'Open Sea Kayaking',
        'is_active' => false,
    ]);

    $this->delete(route('admin.activities.destroy', $activity))
        ->assertRedirect(route('admin.activities.index'));

    $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
});

test('admin tables paginate at 10 items per page', function () {
    $destination = Destination::factory()->create();
    $category = TourCategory::factory()->create(['sort_order' => 999]);
    $tourType = TourType::factory()->create();
    $activity = Activity::factory()->create();

    foreach (range(1, 12) as $index) {
        Tour::factory()->create([
            'destination_id' => $destination->id,
            'tour_category_id' => $category->id,
            'tour_type_id' => $tourType->id,
            'activity_id' => $activity->id,
            'title' => sprintf('Paginated Tour %02d', $index),
            'sort_order' => $index,
        ]);
    }

    foreach (range(1, 11) as $index) {
        TourCategory::factory()->create([
            'name' => sprintf('Paginated Category %02d', $index),
            'sort_order' => $index,
        ]);
    }

    $this->get(route('admin.tours.index'))
        ->assertOk()
        ->assertSee('Paginated Tour 01')
        ->assertSee('Paginated Tour 10')
        ->assertDontSee('Paginated Tour 11')
        ->assertSee('?page=2', false);

    $this->get(route('admin.tours.index', ['page' => 2]))
        ->assertOk()
        ->assertSee('Paginated Tour 11')
        ->assertSee('Paginated Tour 12')
        ->assertDontSee('Paginated Tour 01');

    $this->get(route('admin.categories.index'))
        ->assertOk()
        ->assertSee('Paginated Category 10')
        ->assertDontSee('Paginated Category 11')
        ->assertSee('?page=2', false);
});

test('admin can assign parent category, prevents circular parenting, and nullifies child parent on delete', function () {
    $parent = TourCategory::factory()->create([
        'name' => 'Water Sports',
        'slug' => 'water-sports',
    ]);

    $this->post(route('admin.categories.store'), [
        'name' => 'Scuba Diving',
        'parent_id' => $parent->id,
        'sort_order' => 1,
        'is_active' => '1',
    ])->assertRedirect(route('admin.categories.index'));

    $child = TourCategory::query()->where('name', 'Scuba Diving')->firstOrFail();
    expect($child->parent_id)->toBe($parent->id);

    $this->get(route('admin.categories.index'))
        ->assertOk()
        ->assertSee('Water Sports');

    $this->get(route('admin.categories.edit', $parent))
        ->assertOk()
        ->assertDontSee('<option value="'.$parent->id.'"', false)
        ->assertDontSee('<option value="'.$child->id.'"', false);

    $this->from(route('admin.categories.edit', $parent))
        ->put(route('admin.categories.update', $parent), [
            'name' => $parent->name,
            'parent_id' => $parent->id,
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.categories.edit', $parent))
        ->assertSessionHasErrors('parent_id');

    $this->from(route('admin.categories.edit', $parent))
        ->put(route('admin.categories.update', $parent), [
            'name' => $parent->name,
            'parent_id' => $child->id,
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.categories.edit', $parent))
        ->assertSessionHasErrors('parent_id');

    $this->delete(route('admin.categories.destroy', $parent))
        ->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseHas('tour_categories', [
        'id' => $child->id,
        'parent_id' => null,
    ]);
});

test('admin index tables link to internal admin show pages and render full details', function () {
    $parentCategory = TourCategory::factory()->create(['name' => 'Marine Tours', 'slug' => 'marine-tours']);
    $category = TourCategory::factory()->create(['name' => 'Coastal Sailing', 'slug' => 'coastal-sailing', 'parent_id' => $parentCategory->id]);
    $tourType = TourType::factory()->create(['name' => 'Luxury Escape', 'slug' => 'luxury-escape']);
    $activity = Activity::factory()->create(['name' => 'Reef Diving', 'slug' => 'reef-diving']);
    $destination = Destination::factory()->create(['name' => 'Inactive Island', 'slug' => 'inactive-island', 'is_active' => false]);
    $tour = Tour::factory()->create([
        'destination_id' => $destination->id,
        'tour_category_id' => $category->id,
        'tour_type_id' => $tourType->id,
        'activity_id' => $activity->id,
        'title' => 'Hidden Expedition',
        'slug' => 'hidden-expedition',
        'is_active' => false,
    ]);
    $galleryItem = GalleryItem::factory()->create(['title' => 'Coral Lagoon Shot']);
    $guide = TourGuide::factory()->create(['name' => 'Marcus Reed']);
    $testimonial = Testimonial::factory()->create(['client_name' => 'Sophia Hayes', 'quote' => 'Incredible service throughout!']);
    $post = BlogPost::factory()->create(['title' => 'Draft Travel Story', 'slug' => 'draft-story', 'is_published' => false]);

    $this->get(route('admin.tours.index'))->assertOk()->assertSee(route('admin.tours.show', $tour));
    $this->get(route('admin.destinations.index'))->assertOk()->assertSee(route('admin.destinations.show', $destination));
    $this->get(route('admin.categories.index'))->assertOk()->assertSee(route('admin.categories.show', $category));
    $this->get(route('admin.tour-types.index'))->assertOk()->assertSee(route('admin.tour-types.show', $tourType));
    $this->get(route('admin.activities.index'))->assertOk()->assertSee(route('admin.activities.show', $activity));
    $this->get(route('admin.gallery.index'))->assertOk()->assertSee(route('admin.gallery.show', $galleryItem));
    $this->get(route('admin.guides.index'))->assertOk()->assertSee(route('admin.guides.show', $guide));
    $this->get(route('admin.testimonials.index'))->assertOk()->assertSee(route('admin.testimonials.show', $testimonial));
    $this->get(route('admin.blog-posts.index'))->assertOk()->assertSee(route('admin.blog-posts.show', $post));

    $this->get(route('admin.tours.show', $tour))->assertOk()->assertSee('Hidden Expedition')->assertSee('Marine Tours');
    $this->get(route('admin.destinations.show', $destination))->assertOk()->assertSee('Inactive Island');
    $this->get(route('admin.categories.show', $category))->assertOk()->assertSee('Coastal Sailing')->assertSee('Marine Tours');
    $this->get(route('admin.tour-types.show', $tourType))->assertOk()->assertSee('Luxury Escape');
    $this->get(route('admin.activities.show', $activity))->assertOk()->assertSee('Reef Diving');
    $this->get(route('admin.gallery.show', $galleryItem))->assertOk()->assertSee('Coral Lagoon Shot');
    $this->get(route('admin.guides.show', $guide))->assertOk()->assertSee('Marcus Reed');
    $this->get(route('admin.testimonials.show', $testimonial))->assertOk()->assertSee('Sophia Hayes')->assertSee('Incredible service throughout!');
    $this->get(route('admin.blog-posts.show', $post))->assertOk()->assertSee('Draft Travel Story');

    $this->get(route('tours.show', $tour))->assertNotFound();
    $this->get(route('destinations.show', $destination))->assertNotFound();
    $this->get(route('blog.show', $post))->assertNotFound();
});
