<?php

namespace App\Models;

use App\Models\Concerns\MemilikiGambar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionImage extends Model
{
    use HasFactory, MemilikiGambar;

    public $timestamps = false;

    protected $fillable = [
        'collection_id',
        'image_url',
        'caption',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function getUrlAttribute(): ?string
    {
        return $this->urlGambar($this->image_url);
    }
}
