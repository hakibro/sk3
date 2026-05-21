<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi SK3 {{ $boyong->nomor_surat }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900 antialiased">
    <main class="mx-auto max-w-3xl px-4 py-8">
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-green-800">
            <div class="text-sm font-bold uppercase tracking-wide">Surat Terverifikasi</div>
            <p class="mt-1 text-sm">Data berikut berasal dari sistem SK3 dan dapat diakses tanpa login melalui QR pada surat.</p>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-5">
                <h1 class="text-2xl font-bold text-gray-800">Data Santri Boyong</h1>
                <p class="mt-1 text-sm text-gray-500">Nomor Surat: {{ $boyong->nomor_surat ?? '-' }}</p>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y divide-gray-100 md:grid-cols-2 md:divide-x md:divide-y-0">
                <div class="p-6">
                    <h2 class="mb-4 font-bold text-gray-700">Data Santri</h2>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-gray-500">Nama</dt>
                            <dd class="font-semibold text-gray-800">{{ $boyong->siswa->nama ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">ID Person</dt>
                            <dd class="font-semibold text-gray-800">{{ $boyong->idperson }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Asrama</dt>
                            <dd class="font-semibold text-gray-800">{{ $boyong->asrama_asal ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Kamar</dt>
                            <dd class="font-semibold text-gray-800">{{ $boyong->siswa->kamar ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="p-6">
                    <h2 class="mb-4 font-bold text-gray-700">Data Surat</h2>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-gray-500">Status</dt>
                            <dd>
                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-bold text-green-700">
                                    {{ strtoupper($boyong->status) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Alasan</dt>
                            <dd class="font-semibold text-gray-800">{{ $boyong->alasan_kategori ?? $boyong->alasan }}</dd>
                        </div>
                        @if ($boyong->alasan_detail)
                        <div>
                            <dt class="text-gray-500">Detail Alasan</dt>
                            <dd class="font-semibold text-gray-800">{{ $boyong->alasan_detail }}</dd>
                        </div>
                        @endif
                        @if ($boyong->tanggal_boyong)
                        <div>
                            <dt class="text-gray-500">Tanggal Boyong</dt>
                            <dd class="font-semibold text-gray-800">{{ $boyong->tanggal_boyong->translatedFormat('d F Y') }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-gray-500">Tanggal Disetujui</dt>
                            <dd class="font-semibold text-gray-800">{{ optional($boyong->tgl_disetujui)->translatedFormat('d F Y H:i') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Validator</dt>
                            <dd class="font-semibold text-gray-800">{{ $boyong->validator->name ?? 'Pengurus Pusat' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if ($boyong->catatan_pusat)
                <div class="border-t border-gray-100 px-6 py-5">
                    <h2 class="mb-2 font-bold text-gray-700">Catatan Pusat</h2>
                    <p class="text-sm text-gray-600">{{ $boyong->catatan_pusat }}</p>
                </div>
            @endif
        </div>
    </main>
</body>

</html>
