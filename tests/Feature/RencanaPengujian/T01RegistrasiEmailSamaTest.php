<?php

namespace Tests\Feature\RencanaPengujian;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * T01 · Registrasi dengan email sama
 * Hasil yang diharapkan: ditolak dengan pesan validasi.
 */
class T01RegistrasiEmailSamaTest extends TestCase
{
    use RefreshDatabase;

    private function daftar(string $email)
    {
        return $this->from(route('register'))->post(route('register'), [
            'name' => 'Pengunjung Contoh',
            'email' => $email,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);
    }

    public function test_email_yang_sama_persis_ditolak_dengan_pesan(): void
    {
        User::factory()->create(['email' => 'budi@contoh.test']);

        $this->daftar('budi@contoh.test')
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['email' => 'Email sudah terdaftar. Gunakan email lain atau masuk.']);

        $this->assertGuest();
        $this->assertSame(1, User::where('email', 'budi@contoh.test')->count());
    }

    public function test_email_sama_dengan_huruf_besar_dan_spasi_tetap_ditolak(): void
    {
        User::factory()->create(['email' => 'budi@contoh.test']);

        $this->daftar('  BUDI@Contoh.test ')->assertSessionHasErrors('email');

        $this->assertSame(1, User::count());
    }

    public function test_input_nama_tetap_terisi_setelah_ditolak(): void
    {
        User::factory()->create(['email' => 'budi@contoh.test']);

        $this->daftar('budi@contoh.test');

        $this->get(route('register'))->assertSee('Pengunjung Contoh')->assertSee('Email sudah terdaftar');
    }

    public function test_login_tidak_peka_huruf_besar_pada_email(): void
    {
        User::factory()->create(['email' => 'budi@contoh.test']);

        $this->post(route('login'), ['email' => ' Budi@Contoh.TEST', 'password' => 'password'])
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }
}
