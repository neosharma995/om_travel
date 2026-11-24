<?php

use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Inquiry;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\TrashItem;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->actingAs($this->admin);
});

test('admin can view trash bin index with counts and filter by type', function () {
    $booking = Booking::factory()->create();
    $inquiry = Inquiry::factory()->create();

    TrashItem::trash($booking);
    TrashItem::trash($inquiry);

    $response = $this->get(route('admin.trash.index'));
    $response->assertOk();
    $response->assertSee('Trash Bin &amp; Recovery', false);
    $response->assertSee('All Items (2)');

    // Filter by type
    $bookingFilterResponse = $this->get(route('admin.trash.index', ['type' => 'booking']));
    $bookingFilterResponse->assertOk();
    $bookingFilterResponse->assertSee($booking->customer_name);
    $bookingFilterResponse->assertDontSee($inquiry->name);
});

test('admin can restore an item from trash bin', function () {
    $booking = Booking::factory()->create([
        'customer_name' => 'John Doe Traveller',
        'status' => 'pending',
    ]);

    $trash = TrashItem::trash($booking);
    $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    $this->assertDatabaseHas('trash_items', ['id' => $trash->id]);

    $response = $this->post(route('admin.trash.restore', $trash));
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('trash_items', ['id' => $trash->id]);
    $this->assertDatabaseHas('bookings', [
        'customer_name' => 'John Doe Traveller',
        'status' => 'pending',
    ]);
});

test('admin can permanently purge an item from trash bin', function () {
    $inquiry = Inquiry::factory()->create(['name' => 'Purge Test Customer']);
    $trash = TrashItem::trash($inquiry);

    $response = $this->delete(route('admin.trash.destroy', $trash));
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('trash_items', ['id' => $trash->id]);
    $this->assertDatabaseMissing('inquiries', ['id' => $inquiry->id]);
});

test('admin can empty entire trash bin', function () {
    $booking1 = Booking::factory()->create();
    $booking2 = Booking::factory()->create();
    TrashItem::trash($booking1);
    TrashItem::trash($booking2);

    expect(TrashItem::count())->toBe(2);

    $response = $this->post(route('admin.trash.empty'));
    $response->assertRedirect(route('admin.trash.index'));
    $response->assertSessionHas('success');

    expect(TrashItem::count())->toBe(0);
});

test('admin can perform bulk restore and bulk purge from trash bin', function () {
    $b1 = Booking::factory()->create(['customer_name' => 'Bulk Restored One']);
    $b2 = Booking::factory()->create(['customer_name' => 'Bulk Restored Two']);
    $t1 = TrashItem::trash($b1);
    $t2 = TrashItem::trash($b2);

    // Bulk restore
    $this->post(route('admin.trash.bulk'), [
        'action' => 'restore',
        'ids' => [$t1->id, $t2->id],
    ])->assertRedirect()->assertSessionHas('success');

    $this->assertDatabaseHas('bookings', ['customer_name' => 'Bulk Restored One']);
    $this->assertDatabaseHas('bookings', ['customer_name' => 'Bulk Restored Two']);
    expect(TrashItem::count())->toBe(0);

    // Now re-trash and bulk purge
    $restoredB1 = Booking::where('customer_name', 'Bulk Restored One')->first();
    $newTrash = TrashItem::trash($restoredB1);

    $this->post(route('admin.trash.bulk'), [
        'action' => 'delete',
        'ids' => [$newTrash->id],
    ])->assertRedirect()->assertSessionHas('success');

    expect(TrashItem::count())->toBe(0);
    $this->assertDatabaseMissing('bookings', ['customer_name' => 'Bulk Restored One']);
});

