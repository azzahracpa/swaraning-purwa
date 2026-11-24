<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Cek role user yang sedang login.
     * Contoh pemakaian di route: ->middleware(['auth', 'role:admin'])
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Admin yang membuka halaman khusus pengguna (misal tautan "Akun saya"
        // atau Dashboard) diarahkan ke ruang pengelola, bukan ditolak.
        if ($user && $user->isAdmin() && ! in_array('admin', $roles, true) && $request->isMethod('GET')) {
            return redirect()->route('admin.dashboard')
                ->with('info', 'Halaman tersebut khusus akun pengguna. Anda dialihkan ke ruang pengelola.');
        }

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk '.implode(' atau ', $roles).'.');
        }

        return $next($request);
    }
}
