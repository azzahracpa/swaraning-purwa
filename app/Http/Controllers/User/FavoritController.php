<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreFavoritRequest;
use App\Models\Collection;
use App\Models\Favorite;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoritController extends Controller
{
    /**
     * Daftar koleksi favorit milik pengguna, bisa disaring per kategori.
     */
    public function index(Request $request): View
    {
        $kategoriAktif = $request->integer('kategori') ?: null;

        $favorit = $request->user()->favorites()
            ->with(['collection.category', 'collection.primaryImage'])
            ->when($kategoriAktif, fn ($q) => $q->whereHas('collection', fn ($k) => $k->where('category_id', $kategoriAktif)))
            ->when($request->query('urut') === 'nama',
                fn ($q) => $q->join('collections', 'collections.id', '=', 'favorites.collection_id')
                    ->orderBy('collections.name')
                    ->select('favorites.*'),
                fn ($q) => $q->latest('created_at'))
            ->get();

        // Kategori yang muncul di favorit pengguna, untuk chip filter
        $kategori = $request->user()->favoriteCollections()->with('category')->get()
            ->pluck('category')->unique('id')->sortBy('name')->values();

        return view('user.favorit.index', compact('favorit', 'kategori', 'kategoriAktif'));
    }

    /**
     * Simpan ke favorit. Koleksi yang sama tidak akan tersimpan dua kali:
     * firstOrCreate mengecek dulu, dan indeks UNIQUE(user_id, collection_id)
     * di database menjadi pengaman terakhir kalau ada dua permintaan bersamaan.
     */
    public function store(StoreFavoritRequest $request): RedirectResponse
    {
        $koleksi = Collection::findOrFail($request->validated('collection_id'));

        try {
            $favorit = Favorite::firstOrCreate([
                'user_id' => $request->user()->id,
                'collection_id' => $koleksi->id,
            ]);
        } catch (UniqueConstraintViolationException) {
            return back()->with('info', $koleksi->name.' sudah ada di favorit Anda.');
        }

        return back()->with(
            $favorit->wasRecentlyCreated ? 'success' : 'info',
            $favorit->wasRecentlyCreated ? $koleksi->name.' disimpan ke favorit.' : $koleksi->name.' sudah ada di favorit Anda.'
        );
    }

    /**
     * Hapus dari favorit. Pesan "Urungkan" ditampilkan lewat session.
     */
    public function destroy(Request $request, Collection $collection): RedirectResponse
    {
        $request->user()->favorites()->where('collection_id', $collection->id)->delete();

        return back()->with('favorit_dihapus', ['id' => $collection->id, 'nama' => $collection->name]);
    }
}
