<?php

namespace App\Services;

use App\Models\Boyong;
use App\Models\Siswa;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class BoyongService
{
    public function __construct(
        protected PembayaranService $pembayaranService
    ) {
    }

    public function cekKelayakanBoyong(string $idperson): array
    {
        $siswa = Siswa::findOrFail($idperson);
        $sisaTagihan = $this->pembayaranService->getTotalBelumLunas($idperson);
        $detailBelumLunas = $this->pembayaranService->getDetailBelumLunas($idperson);
        $pengajuanAktif = Boyong::where('idperson', $idperson)
            ->whereIn('status', ['pending', 'approved'])
            ->latest()
            ->first();

        return [
            'boleh_boyong' => ! $pengajuanAktif,
            'sisa_tagihan' => $sisaTagihan,
            'formatted_tagihan' => 'Rp ' . number_format($sisaTagihan, 0, ',', '.'),
            'detail_belum_lunas' => $detailBelumLunas,
            'pengajuan_aktif' => $pengajuanAktif,
            'siswa' => $siswa,
        ];
    }

    public function ajukanBoyong(string $idperson, array $data): Boyong
    {
        $user = Auth::user();
        $siswa = Siswa::findOrFail($idperson);

        if ($user->isAsrama() && $siswa->asrama !== $user->lembaga) {
            throw new RuntimeException('Anda hanya dapat mengajukan santri dari asrama Anda.');
        }

        $kelayakan = $this->cekKelayakanBoyong($idperson);

        if ($kelayakan['pengajuan_aktif']) {
            throw new RuntimeException('Santri ini sudah memiliki pengajuan boyong yang masih aktif.');
        }

        return DB::transaction(function () use ($user, $siswa, $idperson, $data, $kelayakan) {
            $status = $user->isPusat() ? 'approved' : 'pending';
            $tanggalBoyong = Carbon::parse($data['tanggal_boyong']);
            $sisaTagihan = (int) $kelayakan['sisa_tagihan'];
            $pembayaranBelumLunas = $sisaTagihan > 0;
            $alasanKategori = $data['alasan_kategori'] === 'Lainnya'
                ? trim($data['alasan_lainnya'] ?? 'Lainnya')
                : $data['alasan_kategori'];
            $alasanDetail = trim($data['alasan_detail']);

            $boyong = Boyong::create([
                'idperson' => $idperson,
                'user_id' => $user->id,
                'asrama_asal' => $siswa->asrama,
                'tanggal_boyong' => $tanggalBoyong,
                'alasan' => $alasanKategori . ' - ' . $alasanDetail,
                'alasan_kategori' => $alasanKategori,
                'alasan_detail' => $alasanDetail,
                'status' => $status,
                'catatan_pusat' => $user->isPusat() ? 'Dibuat langsung oleh pengurus pusat.' : null,
                'pembayaran_belum_lunas' => $pembayaranBelumLunas,
                'total_tagihan_saat_pengajuan' => $sisaTagihan,
                'kos_makan_bulan_berjalan' => (int) ($data['kos_makan_bulan_berjalan'] ?? 0),
                'spp_bulan_berjalan_full' => $tanggalBoyong->day > 6,
                'status_cut_pembayaran' => $pembayaranBelumLunas ? 'menunggu_cut' : 'tidak_perlu',
                'snapshot_tagihan' => $this->buatSnapshotTagihan($idperson, $kelayakan, $tanggalBoyong, (int) ($data['kos_makan_bulan_berjalan'] ?? 0)),
                'tgl_disetujui' => $user->isPusat() ? now() : null,
                'approved_by' => $user->isPusat() ? $user->id : null,
                'public_token' => $user->isPusat() ? $this->generatePublicToken() : null,
            ]);

            if ($user->isPusat()) {
                $boyong->update(['nomor_surat' => $this->generateNomorSurat($boyong)]);
            }

            return $boyong;
        });
    }

    public function setujui(Boyong $boyong, ?string $catatan = null): Boyong
    {
        $boyong->update([
            'status' => 'approved',
            'catatan_pusat' => $catatan,
            'tgl_disetujui' => now(),
            'approved_by' => Auth::id(),
            'nomor_surat' => $boyong->nomor_surat ?: $this->generateNomorSurat($boyong),
            'public_token' => $boyong->public_token ?: $this->generatePublicToken(),
        ]);

        return $boyong;
    }

    public function getTotalBelumLunasSaatIni(string $idperson): int
    {
        return $this->pembayaranService->getTotalBelumLunas($idperson);
    }

    public function buatPayloadTagihanTerakhir(Boyong $boyong): array
    {
        $snapshot = $boyong->snapshot_tagihan ?: [];
        $detailTagihan = collect($snapshot['detail_belum_lunas'] ?? []);

        return [
            'boyong_id' => $boyong->id,
            'idperson' => $boyong->idperson,
            'nama' => $boyong->siswa->nama ?? null,
            'asrama_asal' => $boyong->asrama_asal,
            'tanggal_boyong' => optional($boyong->tanggal_boyong)->toDateString(),
            'status_boyong' => $boyong->status,
            'status_cut_pembayaran' => $boyong->status_cut_pembayaran,
            'pembayaran_belum_lunas' => $boyong->pembayaran_belum_lunas,
            'total_tagihan_saat_pengajuan' => (int) $boyong->total_tagihan_saat_pengajuan,
            'kos_makan_bulan_berjalan' => (int) $boyong->kos_makan_bulan_berjalan,
            'spp_bulan_berjalan_full' => (bool) $boyong->spp_bulan_berjalan_full,
            'aturan_cut' => [
                'spp' => $boyong->spp_bulan_berjalan_full
                    ? 'Tagihan SPP bulan berjalan tetap full karena tanggal boyong lebih dari tanggal 6.'
                    : 'Tagihan SPP bulan berjalan dapat dicut karena tanggal boyong sampai tanggal 6.',
                'kos_makan' => 'Gunakan nominal kos makan bulan berjalan yang diinput pengurus.',
            ],
            'periode_terakhir' => $detailTagihan->max('idperiode'),
            'detail_belum_lunas' => $detailTagihan->values()->all(),
            'generated_at' => now()->toIso8601String(),
        ];
    }

    public function tolak(Boyong $boyong, ?string $catatan = null): Boyong
    {
        $boyong->update([
            'status' => 'rejected',
            'catatan_pusat' => $catatan,
            'tgl_disetujui' => null,
            'approved_by' => null,
            'nomor_surat' => null,
            'public_token' => null,
        ]);

        return $boyong;
    }

    public function generateNomorSurat(Boyong $boyong): string
    {
        return sprintf(
            '%03d/SK3/%s/%s',
            $boyong->id,
            Str::upper(Str::of($boyong->asrama_asal ?: 'PUSAT')->replace(' ', '-')->limit(12, '')),
            now()->format('Y')
        );
    }

    public function generatePublicToken(): string
    {
        do {
            $token = Str::random(48);
        } while (Boyong::where('public_token', $token)->exists());

        return $token;
    }

    protected function buatSnapshotTagihan(string $idperson, array $kelayakan, Carbon $tanggalBoyong, int $kosMakan): array
    {
        return [
            'idperson' => $idperson,
            'tanggal_pengajuan' => now()->toDateTimeString(),
            'tanggal_boyong' => $tanggalBoyong->toDateString(),
            'total_belum_lunas' => (int) $kelayakan['sisa_tagihan'],
            'kos_makan_bulan_berjalan' => $kosMakan,
            'spp_bulan_berjalan_full' => $tanggalBoyong->day > 6,
            'detail_belum_lunas' => collect($kelayakan['detail_belum_lunas'])
                ->map(fn ($item) => (array) $item)
                ->values()
                ->all(),
        ];
    }
}
