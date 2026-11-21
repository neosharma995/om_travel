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
