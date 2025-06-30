<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\User;
use App\Models\Driver;
use App\Models\Tarif;
use App\Models\FcmToken;
use App\Models\OrderDriverCandidate;
use App\Http\Controllers\FCMTokenController;
use Midtrans\Snap;
use Midtrans\Config;


class OrderController extends Controller
{
    // ✅ Hitung tarif berdasarkan jarak
    private function hitungTarif($jarak, $tipeKendaraan)
{
    $konfigurasiTarif = \App\Models\Tarif::where('jenis_kendaraan', $tipeKendaraan)->first();

    if (!$konfigurasiTarif) {
        throw new \Exception('Tarif belum dikonfigurasi oleh admin untuk kendaraan ' . $tipeKendaraan);
    }

    $tarifDasar   = $konfigurasiTarif->tarif_minimum ?? 0;
    $tarifPerKm   = $konfigurasiTarif->tarif_per_km ?? 0;
    $biayaAdmin   = $konfigurasiTarif->biaya_tambahan ?? 0;
    $pajakPersen  = 10; // default jika belum disimpan di DB

    $tarif = ($jarak <= 1)
        ? $tarifDasar
        : $tarifDasar + (($jarak - 1) * $tarifPerKm);

    $pajak = round(($tarif + $biayaAdmin) * ($pajakPersen / 100));
    $total = $tarif + $biayaAdmin + $pajak;

    return [
        'tarif'        => round($tarif),
        'biaya_admin'  => $biayaAdmin,
        'pajak'        => $pajak,
        'total' => round($total, -3),
    ];
}


