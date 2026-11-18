<?php

namespace Database\Factories;

use App\Models\VisitSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VisitSchedule>
 */
class VisitScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'visit_date' => now()->addDays(fake()->numberBetween(1, 14))->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'capacity' => 30,
            'is_available' => true,
        ];
    }
}
