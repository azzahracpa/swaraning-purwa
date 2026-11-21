<?php

namespace App\Models;

use App\Models\Concerns\MemilikiGambar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, MemilikiGambar;

    // author_id diisi dari user yang login di controller, bukan dari form
    protected $fillable = [
        'title',
        'content',
        'image_url',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Hanya artikel yang sudah terbit (published_at terisi dan tidak di masa depan).
     */
    public function scopeTerbit(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getGambarUrlAttribute(): ?string
    {
        return $this->urlGambar($this->image_url);
    }

    /**
     * Artikel terjadwal: tanggal terbit sudah diisi tetapi masih di masa depan.
     */
    public function scopeTerjadwal(Builder $query): Builder
    {
        return $query->where('published_at', '>', now());
    }

    public function scopeDraf(Builder $query): Builder
    {
        return $query->whereNull('published_at');
    }

    /**
     * Status untuk tampilan admin: terbit, terjadwal, atau draf.
     */
    public function status(): string
    {
        if ($this->published_at === null) {
            return 'draf';
        }

        return $this->published_at->isFuture() ? 'terjadwal' : 'terbit';
    }

    public function sudahTerbit(): bool
    {
        return $this->published_at !== null && $this->published_at->lte(now());
    }

    /**
     * Isi artikel dipecah per paragraf (dipisah baris kosong).
     * Baris yang diawali "## " dianggap subjudul.
     *
     * @return array<int, array{jenis: string, teks: string}>
     */
    public function blokIsi(): array
    {
        $blok = preg_split('/\R\s*\R/', trim((string) $this->content)) ?: [];

        return collect($blok)
            ->map(fn ($teks) => trim($teks))
            ->filter()
            ->map(fn ($teks) => str_starts_with($teks, '## ')
                ? ['jenis' => 'subjudul', 'teks' => trim(substr($teks, 3))]
                : ['jenis' => 'paragraf', 'teks' => $teks])
            ->values()
            ->all();
    }

    /**
     * Ringkasan dari paragraf pembuka, dipakai di daftar artikel.
     */
    public function ringkasan(int $batas = 170): string
    {
        $pembuka = collect($this->blokIsi())->firstWhere('jenis', 'paragraf');

        return Str::limit($pembuka['teks'] ?? '', $batas);
    }
}
