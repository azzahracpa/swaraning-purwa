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
        // Isi disusun dari keterangan foto koleksi di Wikimedia Commons
        // (kategori "Museum Mpu Purwa"). Sampul memakai foto di public/images/museum/.
        $artikel = [
            [
                'title' => 'Mengenal Museum Mpu Purwa',
                'content' => "Museum Mpu Purwa adalah museum arkeologi milik Pemerintah Kota Malang. Museum ini menyimpan benda cagar budaya yang ditemukan di wilayah Malang, mulai dari arca, prasasti, yoni, fragmen bangunan candi, hingga alat batu dari masa prasejarah.\n\n## Asal nama museum\n\nNama Mpu Purwa diambil dari tokoh dalam kisah Ken Dedes, yaitu seorang pendeta yang merupakan ayah Ken Dedes.\n\n## Isi ruang pamer\n\nLantai dua museum berisi arca, prasasti, benda-benda pemujaan, fragmen, serta diorama sejarah Kerajaan Singhasari dan Majapahit.",
                'image_url' => 'images/museum/museum-tampak-depan.jpg',
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Empat Arca Ganesha di Museum Mpu Purwa',
                'content' => "Ganesha, putra Dewa Siwa, dipuja sebagai dewa ilmu pengetahuan dan penghalau rintangan. Museum Mpu Purwa menyimpan beberapa arca Ganesha yang menarik untuk dibandingkan.\n\n## Sama-sama duduk seperti bayi\n\nHampir semua arca Ganesha di museum ini digambarkan duduk seperti bayi dengan perut buncit. Salah satunya memakai mahkota dari rambut yang disanggul (jatamakuta).\n\n## Perbedaan yang bisa diamati\n\nArca Ganesha Bunulrejo duduk di atas bantalan teratai merah dan ditemukan di Kampung Beji, Bunulrejo. Ada pula Ganesha yang duduk di atas lapik bulat dengan dua tangan depan memegang mangkuk, serta Ganesha bertubuh tambun dengan raut wajah garang.",
                'image_url' => 'images/museum/arca-ganesha.jpg',
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Prasasti Dinoyo II dan Prasasti Muncang',
                'content' => "Prasasti adalah sumber tertulis yang membantu kita memahami peristiwa pada masa lalu. Museum Mpu Purwa menyimpan dua prasasti penting dari wilayah Malang.\n\n## Prasasti Dinoyo II\n\nBatu prasasti ini ditemukan pada tahun 1985 di sekitar pertigaan Jalan M.T. Haryono dan Jalan Gajayana, Kota Malang. Tulisannya memakai aksara dan bahasa Jawa Kuno.\n\n## Prasasti Muncang\n\nPrasasti Muncang berasal dari masa pemerintahan Mpu Sindok. Isinya perintah raja melalui Rakai Kanuluhan untuk menetapkan sebidang tanah di Desa Muncang, wilayah Hujung, sebagai tanah dengan hak khusus.",
                'image_url' => 'images/museum/prasasti-dinoyo-2.jpg',
                'published_at' => now()->subDays(8),
            ],
            [
                'title' => 'Kisah Singhasari dalam Diorama Lantai Dua',
                'content' => "Selain benda cagar budaya, Museum Mpu Purwa menampilkan diorama yang menceritakan sejarah Kerajaan Singhasari.\n\n## Pembangunan Candi Jago\n\nDiorama ini menggambarkan pembangunan Candi Jago, peninggalan Singhasari yang berada di Kecamatan Tumpang, Kabupaten Malang.\n\n## Ekspedisi Pamalayu\n\nDiorama berikutnya menggambarkan perluasan pengaruh Singhasari ke kerajaan-kerajaan di tanah Melayu.\n\n## Dari Singhasari ke Majapahit\n\nDiorama di lantai dua menceritakan keruntuhan Singhasari dan berdirinya Kerajaan Majapahit.",
                'image_url' => 'images/museum/diorama-singhasari-majapahit.jpg',
                'published_at' => now()->subDays(12),
            ],
            [
                'title' => 'Jejak Agama Buddha: Arca Awalokiteswara dan Manjusri',
                'content' => "Koleksi Museum Mpu Purwa tidak hanya berasal dari agama Hindu. Ada pula arca bodhisatwa dari agama Buddha Mahayana.\n\n## Awalokiteswara\n\nArca Awalokiteswara berdiri dengan tubuh melengkung tiga (tribhangga). Lingkaran di belakang kepalanya berhias motif lidah api, dan tangan kirinya memegang tangkai teratai.\n\n## Manjusri\n\nManjusri dipuja sebagai bodhisatwa yang memiliki kebijaksanaan tertinggi.",
                'image_url' => 'images/museum/arca-awalokiteswara.jpg',
                'published_at' => now()->subDays(16),
            ],
            [
                'title' => 'Alat Batu dari Masa Prasejarah',
                'content' => "Sebelum mengenal tulisan, manusia sudah membuat berbagai alat dari batu. Beberapa di antaranya dipamerkan di Museum Mpu Purwa.\n\n## Kapak genggam\n\nKapak genggam dipakai langsung dengan digenggam, tanpa tangkai.\n\n## Batu pipisan dan batu pelor\n\nBatu pipisan dipakai untuk menghaluskan ramuan herbal sebagai obat. Batu pelor adalah peninggalan budaya megalitik yang fungsinya masih diteliti.",
                'image_url' => 'images/museum/batu-pipisan.jpg',
                'published_at' => now()->subDays(20),
            ],
            [
                'title' => 'Hiasan Candi yang Tersimpan di Museum',
                'content' => "Museum Mpu Purwa juga menyimpan bagian-bagian bangunan candi yang sudah terlepas.\n\n## Kemuncak dan pipi tangga\n\nFragmen kemuncak dulunya menghiasi atap candi, seperti yang dapat dilihat di Candi Badut. Fragmen pipi tangga berasal dari sisi tangga candi dan berhias tumpal di bagian puncaknya.\n\n## Kuncup teratai\n\nOrnamen kuncup teratai menghiasi bangunan suci Hindu dan Buddha sebagai lambang penciptaan dewa-dewi di kahyangan.",
                'image_url' => 'images/museum/fragmen-kemuncak-candi.jpg',
                'published_at' => now()->subDays(25),
            ],
            [
                'title' => 'Tips Berkunjung Bersama Rombongan Sekolah',
                'content' => "Ajukan reservasi lebih awal lewat akun, datang tepat waktu, dan jaga kebersihan selama di ruang pamer.\n\nJangan menyentuh koleksi dan ikuti arahan petugas museum.",
                'image_url' => 'images/museum/ruang-koleksi-lantai-dua.jpg',
                // Draf: belum terbit, tidak tampil di halaman publik
                'published_at' => null,
            ],
        ];

        foreach ($artikel as $data) {
            // Artikel dengan judul yang sama sudah ada: lewati, jangan timpa suntingan pengelola
            $article = Article::firstOrNew(['title' => $data['title']]);

            if ($article->exists) {
                continue;
            }

            $article->fill($data);
            $article->author_id = $admin->id;
            $article->save();
        }
    }
}
