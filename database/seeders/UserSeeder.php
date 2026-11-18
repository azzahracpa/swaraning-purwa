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
        $passwordAdmin = env('SEED_ADMIN_PASSWORD', 'password');
        $passwordUser = env('SEED_USER_PASSWORD', 'password');

        $admin = new User([
            'name' => 'Admin Museum',
            'email' => 'admin@swaraning-purwa.test',
            'password' => $passwordAdmin,
            'phone' => '081200000001',
        ]);
        $admin->role = User::ROLE_ADMIN;
        $admin->save();

        User::create([
            'name' => 'Nama Pengguna',
            'email' => 'pengguna@swaraning-purwa.test',
            'password' => $passwordUser,
            'phone' => '081200000002',
        ]);

        User::create([
            'name' => 'Pengguna Kedua',
            'email' => 'pengguna2@swaraning-purwa.test',
            'password' => $passwordUser,
            'phone' => '081200000003',
        ]);
    }
}
