<x-app-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <x-heroicon-o-document-plus class="h-6 w-6 inline-block text-indigo-600 mr-2 -mt-1" />Pengajuan SK3
        </h2>
        <p class="text-sm text-gray-500">Pengajuan bisa dibuat meski ada tagihan, dengan catatan cut pembayaran untuk sistem pembayaran.</p>
    </div>

    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <h3 class="font-bold text-gray-800">{{ $siswa->nama }}</h3>
            <p class="mt-1 text-sm text-gray-500">ID {{ $siswa->idperson }}</p>
            <div class="mt-4 space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>Asrama</span>
                    <span class="font-medium">{{ $siswa->asrama ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Kamar</span>
                    <span class="font-medium">{{ $siswa->kamar ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Sisa Tagihan</span>
                    <span class="font-bold {{ $kelayakan['sisa_tagihan'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $kelayakan['formatted_tagihan'] }}
                    </span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            @if ($kelayakan['pengajuan_aktif'])
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    Santri ini sudah memiliki pengajuan aktif dengan status
                    <strong>{{ strtoupper($kelayakan['pengajuan_aktif']->status) }}</strong>.
                </div>
            @else
                @if ($kelayakan['sisa_tagihan'] > 0)
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    Santri masih memiliki tagihan. Pengajuan tetap bisa dibuat, dan informasi ini akan disimpan untuk proses cut pembayaran.
                </div>
                <div class="mt-5">
                    <h3 class="mb-3 font-bold text-gray-800">Detail Tagihan Belum Dibayar</h3>
                    @php
                        $tagihanPerPeriode = collect($kelayakan['detail_belum_lunas'])->groupBy('idperiode');
                    @endphp

                    <div class="space-y-4">
                        @forelse ($tagihanPerPeriode as $periode => $items)
                            <div class="overflow-hidden rounded-lg border border-gray-100 bg-white">
                                <div class="flex flex-col gap-2 bg-gray-50 px-4 py-3 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Periode</p>
                                        <h4 class="font-bold text-gray-800">{{ $periode }}</h4>
                                    </div>
                                    <div class="text-sm font-bold text-red-600">
                                        Subtotal: Rp {{ number_format($items->sum('selisih'), 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm">
                                        <thead class="border-b border-gray-100 text-xs uppercase text-gray-500">
                                            <tr>
                                                <th class="px-3 py-2">Unit</th>
                                                <th class="px-3 py-2">Tagihan</th>
                                                <th class="px-3 py-2 text-right">Kredit</th>
                                                <th class="px-3 py-2 text-right">Debet</th>
                                                <th class="px-3 py-2 text-right">Sisa</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach ($items as $tagihan)
                                                <tr>
                                                    <td class="px-3 py-3 text-gray-600">{{ $tagihan->nama_unit }}</td>
                                                    <td class="px-3 py-3 font-medium text-gray-700">{{ $tagihan->judul }}</td>
                                                    <td class="px-3 py-3 text-right text-gray-600">Rp {{ number_format($tagihan->jml_kredit, 0, ',', '.') }}</td>
                                                    <td class="px-3 py-3 text-right text-gray-600">Rp {{ number_format($tagihan->jml_debet, 0, ',', '.') }}</td>
                                                    <td class="px-3 py-3 text-right font-bold text-red-600">Rp {{ number_format($tagihan->selisih, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-lg border border-gray-100 bg-white px-3 py-8 text-center text-gray-400">
                                Tidak ada detail tagihan belum lunas.
                            </div>
                        @endforelse

                        <div class="rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-right text-sm font-bold text-red-700">
                            Total Sisa Tagihan: {{ $kelayakan['formatted_tagihan'] }}
                        </div>
                    </div>
                </div>
                @endif

                <form action="{{ route('boyong.store') }}" method="POST" class="mt-5 space-y-4">
                    @csrf
                    <input type="hidden" name="idperson" value="{{ $siswa->idperson }}">

                    <div>
                        <x-input-label for="tanggal_boyong" value="Tanggal Boyong" />
                        <input id="tanggal_boyong" name="tanggal_boyong" type="date" required
                            value="{{ old('tanggal_boyong', now()->toDateString()) }}"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Jika tanggal boyong lebih dari tanggal {{ \App\Models\AppSetting::cutOffTanggalMax() }}, SPP bulan berjalan dicatat full.</p>
                        <x-input-error :messages="$errors->get('tanggal_boyong')" class="mt-2" />
                    </div>

                    @if ($kelayakan['sisa_tagihan'] > 0)
                        <div>
                            <x-input-label for="kos_makan_bulan_berjalan" value="Nominal Kos Makan Bulan Berjalan" />
                            <input id="kos_makan_bulan_berjalan" name="kos_makan_bulan_berjalan" type="number" min="0" step="1"
                                value="{{ old('kos_makan_bulan_berjalan', 0) }}"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="0">
                            <x-input-error :messages="$errors->get('kos_makan_bulan_berjalan')" class="mt-2" />
                        </div>
                    @endif

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <x-input-label value="Cakupan Boyong" />
                        <p class="mt-1 text-xs text-gray-500">Tandai lembaga yang menjadi alasan santri tidak lagi beraktivitas. Cut-off pembayaran diterapkan sesuai centang di bawah ini.</p>
                        <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2.5">
                                <input type="checkbox" name="boyong_scope[asrama]" value="1" checked
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm font-semibold text-gray-700">Asrama</span>
                            </label>
                            <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2.5">
                                <input type="checkbox" name="boyong_scope[madin]" value="1"
                                    @checked(old('boyong_scope.madin'))
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm font-semibold text-gray-700">Madin</span>
                            </label>
                            <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2.5">
                                <input type="checkbox" name="boyong_scope[formal]" value="1"
                                    @checked(old('boyong_scope.formal'))
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm font-semibold text-gray-700">Formal</span>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('boyong_scope')" class="mt-2" />
                    </div>

                    <div x-data="{ kategori: @js(old('alasan_kategori', '')) }">
                        <x-input-label for="alasan_kategori" value="Klasifikasi Alasan" />
                        <select id="alasan_kategori" name="alasan_kategori" x-model="kategori" required
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Pilih alasan</option>
                            @foreach ($alasans as $alasan)
                                <option value="{{ $alasan->nama_alasan }}" @selected(old('alasan_kategori') === $alasan->nama_alasan)>{{ $alasan->nama_alasan }}</option>
                            @endforeach
                            <option value="Lainnya" @selected(old('alasan_kategori') === 'Lainnya')>Lainnya</option>
                        </select>
                        <x-input-error :messages="$errors->get('alasan_kategori')" class="mt-2" />

                        <div x-show="kategori === 'Lainnya'" x-cloak class="mt-3">
                            <x-input-label for="alasan_lainnya" value="Alasan Lainnya" />
                            <input id="alasan_lainnya" name="alasan_lainnya" type="text"
                                value="{{ old('alasan_lainnya') }}"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Tulis klasifikasi alasan">
                            <x-input-error :messages="$errors->get('alasan_lainnya')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="alasan_detail" value="Detail Alasan Pengurus" />
                        <textarea id="alasan_detail" name="alasan_detail" rows="5" required
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Contoh: Dijemput wali santri setelah menyelesaikan proses administrasi pondok.">{{ old('alasan_detail') }}</textarea>
                        <x-input-error :messages="$errors->get('alasan_detail')" class="mt-2" />
                    </div>

                    <div class="flex flex-col gap-3 border-t pt-4 md:flex-row md:items-center md:justify-between">
                        <p class="text-sm text-gray-500">
                            {{ Auth::user()->isPusat() ? 'Pengurus pusat akan langsung membuat surat approved.' : 'Pengajuan akan masuk ke antrean validasi pengurus pusat.' }}
                        </p>
                        <x-primary-button class="justify-center">
                            <x-heroicon-o-paper-airplane class="h-4 w-4 mr-2" /> Proses SK3
                        </x-primary-button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
