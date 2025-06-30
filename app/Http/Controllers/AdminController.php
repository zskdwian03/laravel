<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DriverStatus;

class AdminController extends Controller
{
    public function driverStatus(Request $request)
    {
        $search = $request->get('search');
        $statusFilter = $request->get('status'); // nilai: 'online', 'offline', atau null

        $query = User::where('role', 'driver')->with('driverStatus'); // relasi driverStatus di model User

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($statusFilter === 'online') {
            $query->whereHas('driverStatus', function ($q) {
                $q->where('status', 'online');
            });
        } elseif ($statusFilter === 'offline') {
            $query->whereHas('driverStatus', function ($q) {
                $q->where('status', 'offline');
            });
        }

        $drivers = $query->get();

        return view('admin.driver_status', compact('drivers', 'statusFilter', 'search'));
    }
}
