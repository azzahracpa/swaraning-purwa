<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterKatalogRequest;
use App\Models\Category;
use App\Models\Collection;
use Illuminate\View\View;

class KatalogController extends Controller
{
    /**
     * Katalog koleksi dengan pencarian nama dan filter kategori.
     */
    public function index(FilterKatalogRequest $request): View
    {
        $filter = $request->validated();
        $cari = trim($filter['cari'] ?? '');
        $kategoriAktif = isset($filter['kategori']) ? (int) $filter['kategori'] : null;
        $urut = $filter['urut'] ?? 'nama';

        $kategori = Category::orderBy('name')->get();

        $koleksi = Collection::with(['category', 'primaryImage'])
            ->cari($cari)
            ->dalamKategori($kategoriAktif)
            ->when($urut === 'nama', fn ($q) => $q->orderBy('name'))
            ->when($urut === 'nama_za', fn ($q) => $q->orderByDesc('name'))
            ->when($urut === 'terbaru', fn ($q) => $q->latest()->orderByDesc('id'))
            ->paginate(8)
            ->withQueryString();

        // Deskripsi kategori ditampilkan di atas hasil saat filter kategori aktif
        $kategoriTerpilih = $kategori->firstWhere('id', $kategoriAktif);
        $urutan = FilterKatalogRequest::URUTAN;

        return view('publik.koleksi.index', compact('kategori', 'koleksi', 'cari', 'kategoriAktif', 'kategoriTerpilih', 'urut', 'urutan'));
    }

    /**
     * Detail koleksi: galeri foto, keterangan, dan koleksi lain di kategori yang sama.
     */
    public function show(Collection $collection): View
    {
        // Foto utama selalu ditaruh paling depan di galeri
        $collection->load([
            'category',
            'images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('id'),
        ]);

        $koleksiLain = Collection::with(['category', 'primaryImage'])
            ->where('category_id', $collection->category_id)
            ->whereKeyNot($collection->id)
            ->orderBy('name')
            ->take(3)
            ->get();

        // Status favorit untuk tombol "Simpan ke favorit"
        $sudahFavorit = auth()->check()
            && auth()->user()->favorites()->where('collection_id', $collection->id)->exists();

        return view('publik.koleksi.show', [
            'koleksi' => $collection,
            'koleksiLain' => $koleksiLain,
            'sudahFavorit' => $sudahFavorit,
        ]);
    }
}
