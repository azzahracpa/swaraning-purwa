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
 * T08 · Submit kuis
 * Hasil yang diharapkan: skor sesuai kunci jawaban.
 */
class T08SubmitKuisTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Quiz $kuis;

    /** @var array<int, array{soal: Question, benar: AnswerOption, salah: AnswerOption}> */
    private array $soal = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->kuis = Quiz::factory()->aktif()->create();

        foreach ([10, 20, 30] as $poin) {
            $q = Question::factory()->for($this->kuis)->create(['points' => $poin]);
            $this->soal[] = [
                'soal' => $q,
                'benar' => AnswerOption::factory()->for($q)->benar()->create(),
                'salah' => AnswerOption::factory()->for($q)->create(),
            ];
        }
    }

    private function kerjakan(array $pilihan, array $tambahan = []): QuizAttempt
    {
        $this->actingAs($this->user)->post(route('kuis.mulai', $this->kuis));
        $attempt = QuizAttempt::where('user_id', $this->user->id)->latest('id')->firstOrFail();

        $jawaban = [];
        foreach ($pilihan as $i => $jenis) {
            $jawaban[$this->soal[$i]['soal']->id] = $this->soal[$i][$jenis]->id;
        }

        $this->actingAs($this->user)
            ->post(route('kuis.kirim', $attempt), ['jawaban' => $jawaban, ...$tambahan])
            ->assertRedirect(route('kuis.hasil', $attempt));

        return $attempt->refresh();
    }

    public function test_semua_benar_mendapat_poin_penuh(): void
    {
        $this->assertSame(60, $this->kerjakan(['benar', 'benar', 'benar'])->score);
    }

    public function test_campuran_benar_salah_dihitung_per_poin_soal(): void
    {
        // benar 10 + salah + benar 30 = 40
        $this->assertSame(40, $this->kerjakan(['benar', 'salah', 'benar'])->score);
    }

    public function test_soal_yang_tidak_dijawab_bernilai_nol(): void
    {
        $attempt = $this->kerjakan([1 => 'benar']);

        $this->assertSame(20, $attempt->score);
        $this->assertNotNull($attempt->completed_at);
    }

    public function test_skor_kiriman_browser_diabaikan(): void
    {
        $attempt = $this->kerjakan(['salah', 'salah', 'salah'], ['score' => 60, 'skor' => 60]);

        $this->assertSame(0, $attempt->score);
    }

    public function test_halaman_hasil_menampilkan_skor_yang_sama(): void
    {
        $attempt = $this->kerjakan(['benar', 'salah', 'salah']);

        $this->actingAs($this->user)->get(route('kuis.hasil', $attempt))->assertOk()->assertSee('10');
    }
}
