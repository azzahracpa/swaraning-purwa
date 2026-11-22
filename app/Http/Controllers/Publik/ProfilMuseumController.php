<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\MuseumProfile;
use Illuminate\View\View;

class ProfilMuseumController extends Controller
{
    /**
     * Halaman profil Museum Mpu Purwa (satu baris data utama).
     */
    public function show(): View
    {
        $profil = MuseumProfile::utama();

        // Kalau pengelola belum mengisi profil, tampilkan keadaan kosong (bukan galat)
        if ($profil === null) {
            return view('publik.profil-museum-kosong');
        }

        return view('publik.profil-museum', compact('profil'));
    }
}
