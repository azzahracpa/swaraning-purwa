<?php

namespace App\Http\Requests\Admin;

use App\Support\Gambar;
use Illuminate\Foundation\Http\FormRequest;

class StoreKoleksiRequest extends FormRequest
{
    /** Batas jumlah foto per koleksi */
    public const MAKS_FOTO = 8;

    /**
     * Aturan foto koleksi: aturan gambar umum ditambah cek isi berkas (mimetypes),
     * jadi file .exe yang diganti namanya menjadi .jpg tetap ditolak.
     * Ukuran minimal 400 px supaya foto tidak pecah di galeri detail.
     */
    public const ATURAN_FOTO = [
        ...Gambar::ATURAN,
        'mimetypes:image/jpeg,image/png,image/webp',
        'dimensions:min_width=400,min_height=400',
    ];

    public const PESAN_FOTO = [
        'foto.*.mimetypes' => 'Isi berkas bukan gambar JPG, PNG, atau WEBP. Pastikan berkas tidak sekadar diganti ekstensinya.',
        'foto.*.dimensions' => 'Ukuran foto minimal 400 × 400 piksel.',
    ];

    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'historical_period' => ['nullable', 'string', 'max:150'],
            'origin' => ['nullable', 'string', 'max:255'],
            'material' => ['nullable', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:255'],
            // Foto boleh diunggah sekaligus saat menambah koleksi
            'foto' => ['nullable', 'array', 'max:'.self::MAKS_FOTO],
            'foto.*' => ['required', ...self::ATURAN_FOTO],
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'Deskripsi wajib diisi agar halaman detail koleksi tidak kosong.',
            'foto.max' => 'Maksimal '.self::MAKS_FOTO.' foto per koleksi.',
            ...Gambar::pesan('foto.*'),
            ...self::PESAN_FOTO,
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama koleksi',
            'foto.*' => 'Foto',
        ];
    }
}
