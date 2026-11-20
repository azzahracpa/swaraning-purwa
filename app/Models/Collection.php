<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Koleksi museum. Nama class "Collection" mengikuti nama tabel di ERD,
 * jangan tertukar dengan Illuminate\Support\Collection.
 */
class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'historical_period',
        'origin',
        'material',
        'location',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(CollectionImage::class);
    }

    /**
     * Foto utama (is_primary = true). Dipakai di kartu katalog.
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(CollectionImage::class)->where('is_primary', true);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')
            ->withPivot('created_at');
    }

    /**
     * Pencarian sederhana berdasarkan nama atau deskripsi.
     */
    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        if (blank($kata)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($kata) {
            $q->where('name', 'like', "%{$kata}%")
                ->orWhere('description', 'like', "%{$kata}%");
        });
    }

    /**
     * Filter berdasarkan kategori. Kalau kosong, semua kategori ditampilkan.
     */
    public function scopeDalamKategori(Builder $query, ?int $kategoriId): Builder
    {
        return $kategoriId ? $query->where('category_id', $kategoriId) : $query;
    }

    /**
     * Teks singkat di kartu, misal "Abad ke-13 · Batu andesit".
     */
    public function metaSingkat(): string
    {
        return collect([$this->historical_period, $this->material])->filter()->implode(' · ');
    }

    /**
     * Pastikan selalu ada tepat satu foto utama selama koleksi punya foto.
     * Dipanggil setelah foto ditambah atau dihapus.
     */
    public function rapikanFotoUtama(): void
    {
        $foto = $this->images()->orderByDesc('is_primary')->orderBy('id')->get();

        if ($foto->isEmpty()) {
            return;
        }

        $utama = $foto->firstWhere('is_primary', true) ?? $foto->first();

        $this->images()->whereKeyNot($utama->id)->where('is_primary', true)->update(['is_primary' => false]);

        if (! $utama->is_primary) {
            $utama->update(['is_primary' => true]);
        }
    }
}
