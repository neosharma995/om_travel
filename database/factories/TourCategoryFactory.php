<?php

namespace Database\Factories;

use App\Models\TourCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TourCategory>
 */
class TourCategoryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'image' => '/images/seed/cat-cruise.jpg',
            'sort_order' => fake()->numberBetween(1, 20),
            'is_active' => true,
        ];
    }
}
