<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AksiMassalKoleksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'aksi' => ['required', 'in:hapus,kategori'],
            'pilih' => ['required', 'array', 'min:1'],
            'pilih.*' => ['integer', 'exists:collections,id'],
            'category_id' => ['required_if:aksi,kategori', 'nullable', 'integer', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'pilih.required' => 'Pilih minimal satu koleksi.',
            'category_id.required_if' => 'Pilih kategori tujuan.',
        ];
    }
}
