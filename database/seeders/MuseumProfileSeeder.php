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

        // Deskripsi dan sejarah disusun dari Kitab Pararaton dan keterangan koleksi.
        // Alamat, telepon, dan jam buka dari liputan Kompas dan Detik (2023) serta
        // direktori museum.co.id. Email resmi belum diketahui, jadi dikosongkan.
        MuseumProfile::create([
            'name' => 'Museum Mpu Purwa',
            'description' => 'Museum Mpu Purwa adalah museum arkeologi milik Pemerintah Kota Malang yang menyimpan benda cagar budaya dari masa Hindu-Buddha, seperti arca, prasasti, yoni, fragmen bangunan candi, dan alat batu prasejarah.',
            'history' => "Nama Mpu Purwa diambil dari tokoh dalam Kitab Pararaton, yaitu seorang pendeta Buddha dari Desa Panawijen yang merupakan ayah Ken Dedes. Panawijen diyakini berada di sekitar Kelurahan Polowijen, Kota Malang.\n\nMuseum ini dibangun untuk menampung benda cagar budaya yang ditemukan di wilayah Kota Malang agar terawat dan dapat dipelajari masyarakat. Koleksinya meliputi arca dewa dan bodhisatwa, prasasti beraksara Jawa Kuno, yoni, fragmen bangunan candi, alat batu dari masa prasejarah, serta diorama sejarah Kerajaan Singhasari.",
            'address' => 'Jl. Soekarno Hatta No. 210 (Perumahan Griya Shanta), Mojolangu, Lowokwaru, Kota Malang, Jawa Timur 65141',
            'phone' => '(0341) 404515',
            'email' => null,
            'opening_hours' => 'Selasa–Minggu 08.30–15.00 WIB (Jumat sampai 14.00), Senin tutup',
            // Foto: Muhamad Abdu (CC BY-SA 4.0), Wikimedia Commons
            'image_url' => 'images/museum/museum-tampak-depan.jpg',
        ]);
    }
}
