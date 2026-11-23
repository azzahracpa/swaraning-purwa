<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterKatalogRequest extends FormRequest
{
    /** Pilihan urutan katalog: kunci di URL => label di layar */
    public const URUTAN = [
        'nama' => 'Nama A–Z',
        'nama_za' => 'Nama Z–A',
        'terbaru' => 'Terbaru ditambahkan',
    ];

    /**
     * Katalog bisa dibuka siapa saja, termasuk tamu.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validasi parameter pencarian dan filter dari URL (?cari=...&kategori=...).
     */
    public function rules(): array
    {
        return [
            'cari' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'integer', 'exists:categories,id'],
            'urut' => ['nullable', Rule::in(array_keys(self::URUTAN))],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori.exists' => 'Kategori yang dipilih tidak ditemukan.',
            'urut.in' => 'Pilihan urutan tidak dikenal.',
        ];
    }
}
