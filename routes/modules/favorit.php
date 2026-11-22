<?php

use App\Http\Controllers\User\FavoritController;
use Illuminate\Support\Facades\Route;

/*
| Modul Koleksi Favorit (pengguna)
| Penanggung jawab: Devi
*/

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/favorit', [FavoritController::class, 'index'])->name('favorit.index');
    Route::post('/favorit', [FavoritController::class, 'store'])->name('favorit.store');
    Route::delete('/favorit/{collection}', [FavoritController::class, 'destroy'])->name('favorit.destroy');
});
