<?php

namespace Tests\Feature\RencanaPengujian;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * T07 · User melihat reservasi user lain
 * Hasil yang diharapkan: tidak dapat diakses.
 */
class T07ReservasiUserLainTest extends TestCase
{
    use RefreshDatabase;

    private User $pemilik;

    private User $penyusup;

    private Reservation $reservasi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pemilik = User::factory()->create();
        $this->penyusup = User::factory()->create();
        $this->reservasi = Reservation::factory()->for($this->pemilik)->create(['visitor_name' => 'Rombongan Pemilik']);
    }

    public function test_detail_reservasi_orang_lain_mengembalikan_403(): void
    {
        $this->actingAs($this->penyusup)->get(route('reservasi.show', $this->reservasi))
            ->assertForbidden()
            ->assertDontSee('Rombongan Pemilik');
    }

    public function test_reservasi_orang_lain_tidak_bisa_dibatalkan(): void
    {
        $this->actingAs($this->penyusup)->patch(route('reservasi.batal', $this->reservasi))->assertForbidden();

        $this->assertSame(Reservation::STATUS_PENDING, $this->reservasi->refresh()->status);
    }

    public function test_riwayat_dan_dashboard_tidak_memuat_reservasi_orang_lain(): void
    {
        $this->actingAs($this->penyusup)->get(route('reservasi.riwayat'))
            ->assertOk()->assertDontSee($this->reservasi->booking_code);

        $this->actingAs($this->penyusup)->get(route('dashboard'))
            ->assertOk()->assertDontSee($this->reservasi->booking_code);
    }

    public function test_tamu_diarahkan_ke_login_dan_pemilik_tetap_bisa_membuka(): void
    {
        $this->get(route('reservasi.show', $this->reservasi))->assertRedirect(route('login'));

        $this->actingAs($this->pemilik)->get(route('reservasi.show', $this->reservasi))
            ->assertOk()->assertSee($this->reservasi->booking_code);
    }
}
