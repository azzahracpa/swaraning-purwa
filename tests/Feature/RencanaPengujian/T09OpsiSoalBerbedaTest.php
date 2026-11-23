<?php

namespace Tests\Feature\RencanaPengujian;

use App\Models\AnswerOption;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * T09 · Pilih opsi dari soal berbeda
 * Hasil yang diharapkan: jawaban ditolak.
 */
class T09OpsiSoalBerbedaTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private QuizAttempt $attempt;

    private Question $soalA;

    private Question $soalB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $kuis = Quiz::factory()->aktif()->create();
        $this->soalA = Question::factory()->for($kuis)->create();
        $this->soalB = Question::factory()->for($kuis)->create();
        foreach ([$this->soalA, $this->soalB] as $soal) {
            AnswerOption::factory()->for($soal)->benar()->create();
            AnswerOption::factory()->for($soal)->create();
        }

        $this->actingAs($this->user)->post(route('kuis.mulai', $kuis));
        $this->attempt = QuizAttempt::where('user_id', $this->user->id)->firstOrFail();
    }

    private function kirim(array $jawaban)
    {
        return $this->actingAs($this->user)->post(route('kuis.kirim', $this->attempt), ['jawaban' => $jawaban]);
    }

    private function pastikanTidakTersimpan(): void
    {
        $this->attempt->refresh();
        $this->assertNull($this->attempt->completed_at);
        $this->assertNull($this->attempt->score);
        $this->assertSame(0, $this->attempt->answers()->count());
    }

    public function test_opsi_milik_soal_lain_di_kuis_yang_sama_ditolak(): void
    {
        $opsiB = $this->soalB->options()->where('is_correct', true)->value('id');

        $this->kirim([$this->soalA->id => $opsiB])->assertSessionHasErrors('jawaban');
        $this->pastikanTidakTersimpan();
    }

    public function test_opsi_dari_kuis_lain_ditolak(): void
    {
        $opsiLuar = AnswerOption::factory()->benar()->create();

        $this->kirim([$this->soalA->id => $opsiLuar->id])->assertSessionHasErrors('jawaban');
        $this->pastikanTidakTersimpan();
    }

    public function test_soal_dari_kuis_lain_ditolak(): void
    {
        $opsiLuar = AnswerOption::factory()->benar()->create();

        $this->kirim([$opsiLuar->question_id => $opsiLuar->id])->assertSessionHasErrors('jawaban');
        $this->pastikanTidakTersimpan();
    }

    public function test_satu_jawaban_tidak_sah_menggagalkan_seluruh_kiriman(): void
    {
        $opsiA = $this->soalA->options()->where('is_correct', true)->value('id');
        $opsiB = $this->soalB->options()->where('is_correct', true)->value('id');

        // Soal A benar, tetapi soal B dijawab dengan opsi milik soal A
        $this->kirim([$this->soalA->id => $opsiA, $this->soalB->id => $opsiA])->assertSessionHasErrors('jawaban');
        $this->pastikanTidakTersimpan();

        // Setelah diperbaiki, kiriman diterima
        $this->kirim([$this->soalA->id => $opsiA, $this->soalB->id => $opsiB])->assertSessionHasNoErrors();
    }
}
