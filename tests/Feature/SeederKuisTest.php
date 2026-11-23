<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Database\Seeders\QuizSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederKuisTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_kuis_aman_dijalankan_ulang(): void
    {
        $this->seed([UserSeeder::class, QuizSeeder::class]);
        $jumlah = [Quiz::count(), Question::count(), QuizAttempt::count()];

        $this->seed(QuizSeeder::class);

        $this->assertSame($jumlah, [Quiz::count(), Question::count(), QuizAttempt::count()]);
    }

    public function test_setiap_soal_punya_tepat_satu_jawaban_benar(): void
    {
        $this->seed([UserSeeder::class, QuizSeeder::class]);

        foreach (Question::withCount(['options as benar' => fn ($q) => $q->where('is_correct', true)])->get() as $soal) {
            $this->assertSame(1, $soal->benar, $soal->question_text);
        }
        $this->assertTrue(Quiz::aktif()->get()->every->bisaDiaktifkan());
    }

    public function test_skor_contoh_pengerjaan_sesuai_kunci(): void
    {
        $this->seed([UserSeeder::class, QuizSeeder::class]);

        $attempt = QuizAttempt::with('answers.question')->firstOrFail();
        $harapan = $attempt->answers->where('is_correct', true)->sum(fn ($j) => $j->question->points);

        $this->assertSame($harapan, $attempt->score);
        $this->assertNotNull($attempt->completed_at);
    }
}
