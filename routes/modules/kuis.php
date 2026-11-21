<?php

use App\Http\Controllers\Admin\KuisController as AdminKuisController;
use Illuminate\Support\Facades\Route;

/*
| Modul Kuis
| Penanggung jawab: Izza
| Admin: /admin/kuis (kuis, soal, dan pilihan jawaban dalam satu editor)
| Halaman kuis untuk pengguna ditambahkan pada Progress 4.
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('kuis', AdminKuisController::class)
            ->except(['show'])
            ->parameters(['kuis' => 'quiz']);

        // Aktifkan / nonaktifkan kuis dari halaman daftar
        Route::patch('kuis/{quiz}/status', [AdminKuisController::class, 'ubahStatus'])->name('kuis.status');
    });
