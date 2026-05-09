<x-app-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Assalamu'alaikum, {{ Auth::user()->name }}</h2>
        <p class="text-gray-500">Selamat datang di Sistem Informasi Santri Boyong.</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <a href="{{ route('siswa.index') }}"
            class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-indigo-500 transition-all flex flex-col items-center text-center">
            <div
                class="bg-indigo-50 p-4 rounded-xl text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors mb-4">
                <i class="fa-solid fa-magnifying-glass text-2xl"></i>
            </div>
            <h4 class="font-bold text-gray-700 text-sm md:text-base">Cari Santri</h4>
            <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider font-bold">Pengajuan Baru</p>
        </a>

        <a href="{{ route('boyong.index') }}"
            class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-indigo-500 transition-all flex flex-col items-center text-center">
            <div
                class="bg-green-50 p-4 rounded-xl text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors mb-4 relative">
                <i class="fa-solid fa-file-invoice text-2xl"></i>
                @php $pendingCount = \App\Models\Boyong::where('status', 'pending')->count(); @endphp
                @if ($pendingCount > 0 && Auth::user()->isPusat())
                    <span
                        class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full border-2 border-white">{{ $pendingCount }}</span>
                @endif
            </div>
            <h4 class="font-bold text-gray-700 text-sm md:text-base">Data Boyong</h4>
            <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider font-bold">List Pengajuan</p>
        </a>

        @if (Auth::user()->isAdmin())
            <a href="{{ route('admin.index') }}"
                class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-indigo-500 transition-all flex flex-col items-center text-center">
                <div
                    class="bg-amber-50 p-4 rounded-xl text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors mb-4">
                    <i class="fa-solid fa-user-gear text-2xl"></i>
                </div>
                <h4 class="font-bold text-gray-700 text-sm md:text-base">Kelola Sistem</h4>
                <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider font-bold">Manajemen Admin</p>
            </a>
        @endif

        <a href="{{ route('profile.edit') }}"
            class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-indigo-500 transition-all flex flex-col items-center text-center">
            <div
                class="bg-slate-50 p-4 rounded-xl text-slate-600 group-hover:bg-slate-600 group-hover:text-white transition-colors mb-4">
                <i class="fa-solid fa-id-card text-2xl"></i>
            </div>
            <h4 class="font-bold text-gray-700 text-sm md:text-base">Akun Saya</h4>
            <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider font-bold">Ubah Profil</p>
        </a>
    </div>

    <div class="mt-8 bg-indigo-900 rounded-2xl p-6 text-white flex items-center justify-between">
        <div class="max-w-md">
            <h3 class="text-lg font-bold mb-1">Penting bagi Pengurus Asrama!</h3>
            <p class="text-indigo-200 text-sm leading-relaxed">Pastikan mengecek detail pembayaran santri sebelum
                mengajukan boyong untuk menghindari penolakan dari Pengurus Pusat.</p>
        </div>
        <i class="fa-solid fa-circle-info text-4xl text-indigo-700 hidden md:block"></i>
    </div>
</x-app-layout>
