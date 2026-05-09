<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Santri Boyong') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Mencegah konten tertutup bottom nav di mobile */
        @media (max-width: 768px) {
            .content-wrapper {
                padding-bottom: 5rem;
            }
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased">
    <div class="flex min-h-screen">

        <aside class="hidden md:flex flex-col w-72 bg-indigo-900 text-white sticky top-0 h-screen shadow-xl">
            <div class="p-6">
                <h1 class="text-2xl font-bold tracking-wider">BOYONG <span class="text-indigo-300">APP</span></h1>
                <p class="text-xs text-indigo-200 mt-1 uppercase tracking-widest">{{ Auth::user()->role }}</p>
            </div>

            <nav class="flex-1 px-4 mt-4 space-y-1">
                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <i class="fas fa-house fa-fw mr-2"></i> Dashboard
                </x-sidebar-link>

                @if (Auth::user()->isAdmin())
                    <x-sidebar-link :href="route('admin.index')" :active="request()->routeIs('admin.*')">
                        <i class="fas fa-user-shield fa-fw mr-2"></i> Manajemen Admin
                    </x-sidebar-link>
                @endif

                <x-sidebar-link :href="route('boyong.index')" :active="request()->routeIs('boyong.*')">
                    <i class="fas fa-door-open fa-fw mr-2"></i> Data Boyong
                </x-sidebar-link>
            </nav>

            <div class="p-4 border-t border-indigo-800">
                <div class="flex items-center p-2 space-x-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-indigo-700 flex items-center justify-center font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-indigo-300 truncate">{{ Auth::user()->lembaga ?? 'Pusat' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        class="w-full text-left px-4 py-2 text-sm text-indigo-200 hover:bg-indigo-800 rounded-lg transition">
                        Keluar Aplikasi
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="md:hidden bg-white border-b px-4 py-3 flex justify-between items-center sticky top-0 z-40">
                <span class="font-bold text-indigo-700">Santri Boyong</span>
                <div class="flex items-center space-x-2">
                    <span
                        class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded">{{ Auth::user()->lembaga }}</span>
                </div>
            </header>

            <main class="content-wrapper p-4 md:p-10">
                {{ $slot }}
            </main>
        </div>

        @php
            // Contoh sederhana: Ambil jumlah pengajuan pending jika user adalah Pusat
            $pendingCount = Auth::user()->isPusat() ? \App\Models\Boyong::where('status', 'pending')->count() : 0;
        @endphp

        <nav
            class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 flex justify-around items-center py-2 px-1 z-50 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
            <x-bottom-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">Home</x-bottom-nav-link>

            <x-bottom-nav-link :href="route('boyong.index')" :active="request()->routeIs('boyong.index')" icon="list">Daftar</x-bottom-nav-link>

            <x-bottom-nav-link :href="route('boyong.create', ['idperson' => 'search'])" :active="request()->routeIs('boyong.create')" icon="plus-circle"
                isCenter="true">Boyong</x-bottom-nav-link>

            @if (Auth::user()->isAdmin())
                <x-bottom-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')" icon="shield">Admin</x-bottom-nav-link>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button class="w-full flex flex-col items-center text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="text-[10px] mt-1">Keluar</span>
                </button>
            </form>
        </nav>
    </div>
</body>

</html>
