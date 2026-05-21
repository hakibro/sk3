<x-app-layout>
    @php
        $user = Auth::user();
        $baseBoyong = \App\Models\Boyong::query();
        if ($user->isAsrama()) {
            $baseBoyong->where('asrama_asal', $user->lembaga);
        }

        $totalBoyong = (clone $baseBoyong)->count();
        $pendingCount = (clone $baseBoyong)->where('status', 'pending')->count();
        $approvedCount = (clone $baseBoyong)->where('status', 'approved')->count();
        $rejectedCount = (clone $baseBoyong)->where('status', 'rejected')->count();
    @endphp

    <div class="mb-6 overflow-hidden rounded-2xl bg-emerald-950 text-white shadow-sm">
        <div class="flex flex-col gap-5 p-6 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4">
                <img src="{{ asset('assets/logo-ppn-smaller.png') }}" alt="Logo pesantren" class="h-16 w-16 rounded-full bg-white p-1.5">
                <div>
                    <p class="text-sm text-emerald-200">Assalamu'alaikum, {{ $user->name }}</p>
                    <h2 class="mt-1 text-2xl font-bold">Sistem SK3 Santri Boyong</h2>
                    <p class="mt-1 text-sm text-emerald-100">
                        {{ $user->isPusat() ? 'Pantau validasi pengajuan asrama dan penerbitan surat.' : ($user->isAsrama() ? 'Ajukan santri boyong dan catat kebutuhan cut pembayaran.' : 'Kelola akun pengurus dan konfigurasi aplikasi.') }}
                    </p>
                </div>
            </div>
            <a href="{{ $user->isAdmin() ? route('admin.index') : route('siswa.index') }}"
                class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-3 text-sm font-bold text-emerald-800 hover:bg-emerald-50">
                <i class="fa-solid {{ $user->isAdmin() ? 'fa-user-gear' : 'fa-magnifying-glass' }} mr-2"></i>
                {{ $user->isAdmin() ? 'Kelola Sistem' : 'Cari Santri' }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-5">
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-bold uppercase text-gray-400">Total Boyong</p>
            <h3 class="mt-2 text-2xl font-bold text-gray-800">{{ $totalBoyong }}</h3>
        </div>
        <div class="rounded-xl border border-amber-100 bg-amber-50 p-4 shadow-sm">
            <p class="text-xs font-bold uppercase text-amber-600">Menunggu</p>
            <h3 class="mt-2 text-2xl font-bold text-amber-700">{{ $pendingCount }}</h3>
        </div>
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4 shadow-sm">
            <p class="text-xs font-bold uppercase text-emerald-600">Disetujui</p>
            <h3 class="mt-2 text-2xl font-bold text-emerald-700">{{ $approvedCount }}</h3>
        </div>
        <div class="rounded-xl border border-red-100 bg-red-50 p-4 shadow-sm">
            <p class="text-xs font-bold uppercase text-red-600">Ditolak</p>
            <h3 class="mt-2 text-2xl font-bold text-red-700">{{ $rejectedCount }}</h3>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
        @if (!$user->isAdmin())
            <a href="{{ route('siswa.index') }}" class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:border-emerald-300">
                <i class="fa-solid fa-user-graduate text-2xl text-emerald-600"></i>
                <h4 class="mt-4 font-bold text-gray-800">Cari Santri</h4>
                <p class="mt-1 text-sm text-gray-500">Cek asrama, kamar, dan status pembayaran sebelum membuat SK3.</p>
            </a>
        @endif

        <a href="{{ route('boyong.index') }}" class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:border-emerald-300">
            <i class="fa-solid fa-file-signature text-2xl text-teal-600"></i>
            <h4 class="mt-4 font-bold text-gray-800">Daftar Boyong</h4>
            <p class="mt-1 text-sm text-gray-500">{{ $user->isPusat() ? 'Validasi pengajuan dan cetak surat dengan QR verifikasi.' : 'Pantau status pengajuan dari asrama Anda.' }}</p>
        </a>

        @if ($user->isAdmin())
            <a href="{{ route('admin.index') }}" class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:border-emerald-300">
                <i class="fa-solid fa-users-gear text-2xl text-amber-600"></i>
                <h4 class="mt-4 font-bold text-gray-800">Manajemen Admin</h4>
                <p class="mt-1 text-sm text-gray-500">Buat akun pengurus asrama dan pusat, serta kelola alasan boyong.</p>
            </a>
        @endif
    </div>

    <div class="mt-6 rounded-xl border border-emerald-100 bg-white p-5 shadow-sm">
        <h3 class="font-bold text-gray-800">{{ $user->isPusat() ? 'Catatan Validasi Pusat' : 'Pengingat Pengurus Asrama' }}</h3>
        <p class="mt-2 text-sm leading-relaxed text-gray-600">
            {{ $user->isPusat() ? 'Setiap persetujuan akan menghasilkan nomor surat dan QR publik untuk verifikasi dokumen tanpa login.' : 'Jika masih ada tunggakan, sistem menyimpan snapshot tagihan dan aturan cut pembayaran untuk dibaca sistem pembayaran.' }}
        </p>
    </div>
</x-app-layout>
