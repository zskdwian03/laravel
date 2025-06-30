<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function logout(Request $request)
{
    $user = $request->user();

    if (!$user) {
        \Log::warning("Gagal logout: user tidak terautentikasi.");
        return response()->json(['message' => 'Tidak terautentikasi.'], 401);
    }

    // Hapus token FCM dari database
    if ($request->has('fcm_token')) {
        \App\Models\FcmToken::where('user_id', $user->id)
            ->where('token', $request->fcm_token)
            ->delete();

        \Log::info("Token FCM milik user {$user->email} berhasil dihapus.");
    }

    // Hapus token akses (sanctum token)
    $user->currentAccessToken()->delete();

    \Log::info("User {$user->email} berhasil logout.");

    return response()->json(['message' => 'Logout berhasil']);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
