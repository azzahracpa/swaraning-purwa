<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'author_id' => User::factory()->admin(),
            'title' => fake()->sentence(5),
            'content' => fake()->paragraphs(4, true),
            'image_url' => null,
            'published_at' => now()->subDay(),
        ];
    }

    public function draf(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
        ]);
    }
}
