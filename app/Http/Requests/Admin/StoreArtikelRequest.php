<?php

namespace App\Http\Requests\Admin;

use App\Support\Gambar;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Validator;

class StoreArtikelRequest extends FormRequest
{
    /**
     * Hak akses admin sudah dicek middleware role:admin di route.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            // draf = belum terbit, terbit = terbit sekarang, jadwal = terbit di tanggal tertentu
            'status' => ['required', 'in:draf,terbit,jadwal'],
            'tanggal_terbit' => ['required_if:status,jadwal', 'nullable', 'date_format:Y-m-d'],
            'jam_terbit' => ['required_if:status,jadwal', 'nullable', 'date_format:H:i'],
            'image' => ['nullable', ...Gambar::ATURAN],
            'hapus_image' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Isi artikel wajib diisi.',
            'tanggal_terbit.required_if' => 'Tanggal terbit wajib diisi untuk artikel terjadwal.',
            'jam_terbit.required_if' => 'Jam terbit wajib diisi untuk artikel terjadwal.',
            ...Gambar::pesan('image'),
        ];
    }

    public function attributes(): array
    {
        return [
            'image' => 'Gambar sampul',
            'tanggal_terbit' => 'Tanggal terbit',
            'jam_terbit' => 'Jam terbit',
        ];
    }

    /**
     * Artikel terjadwal harus punya waktu terbit di masa depan.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->input('status') !== 'jadwal' || $validator->errors()->hasAny(['tanggal_terbit', 'jam_terbit'])) {
                    return;
                }

                if ($this->waktuTerbit()->lte(now())) {
                    $validator->errors()->add('tanggal_terbit', 'Tanggal sudah lewat. Pilih hari ini atau setelahnya.');
                }
            },
        ];
    }

    /**
     * Gabungan tanggal dan jam terbit untuk status jadwal.
     */
    public function waktuTerbit(): Carbon
    {
        return Carbon::createFromFormat('Y-m-d H:i', $this->input('tanggal_terbit').' '.$this->input('jam_terbit'));
    }
}
