<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FcmToken;

class FCMTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'token' => 'required|string',
            'device_type' => 'nullable|string',
        ]);

        // Simpan atau update token untuk user+device
        FcmToken::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'token' => $request->token,
            ],
            [
                'device_type' => $request->device_type ?? 'unknown',
            ]
        );

        return response()->json(['message' => 'Token disimpan']);
    }

    public static function kirimFcm($userId, $title, $body, $data = [])
    {
        $tokens = FcmToken::where('user_id', $userId)->pluck('token');

        foreach ($tokens as $token) {
            $payload = [
                'to' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . env('FCM_SERVER_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            \Log::info('🔔 Kirim FCM ke user ID ' . $userId, [
                'token' => $token,
                'response' => $response->json(),
                'payload' => $payload
            ]);
        }
    }

   public function simpanToken(Request $request)
{
    $request->validate([
        'token' => 'required|string'
    ]);

    FcmToken::updateOrCreate(
        ['user_id' => auth()->id()],
        ['token' => $request->token]
    );

    return response()->json(['message' => 'Token FCM berhasil disimpan']);
}



}
