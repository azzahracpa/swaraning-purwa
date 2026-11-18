<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard akun user (kerangka awal).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('user.dashboard', compact('user'));
    }
}
