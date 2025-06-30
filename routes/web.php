<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController as UserAuthController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DriverStatusController;
use App\Http\Controllers\Admin\LaporanPenghasilanController;
use App\Http\Controllers\Admin\TarifController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to login
// ✅ Redirect root "/" ke halaman login
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// ✅ Form signup & prosesnya
// Menampilkan form pendaftaran (showSignUp) dan memproses datanya (signUp).
Route::get('/signup', [UserAuthController::class, 'showSignUp'])->name('signup');
Route::post('/signup', [UserAuthController::class, 'signUp'])->name('signup.process');

// ✅ Form login & prosesnya
Route::post('/login', [UserAuthController::class, 'login'])->name('login.process');

// ✅ Login & Logout admin
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/profile', [AdminAuthController::class, 'showProfile'])->name('admin.profile');
Route::get('/admin/profile/edit', [AdminAuthController::class, 'editProfile'])->name('admin.profile.edit');
Route::post('/admin/profile/update', [AdminAuthController::class, 'updateProfile'])->name('admin.profile.update');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// ✅ Sidebar Admin
Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.user.delete');
Route::get('/admin/driver-status', [DriverStatusController::class, 'index'])->name('admin.driver.status');
Route::prefix('admin')->name('admin.')->middleware(['auth:admin'])->group(function () {
    Route::resource('tarif', App\Http\Controllers\Admin\TarifController::class)->except(['show']);
});
Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('admin.chat');
});

Route::prefix('admin')->middleware('auth:admin')->name('admin.')->group(function () {
    Route::get('/laporan', [LaporanPenghasilanController::class, 'index'])->name('laporan.index');
Route::get('/laporan/export', [LaporanPenghasilanController::class, 'exportExcel'])->name('laporan.export');
});


// ✅ admin update profile
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/profile', [AdminProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');
});

// ✅ Foto profil Admin
Route::put('/admin/profile/photo', [AdminProfileController::class, 'updatePhoto'])->name('admin.profile.updatePhoto');
Route::delete('/admin/profile/photo', [AdminProfileController::class, 'deletePhoto'])->name('admin.profile.deletePhoto');

// ✅ Verifikasi kode OTP (untuk email)
Route::get('/verification', [UserAuthController::class, 'showVerification'])->name('verification');
Route::post('/verification/verify', [UserAuthController::class, 'verify'])->name('verification.verify');
Route::post('/verification/resend', [UserAuthController::class, 'resendVerificationCode'])->name('verification.resend');

// ✅ Route yang hanya bisa diakses setelah login (untuk admin)
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tambahkan ini:
    Route::get('/tarif', [TarifController::class, 'index'])->name('tarif.index');
});