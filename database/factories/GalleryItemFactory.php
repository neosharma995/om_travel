<?php

namespace Database\Factories;

use App\Models\GalleryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GalleryItem>
 */
class GalleryItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => ucwords(fake()->words(3, true)),
            'location' => fake()->city().', '.fake()->country(),
            'image' => '/images/seed/cat-beach.jpg',
            'sort_order' => fake()->numberBetween(1, 20),
            'is_active' => true,
        ];
    }
}
