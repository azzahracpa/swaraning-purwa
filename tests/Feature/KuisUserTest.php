<?php

namespace Tests\Feature;

use App\Models\AnswerOption;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KuisUserTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Quiz $kuis;

    /** @var array<int, array{soal: Question, benar: AnswerOption, salah: AnswerOption}> */
    private array $data = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->kuis = Quiz::factory()->aktif()->create();

        foreach ([10, 20] as $poin) {
            $soal = Question::factory()->for($this->kuis)->create(['points' => $poin]);
            $this->data[] = [
                'soal' => $soal,
                'benar' => AnswerOption::factory()->for($soal)->benar()->create(),
                'salah' => AnswerOption::factory()->for($soal)->create(),
            ];
        }
    }

    private function mulai(): QuizAttempt
    {
        $this->actingAs($this->user)->post(route('kuis.mulai', $this->kuis));

        return QuizAttempt::where('user_id', $this->user->id)->latest('id')->firstOrFail();
    }

    public function test_daftar_kuis_hanya_menampilkan_yang_aktif(): void
    {
        $nonaktif = Quiz::factory()->create(['title' => 'Kuis Nonaktif']);

        $this->actingAs($this->user)->get(route('kuis.index'))
            ->assertOk()->assertSee($this->kuis->title)->assertDontSee($nonaktif->title);
    }

    public function test_halaman_soal_tidak_membocorkan_kunci_jawaban(): void
    {
        $attempt = $this->mulai();

        $this->actingAs($this->user)->get(route('kuis.kerjakan', $attempt))
            ->assertOk()
            ->assertSee($this->data[0]['soal']->question_text, false)
            ->assertDontSee('is_correct');
    }

    public function test_skor_dihitung_server_sesuai_kunci_jawaban(): void
    {
        $attempt = $this->mulai();

        $this->actingAs($this->user)->post(route('kuis.kirim', $attempt), [
            'jawaban' => [
                $this->data[0]['soal']->id => $this->data[0]['benar']->id,   // benar, 10 poin
                $this->data[1]['soal']->id => $this->data[1]['salah']->id,   // salah
            ],
            // Nilai skor dari browser diabaikan
            'score' => 999,
        ])->assertRedirect(route('kuis.hasil', $attempt));

        $attempt->refresh();
        $this->assertSame(10, $attempt->score);
        $this->assertNotNull($attempt->completed_at);
        $this->assertSame(2, $attempt->answers()->count());
    }

    public function test_opsi_dari_soal_berbeda_ditolak(): void
    {
        $attempt = $this->mulai();

        $this->actingAs($this->user)->post(route('kuis.kirim', $attempt), [
            // Soal pertama dijawab dengan opsi milik soal kedua
            'jawaban' => [$this->data[0]['soal']->id => $this->data[1]['benar']->id],
        ])->assertSessionHasErrors('jawaban');

        $this->assertNull($attempt->refresh()->completed_at);
        $this->assertSame(0, $attempt->answers()->count());
    }

    public function test_kirim_dua_kali_tidak_menilai_ulang(): void
    {
        $attempt = $this->mulai();
        $jawabanBenar = [
            $this->data[0]['soal']->id => $this->data[0]['benar']->id,
            $this->data[1]['soal']->id => $this->data[1]['benar']->id,
        ];

        $this->actingAs($this->user)->post(route('kuis.kirim', $attempt), ['jawaban' => $jawabanBenar]);
        $this->actingAs($this->user)->post(route('kuis.kirim', $attempt), ['jawaban' => []]);

        $this->assertSame(30, $attempt->refresh()->score);
        $this->assertSame(2, $attempt->answers()->count());
    }

    public function test_pengguna_lain_tidak_bisa_membuka_pengerjaan(): void
    {
        $attempt = $this->mulai();
        $lain = User::factory()->create();

        $this->actingAs($lain)->get(route('kuis.kerjakan', $attempt))->assertForbidden();
        $this->actingAs($lain)->post(route('kuis.kirim', $attempt), ['jawaban' => []])->assertForbidden();
    }

    public function test_halaman_hasil_menampilkan_skor_dan_ringkasan(): void
    {
        $attempt = $this->mulai();
        $this->actingAs($this->user)->post(route('kuis.kirim', $attempt), [
            'jawaban' => [$this->data[1]['soal']->id => $this->data[1]['benar']->id],
        ]);

        $this->actingAs($this->user)->get(route('kuis.hasil', $attempt))
            ->assertOk()
            ->assertSee('1 dari 2')
            ->assertSee('tidak dijawab');
    }

    public function test_kuis_nonaktif_tidak_bisa_dimulai(): void
    {
        $nonaktif = Quiz::factory()->create();

        $this->actingAs($this->user)->post(route('kuis.mulai', $nonaktif))->assertNotFound();
    }
}
