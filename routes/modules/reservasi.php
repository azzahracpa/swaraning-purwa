<?php

use App\Http\Controllers\Admin\ReservasiController as AdminReservasiController;
use App\Http\Controllers\User\ReservasiController;
use Illuminate\Support\Facades\Route;

/*
| Modul Reservasi Kunjungan
| Penanggung jawab: Nofita
| Pengguna: /reservasi. Admin: /admin/reservasi (setujui / tolak).
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

// Kelola reservasi (admin). Persetujuan memeriksa kapasitas di dalam transaksi.
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin/reservasi')
    ->name('admin.reservasi.')
    ->group(function () {
        Route::get('/', [AdminReservasiController::class, 'index'])->name('index');
        Route::patch('/{reservation}/setujui', [AdminReservasiController::class, 'setujui'])->name('setujui');
        Route::patch('/{reservation}/tolak', [AdminReservasiController::class, 'tolak'])->name('tolak');
    });
