<x-app-layout>
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $siswa->nama }}</h2>
            <p class="text-sm text-gray-500">ID {{ $siswa->idperson }} | {{ $siswa->asrama ?? '-' }} | Kamar {{ $siswa->kamar ?? '-' }}</p>
        </div>
        <a href="{{ route('boyong.create', $siswa->idperson) }}"
            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-700">
            <i class="fa-solid fa-file-circle-plus mr-2"></i> Ajukan SK3
        </a>
    </div>

    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Sisa Tagihan</p>
            <h3 class="mt-2 text-2xl font-bold {{ $tunggakan > 0 ? 'text-red-600' : 'text-green-600' }}">
                Rp {{ number_format($tunggakan, 0, ',', '.') }}
            </h3>
            <p class="mt-2 text-xs text-gray-500">
                {{ $tunggakan > 0 ? 'SK3 bisa diajukan dengan catatan cut pembayaran.' : 'Santri memenuhi syarat pembayaran untuk SK3.' }}
            </p>
        </div>

        <div class="lg:col-span-2 rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <h3 class="mb-4 font-bold text-gray-800">Ringkasan Periode</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-3 py-2">Periode</th>
                            <th class="px-3 py-2">Tagihan</th>
                            <th class="px-3 py-2">Bayar</th>
                            <th class="px-3 py-2">Sisa</th>
                            <th class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($summaryPeriode as $item)
                            <tr>
                                <td class="px-3 py-3 font-medium">{{ $item->idperiode }}</td>
                                <td class="px-3 py-3">Rp {{ number_format($item->total_kredit, 0, ',', '.') }}</td>
                                <td class="px-3 py-3">Rp {{ number_format($item->total_debet, 0, ',', '.') }}</td>
                                <td class="px-3 py-3">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</td>
                                <td class="px-3 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-bold {{ $item->lunas ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $item->lunas ? 'Lunas' : 'Belum Lunas' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center text-gray-400">Belum ada data pembayaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
        <h3 class="mb-4 font-bold text-gray-800">Detail Tagihan</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-2">Periode</th>
                        <th class="px-3 py-2">Unit</th>
                        <th class="px-3 py-2">Judul</th>
                        <th class="px-3 py-2">Sisa</th>
                        <th class="px-3 py-2">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($detailTagihan as $item)
                        <tr>
                            <td class="px-3 py-3">{{ $item->idperiode }}</td>
                            <td class="px-3 py-3">{{ $item->nama_unit }}</td>
                            <td class="px-3 py-3 font-medium">{{ $item->judul }}</td>
                            <td class="px-3 py-3">Rp {{ number_format($item->selisih, 0, ',', '.') }}</td>
                            <td class="px-3 py-3">{{ $item->lunas ? 'Lunas' : 'Belum Lunas' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-gray-400">Detail tagihan kosong.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
