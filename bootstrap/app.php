<?php

use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Di Vercel aplikasi berada di belakang proxy, jadi header X-Forwarded-*
        // harus dipercaya supaya Laravel tahu request aslinya HTTPS.
        $middleware->trustProxies(at: '*');

        // Alias middleware role, dipakai seperti ->middleware('role:admin')
        // User yang sudah login lalu membuka /login atau /register diarahkan
        // sesuai perannya, supaya admin tidak tersasar ke /dashboard (403).
        $middleware->redirectUsersTo(
            fn (Request $request) => $request->user()?->isAdmin() ? route('admin.dashboard') : route('dashboard'),
        );

        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
