<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJadwalRequest;
use App\Http\Requests\Admin\UpdateJadwalRequest;
use App\Models\VisitSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class JadwalController extends Controller
{
    /**
     * Jadwal per pekan (Senin–Minggu), dikelompokkan per tanggal.
     */
    public function index(Request $request): View
    {
        $awalPekan = $this->awalPekan($request->query('pekan'));
        $akhirPekan = $awalPekan->copy()->endOfWeek();
        $filter = $request->query('status');

        $semuaSlot = VisitSchedule::denganJumlahPengunjung()
            ->whereBetween('visit_date', [$awalPekan->toDateString(), $akhirPekan->toDateString()])
            ->orderBy('visit_date')
            ->orderBy('start_time')
            ->get();

        $jumlah = [
            'semua' => $semuaSlot->count(),
            'dibuka' => $semuaSlot->filter(fn ($s) => $s->statusSlot() === 'dibuka')->count(),
            'penuh' => $semuaSlot->filter(fn ($s) => $s->statusSlot() === 'penuh')->count(),
            'ditutup' => $semuaSlot->filter(fn ($s) => $s->statusSlot() === 'ditutup')->count(),
        ];

        $slotPerTanggal = $semuaSlot
            ->when($filter, fn ($c) => $c->filter(fn ($s) => $s->statusSlot() === $filter))
            ->groupBy(fn ($s) => $s->visit_date->toDateString());

        $sedangDiubah = $request->filled('ubah') ? VisitSchedule::find($request->integer('ubah')) : null;

        return view('admin.jadwal.index', compact('awalPekan', 'akhirPekan', 'slotPerTanggal', 'jumlah', 'filter', 'sedangDiubah'));
    }

    public function store(StoreJadwalRequest $request): RedirectResponse
    {
        $slot = VisitSchedule::create($request->dataSlot());

        return redirect()->route('admin.jadwal.index', ['pekan' => $slot->visit_date->toDateString()])
            ->with('success', 'Slot '.$slot->visit_date->translatedFormat('j F').' pukul '.$slot->rentangJam().' berhasil dibuka.');
    }

    public function update(UpdateJadwalRequest $request, VisitSchedule $schedule): RedirectResponse
    {
        $schedule->update($request->dataSlot());

        return redirect()->route('admin.jadwal.index', ['pekan' => $schedule->visit_date->toDateString()])
            ->with('success', 'Slot berhasil diperbarui.');
    }

    /**
     * Buka/tutup slot. Slot yang ditutup tidak muncul di halaman Reservasi pengunjung.
     */
    public function ubahStatus(VisitSchedule $schedule): RedirectResponse
    {
        $schedule->update(['is_available' => ! $schedule->is_available]);

        return back()->with('success', 'Slot '.$schedule->rentangJam().' '.($schedule->is_available ? 'dibuka kembali.' : 'ditutup.'));
    }

    /**
     * Slot yang sudah punya reservasi tidak boleh dihapus (foreign key restrict).
     */
    public function destroy(VisitSchedule $schedule): RedirectResponse
    {
        if ($schedule->reservations()->exists()) {
            return back()->with('error', 'Slot yang sudah memiliki reservasi tidak dapat dihapus. Tutup slot agar tidak menerima pengajuan baru.');
        }

        $tanggal = $schedule->visit_date->toDateString();
        $schedule->delete();

        return redirect()->route('admin.jadwal.index', ['pekan' => $tanggal])
            ->with('success', 'Slot berhasil dihapus.');
    }

    /**
     * Senin dari pekan yang diminta (?pekan=YYYY-MM-DD), default pekan ini.
     */
    private function awalPekan(?string $tanggal): Carbon
    {
        try {
            $acuan = $tanggal ? Carbon::createFromFormat('Y-m-d', $tanggal) : today();
        } catch (\Throwable) {
            $acuan = today();
        }

        return $acuan->startOfWeek(Carbon::MONDAY)->startOfDay();
    }
}
