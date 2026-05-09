@forelse($siswas as $siswa)
    <div
        class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:border-indigo-300 transition-all">
        <div class="flex items-center space-x-4">
            <div>
                <h4 class="font-bold text-gray-800">{{ $siswa->nama }}</h4>
                <p class="text-xs text-gray-400">ID: {{ $siswa->idperson }} | Kamar: {{ $siswa->kamar ?? '-' }}</p>
                <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-bold uppercase">
                    {{ $siswa->asrama ?? 'Non-Asrama' }}
                </span>
            </div>
        </div>
    </div>
@empty
    <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
        <p class="text-gray-400">Data tidak ditemukan.</p>
    </div>
@endforelse
