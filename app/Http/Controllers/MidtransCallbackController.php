<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransCallbackController extends Controller
{
    public function handle(Request $request)
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');

        try {
            $notif = new Notification(); // otomatis baca dari body request

            $status = $notif->transaction_status;
            $order_id = $notif->order_id; // e.g. MBJEK-24
            $realOrderId = str_replace('MBJEK-', '', $order_id); // ambil angka asli

            $order = Order::find($realOrderId);

            if (!$order) {
                Log::warning('❌ Order tidak ditemukan: ' . $realOrderId);
                return response()->json(['message' => 'Order not found'], 404);
            }

            // Update status order berdasarkan status transaksi
            if ($status === 'settlement') {
                $order->status = 'selesai'; // atau "dibayar"
            } elseif ($status === 'pending') {
                $order->status = 'menunggu_pembayaran';
            } elseif (in_array($status, ['deny', 'cancel', 'expire'])) {
                $order->status = 'dibatalkan';
            }

            $order->save();

            Log::info('✅ Callback Midtrans diproses', [
                'order_id' => $order_id,
                'status' => $status,
                'fraud_status' => $notif->fraud_status
            ]);

            return response()->json(['message' => 'Notifikasi berhasil diproses']);

        } catch (\Exception $e) {
            Log::error('❌ Gagal proses callback Midtrans: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
