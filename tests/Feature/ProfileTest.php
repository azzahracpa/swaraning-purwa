<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_edit_profil_bisa_dibuka(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/profil')
            ->assertOk()
            ->assertSee('Edit')
            ->assertSee($user->email);
    }

    public function test_tamu_tidak_bisa_membuka_edit_profil(): void
    {
        $this->get('/profil')->assertRedirect('/login');
    }

    public function test_data_profil_bisa_diperbarui(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profil', [
            'name' => 'Nama Baru',
            'email' => 'baru@contoh.test',
            'phone' => '0812 3456 7890',
        ])->assertSessionHasNoErrors()
            ->assertRedirect('/profil')
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('Nama Baru', $user->name);
        $this->assertSame('baru@contoh.test', $user->email);
        $this->assertSame('0812 3456 7890', $user->phone);
    }

    public function test_kata_sandi_tidak_berubah_jika_dikosongkan(): void
    {
        $user = User::factory()->create();
        $hashLama = $user->password;

        $this->actingAs($user)->patch('/profil', [
            'name' => $user->name,
            'email' => $user->email,
        ])->assertSessionHasNoErrors();

        $this->assertSame($hashLama, $user->refresh()->password);
    }

    public function test_kata_sandi_bisa_diganti_dengan_kata_sandi_lama_yang_benar(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profil', [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'password',
            'password' => 'sandibaru123',
            'password_confirmation' => 'sandibaru123',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('sandibaru123', $user->refresh()->password));
    }

    public function test_kata_sandi_lama_salah_ditolak(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from('/profil')->patch('/profil', [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'salah-sekali',
            'password' => 'sandibaru123',
            'password_confirmation' => 'sandibaru123',
        ])->assertSessionHasErrors('current_password')
            ->assertRedirect('/profil');

        $this->assertTrue(Hash::check('password', $user->refresh()->password));
    }

    public function test_email_milik_akun_lain_ditolak(): void
    {
        User::factory()->create(['email' => 'dipakai@contoh.test']);
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profil', [
            'name' => $user->name,
            'email' => 'dipakai@contoh.test',
        ])->assertSessionHasErrors('email');
    }

    public function test_role_tidak_bisa_diubah_lewat_edit_profil(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profil', [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'admin',
        ]);

        $this->assertSame(User::ROLE_USER, $user->refresh()->role);
    }
}
