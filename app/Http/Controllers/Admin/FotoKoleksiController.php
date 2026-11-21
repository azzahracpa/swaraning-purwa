<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFotoKoleksiRequest;
use App\Http\Requests\Admin\UpdateFotoKoleksiRequest;
use App\Models\Collection;
use App\Models\CollectionImage;
use App\Support\Gambar;
use Illuminate\Http\RedirectResponse;

class FotoKoleksiController extends Controller
{
    /**
     * Unggah satu atau beberapa foto ke koleksi.
     */
    public function store(StoreFotoKoleksiRequest $request, Collection $collection): RedirectResponse
    {
        $berkas = $request->file('foto');

        foreach ($berkas as $foto) {
            $collection->images()->create([
                'image_url' => Gambar::simpan($foto, 'koleksi'),
            ]);
        }

        $collection->rapikanFotoUtama();

        return redirect()->route('admin.koleksi.edit', $collection)
            ->with('success', count($berkas).' foto berhasil diunggah.');
    }

    /**
     * Ubah keterangan foto, atau jadikan foto ini sebagai foto utama.
     */
    public function update(UpdateFotoKoleksiRequest $request, Collection $collection, CollectionImage $image): RedirectResponse
    {
        $data = $request->validated();

        if (array_key_exists('caption', $data)) {
            $image->caption = $data['caption'];
        }

        if ($request->boolean('utama')) {
            // Hanya boleh ada satu foto utama per koleksi
            $collection->images()->whereKeyNot($image->id)->update(['is_primary' => false]);
            $image->is_primary = true;
        }

        $image->save();

        return redirect()->route('admin.koleksi.edit', $collection)
            ->with('success', $request->boolean('utama') ? 'Foto utama berhasil diganti.' : 'Keterangan foto berhasil disimpan.');
    }

    /**
     * Hapus foto. Kalau yang dihapus foto utama, foto berikutnya menjadi utama.
     */
    public function destroy(Collection $collection, CollectionImage $image): RedirectResponse
    {
        Gambar::hapus($image->image_url);
        $image->delete();

        $collection->rapikanFotoUtama();

        return redirect()->route('admin.koleksi.edit', $collection)
            ->with('success', 'Foto berhasil dihapus.');
    }
}
