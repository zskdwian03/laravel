<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class BerandaController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function GetLocation(Request $request)
    {
        \Log::info('Permintaan update lokasi diterima:', $request->all());
    
        $user = $request->user();
    
        if (!$user) {
            \Log::warning('User tidak ditemukan (unauthenticated).');
            return response()->json(['message' => 'Tidak terautentikasi'], 401);
        }
    
        $user->latitude = $request->latitude;
        $user->longitude = $request->longitude;
        $user->save();
    
        \Log::info("Lokasi berhasil disimpan untuk user ID {$user->id}");
    
        return response()->json(['message' => 'Lokasi berhasil diperbarui']);
    }
    


    public function searchLokasi(Request $request)
    {
        $keyword = $request->input('keyword');
        
        if (empty($keyword) || strlen($keyword) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Masukkan minimal 3 karakter untuk pencarian'
            ], 400);
        }

        // Cari dari database lokasi/alamat yang pernah digunakan
        $hasilPencarian = DB::table('orders')
            ->select('alamat_jemput as alamat', DB::raw('COUNT(*) as frekuensi'))
            ->where('alamat_jemput', 'LIKE', "%{$keyword}%")
            ->groupBy('alamat_jemput')
            ->unionAll(
                DB::table('orders')
                    ->select('alamat_tujuan as alamat', DB::raw('COUNT(*) as frekuensi'))
                    ->where('alamat_tujuan', 'LIKE', "%{$keyword}%")
                    ->groupBy('alamat_tujuan')
            )
            ->orderBy('frekuensi', 'desc')
            ->limit(10)
            ->get();

        // Jika tidak ada hasil, bisa integrasikan dengan Google Places API
        if ($hasilPencarian->isEmpty()) {
            // Dummy data atau integrasi Google Places API
            $hasilPencarian = [
                [
                    'alamat' => $keyword . ' - Hasil dari Google Maps',
                    'frekuensi' => 0,
                    'latitude' => null,
                    'longitude' => null
                ]
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $hasilPencarian,
            'keyword' => $keyword,
            'message' => 'Hasil pencarian lokasi'
        ]);
    }

    
}
