<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Collection;
use App\Models\MuseumProfile;
use App\Models\Quiz;
use Illuminate\View\View;

class BerandaController extends Controller
{
    /**
     * Halaman beranda: hero, info kunjungan, koleksi unggulan,
     * artikel terbaru, ajakan reservasi, dan pratinjau kuis.
     */
    public function index(): View
    {
        $profil = MuseumProfile::utama();

        // Eager loading supaya tidak terjadi query berulang (N+1) di kartu
        $koleksiUnggulan = Collection::with(['category', 'primaryImage'])
            ->latest()
            ->take(4)
            ->get();

        $artikelTerbaru = Article::terbit()
            ->latest('published_at')
            ->take(3)
            ->get();

        $daftarKuis = Quiz::aktif()->latest('created_at')->take(2)->get();

        // Pratinjau soal di beranda: hanya teks soal dan opsi, tanpa kunci jawaban
        $kuisPratinjau = Quiz::aktif()
            ->with(['questions' => fn ($q) => $q->orderBy('id')->take(3), 'questions.options'])
            ->withCount('questions')
            ->first();

        return view('publik.beranda', compact(
            'profil',
            'koleksiUnggulan',
            'artikelTerbaru',
            'daftarKuis',
            'kuisPratinjau'
        ));
    }
}
