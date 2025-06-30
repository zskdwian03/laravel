<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\ResetPassword;


class ForgotPasswordController extends Controller
{

    // Kirim link reset password ke email
    public function sendResetLinkEmail(Request $request)
    {
        try {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan.'], 404);
        }

        $token = Str::random(60);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Kirim email (gunakan mail template kamu)
        Mail::to($request->email)->send(new ResetPassword(
        url("http://localhost:8100/reset-password?token=$token&email={$request->email}"),
        $user->username
    ));

        return response()->json(['message' => 'Link reset password telah dikirim.']);

        } catch (\Exception $e) {
        \Log::error('Gagal kirim email: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
    }

    // Reset password dari token
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return response()->json(['message' => 'Token tidak valid.'], 400);
        }

        // CEK EXPIRED: token valid hanya 2 menit
        if (Carbon::parse($reset->created_at)->addMinutes(2)->isPast()) {
            return response()->json(['message' => 'Token sudah kedaluwarsa.'], 400);
        }

       

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token biar gak bisa dipakai lagi
        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password berhasil direset.']);

    }

    public function resendResetLink(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $user = User::where('email', $request->email)->first();
    if (!$user) {
        return response()->json(['message' => 'Email tidak ditemukan.'], 404);
    }

    $token = Str::random(60);

    DB::table('password_resets')->updateOrInsert(
        ['email' => $request->email],
        ['token' => $token, 'created_at' => now()]
    );

    Mail::to($user->email)->send(new ResetPassword(
        url("http://localhost:8100/reset-password?token=$token&email={$request->email}"),
        $user->username
    ));

    return response()->json(['message' => 'Link reset password telah dikirim ulang.']);
}

}
