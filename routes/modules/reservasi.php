<?php

use App\Http\Controllers\User\ReservasiController;
use Illuminate\Support\Facades\Route;

/*
| Modul Reservasi Kunjungan (pengguna)
| Penanggung jawab: Nofita
| Kelola reservasi oleh admin ditambahkan pada Progress 5.
*/

Route::middleware(['auth', 'role:user'])
    ->prefix('reservasi')
    ->name('reservasi.')
    ->group(function () {
        // Pilih jadwal + isi formulir (?tanggal=YYYY-MM-DD untuk memilih tanggal)
        Route::get('/', [ReservasiController::class, 'index'])->name('index');
        Route::post('/', [ReservasiController::class, 'store'])->name('store');
        Route::get('/riwayat', [ReservasiController::class, 'riwayat'])->name('riwayat');
        Route::get('/{reservation}', [ReservasiController::class, 'show'])->name('show');
        Route::patch('/{reservation}/batal', [ReservasiController::class, 'batal'])->name('batal');
    });
