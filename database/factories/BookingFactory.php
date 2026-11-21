<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tour_id' => Tour::factory(),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->phoneNumber(),
            'travel_date' => now()->addDays(fake()->numberBetween(7, 60))->toDateString(),
            'guests' => fake()->numberBetween(1, 6),
            'total_price' => fake()->randomFloat(2, 600, 4000),
            'special_requests' => fake()->sentence(),
            'status' => 'pending',
            'admin_notes' => null,
        ];
    }
}
