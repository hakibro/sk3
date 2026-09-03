<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Santri Boyong') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Mencegah konten tertutup bottom nav di mobile */
        @media (max-width: 768px) {
            .content-wrapper {
                padding-bottom: 5rem;
            }
        }
    </style>
</head>

<body class="bg-slate-50 text-gray-900 antialiased">
    <div x-data="{ accountOpen: false }" class="flex min-h-screen">

        <aside class="hidden md:flex flex-col w-72 bg-emerald-950 text-white sticky top-0 h-screen shadow-xl">
            <div class="p-6">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('assets/logo-ppn-smaller.png') }}" alt="Logo pesantren" class="h-12 w-12 rounded-full bg-white p-1">
                    <div>
                        <h1 class="text-xl font-bold tracking-wide">SK3 Boyong</h1>
                        <p class="text-xs text-emerald-200 mt-1 uppercase tracking-widest">{{ Auth::user()->role }}</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 mt-4 space-y-1">
                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <x-heroicon-o-home class="h-5 w-5 mr-2 shrink-0" /> Dashboard
                </x-sidebar-link>

                @if (!Auth::user()->isAdmin())
                    <x-sidebar-link :href="route('siswa.index')" :active="request()->routeIs('siswa.*')">
                        <x-heroicon-o-magnifying-glass class="h-5 w-5 mr-2 shrink-0" /> Data Santri
                    </x-sidebar-link>
                @endif

                @if (Auth::user()->isAdmin())
                    <x-sidebar-link :href="route('admin.index')" :active="request()->routeIs('admin.*')">
                        <x-heroicon-o-shield-check class="h-5 w-5 mr-2 shrink-0" /> Manajemen Admin
                    </x-sidebar-link>
                @endif

                <x-sidebar-link :href="route('boyong.index')" :active="request()->routeIs('boyong.index', 'boyong.create', 'boyong.cetak')">
                    <x-heroicon-o-arrow-right-on-rectangle class="h-5 w-5 mr-2 shrink-0" /> Data Boyong
                </x-sidebar-link>

                @if (Auth::user()->isAsrama() || Auth::user()->isPusat())
                    <x-sidebar-link :href="route('boyong.laporan')" :active="request()->routeIs('boyong.laporan')">
                        <x-heroicon-o-chart-bar class="h-5 w-5 mr-2 shrink-0" /> Laporan Boyong
                    </x-sidebar-link>
                @endif
            </nav>

            <div class="p-4 border-t border-emerald-900">
                <div class="flex items-center p-2 space-x-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-800 flex items-center justify-center font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-emerald-300 truncate">{{ Auth::user()->lembaga ?? 'Pusat' }}</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}"
                    class="mb-2 block w-full rounded-lg px-4 py-2 text-left text-sm text-emerald-100 transition hover:bg-emerald-900">
                    Edit Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        class="w-full text-left px-4 py-2 text-sm text-emerald-100 hover:bg-emerald-900 rounded-lg transition">
                        Keluar Aplikasi
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col">
            <main class="content-wrapper p-4 md:p-10">
                {{ $slot }}
            </main>
        </div>

        @php
            // Contoh sederhana: Ambil jumlah pengajuan pending jika user adalah Pusat
            $pendingCount = Auth::user()->isPusat() ? \App\Models\Boyong::where('status', 'pending')->count() : 0;
        @endphp

        <nav
            class="md:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur border-t border-gray-200 grid grid-cols-4 gap-1 py-2 px-2 z-50 shadow-[0_-2px_16px_rgba(15,23,42,0.08)]">
            <x-bottom-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">Home</x-bottom-nav-link>

            <x-bottom-nav-link :href="route('boyong.index')" :active="request()->routeIs('boyong.*')" icon="arrow-right-on-rectangle" :badge="$pendingCount">Boyong</x-bottom-nav-link>

            @if (!Auth::user()->isAdmin())
                <x-bottom-nav-link :href="route('siswa.index')" :active="request()->routeIs('siswa.*')" icon="magnifying-glass">Santri</x-bottom-nav-link>
            @endif

            @if (Auth::user()->isAdmin())
                <x-bottom-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')" icon="shield-check">Admin</x-bottom-nav-link>
            @endif

            <button type="button" @click="accountOpen = true"
                class="flex min-w-0 flex-1 flex-col items-center justify-center gap-1 rounded-xl px-2 py-2 text-center text-gray-400 transition-colors hover:text-emerald-700">
                <span class="flex h-8 w-8 items-center justify-center rounded-full {{ request()->routeIs('profile.*') ? 'bg-emerald-100 text-emerald-700' : '' }}">
                    <x-heroicon-o-user class="h-5 w-5" />
                </span>
                <span class="max-w-full truncate text-[10px] font-bold uppercase tracking-normal">Akun</span>
            </button>
        </nav>

        <div x-show="accountOpen" x-cloak class="fixed inset-0 z-[60] md:hidden" aria-modal="true">
            <div class="absolute inset-0 bg-slate-900/40" @click="accountOpen = false"></div>
            <div class="absolute bottom-0 left-0 right-0 rounded-t-2xl bg-white p-5 shadow-2xl">
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-700">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate font-bold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="truncate text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}"
                    class="mb-2 flex w-full items-center rounded-xl border border-gray-200 px-4 py-3 text-sm font-bold text-gray-700">
                    <x-heroicon-o-pencil-square class="h-5 w-5 mr-3 text-emerald-600" /> Edit Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="flex w-full items-center rounded-xl bg-red-50 px-4 py-3 text-sm font-bold text-red-600">
                        <x-heroicon-o-arrow-right-on-rectangle class="h-5 w-5 mr-3" /> Keluar Aplikasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
