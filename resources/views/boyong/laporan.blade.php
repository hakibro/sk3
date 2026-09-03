<x-app-layout>
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <x-heroicon-o-chart-bar class="h-6 w-6 inline-block text-indigo-600 mr-2 -mt-1" />Laporan Boyong
            </h2>
            <p class="text-sm text-gray-500">Rekap pengajuan, status validasi, dan catatan cut pembayaran.</p>
        </div>
        <a href="{{ route('boyong.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
            <x-heroicon-o-arrow-left class="h-5 w-5 mr-2" /> Data Boyong
        </a>
    </div>

    <form method="GET" class="mb-5 grid grid-cols-1 gap-3 rounded-xl border border-gray-100 bg-white p-4 shadow-sm md:grid-cols-5">
        @if (Auth::user()->isPusat())
            <div>
                <x-input-label for="asrama" value="Asrama" />
                <select id="asrama" name="asrama" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    @foreach ($asramas as $asrama)
                        <option value="{{ $asrama }}" @selected(request('asrama') === $asrama)>{{ $asrama }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div>
            <x-input-label for="status" value="Status" />
            <select id="status" name="status" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Semua</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
            </select>
        </div>

        <div>
            <x-input-label for="dari" value="Dari" />
            <input id="dari" name="dari" type="date" value="{{ request('dari') }}"
                class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <x-input-label for="sampai" value="Sampai" />
            <input id="sampai" name="sampai" type="date" value="{{ request('sampai') }}"
                class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="flex items-end gap-2">
            <button class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-700">
                <x-heroicon-o-funnel class="h-5 w-5 mr-2" /> Filter
            </button>
        </div>
    </form>

    <div class="grid grid-cols-2 gap-3 md:grid-cols-6">
        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-bold uppercase text-gray-400">Total</p>
            <h3 class="mt-2 text-2xl font-bold text-gray-800">{{ $summary['total'] }}</h3>
        </div>
        <div class="rounded-xl border border-amber-100 bg-amber-50 p-4 shadow-sm">
            <p class="text-xs font-bold uppercase text-amber-600">Pending</p>
            <h3 class="mt-2 text-2xl font-bold text-amber-700">{{ $summary['pending'] }}</h3>
        </div>
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4 shadow-sm">
            <p class="text-xs font-bold uppercase text-emerald-600">Approved</p>
            <h3 class="mt-2 text-2xl font-bold text-emerald-700">{{ $summary['approved'] }}</h3>
        </div>
        <div class="rounded-xl border border-red-100 bg-red-50 p-4 shadow-sm">
            <p class="text-xs font-bold uppercase text-red-600">Rejected</p>
            <h3 class="mt-2 text-2xl font-bold text-red-700">{{ $summary['rejected'] }}</h3>
        </div>
        <div class="rounded-xl border border-sky-100 bg-sky-50 p-4 shadow-sm">
            <p class="text-xs font-bold uppercase text-sky-600">Perlu Cut</p>
            <h3 class="mt-2 text-2xl font-bold text-sky-700">{{ $summary['perlu_cut'] }}</h3>
        </div>
        <div class="rounded-xl border border-violet-100 bg-violet-50 p-4 shadow-sm">
            <p class="text-xs font-bold uppercase text-violet-600">Tagihan Cut</p>
            <h3 class="mt-2 text-lg font-bold text-violet-700">Rp {{ number_format($summary['total_tagihan_cut'], 0, ',', '.') }}</h3>
        </div>
    </div>

    <div class="mt-5 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Santri</th>
                        <th class="px-4 py-3">Asrama</th>
                        <th class="px-4 py-3">Alasan</th>
                        <th class="px-4 py-3">Pembayaran</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($daftarBoyong as $boyong)
                        <tr class="align-top hover:bg-gray-50/60">
                            <td class="px-4 py-4 text-gray-600">
                                <div>{{ optional($boyong->created_at)->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-400">Boyong: {{ optional($boyong->tanggal_boyong)->format('d/m/Y') ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-gray-800">{{ $boyong->siswa->nama ?? 'Data santri tidak ditemukan' }}</div>
                                <div class="text-xs text-gray-400">ID {{ $boyong->idperson }}</div>
                            </td>
                            <td class="px-4 py-4 text-gray-600">
                                <div>{{ $boyong->asrama_asal ?? '-' }}</div>
                                <div class="mt-1"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">{{ $boyong->scope_summary }}</span></div>
                                <div class="mt-1">
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $boyong->cut_off_status === 'sudah' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                        Cut-off: {{ $boyong->cut_off_status === 'sudah' ? 'Sudah' : 'Belum' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-gray-600">
                                <div class="font-semibold text-gray-700">{{ $boyong->alasan_kategori ?? '-' }}</div>
                                <div class="mt-1 max-w-xs text-xs text-gray-500">{{ $boyong->alasan_detail ?? $boyong->alasan }}</div>
                            </td>
                            <td class="px-4 py-4 text-gray-600">
                                @if ($boyong->pembayaran_belum_lunas)
                                    <div class="font-bold text-amber-700">Rp {{ number_format($boyong->total_tagihan_saat_pengajuan, 0, ',', '.') }}</div>
                                    <div class="mt-1 text-xs text-gray-500">Kos makan Rp {{ number_format($boyong->kos_makan_bulan_berjalan, 0, ',', '.') }}</div>
                                    <div class="text-xs text-gray-500">SPP bulan berjalan {{ $boyong->spp_bulan_berjalan_full ? 'full' : 'cut' }}</div>
                                @else
                                    <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-bold text-emerald-700">Lunas</span>
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
                                    {{ strtoupper($boyong->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center text-gray-400">Tidak ada data laporan.</td>
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
