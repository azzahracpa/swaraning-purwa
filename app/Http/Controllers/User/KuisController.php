<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\KirimKuisRequest;
use App\Models\AnswerOption;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KuisController extends Controller
{
    /**
     * Daftar kuis aktif dan riwayat pengerjaan pengguna.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $kuis = Quiz::aktif()->withCount('questions')->withSum('questions', 'points')->latest('created_at')->get();

        // Skor terbaik per kuis milik pengguna ini
        $skorTerbaik = $user->quizAttempts()->whereNotNull('completed_at')
            ->selectRaw('quiz_id, max(score) as terbaik')->groupBy('quiz_id')->pluck('terbaik', 'quiz_id');

        $riwayat = $user->quizAttempts()->with('quiz')->whereNotNull('completed_at')
            ->latest('completed_at')->take(5)->get();

        return view('user.kuis.index', compact('kuis', 'skorTerbaik', 'riwayat'));
    }

    /**
     * Mulai kuis. Kalau masih ada pengerjaan yang belum selesai, lanjutkan yang itu.
     */
    public function mulai(Request $request, Quiz $quiz): RedirectResponse
    {
        abort_unless($quiz->is_active, 404);

        $attempt = $request->user()->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->whereNull('completed_at')
            ->latest('started_at')
            ->first();

        if (! $attempt) {
            $attempt = new QuizAttempt(['quiz_id' => $quiz->id, 'started_at' => now()]);
            $attempt->user_id = $request->user()->id;
            $attempt->save();
        }

        return redirect()->route('kuis.kerjakan', $attempt);
    }

    /**
     * Halaman soal. Kunci jawaban (is_correct) tidak dikirim ke browser.
     */
    public function kerjakan(Request $request, QuizAttempt $attempt): View|RedirectResponse
    {
        $this->pastikanPemilik($request, $attempt);

        if ($attempt->sudahSelesai()) {
            return redirect()->route('kuis.hasil', $attempt);
        }

        $attempt->load(['quiz.questions' => fn ($q) => $q->orderBy('id'), 'quiz.questions.options' => fn ($q) => $q->orderBy('id')]);

        $soal = $attempt->quiz->questions->map(fn ($s) => [
            'id' => $s->id,
            'teks' => $s->question_text,
            'poin' => $s->points,
            'opsi' => $s->options->map(fn ($o) => ['id' => $o->id, 'teks' => $o->option_text])->values(),
        ])->values();

        return view('user.kuis.kerjakan', compact('attempt', 'soal'));
    }

    /**
     * Kirim jawaban dan hitung skor di server.
     * Baris attempt dikunci (lockForUpdate) supaya klik kirim dua kali tidak dinilai dua kali.
     */
    public function kirim(KirimKuisRequest $request, QuizAttempt $attempt): RedirectResponse
    {
        $jawaban = collect($request->validated('jawaban', []))->mapWithKeys(fn ($opsi, $soal) => [(int) $soal => (int) $opsi]);

        DB::transaction(function () use ($attempt, $jawaban) {
            $attempt = QuizAttempt::whereKey($attempt->id)->lockForUpdate()->first();

            if ($attempt->sudahSelesai()) {
                return;
            }

            $soal = $attempt->quiz->questions()->get()->keyBy('id');
            $kunci = AnswerOption::whereIn('question_id', $soal->keys())->where('is_correct', true)->pluck('id', 'question_id');
            $skor = 0;

            foreach ($jawaban as $idSoal => $idOpsi) {
                $benar = (int) ($kunci[$idSoal] ?? 0) === $idOpsi;

                $baris = new QuizAnswer(['question_id' => $idSoal, 'selected_option_id' => $idOpsi]);
                $baris->attempt_id = $attempt->id;
                $baris->is_correct = $benar;
                $baris->save();

                if ($benar) {
                    $skor += $soal[$idSoal]->points;
                }
            }

            $attempt->score = $skor;
            $attempt->completed_at = now();
            $attempt->save();
        });

        return redirect()->route('kuis.hasil', $attempt)->with('success', 'Jawaban terkirim dan sudah dinilai.');
    }

    /**
     * Hasil pengerjaan: skor, jumlah benar, dan ringkasan jawaban.
     */
    public function hasil(Request $request, QuizAttempt $attempt): View|RedirectResponse
    {
        $this->pastikanPemilik($request, $attempt);

        if (! $attempt->sudahSelesai()) {
            return redirect()->route('kuis.kerjakan', $attempt);
        }

        $attempt->load(['quiz.questions' => fn ($q) => $q->orderBy('id'), 'quiz.questions.options', 'answers.selectedOption']);
        $jawaban = $attempt->answers->keyBy('question_id');

        $ringkasan = $attempt->quiz->questions->map(fn ($soal) => [
            'teks' => $soal->question_text,
            'dipilih' => $jawaban->get($soal->id)?->selectedOption?->option_text,
            'benar' => (bool) $jawaban->get($soal->id)?->is_correct,
            'kunci' => $soal->options->firstWhere('is_correct', true)?->option_text,
        ]);

        $totalPoin = $attempt->quiz->questions->sum('points');
        $skorTerbaik = $request->user()->quizAttempts()->where('quiz_id', $attempt->quiz_id)->max('score');

        return view('user.kuis.hasil', compact('attempt', 'ringkasan', 'totalPoin', 'skorTerbaik'));
    }

    private function pastikanPemilik(Request $request, QuizAttempt $attempt): void
    {
        abort_unless($attempt->user_id === $request->user()->id, 403, 'Pengerjaan kuis ini bukan milik Anda.');
    }
}