test('admin can perform bulk status updates and move to trash on bookings', function () {
    $b1 = Booking::factory()->create(['status' => 'pending']);
    $b2 = Booking::factory()->create(['status' => 'pending']);

    // Bulk confirm
    $this->post(route('admin.bookings.bulk'), [
        'action' => 'confirmed',
        'ids' => [$b1->id, $b2->id],
    ])->assertRedirect()->assertSessionHas('success');

    expect($b1->fresh()->status)->toBe('confirmed');
    expect($b2->fresh()->status)->toBe('confirmed');

    // Bulk move to trash
    $this->post(route('admin.bookings.bulk'), [
        'action' => 'trash',
        'ids' => [$b1->id, $b2->id],
    ])->assertRedirect()->assertSessionHas('success');

    $this->assertDatabaseMissing('bookings', ['id' => $b1->id]);
    $this->assertDatabaseMissing('bookings', ['id' => $b2->id]);
    expect(TrashItem::query()->where('item_type', 'booking')->count())->toBe(2);
});

test('admin can perform bulk status updates, trash, and delete on inquiries', function () {
    $i1 = Inquiry::factory()->create(['status' => 'new']);
    $i2 = Inquiry::factory()->create(['status' => 'new']);

    // Bulk replied
    $this->post(route('admin.inquiries.bulk'), [
        'action' => 'replied',
        'ids' => [$i1->id, $i2->id],
    ])->assertRedirect()->assertSessionHas('success');

    expect($i1->fresh()->status)->toBe('replied');
    expect($i2->fresh()->status)->toBe('replied');

    // Bulk trash
    $this->post(route('admin.inquiries.bulk'), [
        'action' => 'trash',
        'ids' => [$i1->id, $i2->id],
    ])->assertRedirect()->assertSessionHas('success');

    $this->assertDatabaseMissing('inquiries', ['id' => $i1->id]);
    $this->assertDatabaseMissing('inquiries', ['id' => $i2->id]);
    expect(TrashItem::query()->where('item_type', 'inquiry')->count())->toBe(2);
});

test('admin can perform bulk activate, deactivate, feature, and trash on tours', function () {
    $destination = Destination::factory()->create();
    $category = TourCategory::factory()->create();

    $t1 = Tour::factory()->create([
        'destination_id' => $destination->id,
        'tour_category_id' => $category->id,
        'is_active' => false,
        'is_featured' => false,
    ]);
    $t2 = Tour::factory()->create([
        'destination_id' => $destination->id,
        'tour_category_id' => $category->id,
        'is_active' => false,
        'is_featured' => false,
    ]);

    // Bulk activate
    $this->post(route('admin.tours.bulk'), [
        'action' => 'activate',
        'ids' => [$t1->id, $t2->id],
    ])->assertRedirect()->assertSessionHas('success');

    expect($t1->fresh()->is_active)->toBeTrue();
    expect($t2->fresh()->is_active)->toBeTrue();

    // Bulk feature
    $this->post(route('admin.tours.bulk'), [
        'action' => 'feature',
        'ids' => [$t1->id, $t2->id],
    ])->assertRedirect()->assertSessionHas('success');

    expect($t1->fresh()->is_featured)->toBeTrue();
    expect($t2->fresh()->is_featured)->toBeTrue();

    // Bulk trash
    $this->post(route('admin.tours.bulk'), [
        'action' => 'trash',
        'ids' => [$t1->id, $t2->id],
    ])->assertRedirect()->assertSessionHas('success');

    $this->assertDatabaseMissing('tours', ['id' => $t1->id]);
    $this->assertDatabaseMissing('tours', ['id' => $t2->id]);
    expect(TrashItem::query()->where('item_type', 'tour')->count())->toBe(2);
});

test('dashboard displays trash stats, paginated lists, and supports filtering', function () {
    Booking::factory()->count(10)->create();
    Inquiry::factory()->count(10)->create();

    $trashedBooking = Booking::factory()->create(['customer_name' => 'Trashed Dashboard Traveller']);
    TrashItem::trash($trashedBooking);

    $response = $this->get(route('admin.dashboard'));
    $response->assertOk();
    $response->assertSee('Trash Bin');
    $response->assertSee('Manage Trash &rarr;', false);
    $response->assertSee('Recent Bookings');
    $response->assertSee('Recent Inquiries');
    $response->assertSee('Trashed Dashboard Traveller');

    // Test booking status filter on dashboard
    $filterResponse = $this->get(route('admin.dashboard', ['booking_status' => 'pending']));
    $filterResponse->assertOk();
});

