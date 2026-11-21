<?php

use App\Http\Controllers\Admin\KuisController as AdminKuisController;
use App\Http\Controllers\User\KuisController;
use Illuminate\Support\Facades\Route;

/*
| Modul Kuis
| Penanggung jawab: Izza
| Admin: /admin/kuis (kuis, soal, dan pilihan jawaban dalam satu editor)
| Pengguna: /kuis (daftar), mulai, kerjakan, kirim, dan hasil
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

// Kuis untuk pengguna: penilaian selalu dihitung di server
Route::middleware(['auth', 'role:user'])
    ->prefix('kuis')
    ->name('kuis.')
    ->group(function () {
        Route::get('/', [KuisController::class, 'index'])->name('index');
        Route::post('/{quiz}/mulai', [KuisController::class, 'mulai'])->name('mulai');
        Route::get('/kerjakan/{attempt}', [KuisController::class, 'kerjakan'])->name('kerjakan');
        Route::post('/kerjakan/{attempt}', [KuisController::class, 'kirim'])->name('kirim');
        Route::get('/hasil/{attempt}', [KuisController::class, 'hasil'])->name('hasil');
    });
