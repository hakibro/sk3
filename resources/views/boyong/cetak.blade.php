<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SK3 {{ $boyong->siswa->nama ?? $boyong->idperson }}</title>
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
            margin-bottom: 28px;
        }

        .title h2 {
            font-size: 18px;
            text-decoration: underline;
            margin: 0 0 6px;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .meta td {
            padding: 6px 4px;
            vertical-align: top;
        }

        .meta td:first-child {
            width: 180px;
        }

        .content {
            line-height: 1.7;
            font-size: 15px;
        }

        .verification {
            margin-top: 44px;
            width: 280px;
            margin-left: auto;
            text-align: center;
        }

        .verification img {
            width: 150px;
            height: 150px;
            border: 1px solid #e5e7eb;
            padding: 8px;
        }

        .verification .name {
            margin-top: 10px;
            font-weight: bold;
        }

        .verification .hint {
            font-size: 11px;
            color: #4b5563;
            line-height: 1.4;
            margin-top: 8px;
        }

        .actions {
            margin-bottom: 24px;
            text-align: right;
        }

        .actions button {
            background: #4f46e5;
            color: white;
            border: 0;
            border-radius: 8px;
            padding: 10px 14px;
            font-weight: bold;
            cursor: pointer;
        }

        @media print {
            body {
                margin: 24mm;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="actions">
        <button onclick="window.print()">Cetak Surat</button>
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
        <h2>SURAT KETERANGAN KEMBALI KE RUMAH (SK3)</h2>
        <p>Nomor: {{ $boyong->nomor_surat ?? '-' }}</p>
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini menerangkan bahwa santri berikut:</p>

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
                <td>Alasan</td>
                <td>: {{ $boyong->alasan_kategori ?? $boyong->alasan }}</td>
            </tr>
            @if ($boyong->alasan_detail)
            <tr>
                <td>Detail Alasan</td>
                <td>: {{ $boyong->alasan_detail }}</td>
            </tr>
            @endif
            @if ($boyong->tanggal_boyong)
            <tr>
                <td>Tanggal Boyong</td>
                <td>: {{ $boyong->tanggal_boyong->translatedFormat('d F Y') }}</td>
            </tr>
            @endif
        </table>

        <p>
            Telah mendapatkan persetujuan pengurus pusat untuk kembali ke rumah/boyong.
            Berdasarkan pemeriksaan sistem, santri tersebut tidak memiliki tagihan aktif pada saat surat ini diterbitkan.
        </p>
        <p>Demikian surat keterangan ini dibuat agar dapat digunakan sebagaimana mestinya.</p>
    </div>

    @php
        $verificationUrl = route('boyong.verifikasi', $boyong->public_token);
        $qrUrl =
            'https://api.qrserver.com/v1/create-qr-code/?size=180x180&margin=10&data=' . urlencode($verificationUrl);
    @endphp

    <div class="verification">
        <p>Pasuruan,
            {{ optional($boyong->tgl_disetujui)->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}</p>
        <img src="{{ $qrUrl }}" alt="QR verifikasi surat">
        <div class="name">{{ $boyong->validator->name ?? 'Pengurus Pusat' }}</div>
        <div class="hint">
            Scan QR untuk verifikasi data surat boyong tanpa login.<br>
            {{ $verificationUrl }}
        </div>
    </div>
</body>

</html>
