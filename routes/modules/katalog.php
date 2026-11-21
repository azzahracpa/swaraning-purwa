<?php

use App\Http\Controllers\Admin\FotoKoleksiController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\KoleksiController;
use App\Http\Controllers\Publik\KatalogController;
use Illuminate\Support\Facades\Route;

/*
| Modul Katalog Koleksi
| Penanggung jawab: Devi
| Publik: /koleksi (pencarian ?cari= dan filter ?kategori=) dan /koleksi/{koleksi}
| Admin : /admin/kategori, /admin/koleksi, dan foto koleksi
*/

Route::resource('koleksi', KatalogController::class)
    ->only(['index', 'show'])
    ->parameters(['koleksi' => 'collection']);

// Kelola kategori, koleksi, dan foto koleksi (admin)
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Form tambah/ubah kategori ada di halaman daftar, jadi tanpa create, edit, show
        Route::resource('kategori', KategoriController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['kategori' => 'category']);

        // Aksi untuk beberapa koleksi sekaligus (hapus terpilih, ubah kategori)
        Route::post('koleksi/aksi-massal', [KoleksiController::class, 'aksiMassal'])->name('koleksi.aksi-massal');

        Route::resource('koleksi', KoleksiController::class)
            ->except(['show'])
            ->parameters(['koleksi' => 'collection']);

        // Foto milik satu koleksi. scopeBindings() memastikan foto memang milik koleksi di URL.
        Route::post('koleksi/{collection}/foto', [FotoKoleksiController::class, 'store'])->name('koleksi.foto.store');
        Route::scopeBindings()->group(function () {
            Route::patch('koleksi/{collection}/foto/{image}', [FotoKoleksiController::class, 'update'])->name('koleksi.foto.update');
            Route::delete('koleksi/{collection}/foto/{image}', [FotoKoleksiController::class, 'destroy'])->name('koleksi.foto.destroy');
        });
    });
