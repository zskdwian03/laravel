<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DriverStatusController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Driver::with(['user', 'orders']);

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
            });
        }

        $drivers = $query->get()->map(function ($driver) {
            // Hitung order selesai dalam 7 hari terakhir
            $orderCount = $driver->orders()
                ->where('status', 'selesai') // pastikan field status = 'selesai'
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->count();

            // Hitung performa: 10 order = 100%
            $driver->performance = min(100, $orderCount * 10);

            return $driver;
        });

        return view('admin.driver-status', compact('drivers'));
    }
}
