<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\VisitSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReservasiController extends Controller
{
    /**
     * Daftar reservasi untuk ditinjau. Default menampilkan yang Menunggu.
     * ?pilih={id} membuka panel detail, ?cari= mencari kode booking atau nama.
     */
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari'));
        $status = $request->query('status', $cari ? 'semua' : Reservation::STATUS_PENDING);
        $tanggal = $request->query('tanggal');

        $jumlah = Reservation::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $reservasi = Reservation::query()
            ->with(['schedule' => fn ($q) => $q->denganJumlahPengunjung()])
            ->join('visit_schedules', 'visit_schedules.id', '=', 'reservations.schedule_id')
            ->select('reservations.*')
            ->when(array_key_exists($status, Reservation::LABEL_STATUS), fn ($q) => $q->where('reservations.status', $status))
            ->when($tanggal, fn ($q) => $q->whereDate('visit_schedules.visit_date', $tanggal))
            ->when($cari, fn ($q) => $q->where(fn ($w) => $w
                ->where('booking_code', 'like', "%{$cari}%")
                ->orWhere('visitor_name', 'like', "%{$cari}%")))
            ->when($request->query('urut') === 'jadwal',
                fn ($q) => $q->orderBy('visit_schedules.visit_date')->orderBy('visit_schedules.start_time'),
                fn ($q) => $q->orderBy('reservations.created_at'))
            ->paginate(10)
            ->withQueryString();

        // Pilihan tanggal untuk filter: tanggal yang punya reservasi
        $daftarTanggal = VisitSchedule::whereHas('reservations')->orderBy('visit_date')
            ->pluck('visit_date')->map(fn ($t) => $t->toDateString())->unique()->values();

        $dipilih = $request->filled('pilih')
            ? Reservation::with('schedule', 'user')->find($request->integer('pilih'))
            : $reservasi->first()?->load('user');

        return view('admin.reservasi.index', [
            'reservasi' => $reservasi,
            'jumlah' => $jumlah,
            'status' => $status,
            'tanggal' => $tanggal,
            'cari' => $cari,
            'daftarTanggal' => $daftarTanggal,
            'dipilih' => $dipilih,
            'kapasitas' => $dipilih?->cekKapasitas(),
        ]);
    }

    /**
     * Setujui reservasi.
     *
     * Dijalankan dalam transaksi. Baris jadwal dikunci dengan lockForUpdate(),
     * jadi kalau dua admin menyetujui reservasi di slot yang sama bersamaan,
     * yang kedua menunggu sampai yang pertama selesai lalu menghitung ulang kuota.
     * Hasilnya kapasitas slot tidak pernah terlampaui.
     */
    public function setujui(Reservation $reservation): RedirectResponse
    {
        $hasil = DB::transaction(function () use ($reservation) {
            VisitSchedule::whereKey($reservation->schedule_id)->lockForUpdate()->first();
            $reservasi = Reservation::whereKey($reservation->id)->lockForUpdate()->first();

            if ($reservasi->status !== Reservation::STATUS_PENDING) {
                return ['error', 'Reservasi '.$reservasi->booking_code.' sudah '.$reservasi->labelStatus().', tidak bisa disetujui lagi.'];
            }

            $cek = $reservasi->cekKapasitas();

            if (! $cek['cukup']) {
                return ['error', "Kapasitas slot tidak cukup. Sisa {$cek['sisa']} kursi, pengajuan ini {$cek['pengajuan']} orang."];
            }

            $reservasi->status = Reservation::STATUS_APPROVED;
            $reservasi->save();

            return ['success', 'Reservasi '.$reservasi->booking_code.' disetujui. Sisa kursi slot sekarang '.($cek['sisa'] - $cek['pengajuan']).'.'];
        });

        return back()->with($hasil[0], $hasil[1]);
    }

    /**
     * Tolak reservasi yang masih Menunggu.
     */
    public function tolak(Reservation $reservation): RedirectResponse
    {
        $hasil = DB::transaction(function () use ($reservation) {
            $reservasi = Reservation::whereKey($reservation->id)->lockForUpdate()->first();

            if ($reservasi->status !== Reservation::STATUS_PENDING) {
                return ['error', 'Reservasi '.$reservasi->booking_code.' sudah '.$reservasi->labelStatus().', tidak bisa ditolak.'];
            }

            $reservasi->status = Reservation::STATUS_REJECTED;
            $reservasi->save();

            return ['success', 'Reservasi '.$reservasi->booking_code.' ditolak.'];
        });

        return back()->with($hasil[0], $hasil[1]);
    }
}
