<?php

namespace App\Http\Requests\Admin;

use App\Support\Gambar;
use Illuminate\Foundation\Http\FormRequest;

class StoreKoleksiRequest extends FormRequest
{
    /** Batas jumlah foto per koleksi */
    public const MAKS_FOTO = 8;

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
            'foto.*' => ['required', ...Gambar::ATURAN],
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'Deskripsi wajib diisi agar halaman detail koleksi tidak kosong.',
            'foto.max' => 'Maksimal '.self::MAKS_FOTO.' foto per koleksi.',
            ...Gambar::pesan('foto.*'),
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
