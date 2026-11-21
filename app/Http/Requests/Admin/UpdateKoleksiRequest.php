<?php

namespace App\Http\Requests\Admin;

class UpdateKoleksiRequest extends StoreKoleksiRequest
{
    /**
     * Saat mengubah, foto dikelola lewat panel foto terpisah (FotoKoleksiController),
     * jadi aturan foto tidak dipakai di sini.
     */
    public function rules(): array
    {
        return collect(parent::rules())->except(['foto', 'foto.*'])->all();
    }
}
