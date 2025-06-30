<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Admin;

class ProfileController extends Controller
{
    // Tampilkan halaman profil admin
    public function show()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    // Tampilkan halaman edit profil
    public function edit()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.edit-profile', compact('admin'));
    }

    // Proses update data profil (tanpa foto)
    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:255',
        ]);

        $admin->fill($request->only('name', 'email', 'tanggal_lahir', 'jenis_kelamin', 'alamat'));
        $admin->save();

        return redirect()->route('admin.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    // Proses update foto profil
    public function updatePhoto(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Hapus foto lama jika ada
        if ($admin->profile_picture) {
            Storage::disk('public')->delete($admin->profile_picture);
        }

        $path = $request->file('profile_picture')->store('profile_photos', 'public');
        $admin->profile_picture = $path;
        $admin->save();

        return back()->with('success', 'Foto profil berhasil diunggah.');
    }

    // Proses hapus foto profil
    public function deletePhoto()
    {
        $admin = Auth::guard('admin')->user();

        if ($admin->profile_picture) {
            Storage::disk('public')->delete($admin->profile_picture);
            $admin->profile_picture = null;
            $admin->save();
        }

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }
}