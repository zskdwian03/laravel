<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DriverController; 
use App\Http\Controllers\LogoutController; 
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\Midtrans;
use App\Http\Controllers\FCMTokenController;
use App\Http\Controllers\MidtransCallbackController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ChatController;

// ✨ Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify', [AuthController::class, 'verify']);
Route::post('/resend-verification-code', [AuthController::class, 'resendVerificationCode']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
Route::post('/resend-reset-link', [ForgotPasswordController::class, 'resendResetLink']);




Route::get('/proxy-search', function (Request $request) {
    $query = $request->query('q');
    $viewbox = '105.0,-5.0,115.0,-8.0';
    $url = "https://nominatim.openstreetmap.org/search?format=jsonv2&q=" . urlencode($query) . "&viewbox={$viewbox}&bounded=1";

    $response = Http::withHeaders([
        'User-Agent' => 'MbjekApp/1.0'
    ])->get($url);

    return $response->json();
});

// ✨ Tracking order tanpa login (boleh public)

    // routes chat
    Route::get('/chat/{orderId}', [ChatController::class, 'index']);
    Route::post('/chat/send', [ChatController::class, 'store']);
    Route::get('/chat/last-message', [ChatController::class, 'getLastMessage']);
    
// ✨ Routes yang butuh token login
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    Route::get('/profile', [ProfileController::class, 'profile']);
    Route::put('/update-profile', [ProfileController::class, 'updateProfile']);
    Route::post('/profile-upload-photo', [ProfileController::class, 'uploadPhoto']);
    
    // 🟢 Tambahkan di sini: status order & update lokasi driver
    Route::post('/buat-order', [OrderController::class, 'buatOrder']);
    Route::post('/cek-tarif', [OrderController::class, 'cekTarif']);
    Route::post('/order/update-status', [OrderController::class, 'updateStatus']);
    Route::get('/order-status/{id}', [OrderController::class, 'cekStatus']);
    Route::post('/batalkan-order', [OrderController::class, 'batalkanOrder']);
    Route::post('/tolak-order', [OrderController::class, 'tolakOrder']);

    Route::get('/order-driver-terbaru', [OrderController::class, 'getOrderTerbaru']);
    Route::post('/order/terima', [OrderController::class, 'terimaOrder']);
    Route::get('/order/{id}/driver', [DriverController::class, 'getDriverByOrder']);
    Route::post('/perjalanan/update-status', [OrderController::class, 'updatePerjalanan']);
    Route::get('/detail-order/{id}', [HistoryController::class, 'show']);
    
   Route::post('/proxy-ors', [RouteController::class, 'proxyToORS']);

   //chat
    


    //pendapatan
    Route::get('/pendapatan/{driverId}', [DriverController::class, 'PendapatanDriver']);

    // Route di routes/api.php
    Route::post('/midtrans/qris-url', [Midtrans::class, 'getQrisUrl']);
    Route::post('/midtrans-notif', [MidtransController::class, 'handleNotification']);


    Route::post('/driver/update-location', [DriverController::class, 'updateLocation']);
    Route::post('/driver/update-status', [DriverController::class, 'updateStatus']);

    Route::post('/fcm-token', [FCMTokenController::class, 'store']);
    
    Route::post('/beranda', [BerandaController::class, 'GetLocation']);
    Route::get('/riwayat-driver/{id}', [HistoryController::class, 'riwayatLayanan']);
    Route::get('/riwayat-customer/{id}', [HistoryController::class, 'riwayatCustomer']);
    

    Route::post('/logout', [LogoutController::class, 'logout']);


});
