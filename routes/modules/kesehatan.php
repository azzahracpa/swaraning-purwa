<?php

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\JsonResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/*
| Modul Kesehatan Aplikasi: /health untuk uji cepat setelah deploy
| Penanggung jawab: Zahra
|
| Tanpa sesi dan cookie, jadi tetap bisa menjawab walau tabel sessions bermasalah.
| Kode 503 berarti aplikasi hidup tetapi database tidak bisa dihubungi.
*/

Route::get('/health', function (): JsonResponse {
    try {
        DB::connection()->getPdo();
        DB::select('select 1');
        $database = 'ok';
    } catch (Throwable $e) {
        report($e);
        $database = 'gagal';
    }

    return response()->json([
        'status' => $database === 'ok' ? 'ok' : 'gangguan',
        'database' => $database,
    ], $database === 'ok' ? 200 : 503);
})->name('health')->withoutMiddleware([
    EncryptCookies::class,
    AddQueuedCookiesToResponse::class,
    StartSession::class,
    ShareErrorsFromSession::class,
    PreventRequestForgery::class,
]);
