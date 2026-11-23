<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeederAkunTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_akun_aman_dijalankan_ulang(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(UserSeeder::class);

        $this->assertSame(3, User::count());
        $this->assertSame(1, User::where('role', User::ROLE_ADMIN)->count());
    }

    public function test_password_yang_sudah_diganti_tidak_ditimpa(): void
    {
        $this->seed(UserSeeder::class);
        User::where('email', 'admin@swaraning-purwa.test')->first()->update(['password' => 'SandiBaru2026']);

        $this->seed(UserSeeder::class);

        $this->assertTrue(Hash::check('SandiBaru2026', User::where('email', 'admin@swaraning-purwa.test')->value('password')));
    }

    public function test_produksi_menolak_password_bawaan(): void
    {
        $this->app['env'] = 'production';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('SEED_ADMIN_PASSWORD');

        // Dipanggil langsung: perintah db:seed di produksi meminta konfirmasi dulu
        $this->app->make(UserSeeder::class)->run();
    }
}
