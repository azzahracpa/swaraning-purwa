<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_USER = 'user';

    public const ROLE_ADMIN = 'admin';

    // role sengaja tidak dimasukkan ke $fillable supaya user tidak bisa
    // mengubah dirinya menjadi admin lewat form registrasi atau edit profil
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    protected $hidden = [
        'password',
    ];

    // Nilai awal role untuk akun baru dari registrasi
    protected $attributes = [
        'role' => self::ROLE_USER,
    ];

    // Tabel users di ERD tidak punya kolom remember_token
    protected $rememberTokenName = '';

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Inisial nama untuk avatar, misal "Nama Pengguna" menjadi "NP".
     */
    public function inisial(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn ($kata) => Str::upper(Str::substr($kata, 0, 1)))
            ->implode('');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteCollections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'favorites')
            ->withPivot('created_at');
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
