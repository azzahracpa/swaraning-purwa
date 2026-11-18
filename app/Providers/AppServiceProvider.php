<?php

namespace App\Providers;

use App\Models\MuseumProfile;
use App\Models\Reservation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Kalau APP_URL memakai https (produksi di Vercel), semua link dan aset
        // ikut dibuat https supaya tidak kena mixed content.
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Tanggal tampil dalam bahasa Indonesia, misal "Senin, 16 November 2026"
        Carbon::setLocale(config('app.locale'));

        // Tampilan paginasi memakai view sendiri sesuai desain
        Paginator::defaultView('components.paginasi');

        // Data profil museum untuk footer (alamat, jam buka, kontak)
        View::composer('partials.footer', function ($view) {
            $view->with('profilMuseum', MuseumProfile::utama());
        });

        // Angka badge di sidebar admin, misal jumlah reservasi menunggu
        View::composer('partials.sidebar-admin', function ($view) {
            $view->with('jumlahMenu', [
                'reservasi_pending' => Reservation::where('status', Reservation::STATUS_PENDING)->count(),
            ]);
        });
    }
}
