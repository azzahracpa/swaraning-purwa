<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Helper kecil untuk menu di layout (config/menu.php).
 */
class Navigasi
{
    /**
     * URL menu. Kalau route belum ada (modul belum dikerjakan), kembalikan "#".
     */
    public static function url(string $namaRoute): string
    {
        return Route::has($namaRoute) ? route($namaRoute) : '#';
    }

    /**
     * Cek apakah menu sedang aktif berdasarkan pola nama route.
     */
    public static function aktif(string|array $pola): bool
    {
        return request()->routeIs(...(array) $pola);
    }
}