    // ✅ Hitung jarak manual (haversine)
    private function hitungJarak($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2)**2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    // ✅ Hitung jarak via OpenRouteService
    public function hitungJarakORS($jemputLat, $jemputLng, $tujuanLat, $tujuanLng)
    {
        $response = Http::withHeaders([
            'Authorization' => config('services.ors.key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openrouteservice.org/v2/directions/driving-car', [
            'coordinates' => [
                [$jemputLng, $jemputLat],
                [$tujuanLng, $tujuanLat],
            ],
        ]);

        if ($response->failed()) {
            throw new \Exception('Gagal ambil jarak dari OpenRouteService');
        }

        $data = $response->json();
        return $data['routes'][0]['summary']['distance'] / 1000; // dalam kilometer
    }

    // ✅ Cek tarif + driver terdekat
    public function cekTarif(Request $request)
{
    $request->validate([
        'jemput_latitude' => 'required|numeric',
        'jemput_longitude' => 'required|numeric',
        'tujuan_latitude' => 'required|numeric',
        'tujuan_longitude' => 'required|numeric',
    ]);

    $jemputLat = $request->jemput_latitude;
    $jemputLng = $request->jemput_longitude;
    $tujuanLat = $request->tujuan_latitude;
    $tujuanLng = $request->tujuan_longitude;
    

    try {
        $jarak = $this->hitungJarakORS($jemputLat, $jemputLng, $tujuanLat, $tujuanLng);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Gagal menghitung jarak: ' . $e->getMessage()], 500);
    }

    $tarifData = $this->hitungTarif($jarak, $request->tipe_kendaraan);


    // ✅ Ambil daftar driver terdekat
    $drivers = User::where('role', 'driver')
    ->whereHas('driver', function ($q) use ($request) {
        $q->where('status', true)
          ->where('tipe_kendaraan', $request->tipe_kendaraan ?? 'motor'); // opsional: default kendaraan
    })
    ->whereDoesntHave('activeOrder', function ($q) {
        $q->whereIn('status', ['dijemput', 'dalam_perjalanan']);
    })
    ->with('driver')
    ->whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->get()
    ->map(function ($driver) use ($request) {
        $driver->jarak = $this->hitungJarak(
            $request->jemput_latitude,
            $request->jemput_longitude,
            $driver->latitude,
            $driver->longitude
        );
        return $driver;
    })
    ->sortBy('jarak')
    ->values();


    return response()->json([
        'jarak_km' => round($jarak, 2),
        'estimasi_tarif' => $tarifData['tarif'],
        'biaya_admin' => $tarifData['biaya_admin'],
        'pajak' => $tarifData['pajak'],
        'total_harga' => $tarifData['total'],
        'driver_terdekat' => $drivers->take(5)->map(function ($driver) {
            return [
                'id' => $driver->id,
                'nama' => $driver->nama,
                'merek' => $driver->driver->merek ?? '',
                'warna' => $driver->driver->warna_kendaraan ?? '',
                'plat_nomor' => $driver->driver->no_plat ?? '',
                'jarak' => round($driver->jarak, 2),
            ];
        }),
    ]);
}


public function buatOrder(Request $request)
{
    \Log::info('🧾 Request buat order:', $request->all());

    $request->validate([
        'jemput_latitude' => 'required|numeric',
        'jemput_longitude' => 'required|numeric',
        'tujuan_latitude' => 'required|numeric',
        'tujuan_longitude' => 'required|numeric',
        'lokasi_jemput' => 'required|string',
        'lokasi_tujuan' => 'required|string',
        'tipe_kendaraan' => 'required|string',
    ]);

    try {
        $jarak = $this->hitungJarakORS(
            $request->jemput_latitude,
            $request->jemput_longitude,
            $request->tujuan_latitude,
            $request->tujuan_longitude  
        );
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Gagal menghitung jarak: ' . $e->getMessage()
        ], 500);
    }

    $tarifData = $this->hitungTarif($jarak, $request->tipe_kendaraan);


    $order = Order::create([
        'customer_id' => auth()->id(),
        'jemput_latitude' => $request->jemput_latitude,
        'jemput_longitude' => $request->jemput_longitude,
        'tujuan_latitude' => $request->tujuan_latitude,
        'tujuan_longitude' => $request->tujuan_longitude,
        'lokasi_jemput' => $request->lokasi_jemput,
        'lokasi_tujuan' => $request->lokasi_tujuan,
        'tipe_kendaraan' => $request->tipe_kendaraan,
        'status' => 'menunggu',
        'jarak' => $jarak,
        'tarif' => $tarifData['total'],
    ]);

    $drivers = User::where('role', 'driver')
        ->whereHas('driver', function ($q) use ($request) {
            $q->where('status', true)
              ->where('tipe_kendaraan', $request->tipe_kendaraan);
        })
        ->whereDoesntHave('activeOrder', function ($q) {
            $q->whereIn('status', ['dijemput', 'dalam_perjalanan']);
        })
        ->with('driver')
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get()
        ->map(function ($driver) use ($request) {
            $driver->jarak = $this->hitungJarak(
                $request->jemput_latitude,
                $request->jemput_longitude,
                $driver->latitude,
                $driver->longitude
            );
            return $driver;
        })
        ->sortBy('jarak')
        ->values();

    // Jika tidak ada driver tersedia
    if ($drivers->isEmpty()) {
        return response()->json([
            'message' => 'Tidak ada driver yang tersedia',
            'order' => $order,
            'assigned_driver' => null,
            'jarak_km' => round($jarak, 2),
            'total_tarif' => $tarifData['total'],
            'driver_terdekat' => $driver_terdekat
        ], 200);
    }


    $driverTerdekat = $drivers->first();

    if ($driverTerdekat) {
    OrderDriverCandidate::create([
        'order_id' => $order->id,
        'driver_id' => $driverTerdekat->id,
        'status' => 'menunggu'
    ]);

    FCMTokenController::kirimFcm(
        $driverTerdekat->id,
        'Order Baru Masuk!',
        'Ada orderan dari pelanggan dekat lokasi Anda.',
        [
            'order_id' => $order->id,
            'type' => 'order_baru'
        ]
    );
}
    return response()->json([
        'message' => 'Order berhasil dibuat',
        'order' => $order,
        'assigned_driver' => $driverTerdekat->id,
        'jarak_km' => round($jarak, 2),
        'total_tarif' => $tarifData['total'],
        'driver_terdekat' => $drivers->map(function ($d) {
            return [
                'id' => $d->id,
                'nama' => $d->nama,
                'merek' => $d->driver->merek ?? '',
                'warna' => $d->driver->warna_kendaraan ?? '',
                'plat_nomor' => $d->driver->no_plat ?? '',
                'jarak' => round($d->jarak, 2)
            ];
        })->values()
    ], 201);
}



