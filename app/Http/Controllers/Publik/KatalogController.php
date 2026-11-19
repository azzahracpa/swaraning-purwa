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

        $kategori = Category::orderBy('name')->get();

        $koleksi = Collection::with(['category', 'primaryImage'])
            ->cari($cari)
            ->dalamKategori($kategoriAktif)
            ->orderBy('name')
            ->paginate(8)
            ->withQueryString();

        return view('publik.koleksi.index', compact('kategori', 'koleksi', 'cari', 'kategoriAktif'));
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

        return view('publik.koleksi.show', [
            'koleksi' => $collection,
            'koleksiLain' => $koleksiLain,
        ]);
    }
}
