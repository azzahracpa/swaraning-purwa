<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreKuisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Kuis disimpan bersama soal dan pilihannya:
     * soal[i][question_text], soal[i][points], soal[i][benar] (urutan opsi yang benar),
     * soal[i][opsi][j][option_text]. Kolom id diisi untuk soal/opsi yang sudah ada.
     *
     * Kuis yang belum lengkap tetap boleh disimpan, tetapi sebagai Nonaktif.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'soal' => ['nullable', 'array', 'max:50'],
            'soal.*.id' => ['nullable', 'integer'],
            'soal.*.question_text' => ['required', 'string', 'max:1000'],
            'soal.*.points' => ['required', 'integer', 'min:1', 'max:100'],
            'soal.*.benar' => ['nullable', 'integer', 'min:0'],
            'soal.*.opsi' => ['nullable', 'array', 'max:6'],
            'soal.*.opsi.*.id' => ['nullable', 'integer'],
            'soal.*.opsi.*.option_text' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'soal.*.question_text.required' => 'Teks pertanyaan wajib diisi.',
            'soal.*.points.min' => 'Poin minimal 1.',
            'soal.*.opsi.*.option_text.required' => 'Teks pilihan wajib diisi atau hapus pilihan ini.',
            'soal.*.opsi.max' => 'Maksimal 6 pilihan per soal.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Judul kuis',
            'soal.*.points' => 'Poin',
        ];
    }
}
