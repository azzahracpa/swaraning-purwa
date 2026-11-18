<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Isi database dengan data contoh.
     * Jalankan: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MuseumProfileSeeder::class,
            CategorySeeder::class,
            CollectionSeeder::class,
            ArticleSeeder::class,
            VisitScheduleSeeder::class,
            ReservationSeeder::class,
            QuizSeeder::class,
        ]);
    }
}
