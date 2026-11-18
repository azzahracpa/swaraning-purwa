<?php

namespace App\Models;

use App\Models\Concerns\MemilikiGambar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MuseumProfile extends Model
{
    use HasFactory, MemilikiGambar;

    // ERD hanya punya updated_at
    public const CREATED_AT = null;

    protected $fillable = [
        'name',
        'description',
        'history',
        'address',
        'phone',
        'email',
        'opening_hours',
        'image_url',
    ];

    /**
     * Ambil satu-satunya profil museum (baris pertama).
     */
    public static function utama(): ?self
    {
        return static::query()->orderBy('id')->first();
    }

    public function getGambarUrlAttribute(): ?string
    {
        return $this->urlGambar($this->image_url);
    }
}
