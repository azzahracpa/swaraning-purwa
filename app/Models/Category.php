<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    // Tabel categories tidak punya created_at dan updated_at
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
    ];

    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class);
    }
}
