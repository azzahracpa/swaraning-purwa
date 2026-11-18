<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_CANCELLED = 'cancelled';

    // Label status untuk tampilan (bahasa Indonesia)
    public const LABEL_STATUS = [
        self::STATUS_PENDING => 'Menunggu',
        self::STATUS_APPROVED => 'Disetujui',
        self::STATUS_REJECTED => 'Ditolak',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    // user_id, booking_code, dan status diisi oleh server, bukan dari form
    protected $fillable = [
        'schedule_id',
        'visitor_name',
        'phone',
        'total_visitors',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_visitors' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(VisitSchedule::class, 'schedule_id');
    }

    public function labelStatus(): string
    {
        return self::LABEL_STATUS[$this->status] ?? $this->status;
    }

    /**
     * Membuat kode booking unik, contoh: RSV-20261119-7KQ2.
     */
    public static function buatKodeBooking(?\DateTimeInterface $tanggal = null): string
    {
        $tanggal ??= now();

        do {
            $kode = 'RSV-'.$tanggal->format('Ymd').'-'.Str::upper(Str::random(4));
        } while (static::where('booking_code', $kode)->exists());

        return $kode;
    }
}
