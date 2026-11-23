<?php

namespace Database\Seeders;

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
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->firstOrNew(['email' => 'admin@omtravel.com']);
        $admin->name = 'OmTravel Admin';
        if (! $admin->exists) {
            $admin->password = Hash::make('password');
        }
        $admin->email_verified_at = $admin->email_verified_at ?? now();
        $admin->is_admin = true;
        $admin->save();

        $customer = User::query()->firstOrNew(['email' => 'customer@omtravel.com']);
        $customer->name = 'Elena Rostova';
        if (! $customer->exists) {
            $customer->password = Hash::make('password');
        }
        $customer->email_verified_at = $customer->email_verified_at ?? now();
        $customer->is_admin = false;
        $customer->save();

        SiteSetting::setMany(SiteSetting::defaults(), 'general');

        $categories = [
            ['name' => 'Cruises', 'slug' => 'cruises', 'image' => '/images/seed/cat-cruise.jpg', 'sort_order' => 1],
            ['name' => 'Hiking', 'slug' => 'hiking', 'image' => '/images/seed/cat-hiking.jpg', 'sort_order' => 2],
            ['name' => 'Snorkeling', 'slug' => 'snorkeling', 'image' => '/images/seed/cat-diving.jpg', 'sort_order' => 3],
            ['name' => 'Wildlife', 'slug' => 'wildlife', 'image' => '/images/seed/cat-wildlife.jpg', 'sort_order' => 4],
            ['name' => 'Beach', 'slug' => 'beach', 'image' => '/images/seed/cat-beach.jpg', 'sort_order' => 5],
            ['name' => 'Island Hopping', 'slug' => 'island-hopping', 'image' => '/images/seed/tour-tropical.jpg', 'sort_order' => 6],
        ];

        $categoryModels = [];
        foreach ($categories as $categoryData) {
            $categoryModels[$categoryData['slug']] = TourCategory::query()->updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'image' => $categoryData['image'],
                    'sort_order' => $categoryData['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $tourTypes = [
            ['name' => 'Luxury Escape', 'slug' => 'luxury-escape', 'description' => 'Handpicked five-star cliffside hotels, private transfers, and elevated coastal or island retreats.', 'sort_order' => 1],
            ['name' => 'Adventure & Trek', 'slug' => 'adventure-trek', 'description' => 'Active alpine trails, reef excursions, and small-group outdoor exploration led by certified guides.', 'sort_order' => 2],
            ['name' => 'Honeymoon & Romance', 'slug' => 'honeymoon-romance', 'description' => 'Intimate villas, sunset cruises, and secluded beachfront dining designed for couples.', 'sort_order' => 3],
            ['name' => 'Family Holiday', 'slug' => 'family-holiday', 'description' => 'Flexible multi-age itineraries with relaxed pacing and kid-friendly sightseeing.', 'sort_order' => 4],
            ['name' => 'Group Tour', 'slug' => 'group-tour', 'description' => 'Curated social journeys with shared excursions, boat charters, and lively local experiences.', 'sort_order' => 5],
            ['name' => 'Private Custom', 'slug' => 'private-custom', 'description' => 'Tailor-made routes with dedicated skippers, private drivers, and bespoke daily schedules.', 'sort_order' => 6],
        ];

        $tourTypeModels = [];
        foreach ($tourTypes as $tourTypeData) {
            $tourTypeModels[$tourTypeData['slug']] = TourType::query()->updateOrCreate(
                ['slug' => $tourTypeData['slug']],
                [
                    'name' => $tourTypeData['name'],
                    'description' => $tourTypeData['description'],
                    'sort_order' => $tourTypeData['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $destinations = [
            [
                'name' => 'Thailand',
                'slug' => 'thailand',
                'country' => 'Thailand',
                'description' => 'Discover emerald limestone lagoons, sunlit islands, ornate temples, and vibrant coastal cuisine across Thailand.',
                'image' => '/images/seed/tour-tropical.jpg',
                'listings_label' => '22 Listing',
                'sort_order' => 1,
            ],
            [
                'name' => 'Greece',
                'slug' => 'greece',
                'country' => 'Greece',
                'description' => 'Sail the Cyclades, stroll whitewashed Santorini cliffs at sunset, and experience Mediterranean heritage.',
                'image' => '/images/seed/tour-greece.jpg',
                'listings_label' => '18 Listing',
                'sort_order' => 2,
            ],
            [
                'name' => 'Maldives',
                'slug' => 'maldives',
                'country' => 'Maldives',
                'description' => 'Wake up in private overwater bungalows surrounded by crystal-clear coral reefs and tranquil turquoise lagoons.',
                'image' => '/images/seed/cat-beach.jpg',
                'listings_label' => '25 Listing',
                'sort_order' => 3,
            ],
            [
                'name' => 'Switzerland',
                'slug' => 'switzerland',
                'country' => 'Switzerland',
                'description' => 'Breathe crisp alpine air above snow-crowned peaks, glacial turquoise lakes, and scenic panoramic railways.',
                'image' => '/images/seed/tour-switzerland.jpg',
                'listings_label' => '15 Listing',
                'sort_order' => 4,
            ],
            [
                'name' => 'Italy',
                'slug' => 'italy',
                'country' => 'Italy',
                'description' => 'Cruise sunlit Amalfi cliffside coves, historic harbor towns, and timeless coastal Mediterranean landscapes.',
                'image' => '/images/seed/cat-cruise.jpg',
                'listings_label' => '19 Listing',
                'sort_order' => 5,
            ],
        ];

        $destinationModels = [];
        foreach ($destinations as $destinationData) {
            $destinationModels[$destinationData['slug']] = Destination::query()->updateOrCreate(
                ['slug' => $destinationData['slug']],
                [
                    'name' => $destinationData['name'],
                    'country' => $destinationData['country'],
                    'description' => $destinationData['description'],
                    'image' => $destinationData['image'],
                    'listings_label' => $destinationData['listings_label'],
                    'is_popular' => true,
                    'sort_order' => $destinationData['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $tours = [
            [
                'title' => 'Greece Tour Package',
                'slug' => 'greece-tour-package',
                'destination_slug' => 'greece',
                'category_slug' => 'beach',
                'tour_type_slug' => 'luxury-escape',
                'location' => 'Santorini & Mykonos, Greece',
                'price' => 980.00,
                'duration_days' => 7,
                'duration_label' => '7 Days',
                'rating' => 4.8,
                'reviews_count' => 64,
                'summary' => 'Explore iconic blue-domed cliffside villages, caldera sunsets, and crystal Aegean coves.',
                'description' => "Set out on an unforgettable seven-day Aegean odyssey across Santorini and Mykonos.\n\nEnjoy curated boutique cliffside accommodations, private catamaran caldera sailing with warm local cuisine, and guided heritage walks through timeless Cycladic villages.",
                'highlights' => "Boutique cliffside stays in Oia\nSunset catamaran caldera cruise\nGuided ancient Akrotiri excursion\nPrivate island transfers and daily breakfast",
                'image' => '/images/seed/tour-greece.jpg',
                'sort_order' => 1,
            ],
            [
                'title' => 'Italy Coast Sail Package',
                'slug' => 'italy-coast-sail-package',
                'destination_slug' => 'italy',
                'category_slug' => 'cruises',
                'tour_type_slug' => 'private-custom',
                'location' => 'Amalfi Coast & Capri, Italy',
                'price' => 890.00,
                'duration_days' => 5,
                'duration_label' => '5 Days',
                'rating' => 4.9,
                'reviews_count' => 51,
                'summary' => 'Glide along Positano, Capri, and Ravello aboard a classic sunlit Mediterranean yacht.',
                'description' => "Sail past dramatic Amalfi sea cliffs, lemon groves, and hidden cobalt grottos.\n\nEach day balances relaxing open-water sailing with leisurely shore walks, cliffside trattoria dining, and guided exploration of Positano and Capri.",
                'highlights' => "Private yacht sail along Amalfi & Capri\nBlue Grotto coastal boat excursion\nRavello panoramic garden visit\nHarbor transfers and coastal tastings",
                'image' => '/images/seed/cat-cruise.jpg',
                'sort_order' => 2,
            ],
            [
                'title' => 'Switzerland Alpine Escape',
                'slug' => 'switzerland-alpine-escape',
                'destination_slug' => 'switzerland',
                'category_slug' => 'hiking',
                'tour_type_slug' => 'adventure-trek',
                'location' => 'Zermatt & Interlaken, Switzerland',
                'price' => 1150.00,
                'duration_days' => 6,
                'duration_label' => '6 Days',
                'rating' => 4.8,
                'reviews_count' => 48,
                'summary' => 'Traverse crystal alpine lakes, scenic ridge trails, and panoramic Swiss glacier railways.',
                'description' => "Experience Switzerland's highest peaks and emerald alpine lakes with certified mountain leaders.\n\nRide iconic scenic trains between Interlaken and Zermatt, hike panoramic meadows overlooking turquoise waters, and unwind in cozy alpine chalets.",
                'highlights' => "Guided alpine ridge and lake hikes\nPanoramic Swiss mountain rail passes\nChalet stays with mountain views\nCable car ascent to glacier viewpoints",
                'image' => '/images/seed/tour-switzerland.jpg',
                'sort_order' => 3,
            ],
            [
                'title' => 'Maldives Lagoon Retreat',
                'slug' => 'maldives-lagoon-retreat',
                'destination_slug' => 'maldives',
                'category_slug' => 'beach',
                'tour_type_slug' => 'honeymoon-romance',
                'location' => 'Baa Atoll, Maldives',
                'price' => 1280.00,
                'duration_days' => 5,
                'duration_label' => '5 Days',
                'rating' => 4.9,
                'reviews_count' => 78,
                'summary' => 'Unwind in serene overwater villas surrounded by luminous turquoise lagoons and coral reefs.',
                'description' => "Step directly from your private deck into warm turquoise waters teeming with tropical marine life.\n\nIncludes speedboat island transfers, guided house-reef snorkeling, sunset dolphin cruises, and beachfront fine dining.",
                'highlights' => "Overwater lagoon villa accommodation\nGuided coral reef snorkeling session\nSunset traditional dhoni cruise\nSpeedboat transfers and full breakfast",
                'image' => '/images/seed/cat-beach.jpg',
                'sort_order' => 4,
            ],
            [
                'title' => 'Thailand Emerald Cove Tour',
                'slug' => 'thailand-emerald-cove-tour',
                'destination_slug' => 'thailand',
                'category_slug' => 'island-hopping',
                'tour_type_slug' => 'group-tour',
                'location' => 'Phang Nga & Krabi, Thailand',
                'price' => 680.00,
                'duration_days' => 4,
                'duration_label' => '4 Days',
                'rating' => 4.7,
                'reviews_count' => 59,
                'summary' => 'Voyage by traditional wooden longtail boat through towering karst lagoons and hidden beaches.',
                'description' => "Explore southern Thailand's dramatic limestone islets, emerald sea caves, and coral sandbars.\n\nPaddle sea kayaks into secluded lagoons, sample authentic coastal street fare, and relax in beachfront boutique villas.",
                'highlights' => "Private longtail boat island hopping\nEmerald lagoon kayaking excursion\nCoral cove snorkeling gear included\nBoutique beachfront resort stay",
                'image' => '/images/seed/tour-tropical.jpg',
                'sort_order' => 5,
            ],
            [
                'title' => 'Coral Reef Diving Adventure',
                'slug' => 'coral-reef-diving-adventure',
                'destination_slug' => 'maldives',
                'category_slug' => 'snorkeling',
                'tour_type_slug' => 'adventure-trek',
                'location' => 'Ari Atoll, Maldives',
                'price' => 540.00,
                'duration_days' => 3,
                'duration_label' => '3 Days',
                'rating' => 4.8,
                'reviews_count' => 42,
                'summary' => 'Dive crystal reefs alongside tropical fish, sea turtles, and sunlit underwater gardens.',
                'description' => "Tailored for ocean lovers seeking vibrant reef encounters without crowds.\n\nCertified marine guides lead daily boat excursions to calm coral gardens ideal for both snorkeling and introductory diving.",
                'highlights' => "Two daily guided reef boat trips\nMarine biologist briefing & gear\nSandbank picnic lunch on the ocean\nUnderwater photo keepsake package",
                'image' => '/images/seed/cat-diving.jpg',
                'sort_order' => 6,
            ],
        ];

        $tourModels = [];
        foreach ($tours as $tourData) {
            $tourModels[$tourData['slug']] = Tour::query()->updateOrCreate(
                ['slug' => $tourData['slug']],
                [
                    'destination_id' => $destinationModels[$tourData['destination_slug']]->id ?? null,
                    'tour_category_id' => $categoryModels[$tourData['category_slug']]->id ?? null,
                    'tour_type_id' => $tourTypeModels[$tourData['tour_type_slug']]->id ?? null,
                    'title' => $tourData['title'],
                    'location' => $tourData['location'],
                    'price' => $tourData['price'],
                    'duration_days' => $tourData['duration_days'],
                    'duration_label' => $tourData['duration_label'],
                    'rating' => $tourData['rating'],
                    'reviews_count' => $tourData['reviews_count'],
                    'summary' => $tourData['summary'],
                    'description' => $tourData['description'],
                    'highlights' => $tourData['highlights'],
                    'image' => $tourData['image'],
                    'is_featured' => true,
                    'sort_order' => $tourData['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $galleryItems = [
            ['title' => 'Overwater Lagoon Villas', 'location' => 'Baa Atoll, Maldives', 'image' => '/images/seed/cat-beach.jpg', 'sort_order' => 1],
            ['title' => 'Phang Nga Longtail Cruise', 'location' => 'Krabi, Thailand', 'image' => '/images/seed/tour-tropical.jpg', 'sort_order' => 2],
            ['title' => 'Snowy Alpine Ridge Trail', 'location' => 'Zermatt, Switzerland', 'image' => '/images/seed/cat-hiking.jpg', 'sort_order' => 3],
            ['title' => 'Santorini Sunset Cliffs', 'location' => 'Oia, Greece', 'image' => '/images/seed/tour-greece.jpg', 'sort_order' => 4],
            ['title' => 'Mediterranean Yacht Wake', 'location' => 'Amalfi Coast, Italy', 'image' => '/images/seed/cat-cruise.jpg', 'sort_order' => 5],
            ['title' => 'Turquoise Mountain Lake', 'location' => 'Interlaken, Switzerland', 'image' => '/images/seed/tour-switzerland.jpg', 'sort_order' => 6],
            ['title' => 'Sunlit Coral Reef Dive', 'location' => 'Ari Atoll, Maldives', 'image' => '/images/seed/cat-diving.jpg', 'sort_order' => 7],
        ];

        foreach ($galleryItems as $itemData) {
            GalleryItem::query()->updateOrCreate(
                ['title' => $itemData['title']],
                [
                    'location' => $itemData['location'],
                    'image' => $itemData['image'],
                    'sort_order' => $itemData['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $guides = [
            ['name' => 'Jane Cooper', 'role' => 'Tourist Guide', 'photo' => '/images/seed/guide-1.jpg', 'sort_order' => 1],
            ['name' => 'Jacob Jones', 'role' => 'Senior Guide', 'photo' => '/images/seed/guide-2.jpg', 'sort_order' => 2],
            ['name' => 'Jenny Wilson', 'role' => 'Adventure Guide', 'photo' => '/images/seed/guide-3.jpg', 'sort_order' => 3],
            ['name' => 'Guy Hawkins', 'role' => 'Mountain Guide', 'photo' => '/images/seed/guide-4.jpg', 'sort_order' => 4],
        ];

        foreach ($guides as $guideData) {
            TourGuide::query()->updateOrCreate(
                ['name' => $guideData['name']],
                [
                    'role' => $guideData['role'],
                    'bio' => 'Dedicated local specialist crafting seamless cultural and scenic journeys for small travel groups.',
                    'photo' => $guideData['photo'],
                    'facebook_url' => 'https://facebook.com',
                    'twitter_url' => 'https://x.com',
                    'instagram_url' => 'https://instagram.com',
                    'linkedin_url' => 'https://linkedin.com',
                    'sort_order' => $guideData['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $testimonials = [
            [
                'client_name' => 'Maria Doe',
                'client_role' => 'Traveller',
                'avatar' => '/images/seed/client-1.jpg',
                'rating' => 5,
                'quote' => 'A home that perfectly blends sustainability with luxury until I discovered Ecoland Residence. From the sunrise boat trips to every boutique stay, OmTravel made every day effortless.',
                'sort_order' => 1,
            ],
            [
                'client_name' => 'Angelina Rose',
                'client_role' => 'Traveller',
                'avatar' => '/images/seed/guide-1.jpg',
                'rating' => 5,
                'quote' => 'The Santorini and Amalfi itinerary exceeded every expectation. Our guide knew every quiet lookout and authentic cliffside cafe along the coastline.',
                'sort_order' => 2,
            ],
            [
                'client_name' => 'Andrew Simon',
                'client_role' => 'Traveller',
                'avatar' => '/images/seed/guide-4.jpg',
                'rating' => 5,
                'quote' => 'Seamless transfers, warm local hospitality, and breathtaking alpine lake trails in Switzerland. Easily the best organized holiday our family has taken.',
                'sort_order' => 3,
            ],
            [
                'client_name' => 'Sophia Carter',
                'client_role' => 'Traveller',
                'avatar' => '/images/seed/guide-3.jpg',
                'rating' => 5,
                'quote' => 'Snorkeling the Maldives house reefs at sunrise and relaxing in our overwater villa was magical. Every detail was handled with genuine care.',
                'sort_order' => 4,
            ],
        ];

        foreach ($testimonials as $testimonialData) {
            Testimonial::query()->updateOrCreate(
                ['client_name' => $testimonialData['client_name']],
                [
                    'client_role' => $testimonialData['client_role'],
                    'avatar' => $testimonialData['avatar'],
                    'rating' => $testimonialData['rating'],
                    'quote' => $testimonialData['quote'],
                    'is_featured' => true,
                    'sort_order' => $testimonialData['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $blogPosts = [
            [
                'title' => '10 Reason Why You Should Visit New Maldives',
                'slug' => '10-reason-why-you-should-visit-new-maldives',
                'category' => 'Island Guide',
                'read_time' => '6 Min Read',
                'excerpt' => 'Discover crystalline turquoise lagoons, vibrant marine sanctuaries, and peaceful overwater retreats across the Maldives atolls.',
                'content' => "The Maldives continues to redefine serene coastal travel with eco-conscious resorts, thriving coral restoration projects, and effortless island hopping.\n\nWhether you dream of sunrise swims above living reefs, private sandbank picnics, or quiet stargazing over calm waters, choosing the right atoll transforms an island getaway into a lifelong memory.\n\nPlan your stay between November and April for calm seas, clear visibility for snorkeling, and warm golden sunsets every evening.",
                'image' => '/images/seed/cat-beach.jpg',
                'published_at' => now()->subDays(12),
            ],
            [
                'title' => 'The Best Time to Visit Japan & Island Resorts',
                'slug' => 'the-best-time-to-visit-japan-and-island-resorts',
                'category' => 'Seasonal Tips',
                'read_time' => '5 Min Read',
                'excerpt' => 'Timing coastal escapes and mountain rail journeys around mild spring breezes and crystal autumn skies.',
                'content' => "Planning around seasonal weather patterns unlocks gentler crowds, vibrant coastal trails, and clearer mountain horizons.\n\nShoulder months such as late spring and early autumn deliver ideal temperatures for hiking coastal headlands, sailing open bays, and enjoying outdoor markets without peak-season congestion.\n\nPair two nights in a lively cultural hub with four tranquil nights along the coast for the most balanced itinerary.",
                'image' => '/images/seed/cat-wildlife.jpg',
                'published_at' => now()->subDays(8),
            ],
            [
                'title' => 'The 7 Amazing Destinations for Adventure Seekers',
                'slug' => 'the-7-amazing-destinations-for-adventure-seekers',
                'category' => 'Adventure',
                'read_time' => '7 Min Read',
                'excerpt' => 'From Swiss alpine glacier ridges to Thailand sea-cave lagoons, explore seven bucket-list routes for curious travelers.',
                'content' => "True adventure blends awe-inspiring terrain with knowledgeable local guides who reveal hidden routes beyond standard tourist postcards.\n\nOur favorite journeys combine active mornings—ridge trekking in Zermatt, kayaking emerald karst caves in Phang Nga, or reef diving in Ari Atoll—with restful evenings overlooking dramatic scenery.\n\nPack light layers, bring sturdy trail shoes, and leave room for spontaneous scenic detours.",
                'image' => '/images/seed/cat-hiking.jpg',
                'published_at' => now()->subDays(4),
            ],
        ];

        foreach ($blogPosts as $postData) {
            BlogPost::query()->updateOrCreate(
                ['slug' => $postData['slug']],
                [
                    'title' => $postData['title'],
                    'category' => $postData['category'],
                    'read_time' => $postData['read_time'],
                    'excerpt' => $postData['excerpt'],
                    'content' => $postData['content'],
                    'image' => $postData['image'],
                    'published_at' => $postData['published_at'],
                    'is_published' => true,
                ]
            );
        }

        if (isset($tourModels['greece-tour-package'], $tourModels['maldives-lagoon-retreat'])) {
            Booking::query()->firstOrCreate(
                [
                    'tour_id' => $tourModels['greece-tour-package']->id,
                    'customer_email' => $customer->email,
                ],
                [
                    'user_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'customer_phone' => '+1 415 555 0142',
                    'travel_date' => now()->addDays(24)->toDateString(),
                    'guests' => 2,
                    'total_price' => 1960.00,
                    'special_requests' => 'Caldera sunset view room preferred.',
                    'status' => 'confirmed',
                    'admin_notes' => 'Assigned boutique suite in Oia.',
                ]
            );

            Booking::query()->firstOrCreate(
                [
                    'tour_id' => $tourModels['maldives-lagoon-retreat']->id,
                    'customer_email' => $customer->email,
                ],
                [
                    'user_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'customer_phone' => '+1 415 555 0142',
                    'travel_date' => now()->addDays(42)->toDateString(),
                    'guests' => 2,
                    'total_price' => 2560.00,
                    'special_requests' => 'Overwater sunset villa preferred.',
                    'status' => 'pending',
                    'admin_notes' => null,
                ]
            );
        }

        Inquiry::query()->firstOrCreate(
            [
                'email' => $customer->email,
                'subject' => 'Custom Family Itinerary for Switzerland',
            ],
            [
                'name' => $customer->name,
                'phone' => '+1 415 555 0142',
                'message' => 'Hello! We are looking to combine Zermatt and Interlaken for 8 days next summer. Could you share custom private rail options?',
                'status' => 'replied',
            ]
        );

        Inquiry::query()->firstOrCreate(
            [
                'email' => 'daniel.park@example.com',
                'subject' => 'Private Yacht Charter Inquiry',
            ],
            [
                'name' => 'Daniel Park',
                'phone' => '+61 2 5550 1834',
                'message' => 'Interested in extending the Italy Coast Sail Package by two extra days around Capri.',
                'status' => 'new',
            ]
        );
    }
}
