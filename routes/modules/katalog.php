<?php

use App\Http\Controllers\Publik\KatalogController;
use Illuminate\Support\Facades\Route;

/*
| Modul Katalog Koleksi (publik)
| Penanggung jawab: Devi
| URL: /koleksi (pencarian ?cari= dan filter ?kategori=) dan /koleksi/{koleksi}
*/

Route::resource('koleksi', KatalogController::class)
    ->only(['index', 'show'])
    ->parameters(['koleksi' => 'collection']);
