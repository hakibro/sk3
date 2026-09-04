@forelse($siswas as $siswa)
    @php
        $sisaTagihan = $statusLunas[$siswa->idperson] ?? null;
        $isLunas = $sisaTagihan !== null && $sisaTagihan <= 0;
    @endphp
    <div
        class="bg-white px-4 py-3 rounded-xl shadow-sm border border-gray-100 flex flex-row items-center justify-between gap-3 hover:border-emerald-300 transition-all">

        <!-- Bagian Kiri: Informasi Siswa -->
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <h4 class="font-bold text-gray-800 leading-tight truncate">{{ $siswa->nama }}</h4>
                <span
                    class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $isLunas ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                    {{ $isLunas ? 'Lunas' : 'Belum Lunas' }}
                </span>
            </div>
            <p class="mt-1 text-xs text-gray-500">
                ID {{ $siswa->idperson }} | {{ $siswa->asrama ?? 'Non-Asrama' }} | Kamar {{ $siswa->kamar ?? '-' }}
            </p>
            <p class="mt-1 text-xs text-gray-500">
                Formal:
                {{ ($siswa->formal && $siswa->kelas_formal) ? $siswa->formal.' · '.$siswa->kelas_formal : '-' }}
                &nbsp;|&nbsp; Madin:
                {{ ($siswa->madin && $siswa->kelas_madin) ? $siswa->madin.' · '.$siswa->kelas_madin : '-' }}
            </p>
            @if (!$isLunas && $sisaTagihan !== null)
                <p class="mt-1 text-xs font-semibold text-red-600">Sisa: Rp
                    {{ number_format($sisaTagihan, 0, ',', '.') }}</p>
            @endif
        </div>

        <!-- Bagian Kanan: Tombol Aksi -->
        <!-- flex-col (tumpuk di mobile), md:flex-row (sejajar di desktop) -->
        <div class="flex flex-col md:flex-row items-end md:items-center gap-2 shrink-0">
            <a href="{{ route('siswa.show', $siswa->idperson) }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-3 py-2 text-xs font-bold text-gray-600 hover:border-emerald-300 hover:text-emerald-700 w-full md:w-auto">
                <x-heroicon-o-eye class="h-4 w-4 mr-2" /> Detail
            </a>
            <a href="{{ route('boyong.create', $siswa->idperson) }}"
                class="inline-flex items-center justify-center rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-800 w-full md:w-auto">
                <x-heroicon-o-document-plus class="h-4 w-4 mr-2" /> SK3
            </a>
        </div>
    </div>
@empty
    <div class="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300">
        <p class="text-gray-400">Data tidak ditemukan.</p>
    </div>
@endforelse
