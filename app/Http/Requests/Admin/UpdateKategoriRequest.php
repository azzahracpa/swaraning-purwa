<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdateKategoriRequest extends StoreKategoriRequest
{
    /**
     * Sama seperti tambah, tetapi nama boleh sama dengan nama kategori ini sendiri.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($this->route('category'))],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
