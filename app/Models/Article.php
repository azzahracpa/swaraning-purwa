<?php

namespace App\Models;

use App\Models\Concerns\MemilikiGambar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
