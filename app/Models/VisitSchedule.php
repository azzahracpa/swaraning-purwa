<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisitSchedule extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'visit_date',
        'start_time',
        'end_time',
        'capacity',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'capacity' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'schedule_id');
    }

    /**
     * Jumlah pengunjung yang sudah disetujui pada jadwal ini.
     * Kuota hanya berkurang oleh reservasi berstatus approved.
     */
    public function jumlahDisetujui(): int
    {
        return (int) $this->reservations()
            ->where('status', Reservation::STATUS_APPROVED)
            ->sum('total_visitors');
    }

    public function sisaKuota(): int
    {
        return max(0, $this->capacity - $this->jumlahDisetujui());
    }

    /**
     * Jadwal yang masih bisa dipilih: tersedia dan tanggalnya belum lewat.
     */
    public function scopeTersedia(Builder $query): Builder
    {
        return $query->where('is_available', true)
            ->whereDate('visit_date', '>=', today());
    }

    /**
     * Teks jam, misal "09.00–10.00".
     */
    public function rentangJam(): string
    {
        $mulai = str_replace(':', '.', substr((string) $this->start_time, 0, 5));
        $selesai = str_replace(':', '.', substr((string) $this->end_time, 0, 5));

        return $mulai.'–'.$selesai;
    }
}
