@extends('layouts.admin')

@section('content')
    <div>
        <h2 class="text-xl font-bold text-accent mb-6">Laporan Transaksi & Aktivitas</h2>

        <!-- Laporan Transaksi -->
        <div class="bg-white rounded shadow p-4 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-accent">Laporan Transaksi</h3>
                <button class="bg-primary text-white px-4 py-1 rounded">Unduh</button>
            </div>
            <div class="grid grid-cols-3 gap-4 mb-4">
                <input type="date" class="border border-gray-300 px-2 py-1 rounded" placeholder="Tanggal Mulai">
                <input type="date" class="border border-gray-300 px-2 py-1 rounded" placeholder="Tanggal Selesai">
                <input type="text" class="border border-gray-300 px-2 py-1 rounded" placeholder="Pengguna">
            </div>
            <table class="w-full table-auto text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2 text-left">Tanggal</th>
                        <th class="p-2 text-left">Pengguna</th>
                        <th class="p-2 text-left">Jenis</th>
                        <th class="p-2 text-left">Jumlah</th>
                        <th class="p-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t">
                        <td class="p-2">5/26/2025</td>
                        <td class="p-2">Kia</td>
                        <td class="p-2">Perjalanan</td>
                        <td class="p-2">Rp 24.000</td>
                        <td class="p-2">Selesai</td>
                    </tr>
                    <tr class="border-t">
                        <td class="p-2">5/20/2025</td>
                        <td class="p-2">Cay</td>
                        <td class="p-2">Order</td>
                        <td class="p-2">Rp 24.000</td>
                        <td class="p-2">Dibatalkan</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Laporan Aktivitas -->
        <div class="bg-white rounded shadow p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-accent">Laporan Aktivitas</h3>
                <button class="bg-primary text-white px-4 py-1 rounded">Unduh</button>
            </div>
            <div class="grid grid-cols-3 gap-4 mb-4">
                <input type="date" class="border border-gray-300 px-2 py-1 rounded" placeholder="Tanggal Mulai">
                <input type="date" class="border border-gray-300 px-2 py-1 rounded" placeholder="Tanggal Selesai">
                <input type="text" class="border border-gray-300 px-2 py-1 rounded" placeholder="Pengguna">
            </div>
            <table class="w-full table-auto text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2 text-left">Tanggal</th>
                        <th class="p-2 text-left">Pengguna</th>
                        <th class="p-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t">
                        <td class="p-2">5/26/2025</td>
                        <td class="p-2">Kia</td>
                        <td class="p-2">Login</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection