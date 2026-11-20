<?php

namespace App\Http\Requests\Admin;

use App\Models\VisitSchedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreJadwalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'visit_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'is_available' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'visit_date.after_or_equal' => 'Tanggal kunjungan tidak boleh sebelum hari ini.',
            'end_time.after' => 'Harus setelah jam mulai.',
            'capacity.min' => 'Minimal 1 orang.',
        ];
    }

    /**
     * Cegah dua slot dengan tanggal dan jam mulai yang sama.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->hasAny(['visit_date', 'start_time'])) {
                    return;
                }

                $bentrok = VisitSchedule::whereDate('visit_date', $this->input('visit_date'))
                    ->where('start_time', $this->input('start_time').':00')
                    ->when($this->route('schedule'), fn ($q, $slot) => $q->whereKeyNot($slot->id))
                    ->exists();

                if ($bentrok) {
                    $validator->errors()->add('start_time', 'Sudah ada slot di tanggal dan jam mulai yang sama.');
                }
            },
        ];
    }

    /**
     * Data siap simpan: is_available bernilai true/false.
     */
    public function dataSlot(): array
    {
        return [
            ...$this->safe()->except(['is_available', 'start_time', 'end_time']),
            // Disimpan lengkap dengan detik (HH:MM:SS) supaya seragam di MySQL maupun SQLite
            'start_time' => $this->input('start_time').':00',
            'end_time' => $this->input('end_time').':00',
            'is_available' => $this->boolean('is_available'),
        ];
    }
}
