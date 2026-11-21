<?php

namespace Database\Factories;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BlogPost>
 */
class BlogPostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = ucwords(fake()->unique()->sentence(6));

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'category' => 'Travel Tips',
            'read_time' => fake()->numberBetween(4, 9).' Min Read',
            'excerpt' => fake()->paragraph(),
            'content' => fake()->paragraphs(4, true),
            'image' => '/images/seed/cat-beach.jpg',
            'published_at' => now()->subDays(fake()->numberBetween(1, 30)),
            'is_published' => true,
        ];
    }
}
