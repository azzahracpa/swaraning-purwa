<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard akun user: kunjungan berikutnya, ringkasan reservasi, favorit, dan kuis.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Kunjungan terdekat yang masih berjalan (Menunggu atau Disetujui, tanggal belum lewat)
        $kunjungan = $user->reservations()
            ->with('schedule')
            ->whereIn('status', [Reservation::STATUS_PENDING, Reservation::STATUS_APPROVED])
            ->whereHas('schedule', fn ($q) => $q->where('visit_date', '>=', today()->toDateString()))
            ->join('visit_schedules', 'visit_schedules.id', '=', 'reservations.schedule_id')
            ->orderBy('visit_schedules.visit_date')
            ->orderBy('visit_schedules.start_time')
            ->select('reservations.*')
            ->first();

        $jumlahStatus = $user->reservations()
            ->selectRaw('status, count(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $favorit = $user->favoriteCollections()
            ->with('primaryImage')
            ->latest('favorites.created_at')
            ->take(4)
            ->get();
        $jumlahFavorit = $user->favorites()->count();

        $kuisTerakhir = $user->quizAttempts()
            ->with('quiz')
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->first();

        $kuisBelumDikerjakan = Quiz::aktif()
            ->whereDoesntHave('attempts', fn ($q) => $q->where('user_id', $user->id)->whereNotNull('completed_at'))
            ->count();

        return view('user.dashboard', compact(
            'user', 'kunjungan', 'jumlahStatus', 'favorit', 'jumlahFavorit', 'kuisTerakhir', 'kuisBelumDikerjakan'
        ));
    }
}
