<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;


class AuthController extends Controller
{
   public function register(Request $request)
{
  
        // Validasi umum (user)
        $request->validate([
            'username' => 'required|string|unique:users',
            'nama' => 'required|string',
            'email' => 'required|email:rfc,dns|unique:users',
            'phone' => 'required|string',
            'password' => 'required|min:6',
            'role' => 'required|in:customer,driver',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if (!$this->isValidEmailDomain($request->email)) {
            \Log::warning("Email domain tidak valid: " . $request->email);
            return response()->json(['message' => 'Domain email tidak valid.'], 422);
            

        }
        

        // Validasi tambahan jika role = driver (PINDAH KE ATAS!)
        if ($request->role === 'driver') {
            $request->validate([
                'tipeKendaraan' => 'required|string',      // ✅ Sesuai frontend
                'merek' => 'required|string',
                'warnaKendaraan' => 'required|string',     // ✅ Sesuai frontend
                'noPlat' => 'required|string',             // ✅ Sesuai frontend
            ]);
        }

        

        // Upload foto kalau ada
        $path = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $path = $file->store('profile', 'public');
        }

        // Buat kode verifikasi
        $verificationCode = sprintf('%04d', rand(0, 9999));

        try {

        // Simpan data user
        $user = User::create([
            'username' => $request->username,
            'nama' => strtoupper($request->nama),
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'verification_code' => $verificationCode,
            'is_verified' => false,
            'photo' => $path,
        ]);

        // Simpan ke tabel drivers kalau role = driver (DENGAN CEK ROLE!)
        if ($request->role === 'driver') {
            Driver::create([
                'user_id' => $user->id,
                'tipe_kendaraan' => strtoupper($request->tipeKendaraan),
                'merek' =>  strtoupper($request->merek),   // ✅ Ambil dari request
                'warna_kendaraan' =>  strtoupper($request->warnaKendaraan), // ✅ Ambil dari request
                'no_plat' =>  strtoupper($request->noPlat),                 // ✅ Ambil dari request
            ]);
            \Log::info($request->all());

        }

        // Kirim kode verifikasi via email
        Mail::to($user->email)->send(new VerificationCodeMail($user, $verificationCode));

        return response()->json([
            'message' => 'User registered. Verification code sent to email.',
            'user' => $user
        ], 201);

    } catch (\Exception $e) {
        \Log::error('Register error: '.$e->getMessage()); // ✅ Tulis ke log
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
public function login(Request $request)
{
    try {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        if (is_numeric($request->login)) {
            $loginField = 'phone';
        }

        \Log::info('Login request:', $request->all());
        $user = User::where($loginField, $request->login)->first();

        if (!$user) {
            return response()->json(['message' => 'Akun tidak ditemukan. Periksa kembali email, username, atau nomor HP.'], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Password salah. Silakan coba lagi.'], 401);
        }

        if (!$user->is_verified) {
            return response()->json(['message' => 'Akun belum diverifikasi.'], 403);
        }

        $token = $user->createToken('mbjek_token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token
        ]);

    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'digit1' => 'required|numeric',
            'digit2' => 'required|numeric',
            'digit3' => 'required|numeric',
            'digit4' => 'required|numeric',
        ]);

        $code = $request->digit1 . $request->digit2 . $request->digit3 . $request->digit4;

        $user = User::where('email', $request->email)
                    ->where('verification_code', $code)
                    ->first();

        if (!$user) {
            return response()->json(['message' => 'Kode verifikasi salah'], 400);
        }

        $user->is_verified = true;
        $user->verification_code = null;
        $user->save();

         return response()->json([
            'success' => true,
            'message' => 'Verifikasi Berhasil',
            'data' => [
                'user' => $user->only(['id', 'name', 'email', 'username'])
            ]
        ]);
    }

    public function resendVerificationCode(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    $user = User::where('email', $request->email)->first();

    $newCode = sprintf('%04d', rand(0, 9999));
    $user->verification_code = $newCode;
    $user->save();

    Mail::to($user->email)->send(new VerificationCodeMail($user, $newCode));

    return response()->json([
        'success' => true,  // ← TAMBAHKAN INI
        'message' => 'Kode verifikasi baru telah dikirim'
    ]);
}

//  manual cek MX domain
protected function isValidEmailDomain($email)
{
    $allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com'];
    $domain = substr(strrchr($email, "@"), 1);
    return in_array(strtolower($domain), $allowedDomains);
}

    
}



