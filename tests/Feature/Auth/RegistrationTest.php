<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_daftar_bisa_dibuka(): void
    {
        $this->get('/register')->assertOk()->assertSee('Daftar akun');
    }

    public function test_pengguna_baru_bisa_mendaftar(): void
    {
        $response = $this->post('/register', [
            'name' => 'Pengguna Uji',
            'email' => 'Uji@Contoh.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'uji@contoh.test')->first();
        $this->assertNotNull($user, 'Email harus disimpan dalam huruf kecil.');
        $this->assertSame(User::ROLE_USER, $user->role);
        $this->assertTrue(Hash::check('rahasia123', $user->password));
        $this->assertNotSame('rahasia123', $user->password);
    }

    public function test_email_yang_sudah_terdaftar_ditolak(): void
    {
        User::factory()->create(['email' => 'sama@contoh.test']);

        $this->from('/register')->post('/register', [
            'name' => 'Pengguna Kedua',
            'email' => 'sama@contoh.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect('/register')
            ->assertSessionHasErrors(['email' => 'Email sudah terdaftar. Gunakan email lain atau masuk.']);

        $this->assertGuest();
        $this->assertSame(1, User::where('email', 'sama@contoh.test')->count());
    }

    public function test_kata_sandi_harus_kombinasi_huruf_dan_angka(): void
    {
        $this->from('/register')->post('/register', [
            'name' => 'Pengguna Uji',
            'email' => 'uji@contoh.test',
            'password' => 'hanyahuruf',
            'password_confirmation' => 'hanyahuruf',
        ])->assertSessionHasErrors('password');

        $this->assertGuest();
    }
}
