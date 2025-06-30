@extends('layouts.admin')

@section('content')
<div class="p-6">
  <div class="bg-white shadow rounded-lg p-6 text-center">
    {{-- FOTO PROFIL --}}
    @if ($admin->profile_picture)
      <img src="{{ asset('storage/' . $admin->profile_picture) }}" class="mx-auto w-24 h-24 rounded-full object-cover mb-4" />
    @else
      <img src="{{ asset('images/default-profile.png') }}" class="mx-auto w-24 h-24 rounded-full object-cover mb-4" />
    @endif

    <h2 class="text-xl font-bold">{{ $admin->name }}</h2>
    <p class="text-gray-600">ADMIN</p>
    <a href="{{ route('admin.profile.edit') }}" class="inline-block mt-4 text-blue-600 hover:underline">
      <i class="material-icons">edit</i> Edit
    </a>

    <div class="mt-6 text-left space-y-3">
      <p><strong>Email:</strong> {{ $admin->email }}</p>
      <p><strong>Tanggal Lahir:</strong> {{ $admin->tanggal_lahir }}</p>
      <p><strong>Alamat:</strong> {{ $admin->alamat }}</p>
      <p><strong>Jenis Kelamin:</strong> {{ $admin->jenis_kelamin }}</p>
    </div>
  </div>
</div>
@endsection
