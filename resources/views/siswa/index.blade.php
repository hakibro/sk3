<x-app-layout>
    <div x-data="siswaSearch()" x-init="init()" class="space-y-6">

        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    <x-heroicon-o-academic-cap class="h-6 w-6 inline-block text-indigo-600 mr-2 -mt-1" />Data Siswa / Santri
                </h2>
                <p class="text-sm text-gray-500">Cari santri untuk pengajuan boyong atau cek tagihan.</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="relative flex-1">
                    <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                    <input type="text" x-model.debounce.500ms="filters.search"
                        class="w-full pl-10 pr-4 py-2 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Cari nama atau ID Person...">
                </div>

                <select x-model="filters.asrama" {{ Auth::user()->isAsrama() ? 'disabled' : '' }}
                    class="w-full py-2 border-gray-200 rounded-xl disabled:bg-gray-100">
                    @if (!Auth::user()->isAsrama())
                        <option value="">Semua Asrama</option>
                    @endif
                    @foreach ($listAsrama as $asrama)
                        <option value="{{ $asrama }}">{{ $asrama }}</option>
                    @endforeach
                </select>

                <select x-model="filters.kamar"
                    class="w-full py-2 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Semua Kamar</option>
                    <template x-for="namaKamar in filteredKamars" :key="namaKamar">
                        <option :value="namaKamar" x-text="namaKamar"></option>
                    </template>
                </select>
            </div>
        </div>

        <div x-show="loading" class="text-center py-10">
            <x-heroicon-o-arrow-path class="animate-spin h-8 w-8 text-indigo-600 mx-auto" />
            <p class="text-gray-500 mt-2">Memperbarui data...</p>
        </div>

        <div x-show="!loading" id="siswa-list" class="grid grid-cols-1 gap-4">
            {!! $htmlContent !!}
        </div>

        <div x-show="!loading" id="pagination-links" class="mt-6">
            {!! $paginationLinks !!}
        </div>
    </div>

    <script>
        function siswaSearch() {
            return {
                loading: false,
                filters: {
                    search: '{{ request('search') }}',
                    // Pastikan jika pengurus asrama, nilai awal 'asrama' tidak kosong
                    asrama: '{{ Auth::user()->isAsrama() ? Auth::user()->lembaga : request('filter_asrama') }}',
                    kamar: '{{ request('filter_kamar') }}',
                    page: 1
                },
                allKamars: @json($listKamar), // Dari controller

                // Computed-like function untuk filter kamar di dropdown
                get filteredKamars() {
                    if (!this.filters.asrama) {
                        // Jika asrama belum dipilih, tampilkan semua kamar unik
                        return [...new Set(this.allKamars.map(item => item.kamar))];
                    }

                    // Jika asrama dipilih, tampilkan kamar yang hanya milik asrama tersebut
                    return this.allKamars
                        .filter(item => item.asrama === this.filters.asrama)
                        .map(item => item.kamar);
                },

                init() {
                    // Watchers untuk trigger search saat filter berubah
                    this.$watch('filters.search', () => this.fetchSiswa(1));
                    this.$watch('filters.asrama', () => {
                        this.filters.kamar = ''; // Reset pilihan kamar jika asrama ganti
                        this.fetchSiswa(1);
                    });
                    this.$watch('filters.kamar', () => this.fetchSiswa(1));

                    // Tangkap klik pagination secara global di dalam container
                    document.addEventListener('click', (e) => {
                        const link = e.target.closest('#pagination-links a');
                        if (link) {
                            e.preventDefault();
                            const url = new URL(link.href);
                            this.filters.page = url.searchParams.get('page');
                            this.fetchSiswa(this.filters.page);
                        }
                    });
                },

                async fetchSiswa(page = 1) {
                    this.loading = true;
                    this.filters.page = page;

                    const params = new URLSearchParams({
                        search: this.filters.search,
                        filter_asrama: this.filters.asrama, // Pastikan kunci ini 'filter_asrama'
                        filter_kamar: this.filters.kamar, // Pastikan kunci ini 'filter_kamar'
                        page: page,
                        ajax: 1
                    });

                    try {
                        const response = await fetch(`{{ route('siswa.index') }}?${params.toString()}`);
                        const data = await response.json();

                        document.getElementById('siswa-list').innerHTML = data.html;
                        document.getElementById('pagination-links').innerHTML = data.pagination;

                        // Update URL tanpa reload
                        window.history.pushState({}, '', `?${params.toString()}`);
                    } catch (error) {
                        console.error('Error fetching data:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
