@extends('layouts.admin')

@section('content')
    <div class="space-y-4">
        <h2 class="text-2xl font-semibold text-accent">Laporan Transaksi & Penghasilan Driver</h2>

        <!-- Form Filter -->
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex flex-col md:flex-row gap-4 bg-white p-4 rounded-lg shadow">
            <div>
                <label for="tanggal" class="text-sm">Harian</label>
                <input type="date" name="tanggal" id="tanggal" value="{{ request('tanggal') }}" class="border rounded px-2 py-1 w-full">
            </div>
            <div>
                <label for="bulan" class="text-sm">Bulanan</label>
                <input type="month" name="bulan" id="bulan" value="{{ request('bulan') }}" class="border rounded px-2 py-1 w-full">
            </div>
            <div class="self-end">
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-900">Tampilkan</button>
            </div>
            <div class="self-end">
                <a href="{{ route('admin.laporan.export', request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Download Excel</a>
            </div>
        </form>

        <!-- Tabel -->
        <div class="overflow-auto bg-white rounded-lg shadow">
            <table class="min-w-full">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">Driver</th>
                        <th class="px-4 py-2 text-left">Waktu Selesai</th>
                        <th class="px-4 py-2 text-left">Tarif</th>
                        <th class="px-4 py-2 text-left">Pendapatan Driver (80%)</th>
                        <th class="px-4 py-2 text-left">Admin (20%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ optional($order->driver->user)->nama ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $order->waktu_selesai }}</td>
                            <td class="px-4 py-2">Rp{{ number_format($order->tarif) }}</td>
                            <td class="px-4 py-2">Rp{{ number_format($order->pendapatan_driver) }}</td>
                            <td class="px-4 py-2">Rp{{ number_format($order->pendapatan_admin) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection