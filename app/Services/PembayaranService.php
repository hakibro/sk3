<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PembayaranService
{
    /** Periode awal (floor) untuk penghitungan tunggakan historis. */
    const PERIODE_MIN = '20212022';

    /** Mapping kategori boyong ke idunit di daruttaqwa_referensi.tbl_departemen. */
    const UNIT_ASRAMA = ['07'];                       // PPN

    const UNIT_MADIN = ['01'];                        // MADIN

    const UNIT_FORMAL = ['02', '03', '04', '05', '06', '08']; // SMK/SMA/SMP/MTS/MI/MA

    const UNIT_KANTIN = ['KANTIN'];                   // Kantin (selalu di-cut)

    /**
     * Resolve periode aktif dari sumber kebenaran daruttaqwa_referensi.tbl_periode (aktif = 1).
     * Fallback ke periode dengan idperiode tertinggi bila tidak ada yang bertanda aktif.
     */
    public function periodeAktif(): array
    {
        $periode = DB::selectOne(
            'SELECT idperiode, tglmulai, tglakhir, keterangan
             FROM daruttaqwa_referensi.tbl_periode
             WHERE aktif = 1
             ORDER BY idperiode DESC
             LIMIT 1'
        );

        if (! $periode) {
            $periode = DB::selectOne(
                'SELECT idperiode, tglmulai, tglakhir, keterangan
                 FROM daruttaqwa_referensi.tbl_periode
                 ORDER BY idperiode DESC
                 LIMIT 1'
            );
        }

        if (! $periode) {
            return [
                'idperiode' => self::PERIODE_MIN,
                'tglmulai' => null,
                'tglakhir' => null,
                'keterangan' => null,
            ];
        }

        return [
            'idperiode' => $periode->idperiode,
            'tglmulai' => $periode->tglmulai,
            'tglakhir' => $periode->tglakhir,
            'keterangan' => $periode->keterangan,
        ];
    }

    /**
     * Daftar periode untuk penghitungan tunggakan: semua periode dari floor
     * (20212022) sampai & termasuk periode aktif dari tbl_periode.
     */
    public function periodesAktif(): array
    {
        $aktif = $this->periodeAktif()['idperiode'];

        return array_map(
            fn ($row) => $row->idperiode,
            DB::select(
                'SELECT idperiode
                 FROM daruttaqwa_referensi.tbl_periode
                 WHERE idperiode >= ? AND idperiode <= ?
                 ORDER BY idperiode ASC',
                [self::PERIODE_MIN, $aktif]
            )
        );
    }

    public function refreshStatusLunasSiswa(string $idperson): void
    {
        $periodes = $this->periodesAktif();
        $placeholders = implode(',', array_fill(0, count($periodes), '?'));

        $summary = DB::selectOne("
            SELECT
                ? AS idperson,
                COALESCE(SUM(
                    CASE
                        WHEN (iis.jml_kredit - iis.jml_debet) > 0
                            THEN iis.jml_kredit - iis.jml_debet
                        ELSE 0
                    END
                ), 0) AS total_tunggakan
            FROM daruttaqwa_trans.ips_siswa iis
            WHERE iis.idperson = ?
              AND iis.idperiode IN ({$placeholders})
              AND iis.status = '1'
              AND iis.tgl_jurnal < NOW()
        ", array_merge([$idperson, $idperson], $periodes));

        $totalTunggakan = (float) ($summary->total_tunggakan ?? 0);

        DB::table('siswa_status_pembayaran')->updateOrInsert(
            ['idperson' => $idperson],
            [
                'total_tunggakan' => $totalTunggakan,
                'is_lunas' => $totalTunggakan > 0 ? 0 : 1,
                'refreshed_at' => now(),
            ]
        );
    }

    public function refreshStatusLunasSemuaSiswa(): int
    {
        $periodes = $this->periodesAktif();
        // Nilai periode berasal dari DB (aman untuk interpolasi dalam SQL literal).
        $quoted = collect($periodes)->map(fn ($p) => DB::getPdo()->quote($p))->implode(', ');

        DB::statement('DROP TEMPORARY TABLE IF EXISTS tmp_siswa_status_pembayaran');

        DB::statement("
            CREATE TEMPORARY TABLE tmp_siswa_status_pembayaran AS
            SELECT
                summary.idperson,
                summary.total_tunggakan,
                CASE WHEN summary.total_tunggakan > 0 THEN 0 ELSE 1 END AS is_lunas,
                NOW() AS refreshed_at
            FROM (
                SELECT
                    iis.idperson,
                    COALESCE(SUM(
                        CASE
                            WHEN (iis.jml_kredit - iis.jml_debet) > 0
                                THEN iis.jml_kredit - iis.jml_debet
                            ELSE 0
                        END
                    ), 0) AS total_tunggakan
                FROM daruttaqwa_trans.ips_siswa iis
                WHERE iis.idperiode IN ({$quoted})
                  AND iis.status = '1'
                  AND iis.tgl_jurnal < NOW()
                GROUP BY iis.idperson
            ) summary
        ");

        DB::transaction(function () {
            DB::table('siswa_status_pembayaran')->delete();
            DB::statement('
                INSERT INTO siswa_status_pembayaran (idperson, total_tunggakan, is_lunas, refreshed_at)
                SELECT idperson, total_tunggakan, is_lunas, refreshed_at
                FROM tmp_siswa_status_pembayaran
            ');
        });

        return DB::table('siswa_status_pembayaran')->count();
    }

    /**
     * Seluruh tagihan siswa (lunas maupun belum) lintas periode.
     */
    public function getDetailPembayaran(string $idperson, ?array $periodes = null): array
    {
        $periodes = $periodes ?? $this->periodesAktif();
        $placeholders = implode(',', array_fill(0, count($periodes), '?'));

        return DB::select("
            SELECT
                iis.idperson, iis.idperiode,
                tim.judul,
                td.title AS nama_unit,
                iis.jml_kredit, iis.jml_debet,
                (iis.jml_kredit - iis.jml_debet) AS selisih,
                CASE
                    WHEN (iis.jml_kredit - iis.jml_debet) > 0 THEN 0
                    ELSE 1
                END AS lunas,
                iis.tgl_jurnal, iis.tgl_update
            FROM daruttaqwa_trans.ips_siswa iis
            JOIN daruttaqwa_trans.tbl_ips_unit tiu ON tiu.ipsunit = iis.ipsunit
            JOIN daruttaqwa_trans.tbl_ips_main tim ON tim.ipsmain = tiu.ipsmain
            JOIN daruttaqwa_referensi.tbl_departemen td ON td.idunit = iis.idunit
            WHERE iis.idperson = ?
              AND iis.idperiode IN ({$placeholders})
              AND iis.status = '1'
              AND iis.tgl_jurnal < NOW()
            ORDER BY lunas DESC, idperiode DESC, tgl_jurnal, judul ASC
        ", array_merge([$idperson], $periodes));
    }

    /**
     * Hanya item yang masih punya sisa tagihan.
     */
    public function getDetailBelumLunas(string $idperson, ?array $periodes = null): array
    {
        $periodes = $periodes ?? $this->periodesAktif();
        $placeholders = implode(',', array_fill(0, count($periodes), '?'));

        return DB::select("
            SELECT
                iis.idperson, iis.idperiode,
                tim.judul,
                td.title AS nama_unit,
                iis.jml_kredit, iis.jml_debet,
                (iis.jml_kredit - iis.jml_debet) AS selisih,
                CASE
                    WHEN (iis.jml_kredit - iis.jml_debet) > 0 THEN 0
                    ELSE 1
                END AS lunas,
                iis.tgl_jurnal, iis.tgl_update
            FROM daruttaqwa_trans.ips_siswa iis
            JOIN daruttaqwa_trans.tbl_ips_unit tiu ON tiu.ipsunit = iis.ipsunit
            JOIN daruttaqwa_trans.tbl_ips_main tim ON tim.ipsmain = tiu.ipsmain
            JOIN daruttaqwa_referensi.tbl_departemen td ON td.idunit = iis.idunit
            WHERE iis.idperson = ?
              AND iis.idperiode IN ({$placeholders})
              AND iis.status = '1'
              AND iis.tgl_jurnal < NOW()
              AND (iis.jml_kredit - iis.jml_debet) > 0
            ORDER BY lunas DESC, idperiode DESC, tgl_jurnal, judul ASC
        ", array_merge([$idperson], $periodes));
    }

    /**
     * Ringkasan total kredit/debet per periode.
     */
    public function getSummaryPerPeriode(string $idperson, ?array $periodes = null): array
    {
        $periodes = $periodes ?? $this->periodesAktif();
        $placeholders = implode(',', array_fill(0, count($periodes), '?'));

        return DB::select("
            SELECT
                person.idperson,
                person.nama,
                (SELECT GROUP_CONCAT(l.title, ' - ', k.keterangan SEPARATOR ' | ')
                 FROM daruttaqwa_sisda.tbl_siswa s
                 JOIN daruttaqwa_sisda.tbl_kelas k ON s.idkelas = k.idkelas
                 JOIN daruttaqwa_referensi.tbl_departemen l ON k.idunit = l.idunit
                 WHERE s.idperson = person.idperson
                   AND k.idperiode = iis.idperiode
                   AND s.status = 1) AS kelas_history,
                iis.idperiode,
                SUM(iis.jml_kredit)                          AS total_kredit,
                SUM(iis.jml_debet)                           AS total_debet,
                (SUM(iis.jml_kredit) - SUM(iis.jml_debet))  AS sisa_tagihan,
                CASE
                    WHEN SUM(iis.jml_debet) >= SUM(iis.jml_kredit) THEN 1
                    ELSE 0
                END AS lunas
            FROM daruttaqwa_trans.ips_siswa iis
            JOIN daruttaqwa_person.tbl_person person ON person.idperson = iis.idperson
            WHERE iis.idperson = ?
              AND iis.idperiode IN ({$placeholders})
              AND iis.status = '1'
              AND iis.tgl_jurnal < NOW()
            GROUP BY person.idperson, person.nama, iis.idperiode
            ORDER BY iis.idperiode ASC
        ", array_merge([$idperson], $periodes));
    }

    /**
     * Total rupiah kurang bayar siswa saat ini.
     */
    public function getTotalBelumLunas(string $idperson, ?array $periodes = null): int
    {
        $periodes = $periodes ?? $this->periodesAktif();
        $placeholders = implode(',', array_fill(0, count($periodes), '?'));

        $summary = DB::selectOne("
            SELECT
                COALESCE(SUM(
                    CASE
                        WHEN (iis.jml_kredit - iis.jml_debet) > 0
                            THEN iis.jml_kredit - iis.jml_debet
                        ELSE 0
                    END
                ), 0) AS total_tunggakan
            FROM daruttaqwa_trans.ips_siswa iis
            WHERE iis.idperson = ?
              AND iis.idperiode IN ({$placeholders})
              AND iis.status = '1'
              AND iis.tgl_jurnal < NOW()
        ", array_merge([$idperson], $periodes));

        return (int) ($summary->total_tunggakan ?? 0);
    }

    /**
     * Jalankan cut-off pembayaran boyong: set status='0' pada baris tagihan
     * (ips_siswa) unit terpilih di periode aktif, dari tanggal boyong s.d. akhir periode.
     *
     * Aturan SPP bulan berjalan: untuk unit asrama, baris bulan berjalan TIDAK di-cut
     * bila tanggal boyong > batas tanggal (day > threshold) karena SPP tetap full.
     *
     * @param  array<string, bool>  $scope  e.g. ['asrama'=>true,'madin'=>false,'formal'=>false]
     * @return array list ipssiswa (PK) baris yang berhasil di-set status 0
     */
    public function cutOffPembayaran(
        string $idperson,
        array $scope,
        string $tanggalBoyong,
        int $batasTanggal,
        ?array $periodeAktif = null
    ): array {
        $periode = $periodeAktif ?? $this->periodeAktif();
        $idperiode = $periode['idperiode'];
        $tglAkhir = $periode['tglakhir'];

        if (! $tglAkhir) {
            return [];
        }

        $idunits = collect()
            ->when(! empty($scope['asrama']), fn ($c) => $c->concat(self::UNIT_ASRAMA))
            ->when(! empty($scope['madin']), fn ($c) => $c->concat(self::UNIT_MADIN))
            ->when(! empty($scope['formal']), fn ($c) => $c->concat(self::UNIT_FORMAL))
            ->concat(self::UNIT_KANTIN)
            ->unique()
            ->values();

        if ($idunits->isEmpty()) {
            return [];
        }

        $unitPlaceholders = implode(',', array_fill(0, $idunits->count(), '?'));

        // Set baris yang awalnya berstatus 1 dan akan di-cut.
        $cut = collect();

        DB::transaction(function () use ($idperson, $idperiode, $idunits, $unitPlaceholders, $tanggalBoyong, $tglAkhir, $batasTanggal, &$cut) {
            // 1. Kumpulkan & cut semua baris unit terpilih di periode aktif mulai tanggal boyong s.d. akhir periode.
            $targets = DB::select("
                SELECT ipssiswa
                FROM daruttaqwa_trans.ips_siswa
                WHERE idperson = ?
                  AND idperiode = ?
                  AND status = '1'
                  AND idunit IN ({$unitPlaceholders})
                  AND tgl_jurnal >= ?
                  AND tgl_jurnal <= ?
            ", array_merge([$idperson, $idperiode], $idunits->all(), [$tanggalBoyong, $tglAkhir]));

            DB::update("
                UPDATE daruttaqwa_trans.ips_siswa
                SET status = '0'
                WHERE idperson = ?
                  AND idperiode = ?
                  AND status = '1'
                  AND idunit IN ({$unitPlaceholders})
                  AND tgl_jurnal >= ?
                  AND tgl_jurnal <= ?
            ", array_merge([$idperson, $idperiode], $idunits->all(), [$tanggalBoyong, $tglAkhir]));

            $cut = collect($targets)->map(fn ($row) => $row->ipssiswa);

            // 2. Aturan SPP bulan berjalan (asrama): bila tanggal boyong > batas tanggal,
            //    baris SPP bulan berjalan dikembalikan agar tetap full (tidak ter-cut).
            $dayBoyong = (int) Carbon::parse($tanggalBoyong)->day;
            if ($dayBoyong > $batasTanggal && in_array('07', $idunits->all(), true)) {
                $bulanBerjalanAwal = Carbon::parse($tanggalBoyong)->startOfMonth()->toDateString();
                $restored = DB::select("
                    SELECT ipssiswa
                    FROM daruttaqwa_trans.ips_siswa
                    WHERE idperson = ?
                      AND idperiode = ?
                      AND idunit = '07'
                      AND tgl_jurnal >= ?
                      AND tgl_jurnal < ?
                ", [$idperson, $idperiode, $bulanBerjalanAwal, Carbon::parse($bulanBerjalanAwal)->addMonth()->toDateString()]);

                DB::update("
                    UPDATE daruttaqwa_trans.ips_siswa
                    SET status = '1'
                    WHERE idperson = ?
                      AND idperiode = ?
                      AND idunit = '07'
                      AND tgl_jurnal >= ?
                      AND tgl_jurnal < ?
                ", [$idperson, $idperiode, $bulanBerjalanAwal, Carbon::parse($bulanBerjalanAwal)->addMonth()->toDateString()]);

                // Baris yang dipulihkan tidak lagi dianggap ter-cut.
                $restoredIds = collect($restored)->map(fn ($row) => $row->ipssiswa);
                $cut = $cut->diff($restoredIds)->values();
            }
        });

        return $cut->all();
    }

    /**
     * Batalkan cut-off: kembalikan status='1' pada baris ips_siswa yang tercatat ter-cut.
     *
     * @param  array  $ipssiswaIds  daftar PK ipssiswa
     * @return int jumlah baris yang dipulihkan
     */
    public function batalkanCutOff(array $ipssiswaIds): int
    {
        $ids = array_values(array_filter($ipssiswaIds));
        if (empty($ids)) {
            return 0;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        return DB::update("
            UPDATE daruttaqwa_trans.ips_siswa
            SET status = '1'
            WHERE ipssiswa IN ({$placeholders})
              AND status = '0'
        ", $ids);
    }
}
