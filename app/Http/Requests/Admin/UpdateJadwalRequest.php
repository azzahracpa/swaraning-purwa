<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Validator;

class UpdateJadwalRequest extends StoreJadwalRequest
{
    /**
     * Aturan tambahan saat mengubah: kapasitas tidak boleh lebih kecil
     * dari jumlah pengunjung yang sudah disetujui.
     */
    public function after(): array
    {
        return [
            ...parent::after(),
            function (Validator $validator) {
                $disetujui = $this->route('schedule')->jumlahDisetujui();

                if (! $validator->errors()->has('capacity') && (int) $this->input('capacity') < $disetujui) {
                    $validator->errors()->add('capacity', "Minimal 1 orang. Tidak boleh kurang dari jumlah yang sudah disetujui ({$disetujui}).");
                }
            },
        ];
    }
}
