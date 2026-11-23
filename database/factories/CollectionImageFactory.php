<?php

namespace Database\Factories;

use App\Models\Collection;
use App\Models\CollectionImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CollectionImage>
 */
class CollectionImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'collection_id' => Collection::factory(),
            'image_url' => 'images/museum/arca-ganesha.jpg',
            'caption' => fake()->sentence(3),
            'is_primary' => false,
        ];
    }
}
