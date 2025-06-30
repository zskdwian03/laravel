<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanPenghasilanExport implements FromArray, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function array(): array
    {
        $query = Order::with('driver.user')
            ->where('status', 'selesai');

        if ($this->request->filled('tanggal')) {
            $query->whereDate('waktu_selesai', $this->request->tanggal);
        }

        if ($this->request->filled('bulan')) {
            $bulan = Carbon::parse($this->request->bulan);
            $query->whereMonth('waktu_selesai', $bulan->month)
                  ->whereYear('waktu_selesai', $bulan->year);
        }

        $orders = $query->get();

        $rows = [];
        $no = 1;

        foreach ($orders as $order) {
            $rows[] = [
                $no++,
                $order->driver->user->nama ?? '-',
                'Rp ' . number_format($order->tarif ?? 0, 0, ',', '.'),
                'Rp ' . number_format($order->pendapatan_driver ?? 0, 0, ',', '.'),
                'Rp ' . number_format($order->pendapatan_admin ?? 0, 0, ',', '.'),
                $order->waktu_selesai
                    ? Carbon::parse($order->waktu_selesai)->format('Y-m-d H:i')
                    : '-',
                $order->status,
            ];
        }

        // Spacer kosong biar ga tabrakan
        $rows[] = [''];
        $rows[] = ['Ringkasan Total Pendapatan:'];
        $rows[] = ['Tanggal Hari Ini', Carbon::today()->format('Y-m-d')];
        $rows[] = ['Total Hari Ini', 'Rp ' . number_format($this->getTotalHarian(), 0, ',', '.')];
        $rows[] = ['Total Bulan Ini', 'Rp ' . number_format($this->getTotalBulanan(), 0, ',', '.')];

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Driver',
            'Tarif',
            'Pendapatan Driver',
            'Pendapatan Admin',
            'Waktu Selesai',
            'Status',
        ];
    }

    protected function getTotalHarian()
    {
        $query = Order::where('status', 'selesai');

        if ($this->request->filled('tanggal')) {
            return $query->whereDate('waktu_selesai', $this->request->tanggal)->sum('tarif');
        }

        return $query->whereDate('waktu_selesai', Carbon::today())->sum('tarif');
    }

    protected function getTotalBulanan()
    {
        $now = Carbon::now();
        $query = Order::where('status', 'selesai');

        if ($this->request->filled('bulan')) {
            $bulan = Carbon::parse($this->request->bulan);
            return $query->whereMonth('waktu_selesai', $bulan->month)
                         ->whereYear('waktu_selesai', $bulan->year)
                         ->sum('tarif');
        }

        return $query->whereMonth('waktu_selesai', $now->month)
                     ->whereYear('waktu_selesai', $now->year)
                     ->sum('tarif');
    }
}