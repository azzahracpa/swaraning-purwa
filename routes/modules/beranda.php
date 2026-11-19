<?php

use App\Http\Controllers\Publik\BerandaController;
use App\Http\Controllers\Publik\ProfilMuseumController;
use Illuminate\Support\Facades\Route;

/*
| Modul Beranda dan Profil Museum (publik)
| Penanggung jawab: Amel
*/

Route::get('/', [BerandaController::class, 'index'])->name('beranda');
Route::get('/profil-museum', [ProfilMuseumController::class, 'show'])->name('profil-museum');
