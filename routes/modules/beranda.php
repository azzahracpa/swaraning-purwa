<?php

use App\Http\Controllers\Publik\BerandaController;
use Illuminate\Support\Facades\Route;

/*
| Modul Beranda dan Profil Museum (publik)
| Penanggung jawab: Amel
*/

Route::get('/', [BerandaController::class, 'index'])->name('beranda');
