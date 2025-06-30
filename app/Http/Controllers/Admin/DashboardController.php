<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tarif;
use App\Models\Order;
use App\Models\Driver;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $tarifs = Tarif::all();

        // Statistik tambahan
        $today = Carbon::today();
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $jumlahPerjalanan = Order::whereDate('waktu_selesai', $today)->count();
        $driverAktif = Driver::has('user')->count();
        $pendapatanHarian = Order::whereDate('waktu_selesai', $today)->sum('tarif');
        $pendapatanBulanan = Order::whereMonth('waktu_selesai', $month)
                                    ->whereYear('waktu_selesai', $year)
                                    ->sum('tarif');

        return view('admin.dashboard', compact(
            'tarifs',
            'jumlahPerjalanan',
            'driverAktif',
            'pendapatanHarian',
            'pendapatanBulanan'
        ));
    }
}