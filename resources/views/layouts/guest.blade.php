<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SK3 Santri Boyong') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-slate-50">
            <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
                <div class="hidden bg-emerald-950 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/logo-ppn-smaller.png') }}" alt="Logo pesantren" class="h-14 w-14 rounded-full bg-white p-1.5">
                        <div>
                            <h1 class="text-xl font-bold">SK3 Santri Boyong</h1>
                            <p class="text-sm text-emerald-200">Pondok Pesantren Daruttaqwa</p>
                        </div>
                    </div>
                    <div class="max-w-xl">
                        <p class="text-sm font-bold uppercase tracking-[0.24em] text-emerald-300">Surat Keterangan Kembali Ke Rumah</p>
                        <h2 class="mt-4 text-4xl font-bold leading-tight">Alur boyong yang tertib, jelas, dan mudah diverifikasi.</h2>
                        <p class="mt-4 text-base leading-relaxed text-emerald-100">Pengurus asrama mengajukan, pengurus pusat memvalidasi, dan dokumen dapat dicek melalui QR publik.</p>
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-sm">
                        <div class="rounded-xl bg-white/10 p-4">
                            <div class="font-bold">Lunas</div>
                            <div class="mt-1 text-emerald-200">Cek tagihan</div>
                        </div>
                        <div class="rounded-xl bg-white/10 p-4">
                            <div class="font-bold">Valid</div>
                            <div class="mt-1 text-emerald-200">Persetujuan pusat</div>
                        </div>
                        <div class="rounded-xl bg-white/10 p-4">
                            <div class="font-bold">QR</div>
                            <div class="mt-1 text-emerald-200">Verifikasi surat</div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center px-5 py-8">
                    <div class="w-full max-w-md">
                        <div class="mb-6 text-center lg:hidden">
                            <img src="{{ asset('assets/logo-ppn-smaller.png') }}" alt="Logo pesantren" class="mx-auto h-16 w-16 rounded-full bg-white p-1.5 shadow-sm">
                            <h1 class="mt-3 text-xl font-bold text-emerald-900">SK3 Santri Boyong</h1>
                            <p class="text-sm text-gray-500">Pondok Pesantren Daruttaqwa</p>
                        </div>

                        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white px-6 py-6 shadow-sm">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
