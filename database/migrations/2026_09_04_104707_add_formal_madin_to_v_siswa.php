<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambahkan kolom info formal (lembaga + kelas) dan madin (lembaga + kelas)
     * ke view v_siswa, diambil dari kelas santri (tbl_kelas) pada periode aktif:
     * - formal  : idunit selain '01' dan '07'
     * - madin   : idunit = '01'
     */
    public function up(): void
    {
        DB::statement('
            CREATE OR REPLACE VIEW v_siswa AS
            SELECT
                p.idperson,
                p.nama,
                k.idkelas,
                k.asrama,
                k.kamar,
                k.keterangan,
                (
                    SELECT df.title
                    FROM daruttaqwa_sisda.tbl_siswa sf
                    JOIN daruttaqwa_sisda.tbl_kelas kf ON kf.idkelas = sf.idkelas
                    JOIN daruttaqwa_referensi.tbl_departemen df ON df.idunit = kf.idunit
                    JOIN daruttaqwa_referensi.tbl_periode pf ON pf.idperiode = kf.idperiode AND pf.aktif = 1
                    WHERE sf.idperson = p.idperson
                      AND sf.status = 1
                      AND kf.idunit NOT IN (\'01\', \'07\')
                    LIMIT 1
                ) AS formal,
                (
                    SELECT kf.keterangan
                    FROM daruttaqwa_sisda.tbl_siswa sf
                    JOIN daruttaqwa_sisda.tbl_kelas kf ON kf.idkelas = sf.idkelas
                    JOIN daruttaqwa_referensi.tbl_periode pf ON pf.idperiode = kf.idperiode AND pf.aktif = 1
                    WHERE sf.idperson = p.idperson
                      AND sf.status = 1
                      AND kf.idunit NOT IN (\'01\', \'07\')
                    LIMIT 1
                ) AS kelas_formal,
                (
                    SELECT dm.title
                    FROM daruttaqwa_sisda.tbl_siswa sm
                    JOIN daruttaqwa_sisda.tbl_kelas km ON km.idkelas = sm.idkelas
                    JOIN daruttaqwa_referensi.tbl_departemen dm ON dm.idunit = km.idunit
                    JOIN daruttaqwa_referensi.tbl_periode pm ON pm.idperiode = km.idperiode AND pm.aktif = 1
                    WHERE sm.idperson = p.idperson
                      AND sm.status = 1
                      AND km.idunit = \'01\'
                    LIMIT 1
                ) AS madin,
                (
                    SELECT km.keterangan
                    FROM daruttaqwa_sisda.tbl_siswa sm
                    JOIN daruttaqwa_sisda.tbl_kelas km ON km.idkelas = sm.idkelas
                    JOIN daruttaqwa_referensi.tbl_periode pm ON pm.idperiode = km.idperiode AND pm.aktif = 1
                    WHERE sm.idperson = p.idperson
                      AND sm.status = 1
                      AND km.idunit = \'01\'
                    LIMIT 1
                ) AS kelas_madin
            FROM daruttaqwa_person.tbl_person p
            JOIN daruttaqwa_sisda.tbl_siswa s ON p.idperson = s.idperson
            JOIN v_asrama k ON k.idkelas = s.idkelas
            WHERE s.status = 1
        ');
    }

    public function down(): void
    {
        // Kembalikan definisi v_siswa sebelum ada kolom formal/madin.
        DB::statement('
            CREATE OR REPLACE VIEW v_siswa AS
            SELECT
                p.idperson,
                p.nama,
                k.idkelas,
                k.asrama,
                k.kamar,
                k.keterangan
            FROM daruttaqwa_person.tbl_person p
            JOIN daruttaqwa_sisda.tbl_siswa s ON p.idperson = s.idperson
            JOIN v_asrama k ON k.idkelas = s.idkelas
            WHERE s.status = 1
        ');
    }
};
