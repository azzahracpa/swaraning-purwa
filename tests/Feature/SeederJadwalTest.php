<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\VisitSchedule;
use Database\Seeders\ReservationSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\VisitScheduleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederJadwalTest extends TestCase
{
    use RefreshDatabase;

    private function seedJadwal(): void
    {
        $this->seed([VisitScheduleSeeder::class, ReservationSeeder::class]);
    }

    public function test_jadwal_contoh_empat_pekan_tanpa_hari_senin(): void
    {
        $this->seed(UserSeeder::class);
        $this->seedJadwal();

        $tanggal = VisitSchedule::pluck('visit_date');
        $this->assertTrue($tanggal->every(fn ($t) => ! $t->isMonday()), 'Museum tutup hari Senin.');
        $this->assertTrue($tanggal->max()->isSameDay(today()->addDays(VisitScheduleSeeder::JUMLAH_HARI))
            || $tanggal->max()->isSameDay(today()->addDays(VisitScheduleSeeder::JUMLAH_HARI - 1)));
        $this->assertTrue($tanggal->min()->isAfter(today()));
    }

    public function test_seeder_jadwal_dan_reservasi_aman_dijalankan_ulang(): void
    {
        $this->seed(UserSeeder::class);
        $this->seedJadwal();
        $jumlah = [VisitSchedule::count(), Reservation::count()];

        $this->seedJadwal();

        $this->assertSame($jumlah, [VisitSchedule::count(), Reservation::count()]);
        $this->assertSame(6, Reservation::count());
    }

    public function test_reservasi_contoh_disetujui_tidak_melebihi_kapasitas(): void
    {
        $this->seed(UserSeeder::class);
        $this->seedJadwal();

        foreach (VisitSchedule::denganJumlahPengunjung()->get() as $slot) {
            $this->assertLessThanOrEqual($slot->capacity, (int) $slot->jumlah_disetujui);
        }
    }
}
