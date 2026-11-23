<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\TourType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tour>
 */
class TourFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = ucwords(fake()->unique()->words(3, true)).' Tour Package';
        $days = fake()->numberBetween(3, 10);

        return [
            'destination_id' => Destination::factory(),
            'tour_category_id' => TourCategory::factory(),
            'tour_type_id' => TourType::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'location' => fake()->city().', '.fake()->country(),
            'price' => fake()->randomFloat(2, 450, 1650),
            'duration_days' => $days,
            'duration_label' => $days.' Days',
            'rating' => fake()->randomFloat(1, 4.5, 5.0),
            'reviews_count' => fake()->numberBetween(12, 120),
            'summary' => fake()->sentence(16),
            'description' => fake()->paragraphs(3, true),
            'highlights' => "Private airport transfers\nBoutique coastal accommodation\nGuided cultural excursions\nDaily breakfast included",
            'image' => '/images/seed/tour-greece.jpg',
            'is_featured' => true,
            'sort_order' => fake()->numberBetween(1, 20),
            'is_active' => true,
        ];
    }
}
