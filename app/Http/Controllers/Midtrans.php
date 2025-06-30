<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\CoreApi;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class Midtrans extends Controller
{
    public function getQrisUrl(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::find($request->order_id);
        $grossAmount = (int) $order->tarif;

        // Konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => 'MBJEK-' . $order->id . '-' . time(),
                'gross_amount' => $grossAmount,
            ]
        ];

        try {
            $charge = CoreApi::charge($params);

            // Logging biar bisa dicek kalau error
            Log::info('📦 Response dari Midtrans CoreApi::charge():', (array) $charge);

            // Ambil qr_string dari hasil response
            $qrisString = $charge->qr_string;

            return response()->json(['qris_url' => $qrisString]);
        } catch (\Exception $e) {
            Log::error('Gagal membuat QRIS Midtrans: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    // MidtransController.php
    public function handleNotification(Request $request)
{
    $notif = $request->all();

    $orderId = str_replace('order_', '', $notif['order_id']); // Ambil ID aslinya

    if ($notif['transaction_status'] === 'settlement') {
        // ✅ Tandai sebagai sudah dibayar
        \App\Models\Payment::updateOrCreate(
            ['order_id' => $orderId],
            [
                'status' => 'sudah_bayar',
                'jumlah' => $notif['gross_amount'],
                'bukti_bayar' => null // bisa kamu isi pakai snap url dll
            ]
        );

        // Optional: update order status juga
        \App\Models\Order::where('id', $orderId)->update([
            'status' => 'selesai',
            'waktu_selesai' => now()
        ]);
    }

    return response()->json(['message' => 'Notifikasi diproses']);
}

}