<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKuisRequest;
use App\Http\Requests\Admin\UpdateKuisRequest;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class KuisController extends Controller
{
    public function index(): View
    {
        $kuis = Quiz::with('questions.options')
            ->withCount(['questions', 'attempts'])
            ->withSum('questions', 'points')
            ->latest('created_at')
            ->get();

        return view('admin.kuis.index', compact('kuis'));
    }

    public function create(): View
    {
        return view('admin.kuis.edit', ['quiz' => new Quiz]);
    }

    public function store(StoreKuisRequest $request): RedirectResponse
    {
        $quiz = DB::transaction(function () use ($request) {
            $quiz = Quiz::create($request->safe()->only(['title', 'description']));
            $this->simpanSoal($quiz, $request->validated('soal', []));

            return $quiz;
        });

        return $this->selesaiSimpan($quiz, $request->boolean('is_active'), 'Kuis berhasil dibuat.');
    }

    public function edit(Quiz $quiz): View
    {
        $quiz->load(['questions' => fn ($q) => $q->orderBy('id'), 'questions.options' => fn ($q) => $q->orderBy('id')])
            ->loadCount('attempts');

        return view('admin.kuis.edit', compact('quiz'));
    }

    public function update(UpdateKuisRequest $request, Quiz $quiz): RedirectResponse
    {
        DB::transaction(function () use ($request, $quiz) {
            $quiz->update($request->safe()->only(['title', 'description']));
            $this->simpanSoal($quiz, $request->validated('soal', []));
        });

        return $this->selesaiSimpan($quiz, $request->boolean('is_active'), 'Kuis berhasil disimpan.');
    }

    /**
     * Kuis yang sudah dikerjakan pengguna tidak boleh dihapus, cukup dinonaktifkan.
     */
    public function destroy(Quiz $quiz): RedirectResponse
    {
        if ($quiz->attempts()->exists()) {
            return back()->with('error', 'Kuis yang sudah dikerjakan pengguna tidak dapat dihapus, hanya dinonaktifkan.');
        }

        $quiz->delete();

        return redirect()->route('admin.kuis.index')->with('success', 'Kuis berhasil dihapus.');
    }

    /**
     * Aktifkan / nonaktifkan dari daftar. Aktif hanya kalau semua syarat terpenuhi.
     */
    public function ubahStatus(Quiz $quiz): RedirectResponse
    {
        if (! $quiz->is_active && ! $quiz->bisaDiaktifkan()) {
            return back()->with('error', 'Kuis "'.$quiz->title.'" belum bisa diaktifkan. Lengkapi soalnya dulu.');
        }

        $quiz->update(['is_active' => ! $quiz->is_active]);

        return back()->with('success', 'Kuis "'.$quiz->title.'" '.($quiz->is_active ? 'diaktifkan.' : 'dinonaktifkan.'));
    }

    /**
     * Sinkronkan soal dan pilihan dari form: ubah yang lama, tambah yang baru,
     * hapus yang tidak dikirim lagi. Id dari form hanya dipakai kalau memang milik kuis ini.
     */
    private function simpanSoal(Quiz $quiz, array $daftarSoal): void
    {
        $soalLama = $quiz->questions()->with('options')->get()->keyBy('id');
        $sudahDikerjakan = $quiz->attempts()->exists();
        $soalDipakai = [];

        foreach (array_values($daftarSoal) as $dataSoal) {
            $soal = $soalLama->get((int) ($dataSoal['id'] ?? 0)) ?? $quiz->questions()->make();
            $soal->fill([
                'question_text' => $dataSoal['question_text'],
                'points' => $dataSoal['points'],
            ])->save();
            $soalDipakai[] = $soal->id;

            $opsiLama = $soal->exists ? $soal->options()->get()->keyBy('id') : collect();
            $benar = isset($dataSoal['benar']) ? (int) $dataSoal['benar'] : null;
            $opsiDipakai = [];

            foreach (array_values($dataSoal['opsi'] ?? []) as $urutan => $dataOpsi) {
                $opsi = $opsiLama->get((int) ($dataOpsi['id'] ?? 0)) ?? $soal->options()->make();
                $opsi->option_text = $dataOpsi['option_text'];
                // Kunci jawaban ditentukan server dari urutan opsi yang ditandai benar
                $opsi->is_correct = $benar === $urutan;
                $opsi->save();
                $opsiDipakai[] = $opsi->id;
            }

            $opsiDihapus = $soal->options()->whereNotIn('id', $opsiDipakai);
            $this->cegahHapusRiwayat($sudahDikerjakan, $opsiDihapus->exists(), 'pilihan jawaban');
            $opsiDihapus->delete();
        }

        $soalDihapus = $quiz->questions()->whereNotIn('id', $soalDipakai);
        $this->cegahHapusRiwayat($sudahDikerjakan, $soalDihapus->exists(), 'soal');
        $soalDihapus->delete();
    }

    /**
     * Soal/opsi dari kuis yang sudah dikerjakan tidak boleh dihapus,
     * supaya riwayat jawaban pengguna tidak ikut hilang.
     */
    private function cegahHapusRiwayat(bool $sudahDikerjakan, bool $adaYangDihapus, string $apa): void
    {
        if ($sudahDikerjakan && $adaYangDihapus) {
            throw ValidationException::withMessages([
                'soal' => "Kuis ini sudah dikerjakan pengguna, jadi {$apa} lama tidak boleh dihapus. Ubah teksnya saja.",
            ]);
        }
    }

    /**
     * Setelah simpan: aktifkan hanya kalau diminta dan syarat terpenuhi.
     */
    private function selesaiSimpan(Quiz $quiz, bool $mintaAktif, string $pesan): RedirectResponse
    {
        $quiz->unsetRelation('questions');
        $aktif = $mintaAktif && $quiz->bisaDiaktifkan();
        $quiz->update(['is_active' => $aktif]);

        $redirect = redirect()->route('admin.kuis.edit', $quiz)->with('success', $pesan);

        if ($mintaAktif && ! $aktif) {
            $redirect->with('info', 'Kuis disimpan sebagai Nonaktif karena syarat aktif belum terpenuhi.');
        }

        return $redirect;
    }
}
