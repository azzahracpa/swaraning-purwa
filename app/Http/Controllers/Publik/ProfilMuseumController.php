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

        abort_if($profil === null, 404, 'Profil museum belum diisi pengelola.');

        return view('publik.profil-museum', compact('profil'));
    }
}
