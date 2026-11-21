<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfilMuseumRequest;
use App\Models\MuseumProfile;
use App\Support\Gambar;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfilMuseumController extends Controller
{
    /**
     * Form profil museum. Kalau belum ada datanya, form tampil kosong.
     */
    public function edit(): View
    {
        $profil = MuseumProfile::utama() ?? new MuseumProfile(['name' => 'Museum Mpu Purwa']);

        return view('admin.profil-museum.edit', compact('profil'));
    }

    public function update(UpdateProfilMuseumRequest $request): RedirectResponse
    {
        $profil = MuseumProfile::utama() ?? new MuseumProfile;

        $profil->fill($request->safe()->except(['image', 'hapus_image']));

        if ($request->hasFile('image')) {
            Gambar::hapus($profil->image_url);
            $profil->image_url = Gambar::simpan($request->file('image'), 'profil');
        } elseif ($request->boolean('hapus_image')) {
            Gambar::hapus($profil->image_url);
            $profil->image_url = null;
        }

        $profil->save();

        return redirect()->route('admin.profil-museum.edit')
            ->with('success', 'Profil museum berhasil disimpan.');
    }
}
