<?php

namespace App\Http\Requests\Admin;

use App\Support\Gambar;
use Illuminate\Foundation\Http\FormRequest;

class StoreFotoKoleksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        // Sisa slot foto = batas dikurangi foto yang sudah ada
        $sisa = max(0, StoreKoleksiRequest::MAKS_FOTO - $this->route('collection')->images()->count());

        return [
            'foto' => ['required', 'array', 'min:1', 'max:'.$sisa],
            'foto.*' => ['required', ...StoreKoleksiRequest::ATURAN_FOTO],
        ];
    }

    public function messages(): array
    {
        return [
            'foto.required' => 'Pilih minimal satu foto.',
            'foto.max' => 'Batas foto tercapai. Satu koleksi maksimal '.StoreKoleksiRequest::MAKS_FOTO.' foto.',
            ...Gambar::pesan('foto.*'),
            ...StoreKoleksiRequest::PESAN_FOTO,
        ];
    }

    public function attributes(): array
    {
        return ['foto.*' => 'Foto'];
    }
}
