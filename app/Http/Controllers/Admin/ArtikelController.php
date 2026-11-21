<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArtikelRequest;
use App\Http\Requests\Admin\UpdateArtikelRequest;
use App\Models\Article;
use App\Support\Gambar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArtikelController extends Controller
{
    /**
     * Daftar artikel untuk admin dengan pencarian judul dan filter status.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $cari = trim((string) $request->query('cari'));

        $jumlah = [
            'semua' => Article::count(),
            'terbit' => Article::terbit()->count(),
            'terjadwal' => Article::terjadwal()->count(),
            'draf' => Article::draf()->count(),
        ];

        $artikel = Article::with('author')
            ->when($cari, fn ($q) => $q->where('title', 'like', "%{$cari}%"))
            ->when($status === 'terbit', fn ($q) => $q->terbit())
            ->when($status === 'terjadwal', fn ($q) => $q->terjadwal())
            ->when($status === 'draf', fn ($q) => $q->draf())
            ->when($request->query('urut') === 'judul', fn ($q) => $q->orderBy('title'), fn ($q) => $q->latest('updated_at'))
            ->paginate(10)
            ->withQueryString();

        return view('admin.artikel.index', compact('artikel', 'jumlah', 'status', 'cari'));
    }

    public function create(): View
    {
        return view('admin.artikel.create', ['article' => new Article]);
    }

    public function store(StoreArtikelRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $article = new Article([
            'title' => $data['title'],
            'content' => $data['content'],
            'published_at' => $this->waktuTerbit($request),
        ]);
        // Penulis diambil dari akun yang sedang masuk, bukan dari form
        $article->author_id = $request->user()->id;

        if ($request->hasFile('image')) {
            $article->image_url = Gambar::simpan($request->file('image'), 'artikel');
        }

        $article->save();

        return redirect()->route('admin.artikel.index')
            ->with('success', 'Artikel "'.$article->title.'" berhasil disimpan.');
    }

    public function edit(Article $article): View
    {
        return view('admin.artikel.edit', compact('article'));
    }

    public function update(UpdateArtikelRequest $request, Article $article): RedirectResponse
    {
        $data = $request->validated();

        $article->fill([
            'title' => $data['title'],
            'content' => $data['content'],
            'published_at' => $this->waktuTerbit($request, $article),
        ]);

        if ($request->hasFile('image')) {
            Gambar::hapus($article->image_url);
            $article->image_url = Gambar::simpan($request->file('image'), 'artikel');
        } elseif ($request->boolean('hapus_image')) {
            Gambar::hapus($article->image_url);
            $article->image_url = null;
        }

        $article->save();

        return redirect()->route('admin.artikel.index')
            ->with('success', 'Artikel "'.$article->title.'" berhasil diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        Gambar::hapus($article->image_url);
        $article->delete();

        return redirect()->route('admin.artikel.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    /**
     * Tentukan published_at dari pilihan status di form.
     * Artikel yang sudah terbit dan tetap dipilih "Terbitkan" tidak berubah tanggalnya.
     */
    private function waktuTerbit(StoreArtikelRequest $request, ?Article $article = null): ?\DateTimeInterface
    {
        return match ($request->input('status')) {
            'jadwal' => $request->waktuTerbit(),
            'terbit' => $article?->sudahTerbit() ? $article->published_at : now(),
            default => null,
        };
    }
}
