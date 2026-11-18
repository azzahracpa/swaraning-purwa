<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $kuis = [
            [
                'title' => 'Mengenal Koleksi Arca (contoh)',
                'description' => 'Uji pengetahuan dasar tentang arca di Museum Mpu Purwa.',
                'is_active' => true,
                'soal' => [
                    ['Arca penjaga pintu bangunan suci disebut...', 10, ['Dwarapala', 'Ganesha', 'Nandi', 'Lingga'], 0],
                    ['Ganesha dikenal sebagai dewa...', 10, ['Perang', 'Ilmu pengetahuan', 'Laut', 'Hujan'], 1],
                    ['Nandi adalah tunggangan dewa...', 10, ['Wisnu', 'Brahma', 'Siwa', 'Indra'], 2],
                    ['Bahan yang paling banyak dipakai untuk arca di museum ini adalah...', 10, ['Kayu jati', 'Batu andesit', 'Perunggu', 'Emas'], 1],
                ],
            ],
            [
                'title' => 'Prasasti dan Aksara (contoh)',
                'description' => 'Kuis singkat tentang prasasti dan aksara kuno.',
                'is_active' => true,
                'soal' => [
                    ['Prasasti biasanya diawali dengan...', 10, ['Penanggalan', 'Gambar raja', 'Daftar harga', 'Peta'], 0],
                    ['Tanah yang dibebaskan dari pajak dalam prasasti disebut...', 10, ['Sawah', 'Sima', 'Keraton', 'Candi'], 1],
                    ['Aksara yang banyak dipakai pada prasasti di Jawa Timur adalah...', 10, ['Latin', 'Jawa Kuno', 'Arab', 'Hanzi'], 1],
                ],
            ],
            [
                // Kuis nonaktif (draf) untuk menguji tampilan admin
                'title' => 'Gerabah Nusantara (draf contoh)',
                'description' => 'Kuis masih disusun dan belum aktif.',
                'is_active' => false,
                'soal' => [
                    ['Terakota adalah...', 10, ['Tanah liat bakar', 'Batu kapur', 'Logam', 'Kayu'], 0],
                ],
            ],
        ];

        foreach ($kuis as $dataKuis) {
            $quiz = Quiz::create([
                'title' => $dataKuis['title'],
                'description' => $dataKuis['description'],
                'is_active' => $dataKuis['is_active'],
            ]);

            foreach ($dataKuis['soal'] as [$teks, $poin, $opsi, $indexBenar]) {
                $question = $quiz->questions()->create([
                    'question_text' => $teks,
                    'points' => $poin,
                ]);

                foreach ($opsi as $i => $teksOpsi) {
                    $question->options()->create([
                        'option_text' => $teksOpsi,
                        'is_correct' => $i === $indexBenar,
                    ]);
                }
            }
        }

        $this->buatContohPengerjaan();
    }

    /**
     * Satu contoh pengerjaan kuis yang sudah selesai, supaya halaman hasil
     * dan dashboard punya data. Skor dihitung dari kunci jawaban di database.
     */
    private function buatContohPengerjaan(): void
    {
        $pengguna = User::where('email', 'pengguna@swaraning-purwa.test')->first();
        $quiz = Quiz::with('questions.options')->where('is_active', true)->first();

        $attempt = new QuizAttempt([
            'quiz_id' => $quiz->id,
            'started_at' => now()->subDay(),
        ]);
        $attempt->user_id = $pengguna->id;
        $attempt->save();

        $skor = 0;

        foreach ($quiz->questions as $nomor => $question) {
            // Soal terakhir sengaja dijawab salah
            $pilihan = $nomor < $quiz->questions->count() - 1
                ? $question->options->firstWhere('is_correct', true)
                : $question->options->firstWhere('is_correct', false);

            $jawaban = new QuizAnswer([
                'question_id' => $question->id,
                'selected_option_id' => $pilihan->id,
            ]);
            $jawaban->attempt_id = $attempt->id;
            $jawaban->is_correct = $pilihan->is_correct;
            $jawaban->save();

            if ($pilihan->is_correct) {
                $skor += $question->points;
            }
        }

        $attempt->score = $skor;
        $attempt->completed_at = now()->subDay()->addMinutes(5);
        $attempt->save();
    }
}
