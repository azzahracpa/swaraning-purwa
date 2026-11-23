<?php

namespace Database\Seeders;

use App\Models\MuseumProfile;
use Illuminate\Database\Seeder;

class MuseumProfileSeeder extends Seeder
{
    public function run(): void
    {
        // Profil hanya satu baris. Kalau sudah diisi pengelola, jangan ditimpa.
        if (MuseumProfile::query()->exists()) {
            return;
        }

        // Data contoh, wajib diverifikasi ke pihak museum sebelum dipublikasikan
        MuseumProfile::create([
            'name' => 'Museum Mpu Purwa',
            'description' => '(contoh) Museum Mpu Purwa adalah museum arkeologi milik Pemerintah Kota Malang yang menyimpan benda cagar budaya dari masa Hindu-Buddha, seperti arca, prasasti, yoni, dan lingga.',
            'history' => "(contoh) Nama Mpu Purwa diambil dari tokoh dalam kisah Ken Dedes, yaitu ayah Ken Dedes yang dikenal sebagai seorang pendeta.\n\n(contoh) Museum ini dibangun untuk menampung benda-benda purbakala yang ditemukan di wilayah Kota Malang agar terawat dan dapat dipelajari masyarakat.",
            'address' => '(contoh) Jl. Soekarno Hatta, Kota Malang, Jawa Timur',
            'phone' => '(0341) 000000',
            'email' => 'museum.contoh@swaraning-purwa.test',
            'opening_hours' => '(contoh) Selasa–Minggu, 08.00–15.00 WIB',
            'image_url' => 'images/contoh/museum-mpu-purwa.svg',
        ]);
    }
}
