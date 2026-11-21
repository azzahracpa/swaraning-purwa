<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class KirimKuisRequest extends FormRequest
{
    /**
     * Hanya pemilik pengerjaan yang boleh mengirim jawaban.
     */
    public function authorize(): bool
    {
        return $this->route('attempt')->user_id === $this->user()?->id;
    }

    /**
     * Format: jawaban[id_soal] = id_opsi. Soal yang tidak dijawab boleh kosong (dinilai salah).
     */
    public function rules(): array
    {
        return [
            'jawaban' => ['nullable', 'array'],
            'jawaban.*' => ['integer'],
        ];
    }

    /**
     * Setiap jawaban harus milik soal di kuis ini, dan opsinya harus milik soal tersebut.
     * Opsi dari soal lain (misalnya hasil mengubah HTML) ditolak.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $soal = $this->route('attempt')->quiz->questions()->with('options:id,question_id')->get()->keyBy('id');

                foreach ((array) $this->input('jawaban', []) as $idSoal => $idOpsi) {
                    $pertanyaan = $soal->get((int) $idSoal);

                    if (! $pertanyaan || ! $pertanyaan->options->contains('id', (int) $idOpsi)) {
                        $validator->errors()->add('jawaban', 'Jawaban tidak valid: pilihan tidak sesuai dengan soalnya. Muat ulang halaman lalu coba lagi.');

                        return;
                    }
                }
            },
        ];
    }
}
