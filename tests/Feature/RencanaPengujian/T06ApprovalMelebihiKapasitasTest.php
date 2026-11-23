<?php

namespace Tests\Feature\RencanaPengujian;

use App\Models\Reservation;
use App\Models\User;
use App\Models\VisitSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * T06 · Approval melebihi kapasitas
 * Hasil yang diharapkan: sistem menolak persetujuan.
 */
class T06ApprovalMelebihiKapasitasTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_persetujuan_yang_melebihi_sisa_kursi_ditolak(): void
    {
        $slot = VisitSchedule::factory()->create(['capacity' => 20]);
        Reservation::factory()->for($slot, 'schedule')->disetujui()->create(['total_visitors' => 15]);
        $pengajuan = Reservation::factory()->for($slot, 'schedule')->create(['total_visitors' => 6]);

        $this->actingAs($this->admin)->patch(route('admin.reservasi.setujui', $pengajuan))
            ->assertRedirect()
            ->assertSessionHas('error', 'Kapasitas slot tidak cukup. Sisa 5 kursi, pengajuan ini 6 orang.');

        $this->assertSame(Reservation::STATUS_PENDING, $pengajuan->refresh()->status);
        $this->assertSame(5, $slot->sisaKuota());
    }

    public function test_tepat_sama_dengan_sisa_kursi_boleh_disetujui(): void
    {
        $slot = VisitSchedule::factory()->create(['capacity' => 20]);
        Reservation::factory()->for($slot, 'schedule')->disetujui()->create(['total_visitors' => 15]);
        $pengajuan = Reservation::factory()->for($slot, 'schedule')->create(['total_visitors' => 5]);

        $this->actingAs($this->admin)->patch(route('admin.reservasi.setujui', $pengajuan))->assertSessionHas('success');

        $this->assertSame(Reservation::STATUS_APPROVED, $pengajuan->refresh()->status);
        $this->assertSame(0, $slot->sisaKuota());
    }

    public function test_total_disetujui_tidak_pernah_melewati_kapasitas(): void
    {
        $slot = VisitSchedule::factory()->create(['capacity' => 25]);
        $pengajuan = Reservation::factory()->count(4)->for($slot, 'schedule')->create(['total_visitors' => 10]);

        foreach ($pengajuan as $r) {
            $this->actingAs($this->admin)->patch(route('admin.reservasi.setujui', $r));
        }

        $disetujui = $slot->reservations()->where('status', Reservation::STATUS_APPROVED);
        $this->assertSame(2, $disetujui->count());
        $this->assertLessThanOrEqual(25, (int) $disetujui->sum('total_visitors'));
    }

    public function test_reservasi_menunggu_tidak_dihitung_sebagai_kursi_terpakai(): void
    {
        $slot = VisitSchedule::factory()->create(['capacity' => 10]);
        Reservation::factory()->count(3)->for($slot, 'schedule')->create(['total_visitors' => 8]);
        $pengajuan = Reservation::factory()->for($slot, 'schedule')->create(['total_visitors' => 10]);

        $this->actingAs($this->admin)->patch(route('admin.reservasi.setujui', $pengajuan))->assertSessionHas('success');
    }
}
