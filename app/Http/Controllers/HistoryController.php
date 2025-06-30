<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Driver;
use Carbon\Carbon;

class HistoryController extends Controller
{
    //buat driver
   public function riwayatLayanan($userId, Request $request)
{
    $filter = $request->query('filter', 'semua'); // default: semua
    $driver = Driver::where('user_id', $userId)->first();

    if (!$driver) {
        return response()->json([
            'success' => false,
            'message' => 'Driver tidak ditemukan'
        ], 404);
    }

    $query = Order::with(['customer', 'payment', 'driver.user'])
        ->where('driver_id', $driver->id)
        ->whereIn('status', ['selesai', 'dibatalkan']); // ✅ Tambah 'dijemput'

    // 🔍 Filter berdasarkan waktu
    if ($filter === 'hariini') {
        $query->whereDate('waktu_selesai', Carbon::today());
    } elseif ($filter === 'kemarin') {
        $query->whereDate('waktu_selesai', Carbon::yesterday());
    } elseif ($filter === 'minggu') {
        $query->whereBetween('waktu_selesai', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    $orders = $query->orderByDesc('waktu_selesai')->get();

    return response()->json([
        'success' => true,
        'riwayat' => $orders->map(function ($order) {
            $isCanceled = $order->status === 'dibatalkan';

            return [
                'tanggal' => $isCanceled
                    ? ($order->canceled_at ? $order->canceled_at->format('Y-m-d') : '-')
                    : ($order->waktu_selesai ? $order->waktu_selesai->format('Y-m-d') : '-'),
                'waktu' => $isCanceled
                    ? ($order->canceled_at ? $order->canceled_at->format('H:i') : '-')
                    : ($order->waktu_selesai ? $order->waktu_selesai->format('H:i') : '-'),
                'status' => $order->status,
                'harga' => $isCanceled ? 0 : $order->tarif,
                'kendaraan' => $order->tipe_kendaraan,
                'bukti_transaksi' => $order->bukti_transaksi,
                'metode_pembayaran' => optional($order->payment)->status === 'sudah_bayar' ? 'QRIS' : 'Tunai',
                'nama' => optional($order->customer)->nama ?? '-',
                'plat' => optional($order->driver)->no_plat ?? '-',
                'dari' => $order->lokasi_jemput,
                'ke' => $order->lokasi_tujuan
            ];
        })
    ]);
}

//buat cutomer
public function riwayatCustomer($customerId)
{
    $orders = Order::with(['driver.user', 'payment'])
        ->where('customer_id', $customerId)
        ->whereIn('status', ['selesai', 'dibatalkan'])
        ->orderByDesc('waktu_selesai')
        ->get();

    return response()->json([
        'success' => true,
        'riwayat' => $orders->map(function ($order) {
            $isCanceled = $order->status === 'dibatalkan';

            return [
                'tanggal' => $isCanceled
                    ? ($order->canceled_at ? $order->canceled_at->format('Y-m-d H:i') : '-')
                    : ($order->waktu_selesai ? $order->waktu_selesai->format('Y-m-d H:i') : '-'),
                'status' => $order->status,
                'deskripsi' => $isCanceled
                    ? 'Order dibatalkan oleh ' . ($order->dibatalkan_oleh ?? '-') . 
                      ($order->alasan_batal ? ' (Alasan: ' . $order->alasan_batal . ')' : '')
                    : 'Perjalanan dari ' . $order->lokasi_jemput . ' ke ' . $order->lokasi_tujuan,
                'harga' => $isCanceled ? 0 : $order->tarif,
                'kendaraan' => $order->tipe_kendaraan,
                'bukti_transaksi' => $order->bukti_transaksi,
                'driver' => [
                    'nama' => $order->driver->user->nama ?? '-',
                    'plat' => $order->driver->no_plat ?? '-',
                ],
                'metode_pembayaran' => optional($order->payment)->status === 'sudah_bayar' ? 'QRIS' : 'Tunai'
            ];
        })
    ]);
}

    public function show($id)
{
    $order = Order::with(['customer', 'driver.user', 'payment'])->find($id);

    if (!$order) {
        return response()->json(['message' => 'Order tidak ditemukan'], 404);
    }

    return response()->json([
        'success' => true,
        'data' => $order
    ]);
}



}
