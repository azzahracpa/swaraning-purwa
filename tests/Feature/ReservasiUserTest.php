<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use App\Models\VisitSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservasiUserTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    private function slot(array $atribut = []): VisitSchedule
    {
        return VisitSchedule::factory()->create(array_merge(['visit_date' => today()->addDays(2)->toDateString(), 'capacity' => 25], $atribut));
    }

    public function test_halaman_reservasi_menampilkan_slot(): void
    {
        $this->slot(['start_time' => '09:00:00', 'end_time' => '10:00:00']);

        $this->actingAs($this->user)->get(route('reservasi.index'))
            ->assertOk()->assertSee('Pilih jadwal')->assertSee('09.00–10.00', false);
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get(route('reservasi.index'))->assertRedirect('/login');
    }

    public function test_pengajuan_tersimpan_dengan_status_menunggu_dan_kode_booking(): void
    {
        $slot = $this->slot();

        $response = $this->actingAs($this->user)->post(route('reservasi.store'), [
            'schedule_id' => $slot->id,
            'visitor_name' => 'Rombongan SMP',
            'phone' => '0812 3456 7890',
            'total_visitors' => 12,
        ]);

        $reservasi = Reservation::firstOrFail();
        $response->assertRedirect(route('reservasi.show', $reservasi));
        $this->assertSame(Reservation::STATUS_PENDING, $reservasi->status);
        $this->assertSame($this->user->id, $reservasi->user_id);
        $this->assertMatchesRegularExpression('/^RSV-\d{8}-[A-Z0-9]{4}$/', $reservasi->booking_code);
    }

    public function test_jumlah_pengunjung_kosong_ditolak(): void
    {
        $slot = $this->slot();

        $this->actingAs($this->user)->post(route('reservasi.store'), [
            'schedule_id' => $slot->id,
            'visitor_name' => 'Rombongan',
            'phone' => '0812345678',
        ])->assertSessionHasErrors(['total_visitors' => 'Jumlah pengunjung wajib diisi.']);

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_jumlah_melebihi_sisa_kuota_ditolak(): void
    {
        $slot = $this->slot(['capacity' => 25]);
        Reservation::factory()->for($slot, 'schedule')->disetujui()->create(['total_visitors' => 20]);

        $this->actingAs($this->user)->post(route('reservasi.store'), [
            'schedule_id' => $slot->id, 'visitor_name' => 'A', 'phone' => '08123', 'total_visitors' => 6,
        ])->assertSessionHasErrors(['total_visitors' => 'Maksimal 5 orang untuk slot yang dipilih.']);
    }

    public function test_slot_ditutup_tidak_bisa_dipilih(): void
    {
        $slot = $this->slot(['is_available' => false]);

        $this->actingAs($this->user)->post(route('reservasi.store'), [
            'schedule_id' => $slot->id, 'visitor_name' => 'A', 'phone' => '08123', 'total_visitors' => 2,
        ])->assertSessionHasErrors('schedule_id');
    }

    public function test_pengguna_tidak_bisa_melihat_reservasi_orang_lain(): void
    {
        $milikLain = Reservation::factory()->create();

        $this->actingAs($this->user)->get(route('reservasi.show', $milikLain))->assertForbidden();
        $this->actingAs($this->user)->patch(route('reservasi.batal', $milikLain))->assertForbidden();
    }

    public function test_riwayat_hanya_berisi_reservasi_sendiri(): void
    {
        $milikSaya = Reservation::factory()->for($this->user)->create();
        $milikLain = Reservation::factory()->create();

        $this->actingAs($this->user)->get(route('reservasi.riwayat'))
            ->assertOk()->assertSee($milikSaya->booking_code)->assertDontSee($milikLain->booking_code);
    }

    public function test_pembatalan_hanya_untuk_status_menunggu(): void
    {
        $menunggu = Reservation::factory()->for($this->user)->create();
        $disetujui = Reservation::factory()->for($this->user)->disetujui()->create();

        $this->actingAs($this->user)->patch(route('reservasi.batal', $menunggu))->assertRedirect(route('reservasi.riwayat'));
        $this->assertSame(Reservation::STATUS_CANCELLED, $menunggu->refresh()->status);

        $this->actingAs($this->user)->patch(route('reservasi.batal', $disetujui))->assertSessionHas('error');
        $this->assertSame(Reservation::STATUS_APPROVED, $disetujui->refresh()->status);
    }

    public function test_halaman_detail_milik_sendiri_bisa_dibuka(): void
    {
        $reservasi = Reservation::factory()->for($this->user)->create();

        $this->actingAs($this->user)->get(route('reservasi.show', $reservasi))
            ->assertOk()->assertSee($reservasi->booking_code);
    }
}
