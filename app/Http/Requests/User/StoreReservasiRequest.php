<?php

namespace App\Http\Requests\User;

use App\Models\VisitSchedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreReservasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'schedule_id' => ['required', 'integer', 'exists:visit_schedules,id'],
            'visitor_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'total_visitors' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'schedule_id.required' => 'Pilih salah satu slot jadwal kunjungan.',
            'total_visitors.required' => 'Jumlah pengunjung wajib diisi.',
            'total_visitors.min' => 'Jumlah pengunjung minimal 1 orang.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, +, -, dan tanda kurung.',
        ];
    }

    /**
     * Slot harus masih dibuka, belum lewat, dan sisa kuotanya cukup.
     * Sisa kuota = kapasitas dikurangi pengunjung yang sudah DISETUJUI.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->hasAny(['schedule_id', 'total_visitors'])) {
                    return;
                }

                $slot = VisitSchedule::find($this->input('schedule_id'));

                if (! $slot->is_available || $slot->visit_date->lt(today())) {
                    $validator->errors()->add('schedule_id', 'Slot ini sudah ditutup atau tanggalnya sudah lewat. Pilih slot lain.');

                    return;
                }

                $sisa = $slot->sisaKuota();

                if ((int) $this->input('total_visitors') > $sisa) {
                    $validator->errors()->add('total_visitors', $sisa > 0
                        ? "Maksimal {$sisa} orang untuk slot yang dipilih."
                        : 'Kuota slot ini sudah penuh. Pilih slot lain.');
                }
            },
        ];
    }
}
