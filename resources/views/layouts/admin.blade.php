@php
    $admin = Auth::guard('admin')->user();
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0F1D63;
            --secondary-color: #F4F6FA;
            --accent-color: #0B153B;
        }
        .bg-primary { background-color: var(--primary-color); }
        .text-primary { color: var(--primary-color); }
        .bg-accent { background-color: var(--accent-color); }
        .text-accent { color: var(--accent-color); }

        /* Konten animasi fade-in */
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <div class="w-64 bg-primary text-white flex flex-col justify-between">
            <div>
                <!-- Logo -->
                <div class="p-4 text-2xl font-bold border-b border-blue-800">MBJEK</div>

                <!-- Menu -->
                <nav class="mt-4 space-y-2 px-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 hover:bg-blue-800 hover:rounded-lg transition-all duration-300 ease-in-out">
                        <span class="material-icons mr-2">dashboard</span>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-2 hover:bg-blue-800 hover:rounded-lg transition-all duration-300 ease-in-out">
                        <span class="material-icons mr-2">group</span>
                        Manajemen Pengguna & Driver
                    </a>
                    <a href="{{ route('admin.driver.status') }}" class="flex items-center px-4 py-2 hover:bg-blue-800 hover:rounded-lg transition-all duration-300 ease-in-out">
                        <span class="material-icons mr-2">toggle_on</span>
                        Kontrol Status Driver
                    </a>
                    <a href="{{ route('admin.tarif.store') }}" class="flex items-center px-4 py-2 hover:bg-blue-800 hover:rounded-lg transition-all duration-300 ease-in-out">
                        <span class="material-icons mr-2">bar_chart</span>
                        Manajemen Tarif
                    </a>
                    <a href="{{ route('admin.laporan.index') }}" class="flex items-center px-4 py-2 hover:bg-blue-800 hover:rounded-lg transition-all duration-300 ease-in-out">
                        <span class="material-icons mr-2">assessment</span>
                        Laporan Transaksi & Aktivitas
                    </a>
                </nav>
            </div>

            <!-- Logout Button -->
            <div class="p-4 border-t border-blue-800">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left flex items-center px-4 py-2 hover:bg-blue-800 hover:rounded-lg transition-all duration-300 ease-in-out text-white">
                        <span class="material-icons mr-2">exit_to_app</span>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 bg-secondary overflow-y-auto">
            <!-- Header -->
            <div class="bg-primary text-white px-6 py-4 flex justify-between items-center">
                <h1 class="text-2xl font-bold">Admin Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <!-- Chat Icon -->
                    <a href="{{ route('admin.chat') }}" class="relative group">
                        <span class="material-icons text-white cursor-pointer">mark_chat_unread</span>
                        <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1"></span>
                    </a>

                    <!-- Admin Profile Picture -->
                    <a href="{{ route('admin.profile') }}">
                        @if ($admin && $admin->profile_picture)
                            <img src="{{ asset('storage/' . $admin->profile_picture) }}" alt="Foto Profil" class="w-8 h-8 rounded-full object-cover border-2 border-white hover:opacity-80" />
                        @else
                            <img src="{{ asset('images/default-profile.png') }}" alt="Foto Default" class="w-8 h-8 rounded-full object-cover border-2 border-white hover:opacity-80" />
                        @endif
                    </a>
                </div>
            </div>

            <!-- Animated Content Section -->
            <div class="p-6 animate-fade-in">
                @yield('content')
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
