<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_diarahkan_ke_login_saat_membuka_halaman_admin(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_user_biasa_ditolak_di_halaman_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_bisa_membuka_dashboard_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('Ruang pengelola');
    }

    public function test_user_bisa_membuka_dashboard_akun(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee($user->name);
    }

    public function test_role_tidak_bisa_diisi_lewat_form_registrasi(): void
    {
        $this->post('/register', [
            'name' => 'Penyusup',
            'email' => 'penyusup@contoh.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'admin',
        ]);

        $this->assertSame(User::ROLE_USER, User::where('email', 'penyusup@contoh.test')->value('role'));
    }
}
