<?php

namespace App\Http\Requests\Admin;

use App\Support\Gambar;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilMuseumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'history' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'opening_hours' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', ...Gambar::ATURAN],
            'hapus_image' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Format email belum benar. Contoh: nama@domain.id',
            ...Gambar::pesan('image'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama museum',
            'description' => 'Deskripsi singkat',
            'history' => 'Sejarah museum',
            'image' => 'Gambar utama',
        ];
    }
}
