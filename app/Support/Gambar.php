<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Helper upload gambar lewat Storage facade.
 * Disk mengikuti FILESYSTEM_DISK: lokal "public", produksi "s3".
 * Yang disimpan ke database hanya path-nya, misal "koleksi/abc123.jpg".
 */
class Gambar
{
    /**
     * Aturan validasi gambar yang sama untuk semua form upload:
     * hanya JPG/PNG/WEBP, maksimal 2 MB (2048 KB).
     */
    public const ATURAN = ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];

    public const PESAN = [
        'image' => ':attribute harus berupa gambar.',
        'mimes' => ':attribute harus berformat JPG, PNG, atau WEBP.',
        'max' => ':attribute maksimal 2 MB.',
    ];

    /**
     * Simpan berkas ke folder tertentu di disk default, kembalikan path-nya.
     */
    public static function simpan(UploadedFile $berkas, string $folder): string
    {
        // Nama file dibuat acak oleh Laravel supaya tidak bentrok.
        // Tanpa opsi visibility: disk public lokal sudah publik dari config, dan
        // bucket S3 kompatibel (misal Cloudflare R2) diatur publik dari dasbor
        // penyedia karena sebagian penyedia menolak ACL per berkas.
        $path = $berkas->store($folder);

        if ($path === false) {
            throw new \RuntimeException('Berkas gagal disimpan ke disk '.config('filesystems.default').'.');
        }

        return $path;
    }

    /**
     * Hapus berkas lama. Gambar contoh dari seeder (images/...) tidak ikut dihapus
     * karena itu aset statis di folder public, bukan hasil upload.
     */
    public static function hapus(?string $path): void
    {
        if (blank($path) || str_starts_with($path, 'images/')) {
            return;
        }

        if (Storage::exists($path)) {
            Storage::delete($path);
        }
    }

    /**
     * Pesan validasi untuk field gambar tertentu, dipakai di Form Request.
     *
     * @return array<string, string>
     */
    public static function pesan(string $field): array
    {
        return collect(self::PESAN)
            ->mapWithKeys(fn ($pesan, $aturan) => ["{$field}.{$aturan}" => $pesan])
            ->all();
    }
}
