<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreReservasiRequest;
use App\Models\Reservation;
use App\Models\VisitSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReservasiController extends Controller
{
    /**
     * Halaman pilih jadwal dan formulir reservasi.
     */
    public function index(Request $request): View
    {
        // Tanggal yang masih punya slot dibuka (30 hari ke depan)
        $daftarTanggal = VisitSchedule::tersedia()
            ->whereDate('visit_date', '<=', today()->addDays(30))
            ->orderBy('visit_date')
            ->pluck('visit_date')
            ->map(fn ($tanggal) => $tanggal->toDateString())
            ->unique()
            ->values();

        $tanggalDipilih = $daftarTanggal->contains($request->query('tanggal'))
            ? $request->query('tanggal')
            : $daftarTanggal->first();

        $slot = $tanggalDipilih
            ? VisitSchedule::denganJumlahPengunjung()
                ->whereDate('visit_date', $tanggalDipilih)
                ->orderBy('start_time')
                ->get()
            : collect();

        return view('user.reservasi.index', [
            'daftarTanggal' => $daftarTanggal->map(fn ($t) => Carbon::parse($t)),
            'tanggalDipilih' => $tanggalDipilih,
            'slot' => $slot,
            'user' => $request->user(),
        ]);
    }

    /**
     * Simpan pengajuan reservasi dengan status Menunggu (pending).
     */
    public function store(StoreReservasiRequest $request): RedirectResponse
    {
        $reservasi = new Reservation($request->validated());
        // Diisi server, bukan dari form
        $reservasi->user_id = $request->user()->id;
        $reservasi->status = Reservation::STATUS_PENDING;
        $reservasi->booking_code = Reservation::buatKodeBooking(VisitSchedule::find($reservasi->schedule_id)->visit_date);
        $reservasi->save();

        return redirect()->route('reservasi.show', $reservasi)
            ->with('terkirim', true)
            ->with('success', 'Pengajuan reservasi berhasil dikirim dengan kode '.$reservasi->booking_code.'.');
    }

    /**
     * Riwayat reservasi milik pengguna yang sedang masuk.
     */
    public function riwayat(Request $request): View
    {
        $status = $request->query('status');
        $milikSaya = $request->user()->reservations();

        $jumlah = (clone $milikSaya)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $reservasi = (clone $milikSaya)
            ->with('schedule')
            ->when(array_key_exists($status, Reservation::LABEL_STATUS), fn ($q) => $q->where('status', $status))
            ->when($request->query('urut') === 'terbaru',
                fn ($q) => $q->latest(),
                fn ($q) => $q->join('visit_schedules', 'visit_schedules.id', '=', 'reservations.schedule_id')
                    ->orderBy('visit_schedules.visit_date')
                    ->orderBy('visit_schedules.start_time')
                    ->select('reservations.*'))
            ->paginate(10)
            ->withQueryString();

        return view('user.reservasi.riwayat', compact('reservasi', 'jumlah', 'status'));
    }

    /**
     * Detail satu reservasi. Pengguna lain tidak boleh melihatnya.
     */
    public function show(Request $request, Reservation $reservation): View
    {
        $this->pastikanPemilik($request, $reservation);

        $reservation->load('schedule');

        return view('user.reservasi.show', ['reservasi' => $reservation]);
    }

    /**
     * Batalkan pengajuan. Hanya bisa selama status masih Menunggu.
     */
    public function batal(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->pastikanPemilik($request, $reservation);

        if ($reservation->status !== Reservation::STATUS_PENDING) {
            return back()->with('error', 'Reservasi '.$reservation->booking_code.' tidak bisa dibatalkan karena statusnya sudah '.$reservation->labelStatus().'.');
        }

        $reservation->status = Reservation::STATUS_CANCELLED;
        $reservation->save();

        return redirect()->route('reservasi.riwayat')
            ->with('success', 'Reservasi '.$reservation->booking_code.' berhasil dibatalkan.');
    }

    private function pastikanPemilik(Request $request, Reservation $reservation): void
    {
        abort_unless($reservation->user_id === $request->user()->id, 403, 'Reservasi ini bukan milik Anda.');
    }
}
