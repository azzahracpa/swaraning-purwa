<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    // ERD hanya punya created_at
    public const UPDATED_AT = null;

    protected $fillable = [
        'title',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Total poin maksimal kuis (jumlah poin semua soal).
     */
    public function totalPoin(): int
    {
        return (int) $this->questions()->sum('points');
    }

    /**
     * Syarat sebelum kuis boleh diaktifkan. Setiap syarat berisi label,
     * status terpenuhi, dan keterangan soal mana yang belum memenuhi.
     *
     * @return array<int, array{label: string, ok: bool, catatan: ?string}>
     */
    public function syaratAktif(): array
    {
        $soal = $this->relationLoaded('questions') ? $this->questions : $this->questions()->with('options')->get();
        $soal->loadMissing('options');

        $nomor = fn ($filter) => $soal->values()
            ->filter($filter)
            ->keys()
            ->map(fn ($i) => $i + 1);

        $kurangPilihan = $nomor(fn ($q) => $q->options->count() < 2);
        $salahKunci = $nomor(fn ($q) => $q->options->where('is_correct', true)->count() !== 1);
        $poinNol = $nomor(fn ($q) => $q->points < 1);

        return [
            ['label' => 'Judul kuis terisi', 'ok' => filled($this->title), 'catatan' => null],
            ['label' => 'Kuis punya minimal satu soal', 'ok' => $soal->isNotEmpty(), 'catatan' => null],
            ['label' => 'Poin setiap soal lebih dari 0', 'ok' => $poinNol->isEmpty(),
                'catatan' => $poinNol->isEmpty() ? null : 'Soal '.$poinNol->implode(', ').' belum punya poin.'],
            ['label' => 'Setiap soal punya sedikitnya 2 pilihan', 'ok' => $soal->isNotEmpty() && $kurangPilihan->isEmpty(),
                'catatan' => $kurangPilihan->isEmpty() ? null : 'Soal '.$kurangPilihan->implode(', ').' kurang dari 2 pilihan.'],
            ['label' => 'Setiap soal punya tepat 1 jawaban benar', 'ok' => $soal->isNotEmpty() && $salahKunci->isEmpty(),
                'catatan' => $salahKunci->isEmpty() ? null : 'Soal '.$salahKunci->implode(', ').' belum ditandai tepat satu jawaban benar.'],
        ];
    }

    public function bisaDiaktifkan(): bool
    {
        return collect($this->syaratAktif())->every(fn ($syarat) => $syarat['ok']);
    }
}
