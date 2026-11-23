<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use App\Models\VisitSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReservasiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_daftar_menampilkan_reservasi_menunggu_dan_detail(): void
    {
        $reservasi = Reservation::factory()->create(['visitor_name' => 'Budi Santoso']);

        $this->actingAs($this->admin)->get(route('admin.reservasi.index', ['pilih' => $reservasi->id]))
            ->assertOk()->assertSee($reservasi->booking_code)->assertSee('Detail pengajuan')->assertSee('Budi Santoso');
    }

    public function test_pencarian_kode_booking(): void
    {
        $dicari = Reservation::factory()->create();
        $lain = Reservation::factory()->create();

        $this->actingAs($this->admin)->get(route('admin.reservasi.index', ['cari' => $dicari->booking_code]))
            ->assertOk()->assertSee($dicari->booking_code)->assertDontSee($lain->booking_code);
    }

    public function test_setujui_saat_kapasitas_cukup(): void
    {
        $slot = VisitSchedule::factory()->create(['capacity' => 20]);
        $reservasi = Reservation::factory()->for($slot, 'schedule')->create(['total_visitors' => 8]);

        $this->actingAs($this->admin)->patch(route('admin.reservasi.setujui', $reservasi))->assertSessionHas('success');

        $this->assertSame(Reservation::STATUS_APPROVED, $reservasi->refresh()->status);
        $this->assertSame(12, $slot->sisaKuota());
    }

    public function test_persetujuan_melebihi_kapasitas_ditolak_sistem(): void
    {
        $slot = VisitSchedule::factory()->create(['capacity' => 25]);
        Reservation::factory()->for($slot, 'schedule')->disetujui()->create(['total_visitors' => 21]);
        $reservasi = Reservation::factory()->for($slot, 'schedule')->create(['total_visitors' => 8]);

        $this->actingAs($this->admin)->patch(route('admin.reservasi.setujui', $reservasi))
            ->assertSessionHas('error', 'Kapasitas slot tidak cukup. Sisa 4 kursi, pengajuan ini 8 orang.');

        $this->assertSame(Reservation::STATUS_PENDING, $reservasi->refresh()->status);
        $this->assertSame(4, $slot->sisaKuota());
    }

    public function test_dua_pengajuan_yang_jumlahnya_melebihi_kapasitas_hanya_satu_disetujui(): void
    {
        $slot = VisitSchedule::factory()->create(['capacity' => 10]);
        $a = Reservation::factory()->for($slot, 'schedule')->create(['total_visitors' => 6]);
        $b = Reservation::factory()->for($slot, 'schedule')->create(['total_visitors' => 6]);

        $this->actingAs($this->admin)->patch(route('admin.reservasi.setujui', $a));
        $this->actingAs($this->admin)->patch(route('admin.reservasi.setujui', $b))->assertSessionHas('error');

        $this->assertSame(Reservation::STATUS_APPROVED, $a->refresh()->status);
        $this->assertSame(Reservation::STATUS_PENDING, $b->refresh()->status);
        $this->assertLessThanOrEqual(10, $slot->jumlahDisetujui());
    }

    public function test_tolak_reservasi_menunggu(): void
    {
        $reservasi = Reservation::factory()->create();

        $this->actingAs($this->admin)->patch(route('admin.reservasi.tolak', $reservasi))->assertSessionHas('success');
        $this->assertSame(Reservation::STATUS_REJECTED, $reservasi->refresh()->status);
    }

    public function test_reservasi_yang_sudah_diputuskan_tidak_bisa_diubah_lagi(): void
    {
        $dibatalkan = Reservation::factory()->create(['status' => Reservation::STATUS_CANCELLED]);

        $this->actingAs($this->admin)->patch(route('admin.reservasi.setujui', $dibatalkan))->assertSessionHas('error');
        $this->actingAs($this->admin)->patch(route('admin.reservasi.tolak', $dibatalkan))->assertSessionHas('error');
        $this->assertSame(Reservation::STATUS_CANCELLED, $dibatalkan->refresh()->status);
    }

    public function test_pengguna_biasa_tidak_bisa_menyetujui(): void
    {
        $reservasi = Reservation::factory()->create();

        $this->actingAs(User::factory()->create())->patch(route('admin.reservasi.setujui', $reservasi))->assertForbidden();
        $this->assertSame(Reservation::STATUS_PENDING, $reservasi->refresh()->status);
    }
}
