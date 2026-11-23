<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Password akun contoh diambil dari env supaya di produksi bisa diganti
        // tanpa mengubah kode. Default "password" hanya untuk laptop.
        $passwordAdmin = (string) env('SEED_ADMIN_PASSWORD', 'password');
        $passwordUser = (string) env('SEED_USER_PASSWORD', 'password');

        if (app()->isProduction()) {
            $this->pastikanSandiKuat('SEED_ADMIN_PASSWORD', $passwordAdmin);
            $this->pastikanSandiKuat('SEED_USER_PASSWORD', $passwordUser);
        }

        $this->akun('admin@swaraning-purwa.test', 'Admin Museum', '081200000001', $passwordAdmin, User::ROLE_ADMIN);
        $this->akun('pengguna@swaraning-purwa.test', 'Nama Pengguna', '081200000002', $passwordUser);
        $this->akun('pengguna2@swaraning-purwa.test', 'Pengguna Kedua', '081200000003', $passwordUser);
    }

    /**
     * Buat akun bila belum ada. Akun yang sudah ada tidak diubah, jadi
     * seeder aman dijalankan ulang dan tidak menimpa password yang sudah diganti.
     */
    private function akun(string $email, string $nama, string $telepon, string $password, string $role = User::ROLE_USER): void
    {
        $user = User::firstOrNew(['email' => $email]);

        if ($user->exists) {
            return;
        }

        $user->fill(['name' => $nama, 'phone' => $telepon, 'password' => $password]);
        $user->role = $role;
        $user->save();
    }

    /**
     * Di produksi, akun contoh tidak boleh memakai password bawaan.
     */
    private function pastikanSandiKuat(string $variabel, string $nilai): void
    {
        if ($nilai === 'password' || strlen($nilai) < 10) {
            throw new \RuntimeException("Isi {$variabel} dengan kata sandi minimal 10 karakter sebelum menjalankan seeder di produksi.");
        }
    }
}
