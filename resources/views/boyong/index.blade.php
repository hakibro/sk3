<x-app-layout>
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fa-solid fa-door-open text-indigo-600 mr-2"></i>Data SK3 / Santri Boyong
            </h2>
            <p class="text-sm text-gray-500">
                {{ Auth::user()->isPusat() ? 'Validasi pengajuan asrama dan cetak surat santri boyong.' : 'Pantau pengajuan santri boyong dari asrama Anda.' }}
            </p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row">
            <a href="{{ route('boyong.laporan') }}"
                class="inline-flex items-center justify-center rounded-lg border border-indigo-200 px-4 py-2 text-sm font-bold text-indigo-700 hover:bg-indigo-50">
                <i class="fa-solid fa-chart-column mr-2"></i> Laporan
            </a>
            <a href="{{ route('siswa.index') }}"
                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-700">
                <i class="fa-solid fa-plus mr-2"></i> Pengajuan Baru
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="space-y-3 md:hidden">
        @forelse ($daftarBoyong as $boyong)
            @php
                $statusClass = match ($boyong->status) {
                    'approved' => 'bg-emerald-100 text-emerald-700',
                    'rejected' => 'bg-red-100 text-red-700',
                    default => 'bg-amber-100 text-amber-700',
                };
            @endphp
            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="truncate font-bold text-gray-800">{{ $boyong->siswa->nama ?? 'Data santri tidak ditemukan' }}</h3>
                        <p class="mt-1 text-xs text-gray-500">ID {{ $boyong->idperson }} | {{ $boyong->asrama_asal ?? '-' }}</p>
                    </div>
                    <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold {{ $statusClass }}">
                        {{ strtoupper($boyong->status) }}
                    </span>
                </div>

                @if ($boyong->nomor_surat)
                    <p class="mt-2 text-xs font-semibold text-emerald-700">{{ $boyong->nomor_surat }}</p>
                @endif

                <div class="mt-3 rounded-lg bg-gray-50 px-3 py-2">
                    <p class="text-xs font-bold uppercase text-gray-400">Alasan</p>
                    <p class="mt-1 text-sm font-semibold text-gray-700">{{ $boyong->alasan_kategori ?? $boyong->alasan }}</p>
                    @if ($boyong->alasan_detail)
                        <p class="mt-1 text-xs text-gray-500">{{ $boyong->alasan_detail }}</p>
                    @endif
                </div>

                @if ($boyong->pembayaran_belum_lunas)
                    <div class="mt-3 rounded-lg border border-amber-100 bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        Perlu cut pembayaran: Rp {{ number_format($boyong->total_tagihan_saat_pengajuan, 0, ',', '.') }}
                        <div class="mt-1">Kos makan: Rp {{ number_format($boyong->kos_makan_bulan_berjalan, 0, ',', '.') }} | SPP bulan berjalan: {{ $boyong->spp_bulan_berjalan_full ? 'Full' : 'Cut' }}</div>
                    </div>
                @endif

                @if ($boyong->tanggal_boyong)
                    <div class="mt-3 text-xs text-gray-500">
                        Tanggal boyong: {{ $boyong->tanggal_boyong->format('d/m/Y') }}
                    </div>
                @endif

                <div class="mt-3 flex items-center justify-between gap-3 text-xs text-gray-500">
                    <span>{{ $boyong->pengurus->name ?? '-' }}</span>
                    <span>{{ optional($boyong->created_at)->format('d/m/Y H:i') }}</span>
                </div>

                @if ($boyong->catatan_pusat)
                    <p class="mt-3 rounded-lg bg-slate-50 px-3 py-2 text-xs text-gray-600">{{ $boyong->catatan_pusat }}</p>
                @endif

                <div class="mt-4 flex flex-col gap-2">
                    @if (Auth::user()->isPusat() && $boyong->status === 'pending')
                        <form action="{{ route('boyong.update-status', $boyong->id) }}" method="POST" class="space-y-2">
                            @csrf
                            @method('PATCH')
                            <textarea name="catatan_pusat" rows="2"
                                class="w-full rounded-lg border-gray-200 text-xs focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Catatan pusat"></textarea>
                            <div class="grid grid-cols-2 gap-2">
                                <button name="status" value="rejected"
                                    class="rounded-lg border border-red-200 px-3 py-2 text-xs font-bold text-red-600">
                                    Tolak
                                </button>
                                <button name="status" value="approved"
                                    class="rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white">
                                    Setujui
                                </button>
                            </div>
                        </form>
                    @endif

                    @if ($boyong->pembayaran_belum_lunas)
                        <a href="{{ route('boyong.cetak-keterangan', $boyong->id) }}" target="_blank"
                            class="inline-flex items-center justify-center rounded-lg bg-amber-600 px-3 py-2 text-xs font-bold text-white">
                            <i class="fa-solid fa-file-lines mr-2"></i> Cetak Keterangan
                        </a>
                    @endif

                    @if ($boyong->status === 'approved' && ! $boyong->pembayaran_belum_lunas)
                        <a href="{{ route('boyong.cetak', $boyong->id) }}" target="_blank"
                            class="inline-flex items-center justify-center rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white">
                            <i class="fa-solid fa-print mr-2"></i> Cetak Surat
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 bg-white px-4 py-12 text-center text-gray-400">
                Belum ada pengajuan boyong.
            </div>
        @endforelse
    </div>

    <div class="hidden overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Santri</th>
                        <th class="px-4 py-3">Asrama</th>
                        <th class="px-4 py-3">Alasan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Pengurus</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($daftarBoyong as $boyong)
                        <tr class="align-top hover:bg-gray-50/60">
                            <td class="px-4 py-4">
                                <div class="font-bold text-gray-800">{{ $boyong->siswa->nama ?? 'Data santri tidak ditemukan' }}</div>
                                <div class="text-xs text-gray-400">ID {{ $boyong->idperson }}</div>
                                @if ($boyong->nomor_surat)
                                    <div class="mt-1 text-xs font-medium text-indigo-700">{{ $boyong->nomor_surat }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-gray-600">{{ $boyong->asrama_asal ?? '-' }}</td>
                            <td class="px-4 py-4 text-gray-600">
                                <div class="font-semibold text-gray-700">{{ $boyong->alasan_kategori ?? $boyong->alasan }}</div>
                                @if ($boyong->alasan_detail)
                                    <div class="mt-1 max-w-sm text-xs text-gray-500">{{ $boyong->alasan_detail }}</div>
                                @endif
                                @if ($boyong->pembayaran_belum_lunas)
                                    <div class="mt-2 rounded-lg border border-amber-100 bg-amber-50 px-2 py-1 text-xs text-amber-800">
                                        Cut: Rp {{ number_format($boyong->total_tagihan_saat_pengajuan, 0, ',', '.') }}
                                        | Kos makan Rp {{ number_format($boyong->kos_makan_bulan_berjalan, 0, ',', '.') }}
                                        | SPP {{ $boyong->spp_bulan_berjalan_full ? 'full' : 'cut' }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $statusClass = match ($boyong->status) {
                                        'approved' => 'bg-emerald-100 text-emerald-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default => 'bg-amber-100 text-amber-700',
                                    };
                                @endphp
                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}">
                                    {{ str_replace('_', ' ', strtoupper($boyong->status)) }}
                                </span>
                                @if ($boyong->catatan_pusat)
                                    <div class="mt-2 max-w-xs text-xs text-gray-500">{{ $boyong->catatan_pusat }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-gray-600">
                                <div>{{ $boyong->pengurus->name ?? '-' }}</div>
                                <div class="text-xs text-gray-400">{{ optional($boyong->created_at)->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-col items-end gap-2">
                                    @if (Auth::user()->isPusat() && $boyong->status === 'pending')
                                        <form action="{{ route('boyong.update-status', $boyong->id) }}" method="POST" class="flex flex-col gap-2 md:min-w-64">
                                            @csrf
                                            @method('PATCH')
                                            <textarea name="catatan_pusat" rows="2"
                                                class="w-full rounded-lg border-gray-200 text-xs focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="Catatan pusat (opsional untuk setuju, disarankan untuk tolak)"></textarea>
                                            <div class="flex justify-end gap-2">
                                                <button name="status" value="rejected"
                                                    class="rounded-lg border border-red-200 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-50">
                                                    Tolak
                                                </button>
                                                <button name="status" value="approved"
                                                    class="rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-800">
                                                    Setujui
                                                </button>
                                            </div>
                                        </form>
                                    @endif

                                    @if ($boyong->pembayaran_belum_lunas)
                                        <a href="{{ route('boyong.cetak-keterangan', $boyong->id) }}" target="_blank"
                                            class="inline-flex items-center rounded-lg bg-amber-600 px-3 py-2 text-xs font-bold text-white hover:bg-amber-700">
                                            <i class="fa-solid fa-file-lines mr-2"></i> Keterangan
                                        </a>
                                    @endif

                                    @if ($boyong->status === 'approved' && ! $boyong->pembayaran_belum_lunas)
                                        <a href="{{ route('boyong.cetak', $boyong->id) }}" target="_blank"
                                            class="inline-flex items-center rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-800">
                                            <i class="fa-solid fa-print mr-2"></i> Cetak
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center text-gray-400">Belum ada pengajuan boyong.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $daftarBoyong->links() }}
    </div>
</x-app-layout>
