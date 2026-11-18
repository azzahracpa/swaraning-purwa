<?php

namespace Database\Factories;

use App\Models\MuseumProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MuseumProfile>
 */
class MuseumProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Museum Mpu Purwa',
            'description' => fake()->paragraph(),
            'history' => fake()->paragraphs(2, true),
            'address' => fake()->address(),
            'phone' => fake()->numerify('0341######'),
            'email' => fake()->safeEmail(),
            'opening_hours' => 'Selasa–Minggu, 08.00–15.00',
            'image_url' => null,
        ];
    }
}
