<?php

namespace Tests\Feature;

use App\Models\AnswerOption;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminKuisTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    private function soalLengkap(): array
    {
        return [
            ['question_text' => 'Arca penjaga pintu disebut...', 'points' => 10, 'benar' => 0,
                'opsi' => [['option_text' => 'Dwarapala'], ['option_text' => 'Ganesha']]],
            ['question_text' => 'Nandi adalah tunggangan...', 'points' => 20, 'benar' => 1,
                'opsi' => [['option_text' => 'Wisnu'], ['option_text' => 'Siwa'], ['option_text' => 'Brahma']]],
        ];
    }

    public function test_buat_kuis_lengkap_dan_aktifkan(): void
    {
        $this->actingAs($this->admin)->post(route('admin.kuis.store'), [
            'title' => 'Kuis Arca', 'is_active' => 1, 'soal' => $this->soalLengkap(),
        ])->assertSessionHasNoErrors()->assertSessionMissing('info');

        $kuis = Quiz::where('title', 'Kuis Arca')->firstOrFail();
        $this->assertTrue($kuis->is_active);
        $this->assertSame(2, $kuis->questions()->count());
        $this->assertSame(30, $kuis->totalPoin());

        $soalKedua = $kuis->questions()->orderBy('id')->get()[1];
        $this->assertSame('Siwa', $soalKedua->options()->where('is_correct', true)->value('option_text'));
    }

    public function test_kuis_tanpa_jawaban_benar_disimpan_nonaktif(): void
    {
        $soal = $this->soalLengkap();
        unset($soal[0]['benar']);

        $this->actingAs($this->admin)->post(route('admin.kuis.store'), [
            'title' => 'Kuis Belum Lengkap', 'is_active' => 1, 'soal' => $soal,
        ])->assertSessionHas('info');

        $kuis = Quiz::where('title', 'Kuis Belum Lengkap')->firstOrFail();
        $this->assertFalse($kuis->is_active);
        $this->assertFalse($kuis->bisaDiaktifkan());
    }

    public function test_soal_dengan_satu_pilihan_tidak_bisa_diaktifkan_dari_daftar(): void
    {
        $kuis = Quiz::factory()->create();
        $soal = Question::factory()->for($kuis)->create();
        AnswerOption::factory()->for($soal)->benar()->create();

        $this->actingAs($this->admin)->patch(route('admin.kuis.status', $kuis))->assertSessionHas('error');
        $this->assertFalse($kuis->refresh()->is_active);
    }

    public function test_dua_jawaban_benar_dianggap_belum_lengkap(): void
    {
        $kuis = Quiz::factory()->create();
        $soal = Question::factory()->for($kuis)->create();
        AnswerOption::factory()->for($soal)->benar()->count(2)->create();

        $this->assertFalse($kuis->bisaDiaktifkan());
    }

    public function test_ubah_kuis_menyinkronkan_soal_dan_opsi(): void
    {
        $this->actingAs($this->admin)->post(route('admin.kuis.store'), ['title' => 'Kuis', 'soal' => $this->soalLengkap()]);
        $kuis = Quiz::firstOrFail();
        $soal = $kuis->questions()->with('options')->orderBy('id')->get();

        // Kirim ulang hanya soal pertama dengan teks baru dan satu opsi tambahan
        $this->actingAs($this->admin)->put(route('admin.kuis.update', $kuis), [
            'title' => 'Kuis Diubah',
            'soal' => [[
                'id' => $soal[0]->id, 'question_text' => 'Teks baru', 'points' => 15, 'benar' => 2,
                'opsi' => [
                    ['id' => $soal[0]->options[0]->id, 'option_text' => 'Dwarapala'],
                    ['id' => $soal[0]->options[1]->id, 'option_text' => 'Ganesha'],
                    ['option_text' => 'Kala'],
                ],
            ]],
        ])->assertSessionHasNoErrors();

        $this->assertSame(1, $kuis->questions()->count());
        $this->assertSame('Teks baru', $soal[0]->refresh()->question_text);
        $this->assertSame('Kala', $soal[0]->options()->where('is_correct', true)->value('option_text'));
        $this->assertModelMissing($soal[1]);
    }

    public function test_kuis_yang_sudah_dikerjakan_tidak_bisa_dihapus(): void
    {
        $kuis = Quiz::factory()->create();
        QuizAttempt::factory()->for($kuis)->create();

        $this->actingAs($this->admin)->delete(route('admin.kuis.destroy', $kuis))->assertSessionHas('error');
        $this->assertModelExists($kuis);
    }

    public function test_soal_lama_tidak_boleh_dihapus_jika_sudah_dikerjakan(): void
    {
        $this->actingAs($this->admin)->post(route('admin.kuis.store'), ['title' => 'Kuis', 'soal' => $this->soalLengkap()]);
        $kuis = Quiz::firstOrFail();
        QuizAttempt::factory()->for($kuis)->create();

        $this->actingAs($this->admin)->put(route('admin.kuis.update', $kuis), ['title' => 'Kuis', 'soal' => []])
            ->assertSessionHasErrors('soal');

        $this->assertSame(2, $kuis->questions()->count());
    }

    public function test_halaman_kuis_admin_bisa_dibuka(): void
    {
        $kuis = Quiz::factory()->create(['title' => 'Kuis Prasasti']);

        $this->actingAs($this->admin)->get(route('admin.kuis.index'))->assertOk()->assertSee('Kuis Prasasti');
        $this->actingAs($this->admin)->get(route('admin.kuis.edit', $kuis))->assertOk()->assertSee('Syarat sebelum diaktifkan');
        $this->actingAs($this->admin)->get(route('admin.kuis.create'))->assertOk();
    }
}