    public function terimaOrder(Request $request)
{
    $request->validate([
        'order_id' => 'required|exists:orders,id',
        'driver_id' => 'required|exists:users,id',
    ]);

    $order = Order::with('customer')->find($request->order_id);


    if ($order->driver_id !== null) {
        return response()->json(['message' => 'Order sudah diambil oleh driver lain'], 409);
    }

    $candidate = OrderDriverCandidate::where('order_id', $request->order_id)
        ->where('driver_id', $request->driver_id)
        ->where('status', 'menunggu')
        ->first();

    if (!$candidate) {
        return response()->json(['message' => 'Anda tidak punya akses ke order ini'], 403);
    }

    $candidate->update(['status' => 'terima']);



    // ambil dari tabel drivers, bukan users
    $driver = Driver::where('user_id', $request->driver_id)->first();

    if (!$driver) {
        return response()->json(['message' => 'Data driver tidak ditemukan'], 404);
    }

    $order->driver_id = $driver->id; 
    $order->status = 'dijemput';
    $order->save();

    FCMTokenController::kirimFcm(
    $order->customer_id,
    'Driver Menuju Lokasi!',
    'Driver telah menerima order Anda dan dalam perjalanan menjemput.',
    [
        'order_id' => $order->id,
        'status' => 'dijemput',
        'type' => 'update_status'
    ]
);


    return response()->json(['message' => 'Order berhasil diambil', 'order' => $order]);
}


    //untuk customer saat aplikasi menunggu driver datang, polling terus order statusnya.
    public function cekStatus($orderId)
{
    \Log::info('📥 Permintaan cek status diterima untuk Order ID:', ['order_id' => $orderId]);

    $order = Order::with('driverCandidates.driver.user')->find($orderId);

    if (!$order) {
        \Log::warning('⚠️ Order tidak ditemukan:', ['order_id' => $orderId]);
        return response()->json(['message' => 'Order tidak ditemukan.'], 404);
    }

    \Log::info('✅ Status order ditemukan:', [
        'status' => $order->status,
        'driver_id' => $order->driver_id,
        'dibatalkan_oleh' => $order->dibatalkan_oleh,
        'alasan_batal' => $order->alasan_batal
    ]);

    $candidates = $order->driverCandidates?->map(function ($c) {
        return [
            'id' => $c->driver->user->id,
            'nama' => $c->driver->user->nama,
            'merek' => $c->driver->merek ?? '',
            'warna' => $c->driver->warna_kendaraan ?? '',
            'plat_nomor' => $c->driver->no_plat ?? '',
            'status' => $c->status,
            'photo' => $c->driver->user->photo
            ? asset('storage/' . $c->driver->user->photo)
            : null

        ];
    }) ?? collect();

    return response()->json([
        'status' => $order->status,
        'driver_id' => $order->driver_id,
        'dibatalkan_oleh' => $order->dibatalkan_oleh,
        'alasan_batal' => $order->alasan_batal,
        'driver_candidates' => $candidates
    ]);
}


    public function batalkanOrder(Request $request)
{
    $request->validate([
        'order_id' => 'required|exists:orders,id',
        'dibatalkan_oleh' => 'required|in:customer,driver',
        'alasan_batal' => 'nullable|string'
    ]);

    $order = Order::findOrFail($request->order_id);

    if ($order->status === 'selesai') {
        return response()->json(['message' => 'Order sudah selesai, tidak bisa dibatalkan.'], 400);
    }

    $order->status = 'dibatalkan';
    $order->dibatalkan_oleh = $request->dibatalkan_oleh;
    $order->alasan_batal = $request->alasan_batal;
    $order->canceled_at = now();
    $order->save();

    return response()->json(['message' => 'Order berhasil dibatalkan.']);
}

