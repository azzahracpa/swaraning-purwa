<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFotoKoleksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Ubah keterangan foto dan/atau jadikan foto utama.
     */
    public function rules(): array
    {
        return [
            'caption' => ['nullable', 'string', 'max:255'],
            'utama' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['caption' => 'Keterangan foto'];
    }
}
