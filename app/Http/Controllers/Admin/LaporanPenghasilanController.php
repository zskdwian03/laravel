<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Driver;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPenghasilanExport;

class LaporanPenghasilanController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('driver')
            ->where('status', 'selesai');

        // Filter harian
        if ($request->has('tanggal') && $request->tanggal != null) {
            $query->whereDate('waktu_selesai', $request->tanggal);
        }

        // Filter bulanan
        if ($request->has('bulan') && $request->bulan != null) {
            $bulan = Carbon::parse($request->bulan);
            $query->whereMonth('waktu_selesai', $bulan->month)
                  ->whereYear('waktu_selesai', $bulan->year);
        }

        $orders = $query->get();

        return view('admin.laporan.index', compact('orders'));
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new LaporanPenghasilanExport($request), 'laporan_penghasilan.xlsx');
    }
}