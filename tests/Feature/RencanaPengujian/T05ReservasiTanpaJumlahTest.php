<?php

namespace Tests\Feature\RencanaPengujian;

use App\Models\Reservation;
use App\Models\User;
use App\Models\VisitSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * T05 · Reservasi tanpa jumlah pengunjung
 * Hasil yang diharapkan: form ditolak.
 */
class T05ReservasiTanpaJumlahTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private VisitSchedule $slot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->slot = VisitSchedule::factory()->create(['capacity' => 30]);
    }

    private function kirim(array $ubah)
    {
        return $this->actingAs($this->user)->from(route('reservasi.index'))->post(route('reservasi.store'), array_merge([
            'schedule_id' => $this->slot->id,
            'visitor_name' => 'Rombongan Contoh',
            'phone' => '081234567890',
            'total_visitors' => 5,
        ], $ubah));
    }

    public function test_jumlah_kosong_ditolak_dengan_pesan(): void
    {
        $this->kirim(['total_visitors' => ''])
            ->assertRedirect(route('reservasi.index'))
            ->assertSessionHasErrors('total_visitors');

        $this->assertSame(0, Reservation::count());
    }

    public function test_field_jumlah_tidak_dikirim_sama_sekali_ditolak(): void
    {
        $this->actingAs($this->user)->post(route('reservasi.store'), [
            'schedule_id' => $this->slot->id,
            'visitor_name' => 'Rombongan Contoh',
            'phone' => '081234567890',
        ])->assertSessionHasErrors('total_visitors');

        $this->assertSame(0, Reservation::count());
    }

    /**
     * Nilai nol, negatif, pecahan, dan teks juga tidak boleh lolos.
     */
    public function test_jumlah_tidak_wajar_ditolak(): void
    {
        foreach ([0, -3, 2.5, 'lima'] as $nilai) {
            $this->kirim(['total_visitors' => $nilai])->assertSessionHasErrors('total_visitors');
        }

        $this->assertSame(0, Reservation::count());
    }

    public function test_input_lain_tetap_terisi_setelah_ditolak(): void
    {
        $this->kirim(['total_visitors' => '']);

        $this->actingAs($this->user)->get(route('reservasi.index'))->assertSee('Rombongan Contoh');
    }
}
