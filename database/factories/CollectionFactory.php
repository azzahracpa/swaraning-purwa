<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Collection>
 */
class CollectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => 'Koleksi '.fake()->unique()->word(),
            'description' => fake()->paragraph(),
            'historical_period' => 'Abad ke-'.fake()->numberBetween(9, 15),
            'origin' => fake()->city(),
            'material' => fake()->randomElement(['Batu andesit', 'Terakota', 'Perunggu']),
            'location' => 'Ruang pamer '.fake()->numberBetween(1, 3),
        ];
    }
}