test('admin can perform bulk actions on categories, destinations, and blog posts', function () {
    // Categories
    $c1 = TourCategory::factory()->create(['is_active' => false]);
    $c2 = TourCategory::factory()->create(['is_active' => false]);

    $this->post(route('admin.categories.bulk'), [
        'action' => 'activate',
        'ids' => [$c1->id, $c2->id],
    ])->assertRedirect()->assertSessionHas('success');

    expect($c1->fresh()->is_active)->toBeTrue();

    $this->post(route('admin.categories.bulk'), [
        'action' => 'trash',
        'ids' => [$c1->id],
    ])->assertRedirect()->assertSessionHas('success');

    $this->assertDatabaseMissing('tour_categories', ['id' => $c1->id]);
    expect(TrashItem::query()->where('item_type', 'category')->count())->toBe(1);

    // Destinations
    $d1 = Destination::factory()->create(['is_popular' => false]);
    $this->post(route('admin.destinations.bulk'), [
        'action' => 'popular',
        'ids' => [$d1->id],
    ])->assertRedirect()->assertSessionHas('success');

    expect($d1->fresh()->is_popular)->toBeTrue();

    $this->post(route('admin.destinations.bulk'), [
        'action' => 'trash',
        'ids' => [$d1->id],
    ])->assertRedirect()->assertSessionHas('success');

    $this->assertDatabaseMissing('destinations', ['id' => $d1->id]);
    expect(TrashItem::query()->where('item_type', 'destination')->count())->toBe(1);

    // Blog Posts
    $p1 = BlogPost::factory()->create(['is_published' => false]);
    $this->post(route('admin.blog-posts.bulk'), [
        'action' => 'publish',
        'ids' => [$p1->id],
    ])->assertRedirect()->assertSessionHas('success');

    expect($p1->fresh()->is_published)->toBeTrue();

    $this->post(route('admin.blog-posts.bulk'), [
        'action' => 'trash',
        'ids' => [$p1->id],
    ])->assertRedirect()->assertSessionHas('success');

    $this->assertDatabaseMissing('blog_posts', ['id' => $p1->id]);
    expect(TrashItem::query()->where('item_type', 'blog_post')->count())->toBe(1);
});

test('all admin resource index pages render properly with bulk toolbar and pagination', function () {
    $routes = [
        'admin.categories.index',
        'admin.tour-types.index',
        'admin.activities.index',
        'admin.destinations.index',
        'admin.tours.index',
        'admin.gallery.index',
        'admin.guides.index',
        'admin.testimonials.index',
        'admin.blog-posts.index',
        'admin.bookings.index',
        'admin.inquiries.index',
        'admin.trash.index',
    ];

    foreach ($routes as $routeName) {
        $response = $this->get(route($routeName));
        $response->assertOk();
    }
});

test('visiting bulk-action url via GET redirects gracefully without 404', function () {
    $bulkRoutes = [
        ['url' => route('admin.categories.bulk'), 'expected' => route('admin.categories.index')],
        ['url' => route('admin.tours.bulk'), 'expected' => route('admin.tours.index')],
        ['url' => route('admin.bookings.bulk'), 'expected' => route('admin.bookings.index')],
        ['url' => route('admin.inquiries.bulk'), 'expected' => route('admin.inquiries.index')],
        ['url' => route('admin.destinations.bulk'), 'expected' => route('admin.destinations.index')],
        ['url' => route('admin.trash.bulk'), 'expected' => route('admin.trash.index')],
    ];

    foreach ($bulkRoutes as $route) {
        $this->get($route['url'])
            ->assertRedirect($route['expected']);
    }
});
