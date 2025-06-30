<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RouteController extends Controller
{
    public function proxyToORS(Request $request)
{
    try {
        $response = Http::withHeaders([
            'Authorization' => '5b3ce3597851110001cf62480c50fcfee9c04158bea7b7fb125acd3e',
            'Content-Type' => 'application/json',
        ])->timeout(10)->post('https://api.openrouteservice.org/v2/directions/driving-car/geojson', $request->all());

        \Log::info('🔥 Mengirim ke ORS:', $request->all());
        return response()->json($response->json(), $response->status());
    } catch (\Exception $e) {
        \Log::error('❌ Error saat fetch ORS', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Gagal ambil rute dari ORS'], 500);
    }
}

}
