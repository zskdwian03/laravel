<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string',
            'password' => 'required|min:6',
            'role' => 'required|in:customer,driver',
        ]);

        $verificationCode = sprintf('%04d', rand(0, 9999));

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'verification_code' => $verificationCode,
            'is_verified' => false,
        ]);

        Mail::to($user->email)->send(new VerificationCodeMail($user, $verificationCode));

        return response()->json([
            'message' => 'User registered. Verification code sent to email.',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        if (is_numeric($request->login)) {
            $loginField = 'phone';
        }

        $user = User::where($loginField, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Login gagal'], 401);
        }

        if (!$user->is_verified) {
            return response()->json(['message' => 'Akun belum diverifikasi.'], 403);
        }

        $token = $user->createToken('mbjek_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
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

        return response()->json(['message' => 'Verifikasi berhasil']);
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

        return response()->json(['message' => 'Kode verifikasi baru telah dikirim']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }
}
