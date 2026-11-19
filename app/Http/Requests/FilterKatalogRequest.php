<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterKatalogRequest extends FormRequest
{
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
        ];
    }

    public function messages(): array
    {
        return [
            'kategori.exists' => 'Kategori yang dipilih tidak ditemukan.',
        ];
    }
}
