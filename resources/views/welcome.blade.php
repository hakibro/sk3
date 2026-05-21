<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SK3 Santri Boyong') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-gray-900 antialiased">
    <main class="min-h-screen">
        <section class="bg-emerald-950 text-white">
            <div class="mx-auto flex min-h-screen max-w-6xl flex-col px-5 py-6">
                <header class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/logo-ppn-smaller.png') }}" alt="Logo pesantren" class="h-12 w-12 rounded-full bg-white p-1.5">
                        <div>
                            <p class="font-bold">SK3 Santri Boyong</p>
                            <p class="text-xs text-emerald-200">Pondok Pesantren Daruttaqwa</p>
                        </div>
                    </div>

                    @if (Route::has('login'))
                        <a href="{{ route('login') }}"
                            class="rounded-xl bg-white px-4 py-2 text-sm font-bold text-emerald-800 transition hover:bg-emerald-50">
                            Masuk
                        </a>
                    @endif
                </header>

                <div class="grid flex-1 grid-cols-1 items-center gap-10 py-12 lg:grid-cols-2">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.24em] text-emerald-300">Surat Keterangan Kembali Ke Rumah</p>
                        <h1 class="mt-5 text-4xl font-bold leading-tight md:text-6xl">Administrasi boyong santri yang tertib dan mudah diverifikasi.</h1>
                        <p class="mt-5 max-w-xl text-base leading-relaxed text-emerald-100">
                            Pengurus asrama mengajukan santri boyong, sistem mencatat tagihan dan cut pembayaran, lalu surat dicetak dengan QR verifikasi publik.
                        </p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-bold text-emerald-800 hover:bg-emerald-50">
                                Masuk Aplikasi
                            </a>
                            <a href="#alur"
                                class="inline-flex items-center justify-center rounded-xl border border-emerald-700 px-5 py-3 text-sm font-bold text-white hover:bg-emerald-900">
                                Lihat Alur
                            </a>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow-2xl">
                        <div class="rounded-xl bg-white p-5 text-gray-900">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                                <div>
                                    <p class="text-xs font-bold uppercase text-gray-400">Status Pengajuan</p>
                                    <h2 class="mt-1 text-xl font-bold">SK3 siap diproses</h2>
                                </div>
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Lunas</span>
                            </div>
                            <div class="mt-5 space-y-3 text-sm">
                                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-3">
                                    <span>Pengurus Asrama</span>
                                    <strong>Ajukan</strong>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-3">
                                    <span>Pengurus Pusat</span>
                                    <strong>Validasi</strong>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-3">
                                    <span>Dokumen</span>
                                    <strong>QR Publik</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="alur" class="mx-auto max-w-6xl px-5 py-12">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">1</div>
                    <h3 class="mt-4 font-bold">Cek Tagihan</h3>
                    <p class="mt-2 text-sm leading-relaxed text-gray-600">Tagihan dicek dan disimpan sebagai dasar cut pembayaran jika belum lunas.</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">2</div>
                    <h3 class="mt-4 font-bold">Validasi Pusat</h3>
                    <p class="mt-2 text-sm leading-relaxed text-gray-600">Pengurus pusat menyetujui atau menolak pengajuan berdasarkan data sistem.</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">3</div>
                    <h3 class="mt-4 font-bold">Cetak & Verifikasi</h3>
                    <p class="mt-2 text-sm leading-relaxed text-gray-600">Surat dicetak dengan QR agar data boyong bisa dicek tanpa login.</p>
                </div>
            </div>
        </section>
    </main>
</body>

</html>
