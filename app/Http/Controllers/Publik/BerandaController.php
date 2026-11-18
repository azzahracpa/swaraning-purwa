<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\MuseumProfile;
use Illuminate\View\View;

class BerandaController extends Controller
{
    /**
     * Halaman beranda publik.
     * Versi awal; isi lengkap (koleksi unggulan, artikel) dikerjakan di modul Beranda.
     */
    public function index(): View
    {
        $profil = MuseumProfile::utama();

        return view('publik.beranda', compact('profil'));
    }
}
