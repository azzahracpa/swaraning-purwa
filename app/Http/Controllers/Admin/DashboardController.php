<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard admin (kerangka awal, statistik ditambahkan di Progress 5).
     */
    public function index(): View
    {
        return view('admin.dashboard');
    }
}
