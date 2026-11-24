<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VisitSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerbaikanProduksiTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_jadwal_tidak_menjalankan_query_per_slot(): void
    {
        $admin = User::factory()->admin()->create();
        // 21 slot pekan ini tanpa reservasi: dulu setiap slot memicu query tambahan
        foreach (range(0, 6) as $hari) {
            foreach (['09:00', '10:30', '13:00'] as $jam) {
                VisitSchedule::factory()->create([
                    'visit_date' => today()->startOfWeek()->addDays($hari),
                    'start_time' => $jam,
                    'end_time' => date('H:i', strtotime($jam) + 3600),
                ]);
            }
        }

        DB::enableQueryLog();
        $this->actingAs($admin)->get('/admin/jadwal')->assertOk();

        $this->assertLessThan(15, count(DB::getQueryLog()));
    }

    public function test_admin_yang_membuka_dashboard_pengguna_dialihkan_ke_ruang_pengelola(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('info');
    }

    public function test_aksi_pengguna_oleh_admin_tetap_ditolak(): void
    {
        $admin = User::factory()->admin()->create();
        $slot = VisitSchedule::factory()->create(['visit_date' => today()->addDays(2)]);

        $this->actingAs($admin)->post('/reservasi', [
            'schedule_id' => $slot->id, 'visitor_name' => 'Admin', 'phone' => '0812', 'total_visitors' => 1,
        ])->assertForbidden();
    }
}
