<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

/*
| Modul Dashboard Admin
| Semua route admin memakai prefix /admin, nama "admin.", dan middleware role:admin.
| Penanggung jawab: Izza (statistik pada Progress 5)
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });
