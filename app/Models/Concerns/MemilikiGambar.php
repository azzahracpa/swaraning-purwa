<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Membantu mengubah path gambar yang tersimpan di database menjadi URL.
 *
 * - Gambar contoh dari seeder disimpan di public/images/contoh/ (aset statis
 *   di repo), jadi cukup dipanggil dengan asset().
 * - Gambar hasil upload disimpan lewat Storage di disk FILESYSTEM_DISK
 *   (lokal: public, produksi: s3), jadi URL-nya dari Storage::url().
 */
trait MemilikiGambar
{
    protected function urlGambar(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return Storage::url($path);
    }
}
