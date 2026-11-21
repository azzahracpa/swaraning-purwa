<?php

use App\Http\Controllers\Admin\ArtikelController as AdminArtikelController;
use App\Http\Controllers\Publik\ArtikelController;
use Illuminate\Support\Facades\Route;

/*
| Modul Artikel
| Penanggung jawab: Amel
| Publik: /artikel dan /artikel/{artikel}. Admin: /admin/artikel (CRUD).
*/

Route::resource('artikel', ArtikelController::class)
    ->only(['index', 'show'])
    ->parameters(['artikel' => 'article']);

// Kelola artikel (admin)
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('artikel', AdminArtikelController::class)
            ->except(['show'])
            ->parameters(['artikel' => 'article']);
    });
