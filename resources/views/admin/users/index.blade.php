@extends('layouts.admin')

@section('content')
    <h2 class="text-2xl font-bold mb-4">Manajemen Pengguna & Driver</h2>

    <div class="flex items-center mb-4">
        <form method="GET" action="{{ route('admin.users') }}" class="flex">
            <select name="role" onchange="this.form.submit()" class="border rounded px-3 py-1 mr-2">
                <option value="driver" {{ $role === 'driver' ? 'selected' : '' }}>Driver</option>
                <option value="customer" {{ $role === 'customer' ? 'selected' : '' }}>Customer</option>
            </select>
        </form>
        <input type="text" placeholder="Cari berdasarkan nama atau email" class="border rounded px-3 py-1 w-64" />
    </div>

    <div class="bg-white rounded shadow p-4">
        <table class="min-w-full border text-sm text-left">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">No</th>
                    <th class="p-2 border">Nama</th>
                    @if($role === 'driver')
                        <th class="p-2 border">Plat Nomor</th>
                    @endif
                    @if($role === 'customer')
                        <th class="p-2 border">Role</th>
                    @endif
                    <th class="p-2 border">Status Akun</th>
                    <th class="p-2 border">No.HP</th>
                    @if($role === 'driver')
                        <th class="p-2 border">Jenis Kendaraan</th>
                    @endif
                    <th class="p-2 border">Email</th>
                    <th class="p-2 border">Aksi</th> {{-- Kolom baru untuk aksi hapus --}}
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                    <tr>
                        <td class="p-2 border">{{ $index + 1 }}</td>
                        <td class="p-2 border">{{ $user->username }}</td>
                        @if($role === 'driver')
                            <td class="p-2 border">{{ $user->driver->no_plat ?? '-' }}</td>
                        @endif
                        @if($role === 'customer')
                            <td class="p-2 border capitalize">{{ $user->role }}</td>
                        @endif
                        <td class="p-2 border">
                            <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-xs">
                                {{ $user->is_verified ? 'Aktif' : 'Belum Aktif' }}
                            </span>
                        </td>
                        <td class="p-2 border">{{ $user->phone }}</td>
                        @if($role === 'driver')
                            <td class="p-2 border">{{ $user->driver->tipe_kendaraan ?? '-' }}</td>
                        @endif
                        <td class="p-2 border">{{ $user->email }}</td>
                        <td class="p-2 border">
                            <form action="{{ route('admin.user.delete', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white px-2 py-1 text-xs rounded">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
