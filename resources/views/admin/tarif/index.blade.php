@extends('layouts.admin')

@section('content')

<!-- Tambah SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#3085d6',
        });
    </script>
@endif

<div class="bg-white rounded shadow p-6">
    <h2 class="text-xl font-bold text-accent mb-4">Edit Tarif</h2>

    <table class="w-full table-auto border border-gray-300 mb-6">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">Jenis Kendaraan</th>
                <th class="px-4 py-2 border">Tarif per Kilometer</th>
                <th class="px-4 py-2 border">Tarif Minimum</th>
                <th class="px-4 py-2 border">Biaya Tambahan</th>
                <th class="px-4 py-2 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tarifs as $tarif)
                <tr>
                    <td class="px-4 py-2 border">{{ $tarif->jenis_kendaraan }}</td>
                    <td class="px-4 py-2 border">Rp {{ number_format($tarif->tarif_per_km) }}</td>
                    <td class="px-4 py-2 border">Rp {{ number_format($tarif->tarif_minimum) }}</td>
                    <td class="px-4 py-2 border">Rp {{ number_format($tarif->biaya_tambahan) }}</td>
                    <td class="px-4 py-2 border text-center">
                        <form action="{{ route('admin.tarif.destroy', $tarif->id) }}" method="POST" class="inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="text-red-600 hover:underline delete-btn">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="text-xl font-bold text-accent mb-4">Tambah Tarif</h2>
    <form action="{{ route('admin.tarif.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        <div>
            <label>Jenis Kendaraan</label>
            <select name="jenis_kendaraan" class="w-full border px-4 py-2 rounded" required>
                <option value="Motor">Motor</option>
                <option value="Mobil">Mobil</option>
            </select>
        </div>
        <div>
            <label>Tarif Per Kilometer</label>
            <input type="number" name="tarif_per_km" class="w-full border px-4 py-2 rounded" required>
        </div>
        <div>
            <label>Tarif Minimum</label>
            <input type="number" name="tarif_minimum" class="w-full border px-4 py-2 rounded">
        </div>
        <div>
            <label>Biaya Tambahan</label>
            <input type="number" name="biaya_tambahan" class="w-full border px-4 py-2 rounded">
        </div>
        <div class="col-span-2 flex justify-end space-x-2">
            <button type="reset" class="px-4 py-2 border rounded">Batal</button>
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded">Simpan</button>
        </div>
    </form>
</div>

<!-- Script Konfirmasi SweetAlert saat Hapus -->
<script>
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('.delete-form');
            Swal.fire({
                title: 'Hapus Tarif?',
                text: "Data akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>

@endsection
