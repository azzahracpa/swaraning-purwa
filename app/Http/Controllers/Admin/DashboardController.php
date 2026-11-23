<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Reservation;
use App\Models\User;
use App\Models\VisitSchedule;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard pengelola: ringkasan angka, reservasi terbaru, grafik 7 hari, akses cepat.
     */
    public function index(): View
    {
        $awalPekan = today()->startOfWeek(Carbon::MONDAY);
        $akhirPekan = $awalPekan->copy()->endOfWeek();

        $menunggu = Reservation::where('status', Reservation::STATUS_PENDING);
        $pengajuanTerlama = (clone $menunggu)->oldest()->value('created_at');

        $statistik = [
            'menunggu' => (clone $menunggu)->count(),
            'menunggu_pekan_ini' => (clone $menunggu)
                ->whereHas('schedule', fn ($q) => $q->whereBetween('visit_date', [$awalPekan->toDateString(), $akhirPekan->toDateString()]))
                ->count(),
            'terlama' => $pengajuanTerlama ? Carbon::parse($pengajuanTerlama) : null,
            'koleksi' => Collection::count(),
            'koleksi_bulan_ini' => Collection::where('created_at', '>=', now()->startOfMonth())->count(),
            'koleksi_tanpa_foto' => Collection::doesntHave('images')->count(),
            'pengguna' => User::where('role', User::ROLE_USER)->count(),
            'pengunjung_disetujui' => (int) Reservation::where('status', Reservation::STATUS_APPROVED)
                ->whereHas('schedule', fn ($q) => $q->whereBetween('visit_date', [$awalPekan->toDateString(), $akhirPekan->toDateString()]))
                ->sum('total_visitors'),
            'reservasi_disetujui' => Reservation::where('status', Reservation::STATUS_APPROVED)
                ->whereHas('schedule', fn ($q) => $q->whereBetween('visit_date', [$awalPekan->toDateString(), $akhirPekan->toDateString()]))
                ->count(),
        ];

        // Reservasi terbaru, pengajuan Menunggu ditaruh paling atas
        $terbaru = Reservation::with(['schedule' => fn ($q) => $q->denganJumlahPengunjung()])
            ->orderByRaw("case when status = 'pending' then 0 else 1 end")
            ->latest()
            ->take(5)
            ->get();

        $grafik = $this->grafikTujuhHari();

        return view('admin.dashboard', compact('statistik', 'terbaru', 'grafik', 'awalPekan', 'akhirPekan'));
    }

    /**
     * Jumlah pengunjung disetujui dan menunggu per hari, untuk 7 hari ke depan.
     *
     * @return \Illuminate\Support\Collection<int, array{tanggal: Carbon, disetujui: int, menunggu: int}>
     */
    private function grafikTujuhHari(): \Illuminate\Support\Collection
    {
        $slot = VisitSchedule::denganJumlahPengunjung()
            ->whereBetween('visit_date', [today()->toDateString(), today()->addDays(6)->toDateString()])
            ->get()
            ->groupBy(fn ($s) => $s->visit_date->toDateString());

        return collect(range(0, 6))->map(function ($i) use ($slot) {
            $tanggal = today()->addDays($i);
            $hari = $slot->get($tanggal->toDateString(), collect());

            return [
                'tanggal' => $tanggal,
                'disetujui' => (int) $hari->sum('jumlah_disetujui'),
                'menunggu' => (int) $hari->sum('jumlah_menunggu'),
            ];
        });
    }
}
