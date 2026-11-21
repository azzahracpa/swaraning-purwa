<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreFavoritRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'collection_id' => ['required', 'integer', 'exists:collections,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'collection_id.exists' => 'Koleksi tidak ditemukan.',
        ];
    }
}
