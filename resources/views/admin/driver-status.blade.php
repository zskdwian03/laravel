@extends('layouts.admin')

@section('content')
    <div class="space-y-4">
        <h2 class="text-xl font-semibold text-accent">Kontrol Status Driver</h2>

        <!-- Filter -->
        <div class="flex flex-wrap items-center space-x-4">
            <form method="GET" action="{{ route('admin.driver.status') }}" class="flex space-x-2">
                <select name="status" class="border border-gray-300 rounded px-2 py-1">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Online</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Offline</option>
                </select>
                <input type="text" name="search" value="{{ request('search') }}" class="border border-gray-300 rounded px-2 py-1 w-64" placeholder="Cari berdasarkan nama/email">
                <button type="submit" class="bg-primary text-white px-4 py-1 rounded hover:bg-blue-800">Cari</button>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300 mt-4 rounded shadow">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="py-2 px-4 border-b">No</th>
                        <th class="py-2 px-4 border-b">Nama</th>
                        <th class="py-2 px-4 border-b">Status</th>
                        <th class="py-2 px-4 border-b">Waktu Mulai</th>
                        <th class="py-2 px-4 border-b">Performa (7 hari terakhir)</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($drivers as $index => $driver)
                    @php
                        $status = $driver->status;
                        $statusText = $status ? 'Online' : 'Offline';
                        $statusColor = $status ? 'text-green-600' : 'text-red-600';
                        $startTime = $driver->start_time;

                        $progressColor = match(true) {
                            $driver->performance >= 70 => 'bg-green-500',
                            $driver->performance >= 40 => 'bg-yellow-500',
                            default => 'bg-red-500',
                        };
                    @endphp
                    <tr>
                        <td class="py-2 px-4 border-b">{{ $index + 1 }}</td>
                        <td class="py-2 px-4 border-b">{{ $driver->user->nama ?? '-' }}</td>
                        <td class="py-2 px-4 border-b {{ $statusColor }}">
                            {{ $statusText }}
                        </td>
                        <td class="py-2 px-4 border-b">
                            {{ $startTime ? \Carbon\Carbon::parse($startTime)->format('H:i') . ' WIB' : '-' }}
                        </td>
                        <td class="py-2 px-4 border-b">
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2.5 mr-2">
                                    <div 
                                        class="h-2.5 rounded-full {{ $progressColor }} progress-bar" 
                                        style="width: {{ $driver->performance }}%"
                                        title="{{ $driver->performance }}% ({{ round($driver->performance / 10) }} order)"
                                    ></div>
                                </div>
                                <span class="text-xs text-gray-600">{{ $driver->performance }}%</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">Tidak ada data driver.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .progress-bar {
        transition: width 0.3s ease-in-out;
    }
</style>
@endsection