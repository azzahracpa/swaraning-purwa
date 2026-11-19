<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\View\View;

class ArtikelController extends Controller
{
    /**
     * Daftar artikel yang sudah terbit, terbaru di atas.
     * Artikel pertama di halaman pertama ditampilkan sebagai sorotan.
     */
    public function index(): View
    {
        $artikel = Article::terbit()
            ->with('author')
            ->latest('published_at')
            ->paginate(6);

        return view('publik.artikel.index', compact('artikel'));
    }

    /**
     * Halaman baca artikel. Artikel draf atau terjadwal tidak bisa dibuka publik.
     */
    public function show(Article $article): View
    {
        abort_unless($article->sudahTerbit(), 404);

        $article->load('author');

        $artikelLain = Article::terbit()
            ->whereKeyNot($article->id)
            ->latest('published_at')
            ->take(2)
            ->get();

        return view('publik.artikel.show', compact('article', 'artikelLain'));
    }
}
