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

        // Isi artikel: paragraf dipisah baris kosong, baris "## " menjadi subjudul.
        // Semua teks adalah contoh dan perlu diganti tulisan yang sudah diverifikasi.
        $artikel = [
            [
                'title' => 'Sejarah Museum Mpu Purwa (contoh)',
                'content' => "(contoh) Museum Mpu Purwa menyimpan benda cagar budaya yang ditemukan di sekitar Kota Malang, mulai dari arca, prasasti, hingga yoni dan lingga.\n\nArtikel ini adalah data contoh untuk menguji tampilan daftar dan halaman baca artikel. Jarak antarparagraf dibuat lega supaya tulisan panjang tetap nyaman dibaca.\n\n## Asal nama museum\n\n(contoh) Nama Mpu Purwa diambil dari tokoh dalam kisah Ken Dedes. Bagian ini menunggu sumber resmi dari pengelola museum.\n\nParagraf penutup artikel contoh.",
                'image_url' => 'images/contoh/artikel-mengenal-museum.svg',
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Mengenal Arca di Museum Mpu Purwa (contoh)',
                'content' => "(contoh) Arca dari masa klasik dapat dikenali dari sikap tubuh, atribut yang dipegang, dan hiasan yang dipakai.\n\n## Arca Ganesha\n\n(contoh) Ganesha biasanya digambarkan duduk dengan perut buncit dan memegang mangkuk.\n\nTulisan ini masih data contoh.",
                'image_url' => 'images/contoh/artikel-arca-masa-singhasari.svg',
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Pentingnya Pelestarian Cagar Budaya (contoh)',
                'content' => "(contoh) Benda cagar budaya adalah sumber belajar yang tidak bisa diganti. Sekali rusak, informasi di dalamnya ikut hilang.\n\nPengunjung dapat ikut menjaga dengan tidak menyentuh koleksi dan mengikuti arahan petugas.",
                'image_url' => 'images/contoh/artikel-membaca-prasasti.svg',
                'published_at' => now()->subDays(8),
            ],
            [
                'title' => 'Cara Membaca Prasasti untuk Pemula (contoh)',
                'content' => "(contoh) Prasasti biasanya diawali dengan penanggalan, lalu nama raja atau pejabat, kemudian isi keputusan.\n\n## Perhatikan aksaranya\n\n(contoh) Bentuk aksara dan bahasa yang dipakai membantu memperkirakan masa pembuatan prasasti.",
                'image_url' => 'images/contoh/artikel-membaca-prasasti.svg',
                'published_at' => now()->subDays(12),
            ],
            [
                'title' => 'Yoni dan Lingga dalam Pemujaan Siwa (contoh)',
                'content' => "(contoh) Yoni dan lingga adalah pasangan benda pemujaan dari masa Hindu.\n\nArtikel ini masih data contoh untuk menguji paginasi.",
                'image_url' => 'images/contoh/artikel-arca-masa-singhasari.svg',
                'published_at' => now()->subDays(16),
            ],
            [
                'title' => 'Gerabah Terakota dari Masa ke Masa (contoh)',
                'content' => "(contoh) Gerabah dibuat dari tanah liat yang dibakar dan dipakai sebagai wadah sehari-hari.\n\nArtikel ini masih data contoh untuk menguji paginasi.",
                'image_url' => 'images/contoh/artikel-mengenal-museum.svg',
                'published_at' => now()->subDays(20),
            ],
            [
                'title' => 'Relief sebagai Media Bercerita (contoh)',
                'content' => "(contoh) Relief pada panil batu menyampaikan cerita lewat gambar berurutan.\n\nArtikel ini masih data contoh untuk menguji paginasi.",
                'image_url' => 'images/contoh/artikel-arca-masa-singhasari.svg',
                'published_at' => now()->subDays(25),
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
