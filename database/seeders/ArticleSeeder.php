<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $artikel = [
            [
                'title' => 'Mengenal Museum Mpu Purwa (contoh)',
                'content' => "(contoh) Museum Mpu Purwa menyimpan berbagai benda cagar budaya yang ditemukan di sekitar Kota Malang.\n\nArtikel ini adalah data contoh untuk menguji tampilan daftar dan detail artikel. Isi perlu diganti dengan tulisan yang sudah diverifikasi.",
                'image_url' => 'images/contoh/artikel-mengenal-museum.svg',
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'Cara Membaca Prasasti untuk Pemula (contoh)',
                'content' => "(contoh) Prasasti biasanya diawali dengan penanggalan, lalu nama raja atau pejabat, kemudian isi keputusan.\n\nPerhatikan bentuk aksara dan bahasa yang dipakai. Data ini masih contoh.",
                'image_url' => 'images/contoh/artikel-membaca-prasasti.svg',
                'published_at' => now()->subDays(6),
            ],
            [
                'title' => 'Ciri Arca dari Masa Klasik (contoh)',
                'content' => "(contoh) Arca dari masa klasik dapat dikenali dari sikap tubuh, atribut yang dipegang, dan hiasan yang dipakai.\n\nTulisan ini masih data contoh.",
                'image_url' => 'images/contoh/artikel-arca-masa-singhasari.svg',
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Tips Berkunjung Bersama Rombongan Sekolah (contoh)',
                'content' => "(contoh) Ajukan reservasi lebih awal, datang tepat waktu, dan jaga kebersihan selama di ruang pamer.\n\nArtikel ini masih draf contoh.",
                'image_url' => 'images/contoh/artikel-tips-kunjungan.svg',
                // Draf: belum terbit, tidak tampil di halaman publik
                'published_at' => null,
            ],
        ];

        foreach ($artikel as $data) {
            $article = new Article($data);
            $article->author_id = $admin->id;
            $article->save();
        }
    }
}
