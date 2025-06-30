@extends('layouts.admin')

@section('content')
  <section class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Laporan Transaksi & Aktivitas -->
    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="font-semibold mb-2 text-accent">Laporan Transaksi & Aktivitas</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
          <p class="text-gray-600">Jumlah Perjalanan</p>
          <p class="text-xl font-bold text-accent">{{ $jumlahPerjalanan }}</p>
        </div>
        <div>
          <p class="text-gray-600">Driver Aktif</p>
          <p class="text-xl font-bold text-accent">{{ $driverAktif }}</p>
        </div>
        <div>
          <p class="text-gray-600">Penghasilan Hari Ini</p>
          <p class="text-xl font-bold text-accent">Rp {{ number_format($pendapatanHarian, 0, ',', '.') }}</p>
        </div>
        <div>
          <p class="text-gray-600">Penghasilan Bulan Ini</p>
          <p class="text-xl font-bold text-accent">Rp {{ number_format($pendapatanBulanan, 0, ',', '.') }}</p>
        </div>
      </div>

      <!-- Grafik -->
      <div class="mt-6">
        <canvas id="laporanChart" height="100"></canvas>
      </div>
    </div>

    <!-- Manajemen Tarif -->
    <div class="bg-white shadow rounded-lg p-6 overflow-x-auto">
      <h2 class="font-semibold mb-2 text-accent">Manajemen Tarif</h2>
      <p class="text-gray-600 mb-3">Konfigurasi dasar per kilometer, dan biaya tambahan</p>

      <table class="w-full table-auto border border-gray-300 text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-3 py-2 border">Jenis</th>
            <th class="px-3 py-2 border">Tarif/km</th>
            <th class="px-3 py-2 border">Minimum</th>
            <th class="px-3 py-2 border">Tambahan</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($tarifs as $tarif)
            <tr>
              <td class="px-3 py-2 border">{{ $tarif->jenis_kendaraan }}</td>
              <td class="px-3 py-2 border">Rp {{ number_format($tarif->tarif_per_km) }}</td>
              <td class="px-3 py-2 border">Rp {{ number_format($tarif->tarif_minimum ?? 0) }}</td>
              <td class="px-3 py-2 border">Rp {{ number_format($tarif->biaya_tambahan ?? 0) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center px-4 py-2 border text-gray-500">Belum ada data tarif.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <div class="mt-4 text-right">
        <a href="{{ route('admin.tarif.index') }}" class="text-sm text-primary hover:underline">Kelola Tarif Lengkap →</a>
      </div>
    </div>

    <!-- Manajemen Pengguna & Driver -->
    <div class="bg-white shadow rounded-lg p-6 flex justify-between items-center">
      <div class="w-full">
        <h2 class="font-semibold text-accent">Manajemen Pengguna & Driver</h2>
        <p class="text-gray-600">Validasi Pendaftaran, aktif/nonaktif akun</p>
      </div>
      <a href="{{ route('admin.users') }}" class="ml-4 px-4 py-1 bg-primary text-white rounded hover:bg-blue-800 whitespace-nowrap">
        Lihat
      </a>
    </div>

    <!-- Kontrol Status Driver -->
    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="font-semibold mb-2 text-accent">Kontrol Status Driver</h2>
      <div class="flex justify-between items-center">
        <p class="text-gray-600">Cek status online/offline driver</p>
        <a href="{{ route('admin.driver.status') }}" class="px-4 py-1 bg-primary text-white rounded">Lihat</a>
      </div>
    </div>

  </section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('laporanChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Perjalanan', 'Driver Aktif', 'Pendapatan Hari Ini', 'Pendapatan Bulan Ini'],
      datasets: [{
        label: 'Statistik',
        data: [
          {{ $jumlahPerjalanan }},
          {{ $driverAktif }},
          {{ $pendapatanHarian }},
          {{ $pendapatanBulanan }}
        ],
        backgroundColor: [
          'rgba(54, 162, 235, 0.5)',
          'rgba(255, 206, 86, 0.5)',
          'rgba(75, 192, 192, 0.5)',
          'rgba(153, 102, 255, 0.5)'
        ],
        borderColor: [
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>
@endpush