    public function tolakOrder(Request $request)
{
    $request->validate([
        'order_id' => 'required',
        'driver_id' => 'required',
    ]);

    // Ubah status kandidat driver ini jadi 'rejected'
    OrderDriverCandidate::where('order_id', $request->order_id)
        ->where('driver_id', $request->driver_id)
        ->update(['status' => 'tolak']);

    // Ambil kandidat berikutnya yang belum respon
    $next = OrderDriverCandidate::where('order_id', $request->order_id)
        ->where('status', 'menunggu')
        ->orderBy('id')
        ->first();

    if (!$next) {
    // Cari driver yang belum jadi kandidat
     $existingDriverIds = OrderDriverCandidate::where('order_id', $request->order_id)->pluck('driver_id');
        
    
    $order = Order::find($request->order_id);
    $allNearbyDrivers = User::where('role', 'driver')
    ->whereHas('driver', fn($q) => $q->where('status', true)
    ->where('tipe_kendaraan', $order->tipe_kendaraan))
    ->whereNotIn('id', $existingDriverIds)
    ->whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->get()
    ->map(function ($driver) use ($order) {
        $driver->jarak = $this->hitungJarak(
            $order->jemput_latitude,
            $order->jemput_longitude,
            $driver->latitude,
            $driver->longitude
        );
        return $driver;
    })
    ->sortBy('jarak')
    ->values();

    $driverBaru = $allNearbyDrivers->first();

    
    if ($driverBaru) {
        // Tambahkan kandidat baru
        OrderDriverCandidate::create([
            'order_id' => $order->id,
            'driver_id' => $driverBaru->id,
            'status' => 'menunggu'
        ]);

        $tokens = FcmToken::where('user_id', $driverBaru->id)->pluck('token');
        foreach ($tokens as $token) {
            Http::withHeaders([
                'Authorization' => 'key=' . env('FCM_SERVER_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $token,
                'notification' => [
                    'title' => 'Order Masuk!',
                    'body' => 'Ada order baru untuk Anda!',
                ],
                'data' => [
                    'order_id' => $order->id,
                    'type' => 'order_baru',
                ]
            ]);
        }

        return response()->json(['message' => 'Order diteruskan ke driver berikutnya.']);
    }

    return response()->json(['message' => 'Tidak ada driver lain tersedia.'], 404);
}
}



// untuk driveraplikasi polling tiap 5 detik untuk cek apakah ada order baru yang harus direspons.
public function getOrderTerbaru(Request $request)
    {
        $driverId = $request->query('driver_id');

        $candidate = OrderDriverCandidate::where('driver_id', $driverId)
            ->where('status', 'menunggu')
            ->whereHas('order', function ($q) {
                $q->where('status', 'menunggu');
            })
            ->orderBy('created_at')
            ->first();

        if ($candidate) {
            $order = $candidate->order()->with('customer')->first(); // ⬅️ tambah relasi
            return response()->json(['order' => $order]);
        }


        return response()->json(['order' => null]);
    }

    public function updatePerjalanan(Request $request)
{
    \Log::info('🚀 Menerima request updatePerjalanan', $request->all());

    $request->validate([
        'order_id' => 'required|exists:orders,id',
        'status' => 'required|string'
    ]);

    $order = Order::find($request->order_id);

    if (!$order) {
        return response()->json(['message' => 'Order tidak ditemukan.'], 404);
    }

    $order->status = $request->status;

    if ($request->status === 'selesai') {
        $order->waktu_selesai = now(); 

        // Hitung penghasilan driver bersih dari tarif
        $tarifTotal = $order->tarif;
        $totalPotongan = 20 / 100;   // 20%
        $penghasilanBersih = round($tarifTotal * (1 - $totalPotongan));

        $order->penghasilan_driver = $penghasilanBersih;
    }

    $order->save();

    if (in_array($request->status, ['dijemput', 'dalam_perjalanan', 'selesai'])) {
    FCMTokenController::kirimFcm(
        $order->customer_id,
        'Status Perjalanan',
        'Status order Anda kini: ' . ucfirst(str_replace('_', ' ', $request->status)),
        [
            'order_id' => $order->id,
            'status' => $request->status,
            'type' => 'update_status'
        ]
    );
}


    \DB::table('order_status_logs')->insert([
        'order_id' => $order->id,
        'status' => $request->status,
        'created_at' => now(),
    ]);

    return response()->json([
        'message' => '✅ Status perjalanan berhasil diperbarui.',
        'status_baru' => $order->status,
        'penghasilan_driver' => $order->penghasilan_driver,
    ]);
}

}