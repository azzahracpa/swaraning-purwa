<?php

namespace Database\Seeders;

use App\Models\VisitSchedule;
use Illuminate\Database\Seeder;

class VisitScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Jadwal contoh 14 hari ke depan, museum tutup hari Senin
        $sesi = [
            ['09:00:00', '10:00:00', 30],
            ['10:30:00', '11:30:00', 30],
            ['13:00:00', '14:00:00', 20],
        ];

        for ($hari = 1; $hari <= 14; $hari++) {
            $tanggal = now()->addDays($hari);

            if ($tanggal->isMonday()) {
                continue;
            }

            foreach ($sesi as [$mulai, $selesai, $kapasitas]) {
                VisitSchedule::create([
                    'visit_date' => $tanggal->toDateString(),
                    'start_time' => $mulai,
                    'end_time' => $selesai,
                    'capacity' => $kapasitas,
                    'is_available' => true,
                ]);
            }
        }
    }
}
