<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AksiMassalKoleksiRequest;
use App\Http\Requests\Admin\StoreKoleksiRequest;
use App\Http\Requests\Admin\UpdateKoleksiRequest;
use App\Models\Category;
use App\Models\Collection;
use App\Support\Gambar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KoleksiController extends Controller
{
    /**
     * Daftar koleksi untuk admin: cari, filter kategori, urutkan, dan pilih banyak.
     */
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari'));
        $kategoriAktif = $request->integer('kategori') ?: null;

        $kategori = Category::withCount('collections')->orderBy('name')->get();

        $koleksi = Collection::with(['category', 'primaryImage'])
            ->withCount('images')
            ->when($cari, fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$cari}%")
                ->orWhere('historical_period', 'like', "%{$cari}%")
                ->orWhere('material', 'like', "%{$cari}%")))
            ->dalamKategori($kategoriAktif)
            ->when($request->query('urut') === 'nama', fn ($q) => $q->orderBy('name'), fn ($q) => $q->latest('updated_at'))
            ->paginate(10)
            ->withQueryString();

        return view('admin.koleksi.index', compact('koleksi', 'kategori', 'cari', 'kategoriAktif'));
    }

    public function create(): View
    {
        return view('admin.koleksi.create', [
            'koleksi' => new Collection,
            'kategori' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Simpan koleksi baru beserta foto yang diunggah sekaligus.
     * Foto pertama otomatis menjadi foto utama.
     */
    public function store(StoreKoleksiRequest $request): RedirectResponse
    {
        $koleksi = DB::transaction(function () use ($request) {
            $koleksi = Collection::create($request->safe()->except('foto'));

            foreach ($request->file('foto', []) as $berkas) {
                $koleksi->images()->create([
                    'image_url' => Gambar::simpan($berkas, 'koleksi'),
                ]);
            }

            $koleksi->rapikanFotoUtama();

            return $koleksi;
        });

        return redirect()->route('admin.koleksi.edit', $koleksi)
            ->with('success', 'Koleksi "'.$koleksi->name.'" berhasil ditambahkan. Atur foto utama dan keterangannya di sini.');
    }

    public function edit(Collection $collection): View
    {
        $collection->load(['images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('id')]);

        return view('admin.koleksi.edit', [
            'koleksi' => $collection,
            'kategori' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateKoleksiRequest $request, Collection $collection): RedirectResponse
    {
        $collection->update($request->validated());

        return redirect()->route('admin.koleksi.edit', $collection)
            ->with('success', 'Perubahan koleksi berhasil disimpan.');
    }

    /**
     * Hapus koleksi. Baris foto dan favorit ikut terhapus (cascade),
     * berkas fotonya dihapus dari penyimpanan di sini.
     */
    public function destroy(Collection $collection): RedirectResponse
    {
        $this->hapusKoleksi($collection);

        return redirect()->route('admin.koleksi.index')
            ->with('success', 'Koleksi "'.$collection->name.'" berhasil dihapus.');
    }

    /**
     * Aksi untuk beberapa koleksi sekaligus: hapus atau pindah kategori.
     */
    public function aksiMassal(AksiMassalKoleksiRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $daftar = Collection::with('images')->whereIn('id', $data['pilih'])->get();

        if ($data['aksi'] === 'hapus') {
            DB::transaction(fn () => $daftar->each(fn ($koleksi) => $this->hapusKoleksi($koleksi)));
            $pesan = $daftar->count().' koleksi berhasil dihapus.';
        } else {
            Collection::whereIn('id', $daftar->pluck('id'))->update(['category_id' => $data['category_id']]);
            $pesan = 'Kategori '.$daftar->count().' koleksi berhasil diubah.';
        }

        return redirect()->route('admin.koleksi.index')->with('success', $pesan);
    }

    private function hapusKoleksi(Collection $koleksi): void
    {
        foreach ($koleksi->images as $foto) {
            Gambar::hapus($foto->image_url);
        }

        $koleksi->delete();
    }
}
