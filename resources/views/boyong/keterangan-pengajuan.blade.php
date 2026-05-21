<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keterangan Pengajuan Boyong {{ $boyong->siswa->nama ?? $boyong->idperson }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #111827;
            margin: 40px;
        }

        .kop {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            text-align: center;
            border-bottom: 3px double #111827;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .kop img {
            width: 78px;
            height: 78px;
            object-fit: contain;
        }

        .kop .kop-text {
            flex: 1;
        }

        .kop h1 {
            font-size: 21px;
            margin: 0 0 2px;
            letter-spacing: .4px;
            text-transform: uppercase;
        }

        .kop p {
            margin: 2px 0;
            font-size: 12px;
            line-height: 1.35;
        }

        .kop .nspp {
            font-size: 13px;
            font-weight: bold;
        }

        .title {
            text-align: center;
            margin-bottom: 24px;
        }

        .title h2 {
            font-size: 18px;
            text-decoration: underline;
            margin: 0 0 6px;
        }

        .content {
            line-height: 1.65;
            font-size: 14px;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0;
        }

        .meta td {
            padding: 5px 4px;
            vertical-align: top;
        }

        .meta td:first-child {
            width: 190px;
        }

        .tagihan {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 12px;
        }

        .tagihan th,
        .tagihan td {
            border: 1px solid #d1d5db;
            padding: 7px;
            vertical-align: top;
        }

        .tagihan th {
            background: #f3f4f6;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .summary {
            margin-top: 16px;
            border: 1px solid #f59e0b;
            background: #fffbeb;
            padding: 12px;
            font-size: 13px;
        }

        .signature {
            margin-top: 40px;
            width: 280px;
            margin-left: auto;
            text-align: center;
        }

        .signature .space {
            height: 70px;
        }

        .signature .name {
            font-weight: bold;
            text-decoration: underline;
        }

        .actions {
            margin-bottom: 24px;
            text-align: right;
        }

        .actions button {
            background: #d97706;
            color: white;
            border: 0;
            border-radius: 8px;
            padding: 10px 14px;
            font-weight: bold;
            cursor: pointer;
        }

        @media print {
            body {
                margin: 22mm;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="actions">
        <button onclick="window.print()">Cetak Keterangan</button>
    </div>

    <div class="kop">
        <img src="{{ asset('assets/logo-ppn-smaller.png') }}" alt="Logo pesantren">
        <div class="kop-text">
            <h1>{{ $kopSurat['nama_pesantren'] }}</h1>
            @if ($kopSurat['nspp'])
                <p class="nspp">NSPP: {{ $kopSurat['nspp'] }}</p>
            @endif
            <p>{{ $kopSurat['alamat'] }}</p>
        </div>
    </div>

    <div class="title">
        <h2>SURAT KETERANGAN PENGAJUAN BOYONG</h2>
        <p>Nomor Pengajuan: {{ str_pad($boyong->id, 3, '0', STR_PAD_LEFT) }}/PENGAJUAN-BOYONG/{{ now()->format('Y') }}</p>
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini menerangkan bahwa pengurus telah mengajukan proses boyong untuk santri berikut:</p>

        <table class="meta">
            <tr>
                <td>Nama</td>
                <td>: {{ $boyong->siswa->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>ID Person</td>
                <td>: {{ $boyong->idperson }}</td>
            </tr>
            <tr>
                <td>Asrama</td>
                <td>: {{ $boyong->asrama_asal ?? '-' }}</td>
            </tr>
            <tr>
                <td>Kamar</td>
                <td>: {{ $boyong->siswa->kamar ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal Pengajuan</td>
                <td>: {{ optional($boyong->created_at)->translatedFormat('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td>Rencana Tanggal Boyong</td>
                <td>: {{ optional($boyong->tanggal_boyong)->translatedFormat('d F Y') ?? '-' }}</td>
            </tr>
            <tr>
                <td>Pengurus Pengaju</td>
                <td>: {{ $boyong->pengurus->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Alasan</td>
                <td>: {{ $boyong->alasan_kategori ?? $boyong->alasan }}</td>
            </tr>
            @if ($boyong->alasan_detail)
            <tr>
                <td>Detail Alasan</td>
                <td>: {{ $boyong->alasan_detail }}</td>
            </tr>
            @endif
        </table>

        <p>
            Surat ini bukan SK3 final. SK3 hanya dapat dicetak setelah seluruh syarat boyong terpenuhi,
            yaitu tagihan santri sampai bulan berjalan telah dilunasi atau telah diselesaikan sesuai ketentuan pembayaran.
        </p>

        <div class="summary">
            <strong>Syarat pembayaran yang perlu diselesaikan:</strong><br>
            Total tagihan saat pengajuan: <strong>Rp {{ number_format($boyong->total_tagihan_saat_pengajuan, 0, ',', '.') }}</strong><br>
            Sisa tagihan saat surat ini dicetak: <strong>Rp {{ number_format($sisaTagihanSaatIni, 0, ',', '.') }}</strong><br>
            Kos makan bulan berjalan: <strong>Rp {{ number_format($boyong->kos_makan_bulan_berjalan, 0, ',', '.') }}</strong><br>
            SPP bulan berjalan: <strong>{{ $boyong->spp_bulan_berjalan_full ? 'Tagihan full karena tanggal boyong lebih dari tanggal 6' : 'Dapat dicut karena tanggal boyong sampai tanggal 6' }}</strong>
        </div>

        <h3>Rincian Tagihan Saat Pengajuan</h3>
        <table class="tagihan">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Unit</th>
                    <th>Tagihan</th>
                    <th class="text-right">Kredit</th>
                    <th class="text-right">Debet</th>
                    <th class="text-right">Sisa</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($snapshotTagihan as $tagihan)
                    <tr>
                        <td>{{ $tagihan['idperiode'] ?? '-' }}</td>
                        <td>{{ $tagihan['nama_unit'] ?? '-' }}</td>
                        <td>{{ $tagihan['judul'] ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($tagihan['jml_kredit'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($tagihan['jml_debet'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($tagihan['selisih'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Tidak ada rincian tagihan tersimpan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <p>Demikian surat keterangan pengajuan ini dibuat untuk menjadi dasar penyelesaian syarat boyong oleh pengurus.</p>
    </div>

    <div class="signature">
        <p>Pasuruan, {{ now()->translatedFormat('d F Y') }}</p>
        <p>Pengurus</p>
        <div class="space"></div>
        <div class="name">{{ Auth::user()->name }}</div>
    </div>
</body>

</html>
