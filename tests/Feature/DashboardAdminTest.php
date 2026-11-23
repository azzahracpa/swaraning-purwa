<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Reservation;
use App\Models\User;
use App\Models\VisitSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_menampilkan_statistik_dan_reservasi_terbaru(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(3)->create();
        Collection::factory()->count(2)->create();
        $slot = VisitSchedule::factory()->create(['visit_date' => today()->toDateString(), 'capacity' => 30]);
        $menunggu = Reservation::factory()->for($slot, 'schedule')->create(['total_visitors' => 5]);
        Reservation::factory()->for($slot, 'schedule')->disetujui()->create(['total_visitors' => 12]);

        $respons = $this->actingAs($admin)->get('/admin')->assertOk();

        $respons->assertSee('Reservasi menunggu')
            ->assertSee($menunggu->booking_code)
            ->assertSee('Pengunjung 7 hari ke depan')
            ->assertSee('Akses cepat');

        // 3 pengguna dibuat di tes + 2 pengguna dari factory reservasi = 5 akun berperan user
        $this->assertSame(5, $respons->viewData('statistik')['pengguna']);
        $this->assertSame(2, $respons->viewData('statistik')['koleksi']);
        $this->assertSame(1, $respons->viewData('statistik')['menunggu']);
        $this->assertSame(12, $respons->viewData('grafik')->first()['disetujui']);
        $this->assertSame(5, $respons->viewData('grafik')->first()['menunggu']);
    }

    public function test_dashboard_tetap_tampil_tanpa_data(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/admin')
            ->assertOk()->assertSee('Belum ada reservasi');
    }
}
