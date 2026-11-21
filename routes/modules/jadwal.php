<?php

use App\Http\Controllers\Admin\JadwalController;
use Illuminate\Support\Facades\Route;

/*
| Modul Jadwal Kunjungan (admin)
| Penanggung jawab: Nofita
| URL: /admin/jadwal (?pekan=YYYY-MM-DD untuk berpindah pekan, ?ubah={id} untuk mengubah slot)
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('jadwal', JadwalController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['jadwal' => 'schedule']);

        // Buka atau tutup slot tanpa mengubah isian lain
        Route::patch('jadwal/{schedule}/status', [JadwalController::class, 'ubahStatus'])->name('jadwal.status');
    });
