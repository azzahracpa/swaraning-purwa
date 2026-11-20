<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use App\Models\VisitSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminJadwalTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_halaman_jadwal_menampilkan_slot_pekan_ini(): void
    {
        $slot = VisitSchedule::factory()->create(['visit_date' => today()->toDateString(), 'start_time' => '13:00:00', 'end_time' => '14:00:00']);

        $this->actingAs($this->admin)->get(route('admin.jadwal.index'))
            ->assertOk()->assertSee('13.00–14.00');

        $this->actingAs($this->admin)->get(route('admin.jadwal.index', ['ubah' => $slot->id]))
            ->assertOk()->assertSee('Ubah slot');
    }

    public function test_admin_bisa_membuka_slot_baru(): void
    {
        $this->actingAs($this->admin)->post(route('admin.jadwal.store'), [
            'visit_date' => today()->addDays(3)->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'capacity' => 30,
            'is_available' => 1,
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        $this->assertDatabaseHas('visit_schedules', ['capacity' => 30, 'start_time' => '09:00:00', 'is_available' => true]);
    }

    public function test_jam_selesai_harus_setelah_jam_mulai_dan_kapasitas_minimal_satu(): void
    {
        $this->actingAs($this->admin)->post(route('admin.jadwal.store'), [
            'visit_date' => today()->addDay()->toDateString(),
            'start_time' => '14:00',
            'end_time' => '13:00',
            'capacity' => 0,
        ])->assertSessionHasErrors(['end_time', 'capacity']);
    }

    public function test_tanggal_lampau_ditolak(): void
    {
        $this->actingAs($this->admin)->post(route('admin.jadwal.store'), [
            'visit_date' => today()->subDay()->toDateString(),
            'start_time' => '09:00', 'end_time' => '10:00', 'capacity' => 10,
        ])->assertSessionHasErrors('visit_date');
    }

    public function test_slot_bentrok_ditolak(): void
    {
        $tanggal = today()->addDays(2)->toDateString();
        VisitSchedule::factory()->create(['visit_date' => $tanggal, 'start_time' => '09:00:00']);

        $this->actingAs($this->admin)->post(route('admin.jadwal.store'), [
            'visit_date' => $tanggal, 'start_time' => '09:00', 'end_time' => '10:00', 'capacity' => 10,
        ])->assertSessionHasErrors('start_time');
    }

    public function test_kapasitas_tidak_boleh_kurang_dari_yang_disetujui(): void
    {
        $slot = VisitSchedule::factory()->create(['visit_date' => today()->addDay()->toDateString(), 'capacity' => 30]);
        Reservation::factory()->for($slot, 'schedule')->disetujui()->create(['total_visitors' => 12]);

        $this->actingAs($this->admin)->put(route('admin.jadwal.update', $slot), [
            'visit_date' => $slot->visit_date->toDateString(), 'start_time' => '09:00', 'end_time' => '10:00', 'capacity' => 10,
        ])->assertSessionHasErrors('capacity');

        $this->assertSame(30, $slot->refresh()->capacity);
    }

    public function test_tutup_dan_buka_slot(): void
    {
        $slot = VisitSchedule::factory()->create(['is_available' => true]);

        $this->actingAs($this->admin)->patch(route('admin.jadwal.status', $slot));
        $this->assertFalse($slot->refresh()->is_available);

        $this->actingAs($this->admin)->patch(route('admin.jadwal.status', $slot));
        $this->assertTrue($slot->refresh()->is_available);
    }

    public function test_slot_dengan_reservasi_tidak_bisa_dihapus(): void
    {
        $slot = VisitSchedule::factory()->create();
        Reservation::factory()->for($slot, 'schedule')->create();

        $this->actingAs($this->admin)->delete(route('admin.jadwal.destroy', $slot))->assertSessionHas('error');
        $this->assertModelExists($slot);
    }

    public function test_slot_kosong_bisa_dihapus(): void
    {
        $slot = VisitSchedule::factory()->create();

        $this->actingAs($this->admin)->delete(route('admin.jadwal.destroy', $slot))->assertSessionHas('success');
        $this->assertModelMissing($slot);
    }

    public function test_status_penuh_saat_kapasitas_habis(): void
    {
        $slot = VisitSchedule::factory()->create(['capacity' => 5]);
        Reservation::factory()->for($slot, 'schedule')->disetujui()->create(['total_visitors' => 5]);

        $this->assertSame('penuh', VisitSchedule::denganJumlahPengunjung()->find($slot->id)->statusSlot());
    }
}
