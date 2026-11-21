<?php

namespace Database\Factories;

use App\Models\TourGuide;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TourGuide>
 */
class TourGuideFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'role' => 'Tourist Guide',
            'bio' => fake()->sentence(14),
            'photo' => '/images/seed/guide-1.jpg',
            'facebook_url' => 'https://facebook.com',
            'twitter_url' => 'https://x.com',
            'instagram_url' => 'https://instagram.com',
            'linkedin_url' => 'https://linkedin.com',
            'sort_order' => fake()->numberBetween(1, 20),
            'is_active' => true,
        ];
    }
}
