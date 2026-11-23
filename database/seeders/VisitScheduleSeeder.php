<?php

namespace Database\Seeders;

use App\Models\VisitSchedule;
use Illuminate\Database\Seeder;

class VisitScheduleSeeder extends Seeder
{
    public const JUMLAH_HARI = 28;

    public function run(): void
    {
        // Jadwal contoh 28 hari ke depan (cukup untuk masa demo dan penilaian),
        // museum tutup hari Senin
        $sesi = [
            ['09:00:00', '10:00:00', 30],
            ['10:30:00', '11:30:00', 30],
            ['13:00:00', '14:00:00', 20],
        ];

        for ($hari = 1; $hari <= self::JUMLAH_HARI; $hari++) {
            $tanggal = now()->addDays($hari);

            if ($tanggal->isMonday()) {
                continue;
            }

            foreach ($sesi as [$mulai, $selesai, $kapasitas]) {
                // Slot yang sudah ada di tanggal dan jam yang sama dilewati
                $sudahAda = VisitSchedule::whereDate('visit_date', $tanggal->toDateString())
                    ->where('start_time', $mulai)
                    ->exists();

                if ($sudahAda) {
                    continue;
                }

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
