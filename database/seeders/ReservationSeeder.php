<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\User;
use App\Models\VisitSchedule;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $pengguna = User::where('email', 'pengguna@swaraning-purwa.test')->first();
        $pengguna2 = User::where('email', 'pengguna2@swaraning-purwa.test')->first();
        $jadwal = VisitSchedule::orderBy('visit_date')->orderBy('start_time')->get();

        $data = [
            [$pengguna, $jadwal[0], 12, Reservation::STATUS_APPROVED, 'Rombongan kelas (contoh)'],
            [$pengguna, $jadwal[2], 5, Reservation::STATUS_PENDING, null],
            [$pengguna, $jadwal[4], 8, Reservation::STATUS_REJECTED, null],
            [$pengguna, $jadwal[5], 3, Reservation::STATUS_CANCELLED, null],
            [$pengguna2, $jadwal[0], 10, Reservation::STATUS_PENDING, null],
            [$pengguna2, $jadwal[1], 25, Reservation::STATUS_PENDING, 'Kunjungan komunitas (contoh)'],
        ];

        foreach ($data as [$user, $schedule, $jumlah, $status, $catatan]) {
            $reservasi = new Reservation([
                'schedule_id' => $schedule->id,
                'visitor_name' => $user->name,
                'phone' => $user->phone,
                'total_visitors' => $jumlah,
                'notes' => $catatan,
            ]);
            $reservasi->user_id = $user->id;
            $reservasi->booking_code = Reservation::buatKodeBooking($schedule->visit_date);
            $reservasi->status = $status;
            $reservasi->save();
        }
    }
}
