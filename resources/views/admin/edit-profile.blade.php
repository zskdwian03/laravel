@extends('layouts.admin')

@section('content')
<div class="flex min-h-screen">
  {{-- Konten Utama --}}
  <div class="flex-1 p-6 overflow-y-auto">
    <div class="bg-white shadow rounded-lg p-6 max-w-4xl mx-auto">
      <h2 class="text-xl font-bold mb-4">Edit Profil Admin</h2>

      <div class="text-center mb-4">
          @if ($admin->profile_picture)
              <img src="{{ asset('storage/' . $admin->profile_picture) }}" class="rounded-full mx-auto w-24 h-24 object-cover" />
          @else
              <img src="{{ asset('images/default-profile.png') }}" class="rounded-full mx-auto w-24 h-24 object-cover" />
          @endif

          <form action="{{ route('admin.profile.updatePhoto') }}" method="POST" enctype="multipart/form-data" class="mt-2">
              @csrf
              @method('PUT')
              <input type="file" name="profile_picture" required>
              <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded mt-2">Upload Foto</button>
          </form>

          @if ($admin->profile_picture)
              <form action="{{ route('admin.profile.deletePhoto') }}" method="POST" class="mt-2">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Hapus Foto</button>
              </form>
          @endif
      </div>

      <form method="POST" action="{{ route('admin.profile.update') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block">Nama</label>
            <input type="text" name="name" value="{{ $admin->name }}" class="w-full border rounded p-2">
          </div>
          <div>
            <label class="block">Email</label>
            <input type="email" name="email" value="{{ $admin->email }}" class="w-full border rounded p-2">
          </div>
          <div>
            <label class="block">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" value="{{ $admin->tanggal_lahir }}" class="w-full border rounded p-2">
          </div>
          <div>
            <label class="block">Jenis Kelamin</label>
            <input type="text" name="jenis_kelamin" value="{{ $admin->jenis_kelamin }}" class="w-full border rounded p-2">
          </div>
          <div class="md:col-span-2">
            <label class="block">Alamat</label>
            <textarea name="alamat" class="w-full border rounded p-2">{{ $admin->alamat }}</textarea>
          </div>
        </div>

        <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
      </form>
    </div>
  </div>
</div>
@endsection
