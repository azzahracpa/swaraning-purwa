<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\User;
use App\Models\VisitSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'schedule_id' => VisitSchedule::factory(),
            'booking_code' => Reservation::buatKodeBooking(),
            'visitor_name' => fake()->name(),
            'phone' => fake()->numerify('08##########'),
            'total_visitors' => fake()->numberBetween(1, 10),
            'status' => Reservation::STATUS_PENDING,
            'notes' => null,
        ];
    }

    public function disetujui(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Reservation::STATUS_APPROVED,
        ]);
    }
}
