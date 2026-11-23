<?php

namespace Database\Seeders;

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
        $customer->name = 'Aarav Mehta';
        if (! $customer->exists) {
            $customer->password = Hash::make('password');
        }
        $customer->email_verified_at = $customer->email_verified_at ?? now();
        $customer->is_admin = false;
        $customer->save();

        SiteSetting::setMany(SiteSetting::defaults(), 'general');

        // Clear existing records cleanly for fresh Himachal Pradesh content
        Booking::query()->delete();
        Inquiry::query()->delete();
        Tour::query()->delete();
        Destination::query()->delete();
        TourCategory::query()->delete();
        TourType::query()->delete();
        GalleryItem::query()->delete();
        TourGuide::query()->delete();
        Testimonial::query()->delete();
        BlogPost::query()->delete();

        // 1. Tour Categories
        $categories = [
            ['name' => 'Trekking & Trails', 'slug' => 'trekking', 'image' => '/images/himachal/cat-trekking.jpg', 'sort_order' => 1],
            ['name' => 'Paragliding & Adventure', 'slug' => 'adventure', 'image' => '/images/himachal/cat-adventure.jpg', 'sort_order' => 2],
            ['name' => 'Monasteries & Spiritual', 'slug' => 'spiritual', 'image' => '/images/himachal/cat-spiritual.jpg', 'sort_order' => 3],
            ['name' => 'Scenic Valleys & Meadows', 'slug' => 'scenic-valleys', 'image' => '/images/himachal/cat-valleys.jpg', 'sort_order' => 4],
            ['name' => 'Heritage & Hill Stations', 'slug' => 'heritage', 'image' => '/images/himachal/cat-heritage.jpg', 'sort_order' => 5],
            ['name' => 'High Altitude Expeditions', 'slug' => 'expeditions', 'image' => '/images/himachal/cat-expeditions.jpg', 'sort_order' => 6],
        ];

        $categoryModels = [];
        foreach ($categories as $categoryData) {
            $categoryModels[$categoryData['slug']] = TourCategory::query()->create([
                'name' => $categoryData['name'],
                'slug' => $categoryData['slug'],
                'image' => $categoryData['image'],
                'sort_order' => $categoryData['sort_order'],
                'is_active' => true,
            ]);
        }

        // 2. Tour Types
        $tourTypes = [
            ['name' => 'Luxury Mountain Escape', 'slug' => 'luxury-escape', 'description' => 'Handcrafted cedar chalets, private heating, luxury balcony views, and seamless chauffeured mountain transfers.', 'sort_order' => 1],
            ['name' => 'Adventure & High Trek', 'slug' => 'adventure-trek', 'description' => 'Certified alpine leaders, summit passes, camping under Himalayan stars, and complete mountain safety gear.', 'sort_order' => 2],
            ['name' => 'Honeymoon & Romance', 'slug' => 'honeymoon-romance', 'description' => 'Cozy hillside cottages, scenic river picnics, private candlelit valley dining, and intimate mountain seclusion.', 'sort_order' => 3],
            ['name' => 'Family Mountain Holiday', 'slug' => 'family-holiday', 'description' => 'Relaxed pacing, comfortable private vehicles, kid-friendly snow points, and central boutique family suites.', 'sort_order' => 4],
            ['name' => 'Group Tour & Expedition', 'slug' => 'group-tour', 'description' => 'Social journeys across legendary mountain circuits with shared 4x4 vehicles, group campfires, and vibrant stops.', 'sort_order' => 5],
            ['name' => 'Private Custom Road Trip', 'slug' => 'private-custom', 'description' => 'Bespoke itineraries, dedicated 4x4 SUV and native driver, flexible pacing, and customized daily excursions.', 'sort_order' => 6],
        ];

        $tourTypeModels = [];
        foreach ($tourTypes as $tourTypeData) {
            $tourTypeModels[$tourTypeData['slug']] = TourType::query()->create([
                'name' => $tourTypeData['name'],
                'slug' => $tourTypeData['slug'],
                'description' => $tourTypeData['description'],
                'sort_order' => $tourTypeData['sort_order'],
                'is_active' => true,
            ]);
        }

        // 3. Destinations (12 authentic Himachal Pradesh destinations)
        $destinations = [
            [
                'name' => 'Manali',
                'slug' => 'manali',
                'country' => 'Himachal Pradesh, India',
                'description' => 'A high-altitude paradise tucked in the Kullu Valley, offering roaring rivers, snow-crowned Solang peaks, Rohtang Pass, and tranquil apple orchards.',
                'image' => '/images/himachal/manali.jpg',
                'listings_label' => '18 Listings',
                'sort_order' => 1,
            ],
            [
                'name' => 'Spiti Valley',
                'slug' => 'spiti-valley',
                'country' => 'Himachal Pradesh, India',
                'description' => 'The enchanting middle land of cold desert valleys, ancient cliffside Tibetan monasteries, turquoise Chandratal Lake, and barren Himalayan summits.',
                'image' => '/images/himachal/spiti.jpg',
                'listings_label' => '12 Listings',
                'sort_order' => 2,
            ],
            [
                'name' => 'Shimla',
                'slug' => 'shimla',
                'country' => 'Himachal Pradesh, India',
                'description' => 'The iconic Queen of Hills featuring historic British-colonial architecture on the Mall Road, pine-cloaked ridge trails, and the UNESCO Kalka-Shimla heritage railway.',
                'image' => '/images/himachal/shimla.jpg',
                'listings_label' => '14 Listings',
                'sort_order' => 3,
            ],
            [
                'name' => 'Dharamshala',
                'slug' => 'dharamshala',
                'country' => 'Himachal Pradesh, India',
                'description' => 'Set against the dramatic Dhauladhar range, the spiritual sanctuary of McLeodganj, home to the Dalai Lama, Tibetan monasteries, and cedar forests.',
                'image' => '/images/himachal/dharamshala.jpg',
                'listings_label' => '15 Listings',
                'sort_order' => 4,
            ],
            [
                'name' => 'Kasol',
                'slug' => 'kasol',
                'country' => 'Himachal Pradesh, India',
                'description' => 'The vibrant heart of the Parvati Valley, famous for scenic riverside pine trails, roaring river streams, and the mystical Kheerganga hot springs trek.',
                'image' => '/images/himachal/kasol.jpg',
                'listings_label' => '10 Listings',
                'sort_order' => 5,
            ],
            [
                'name' => 'Kinnaur',
                'slug' => 'kinnaur',
                'country' => 'Himachal Pradesh, India',
                'description' => 'Land of fairytales with Sangla Valley, Kalpa apple groves, carved wooden temples, and majestic views of the sacred Kinnaur Kailash peak.',
                'image' => '/images/himachal/kinnaur.jpg',
                'listings_label' => '9 Listings',
                'sort_order' => 6,
            ],
            [
                'name' => 'Dalhousie',
                'slug' => 'dalhousie',
                'country' => 'Himachal Pradesh, India',
                'description' => 'A serene colonial hill town set across five pine-clad hills, boasting misty deodar forests, Victorian architecture, and panoramic Himalayan vistas.',
                'image' => '/images/himachal/dalhousie.jpg',
                'listings_label' => '8 Listings',
                'sort_order' => 7,
            ],
            [
                'name' => 'Khajjiar',
                'slug' => 'khajjiar',
                'country' => 'Himachal Pradesh, India',
                'description' => 'Fondly known as the Mini Switzerland of India, featuring a saucer-shaped emerald meadow, floating island lake, and dense cedar woods.',
                'image' => '/images/himachal/khajjiar.jpg',
                'listings_label' => '7 Listings',
                'sort_order' => 8,
            ],
            [
                'name' => 'Bir Billing',
                'slug' => 'bir-billing',
                'country' => 'Himachal Pradesh, India',
                'description' => 'The world-renowned paragliding capital of India, surrounded by Tibetan monasteries, serene tea gardens, and panoramic Kangra Valley sunsets.',
                'image' => '/images/himachal/bir-billing.jpg',
                'listings_label' => '8 Listings',
                'sort_order' => 9,
            ],
            [
                'name' => 'Tirthan Valley',
                'slug' => 'tirthan-valley',
                'country' => 'Himachal Pradesh, India',
                'description' => 'An untouched eco-paradise at the gateway of Great Himalayan National Park, renowned for pristine trout streams, wooden hamlets, and Serolsar Lake.',
                'image' => '/images/himachal/tirthan.jpg',
                'listings_label' => '6 Listings',
                'sort_order' => 10,
            ],
            [
                'name' => 'Kullu',
                'slug' => 'kullu',
                'country' => 'Himachal Pradesh, India',
                'description' => 'The Valley of the Gods, renowned for Beas river white-water rafting, ancient wooden pagoda temples, and colorful Dussehra heritage.',
                'image' => '/images/himachal/kullu.jpg',
                'listings_label' => '11 Listings',
                'sort_order' => 11,
            ],
            [
                'name' => 'Chamba',
                'slug' => 'chamba',
                'country' => 'Himachal Pradesh, India',
                'description' => 'An ancient Himalayan kingdom perched above the Ravi River, celebrated for centuries-old Lakshmi Narayan temples, miniature paintings, and pristine valleys.',
                'image' => '/images/himachal/chamba.jpg',
                'listings_label' => '5 Listings',
                'sort_order' => 12,
            ],
        ];

        $destinationModels = [];
        foreach ($destinations as $destinationData) {
            $destinationModels[$destinationData['slug']] = Destination::query()->create([
                'name' => $destinationData['name'],
                'slug' => $destinationData['slug'],
                'country' => $destinationData['country'],
                'description' => $destinationData['description'],
                'image' => $destinationData['image'],
                'listings_label' => $destinationData['listings_label'],
                'is_popular' => true,
                'sort_order' => $destinationData['sort_order'],
                'is_active' => true,
            ]);
        }

        // 4. Himachal Pradesh Tour Packages
        $tours = [
            [
                'title' => 'Spiti Valley High Altitude Expedition',
                'slug' => 'spiti-valley-high-altitude-expedition',
                'destination_slug' => 'spiti-valley',
                'category_slug' => 'expeditions',
                'tour_type_slug' => 'adventure-trek',
                'location' => 'Kaza, Key Monastery & Chandratal Lake, Himachal',
                'price' => 28500.00,
                'duration_days' => 8,
                'duration_label' => '8 Days',
                'rating' => 4.9,
                'reviews_count' => 86,
                'summary' => 'Journey through dramatic cold desert canyons, ancient thousand-year-old monasteries, and high-altitude glacial lakes.',
                'description' => "Embark on an unforgettable eight-day overland odyssey across the mystical Spiti Valley.\n\nTraverse high mountain passes including Kunzum Pass, visit the cliffside Key Monastery and Dhankar Gompa, send a postcard from the world's highest post office at Hikkim, and camp beside the turquoise crescent waters of Chandratal Lake.",
                'highlights' => "Guided exploration of Key & Dhankar Monasteries\nStargazing campsite stay at Chandratal Lake\nVisit to Hikkim, Komic, and Langza fossil village\nPrivate 4x4 mountain vehicle with seasoned local driver",
                'image' => '/images/himachal/spiti.jpg',
                'sort_order' => 1,
            ],
            [
                'title' => 'Manali & Rohtang Pass Alpine Escape',
                'slug' => 'manali-rohtang-pass-alpine-escape',
                'destination_slug' => 'manali',
                'category_slug' => 'scenic-valleys',
                'tour_type_slug' => 'luxury-escape',
                'location' => 'Solang Valley & Rohtang Pass, Manali',
                'price' => 18900.00,
                'duration_days' => 5,
                'duration_label' => '5 Days',
                'rating' => 4.8,
                'reviews_count' => 112,
                'summary' => 'Experience snow-clad mountain passes, lush pine forests, apple orchards, and luxury cedar resort stays in Manali.',
                'description' => "Unwind amidst the breathtaking mountains of Manali, Solang Valley, and Rohtang Pass.\n\nEnjoy luxury cottage accommodations surrounded by deodar groves, adrenaline activities in Solang Valley, day trips to the snow line of Rohtang, and peaceful strolls through Old Manali cafes and Hadimba Temple.",
                'highlights' => "Rohtang Pass & Atal Tunnel mountain excursion\nParagliding and snow activities in Solang Valley\nBoutique riverside luxury resort with valley views\nGuided heritage walk to Hadimba Temple and Vashisht springs",
                'image' => '/images/himachal/manali.jpg',
                'sort_order' => 2,
            ],
            [
                'title' => 'Shimla Colonial Heritage & Toy Train',
                'slug' => 'shimla-colonial-heritage-toy-train',
                'destination_slug' => 'shimla',
                'category_slug' => 'heritage',
                'tour_type_slug' => 'family-holiday',
                'location' => 'Mall Road, Ridge & Kufri, Shimla',
                'price' => 16500.00,
                'duration_days' => 4,
                'duration_label' => '4 Days',
                'rating' => 4.8,
                'reviews_count' => 94,
                'summary' => 'Relive British-colonial hill grandeur with the UNESCO Kalka-Shimla toy train, historic Viceregal Lodge, and panoramic Kufri.',
                'description' => "Step into the timeless nostalgia of the Queen of Hills on this curated four-day journey.\n\nRide the historic UNESCO World Heritage Kalka-Shimla mountain rail, stroll along the pedestrian-only Mall Road and Ridge, admire the neo-gothic Christ Church, and take in panoramic Himalayan views from Kufri.",
                'highlights' => "Scenic UNESCO Kalka-Shimla toy train journey\nHeritage walk of Viceregal Lodge and Christ Church\nPanoramic pine forest day trip to Kufri and Jakhoo Hill\nHeritage boutique hotel stay on the historic Mall",
                'image' => '/images/himachal/shimla.jpg',
                'sort_order' => 3,
            ],
            [
                'title' => 'Dharamshala & McLeodganj Spiritual Retreat',
                'slug' => 'dharamshala-mcleodganj-spiritual-retreat',
                'destination_slug' => 'dharamshala',
                'category_slug' => 'spiritual',
                'tour_type_slug' => 'luxury-escape',
                'location' => 'McLeodganj & Kangra Valley, Dharamshala',
                'price' => 21500.00,
                'duration_days' => 5,
                'duration_label' => '5 Days',
                'rating' => 4.9,
                'reviews_count' => 78,
                'summary' => 'Immerse in Tibetan spirituality, serene monasteries, lush Kangra tea gardens, and Dhauladhar mountain viewpoints.',
                'description' => "Discover inner peace against the snow-draped backdrop of the majestic Dhauladhar mountains.\n\nVisit the Tsuglagkhang Complex (the Dalai Lama Temple), explore the peaceful Norbulingka Institute preserving traditional Tibetan arts, trek through pine forests to Bhagsu Waterfall, and stroll peaceful tea gardens in Kangra.",
                'highlights' => "Private guided tour of Dalai Lama Temple & Namgyal Monastery\nCultural visit and artisan workshops at Norbulingka Institute\nScenic short trek to Bhagsu Waterfall and Triund viewpoint\nBoutique hillside retreat with panoramic mountain vistas",
                'image' => '/images/himachal/dharamshala.jpg',
                'sort_order' => 4,
            ],
            [
                'title' => 'Kasol & Kheerganga Trekking Odyssey',
                'slug' => 'kasol-kheerganga-trekking-odyssey',
                'destination_slug' => 'kasol',
                'category_slug' => 'trekking',
                'tour_type_slug' => 'adventure-trek',
                'location' => 'Parvati Valley & Kheerganga, Kasol',
                'price' => 12800.00,
                'duration_days' => 4,
                'duration_label' => '4 Days',
                'rating' => 4.7,
                'reviews_count' => 104,
                'summary' => 'Trek through enchanting pine forests, roaring river gorges, and soak in natural hot springs at Kheerganga under starry skies.',
                'description' => "Venture deep into the vibrant and mystical Parvati Valley.\n\nHike alongside the turquoise Parvati River, explore charming wooden hamlets like Tosh and Chalal, and embark on the scenic trek to Kheerganga to bathe in natural mineral hot springs perched high in the Himalayas.",
                'highlights' => "Guided scenic trek to Kheerganga with mountain guides\nNatural sulphur hot spring bath overlooking snow peaks\nCampfire night and stargazing dome tent stay\nExploration of Kasol cafes and picturesque Tosh village",
                'image' => '/images/himachal/kasol.jpg',
                'sort_order' => 5,
            ],
            [
                'title' => 'Bir Billing Paragliding & Tibetan Culture',
                'slug' => 'bir-billing-paragliding-tibetan-culture',
                'destination_slug' => 'bir-billing',
                'category_slug' => 'adventure',
                'tour_type_slug' => 'group-tour',
                'location' => 'Bir & Billing Takeoff Site, Kangra',
                'price' => 14500.00,
                'duration_days' => 3,
                'duration_label' => '3 Days',
                'rating' => 4.9,
                'reviews_count' => 67,
                'summary' => "Soar over the Kangra Valley from Asia's top paragliding launch pad and explore peaceful monasteries and tea gardens.",
                'description' => "Take flight over terraced tea gardens and emerald valleys from Billing at 2,400 meters.\n\nAccompanied by licensed tandem pilots, enjoy a thrilling 25-minute paragliding flight before landing smoothly in Bir. Spend peaceful evenings exploring Chokling Monastery and sampling Tibetan delicacies in local mountain cafes.",
                'highlights' => "Tandem paragliding flight from Billing with GoPro recording\nVisit to Palpung Sherabling and Chokling Monasteries\nSunset tea garden walk in Bir Valley\nEco-lodge stay with organic local Himachali cuisine",
                'image' => '/images/himachal/bir-billing.jpg',
                'sort_order' => 6,
            ],
            [
                'title' => 'Kinnaur Sangla & Kalpa Enchanted Odyssey',
                'slug' => 'kinnaur-sangla-kalpa-odyssey',
                'destination_slug' => 'kinnaur',
                'category_slug' => 'scenic-valleys',
                'tour_type_slug' => 'private-custom',
                'location' => 'Sangla Valley, Chitkul & Kalpa, Kinnaur',
                'price' => 26000.00,
                'duration_days' => 6,
                'duration_label' => '6 Days',
                'rating' => 4.9,
                'reviews_count' => 53,
                'summary' => 'Witness the majestic Kinnaur Kailash peaks, apple orchards, Kamru Fort, and Chitkul—the last Indian village.',
                'description' => "Explore one of Himachal's most awe-inspiring and culturally rich districts.\n\nDrive along dramatic cliff roads into the Baspa River valley of Sangla, visit Chitkul on the Indo-Tibetan border, explore ancient wooden temples at Kamru Fort, and watch sunrise glow orange on the holy Kinnaur Kailash peak from Kalpa.",
                'highlights' => "Excursion to Chitkul—the last inhabited village near the border\nGolden sunrise view of the sacred Kinnaur Kailash\nVisit to centuries-old Kamru Fort and Bering Nag Temple\nStay in Swiss-style luxury alpine camps in Sangla",
                'image' => '/images/himachal/kinnaur.jpg',
                'sort_order' => 7,
            ],
            [
                'title' => 'Dalhousie & Khajjiar Mini Switzerland Tour',
                'slug' => 'dalhousie-khajjiar-mini-switzerland-tour',
                'destination_slug' => 'khajjiar',
                'category_slug' => 'scenic-valleys',
                'tour_type_slug' => 'family-holiday',
                'location' => 'Khajjiar, Kalatop & Dalhousie',
                'price' => 15900.00,
                'duration_days' => 4,
                'duration_label' => '4 Days',
                'rating' => 4.8,
                'reviews_count' => 82,
                'summary' => 'Lush green saucer meadows bordered by towering deodars, misty nature walks, and Victorian hill elegance.',
                'description' => "Immerse your family in the fairytale landscape of Khajjiar and Dalhousie.\n\nStroll the sprawling deodar-rimmed meadow of Khajjiar surrounding its tranquil lake, walk the Kalatop Wildlife Sanctuary trail, and enjoy serene sunsets from Subhash Baoli and Dainkund Peak in Dalhousie.",
                'highlights' => "Full day in Khajjiar meadow with horseback rides and zorbing\nNature walk through Kalatop Wildlife Sanctuary pine trails\nPanoramic view of three Himalayan rivers from Dainkund Peak\nHeritage resort stay in colonial Dalhousie",
                'image' => '/images/himachal/khajjiar.jpg',
                'sort_order' => 8,
            ],
            [
                'title' => 'Tirthan Valley & Great Himalayan National Park',
                'slug' => 'tirthan-valley-great-himalayan-national-park',
                'destination_slug' => 'tirthan-valley',
                'category_slug' => 'trekking',
                'tour_type_slug' => 'private-custom',
                'location' => 'Jibhi, Gushaini & Jalori Pass, Tirthan',
                'price' => 19500.00,
                'duration_days' => 5,
                'duration_label' => '5 Days',
                'rating' => 4.9,
                'reviews_count' => 61,
                'summary' => 'Crystal trout rivers, traditional deodar-wood cottages, Jalori Pass, and the UNESCO Great Himalayan National Park.',
                'description' => "Escape the crowds to the tranquil, pristine eco-sanctuary of Tirthan Valley.\n\nStay in handcrafted riverside wooden cottages along the Tirthan River, hike through oak and rhododendron forests to the mystical Serolsar Lake via Jalori Pass, visit the cascading Jibhi waterfalls, and spot vibrant Himalayan birds.",
                'highlights' => "Guided trek to Serolsar Lake and historic Raghupur Fort\nAngling and riverside nature walk along Tirthan River\nDay trip to Jibhi waterfalls and Chehni Kothi tower\nTraditional Himachali riverside wooden lodge stay",
                'image' => '/images/himachal/tirthan.jpg',
                'sort_order' => 9,
            ],
        ];

        $tourModels = [];
        foreach ($tours as $tourData) {
            $tourModels[$tourData['slug']] = Tour::query()->create([
                'destination_id' => $destinationModels[$tourData['destination_slug']]->id ?? null,
                'tour_category_id' => $categoryModels[$tourData['category_slug']]->id ?? null,
                'tour_type_id' => $tourTypeModels[$tourData['tour_type_slug']]->id ?? null,
                'title' => $tourData['title'],
                'slug' => $tourData['slug'],
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
            ]);
        }

        // 5. Gallery Items (Himachal Pradesh Photography)
        $galleryItems = [
            ['title' => 'Key Monastery on Hilltop', 'location' => 'Spiti Valley, Himachal', 'image' => '/images/himachal/gallery-1.jpg', 'sort_order' => 1],
            ['title' => 'Solang Valley Snow Ridges', 'location' => 'Manali, Himachal', 'image' => '/images/himachal/gallery-2.jpg', 'sort_order' => 2],
            ['title' => 'Khajjiar Saucer Meadow', 'location' => 'Khajjiar, Himachal', 'image' => '/images/himachal/gallery-3.jpg', 'sort_order' => 3],
            ['title' => 'Parvati River Pine Valley', 'location' => 'Kasol, Himachal', 'image' => '/images/himachal/gallery-4.jpg', 'sort_order' => 4],
            ['title' => 'Dhauladhar Snow Peaks', 'location' => 'Dharamshala, Himachal', 'image' => '/images/himachal/gallery-5.jpg', 'sort_order' => 5],
            ['title' => 'Kinnaur Kailash at Sunrise', 'location' => 'Kalpa, Kinnaur', 'image' => '/images/himachal/gallery-6.jpg', 'sort_order' => 6],
            ['title' => 'UNESCO Kalka-Shimla Toy Train', 'location' => 'Shimla, Himachal', 'image' => '/images/himachal/gallery-7.jpg', 'sort_order' => 7],
        ];

        foreach ($galleryItems as $itemData) {
            GalleryItem::query()->create([
                'title' => $itemData['title'],
                'location' => $itemData['location'],
                'image' => $itemData['image'],
                'sort_order' => $itemData['sort_order'],
                'is_active' => true,
            ]);
        }

        // 6. Tour Guides (Certified Local Himachali Guides)
        $guides = [
            [
                'name' => 'Tenzin Norbu',
                'role' => 'Senior Expedition Leader',
                'bio' => 'Born in Spiti Valley, mountaineering graduate with 14 years leading high-altitude Himalayan traverses.',
                'photo' => '/images/seed/guide-1.jpg',
                'sort_order' => 1,
            ],
            [
                'name' => 'Rajesh Thakur',
                'role' => 'Mountain & Trekking Guide',
                'bio' => 'Manali native, certified Wilderness First Responder and veteran guide across Hampta and Pin Parvati trails.',
                'photo' => '/images/seed/guide-2.jpg',
                'sort_order' => 2,
            ],
            [
                'name' => 'Sunita Sharma',
                'role' => 'Cultural & Heritage Specialist',
                'bio' => "Historian and storyteller specializing in Shimla's colonial archives and Kangra Valley temple architecture.",
                'photo' => '/images/seed/guide-3.jpg',
                'sort_order' => 3,
            ],
            [
                'name' => 'Vikram Negi',
                'role' => 'Kinnaur & Adventure Navigator',
                'bio' => 'Lifelong resident of Sangla Valley with expertise in botanical trails, local folklore, and wildlife photography.',
                'photo' => '/images/seed/guide-4.jpg',
                'sort_order' => 4,
            ],
        ];

        foreach ($guides as $guideData) {
            TourGuide::query()->create([
                'name' => $guideData['name'],
                'role' => $guideData['role'],
                'bio' => $guideData['bio'],
                'photo' => $guideData['photo'],
                'facebook_url' => 'https://facebook.com',
                'twitter_url' => 'https://x.com',
                'instagram_url' => 'https://instagram.com',
                'linkedin_url' => 'https://linkedin.com',
                'sort_order' => $guideData['sort_order'],
                'is_active' => true,
            ]);
        }

        // 7. Verified Testimonials (Himachal Travelers)
        $testimonials = [
            [
                'client_name' => 'Aarav Mehta',
                'client_role' => 'Adventure Traveler',
                'avatar' => '/images/seed/client-1.jpg',
                'rating' => 5,
                'quote' => 'The Spiti Valley expedition organized by OmTravel was life-changing. From our 4x4 driver\'s skill on the Kunzum Pass to stargazing at Chandratal, every moment was pure magic.',
                'sort_order' => 1,
            ],
            [
                'client_name' => 'Pooja & Rohan Sharma',
                'client_role' => 'Honeymooners',
                'avatar' => '/images/seed/guide-1.jpg',
                'rating' => 5,
                'quote' => 'Our honeymoon in Manali and Solang Valley felt straight out of a movie. The riverside luxury resort in Old Manali and private Rohtang snow trip exceeded all our dreams.',
                'sort_order' => 2,
            ],
            [
                'client_name' => 'Dr. Sunita Rao',
                'client_role' => 'Cultural Traveler',
                'avatar' => '/images/seed/guide-3.jpg',
                'rating' => 5,
                'quote' => 'Traveling through McLeodganj and Dharamshala with OmTravel was so peaceful. The Tibetan monastery tours and private tea garden walk in Kangra were meticulously planned.',
                'sort_order' => 3,
            ],
            [
                'client_name' => 'Kavita Desai',
                'client_role' => 'Nature Enthusiast',
                'avatar' => '/images/seed/guide-4.jpg',
                'rating' => 5,
                'quote' => 'Tirthan Valley is an absolute paradise! Handcrafted wooden cottage by the river, quiet hikes to Serolsar Lake, and delicious Himachali siddu prepared fresh daily.',
                'sort_order' => 4,
            ],
        ];

        foreach ($testimonials as $testimonialData) {
            Testimonial::query()->create([
                'client_name' => $testimonialData['client_name'],
                'client_role' => $testimonialData['client_role'],
                'avatar' => $testimonialData['avatar'],
                'rating' => $testimonialData['rating'],
                'quote' => $testimonialData['quote'],
                'is_featured' => true,
                'sort_order' => $testimonialData['sort_order'],
                'is_active' => true,
            ]);
        }

        // 8. Blog Posts (Himachal Pradesh Guides)
        $blogPosts = [
            [
                'title' => 'The Complete Guide to Spiti Valley Road Trip',
                'slug' => 'complete-guide-to-spiti-valley-road-trip',
                'category' => 'Road Trip & Expedition',
                'read_time' => '8 Min Read',
                'excerpt' => 'Everything you need to know about navigating the rugged Hindustan-Tibet road, Kunzum Pass, ancient monasteries, and high-altitude permits.',
                'content' => "Spiti Valley—literally 'The Middle Land'—lies between Tibet and India, offering an otherworldly terrain of barren mountains, dramatic river gorges, and ancient Buddhist heritage.\n\nWhether entering from the lush cedar forests of Shimla or the high-altitude pass of Rohtang and Kunzum from Manali, a Spiti road trip tests your endurance and rewards you with unmatched Himalayan vistas.\n\nTake time to acclimatize in Kaza, send a handwritten letter from Hikkim—the world's highest post office—and spend a night stargazing beside the ethereal blue waters of Chandratal Lake.",
                'image' => '/images/himachal/blog-spiti.jpg',
                'published_at' => now()->subDays(12),
            ],
            [
                'title' => 'Best Time to Visit Himachal Pradesh: Season by Season Guide',
                'slug' => 'best-time-to-visit-himachal-pradesh',
                'category' => 'Seasonal Tips',
                'read_time' => '6 Min Read',
                'excerpt' => 'From winter snows in Manali and Shimla to spring apple blooms in Kinnaur and summer treks in Spiti, plan your ideal journey.',
                'content' => "Himachal Pradesh is a year-round mountain wonderland, each season unveiling a completely distinct personality across its valleys.\n\nFrom December to February, hill stations like Shimla, Manali, and Dalhousie transform under a blanket of snow, ideal for skiing and cozy chalet retreats. Spring (March to May) brings blooming rhododendrons and emerald tea gardens in Kangra.\n\nSummer is the prime season to cross high-altitude passes into Spiti and Kinnaur, while autumn (September to November) offers crisp mountain air, golden orchards, and razor-sharp Himalayan views.",
                'image' => '/images/himachal/blog-seasons.jpg',
                'published_at' => now()->subDays(8),
            ],
            [
                'title' => 'Top 7 Offbeat Valleys in Himachal Pradesh for Nature Lovers',
                'slug' => 'top-7-offbeat-valleys-in-himachal-pradesh',
                'category' => 'Hidden Gems',
                'read_time' => '7 Min Read',
                'excerpt' => 'Venture beyond the crowded tourist trails into the pristine, untouched beauty of Tirthan, Sainj, Sangla, and Pangi Valleys.',
                'content' => "While the popular hubs of Shimla and Manali have timeless charm, the true soul of Devbhoomi Himachal lies in its remote, quiet valleys.\n\nTirthan Valley invites travelers with crystal-clear trout streams and rustic Kathkuni wooden architecture on the edge of Great Himalayan National Park. Sangla Valley in Kinnaur enchants with sprawling apple orchards and views of the sacred Kinnaur Kailash.\n\nPack light, respect local customs, and embrace the slow, peaceful rhythms of Himalayan village life.",
                'image' => '/images/himachal/blog-offbeat.jpg',
                'published_at' => now()->subDays(4),
            ],
        ];

        foreach ($blogPosts as $postData) {
            BlogPost::query()->create([
                'title' => $postData['title'],
                'slug' => $postData['slug'],
                'category' => $postData['category'],
                'read_time' => $postData['read_time'],
                'excerpt' => $postData['excerpt'],
                'content' => $postData['content'],
                'image' => $postData['image'],
                'published_at' => $postData['published_at'],
                'is_published' => true,
            ]);
        }

        // Seed sample customer booking
        if (isset($tourModels['spiti-valley-high-altitude-expedition'], $tourModels['manali-rohtang-pass-alpine-escape'])) {
            Booking::query()->create([
                'tour_id' => $tourModels['spiti-valley-high-altitude-expedition']->id,
                'user_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => '+91 98765 43210',
                'travel_date' => now()->addDays(24)->toDateString(),
                'guests' => 2,
                'total_price' => 57000.00,
                'special_requests' => 'Chandratal Lake dome tent with extra thermal sleeping bags preferred.',
                'status' => 'confirmed',
                'admin_notes' => 'Allocated premium 4x4 mountain vehicle with senior guide Tenzin.',
            ]);

            Booking::query()->create([
                'tour_id' => $tourModels['manali-rohtang-pass-alpine-escape']->id,
                'user_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => '+91 98765 43210',
                'travel_date' => now()->addDays(42)->toDateString(),
                'guests' => 2,
                'total_price' => 37800.00,
                'special_requests' => 'Riverside cottage with balcony view in Old Manali.',
                'status' => 'pending',
                'admin_notes' => null,
            ]);
        }

        Inquiry::query()->create([
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => '+91 98765 43210',
            'subject' => 'Custom Family Road Trip for Spiti & Kinnaur Circuit',
            'message' => 'Hello! We are planning a 10-day circuit combining Shimla, Kinnaur, and Spiti Valley with our parents. Could you arrange an Innova Crysta with a seasoned mountain driver?',
            'status' => 'replied',
        ]);

        Inquiry::query()->create([
            'name' => 'Pooja Sharma',
            'email' => 'pooja.sharma@example.com',
            'phone' => '+91 98123 45678',
            'subject' => 'Bir Billing Paragliding & Camping Query',
            'message' => 'Interested in booking the Bir Billing paragliding package for a group of 6 people next weekend. Do you also provide tandem flight video recording?',
            'status' => 'new',
        ]);
    }
}
