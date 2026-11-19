<?php

use App\Http\Controllers\Publik\ArtikelController;
use Illuminate\Support\Facades\Route;

/*
| Modul Artikel (publik)
| Penanggung jawab: Amel
| URL: /artikel dan /artikel/{artikel}
*/

Route::resource('artikel', ArtikelController::class)
    ->only(['index', 'show'])
    ->parameters(['artikel' => 'article']);
