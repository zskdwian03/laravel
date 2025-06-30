<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    public function profile(Request $request)
{
    $user = $request->user()->load('driver');
    
    return response()->json([
        'id' => $user->id,
        'username' => $user->username,
        'nama' => $user->nama,
        'email' => $user->email,
        'phone' => $user->phone,
        'role' => $user->role,
        'is_driver' => $user->role === 'driver', // ✅ Tambahkan ini
        'photo' => $user->photo ? asset('storage/' . $user->photo) : null,
        // ⬇️ Tambahkan ini untuk akses data kendaraan
        'no_plat' => $user->driver->no_plat ?? null,
        'tipe_kendaraan' => $user->driver->tipe_kendaraan ?? null,
        'warna_kendaraan' => $user->driver->warna_kendaraan ?? null,
        'merek' =>$user->driver->merek ?? null,
        'status' => $user->driver->status ?? false, // kirim boolean langsung
        ]);
        
}

public function updateProfile(Request $request)
{
    $user = $request->user();

    $request->validate([
        'email' => 'email:rfc,dns|unique:users,email,' . $user->id,
    ]);

    // Ubah ke huruf kapital sebelum update
    $updateUserData = [
        'nama' => strtoupper($request->nama),
        'email' => $request->email,
        'phone' => $request->phone,
        
    ];

    $user->update(array_filter($updateUserData)); // Filter null biar aman

    // Kalau driver, update juga data drivernya
    if ($user->role === 'driver' && $user->driver) {
        $updateDriverData = [
            'tipe_kendaraan' => strtoupper($request->tipe_kendaraan),
            'warna_kendaraan' => strtoupper($request->warna_kendaraan),
            'no_plat' => strtoupper($request->no_plat),
            'merek' => strtoupper($request->merek),
        ];

        $user->driver->update(array_filter($updateDriverData));
    }

    return response()->json(['message' => 'Profil berhasil diperbarui']);
}


    
     // upload foto profil
     
    public function uploadPhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'File foto tidak valid'
            ], 422);
        }

        $user = Auth::user();
        \Log::info('👤 Upload foto oleh:', ['id' => $user->id, 'role' => $user->role]);



        // Hapus foto lama jika ada
        if ($user->photo && Storage::exists('public/' . $user->photo)) {
            Storage::delete('public/' . $user->photo);
        }

        // Upload foto baru
        $photoPath = $request->file('photo')->store('profile', 'public');

        // Update database
        $user->update([
            'photo' => $photoPath
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'photo' => asset('storage/' . $photoPath)
            ],
            'message' => 'Foto profil berhasil diupload'
        ]);
    }

}
