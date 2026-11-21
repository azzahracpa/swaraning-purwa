<?php

use App\Http\Controllers\Admin\ProfilMuseumController as AdminProfilMuseumController;
use App\Http\Controllers\Publik\BerandaController;
use App\Http\Controllers\Publik\ProfilMuseumController;
use Illuminate\Support\Facades\Route;

/*
| Modul Beranda dan Profil Museum
| Penanggung jawab: Amel
*/

Route::get('/', [BerandaController::class, 'index'])->name('beranda');
Route::get('/profil-museum', [ProfilMuseumController::class, 'show'])->name('profil-museum');

// Kelola profil museum (admin). Hanya ada satu profil, jadi cukup edit dan update.
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/profil-museum', [AdminProfilMuseumController::class, 'edit'])->name('profil-museum.edit');
        Route::put('/profil-museum', [AdminProfilMuseumController::class, 'update'])->name('profil-museum.update');
    });
