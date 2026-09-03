<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jadikan v_asrama & v_siswa mengikuti periode aktif secara dinamis dari
     * daruttaqwa_referensi.tbl_periode (aktif = 1), menggantikan hardcode '20252026'.
     */
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW v_asrama AS
            SELECT
                k.idkelas,
                k.idtingkat AS asrama,
                k.idrombel AS kamar,
                k.keterangan
            FROM daruttaqwa_sisda.tbl_kelas k
            JOIN daruttaqwa_referensi.tbl_departemen s ON k.idunit = s.idunit
            JOIN daruttaqwa_referensi.tbl_periode p ON p.idperiode = k.idperiode AND p.aktif = 1
            WHERE s.idunit = '07'
        ");

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

    public function down(): void
    {
        // Kembalikan ke definisi hardcode periode 20252026 (perilaku lama).
        DB::statement("
            CREATE OR REPLACE VIEW v_asrama AS
            SELECT
                k.idkelas,
                k.idtingkat AS asrama,
                k.idrombel AS kamar,
                k.keterangan
            FROM daruttaqwa_sisda.tbl_kelas k
            JOIN daruttaqwa_referensi.tbl_departemen s ON k.idunit = s.idunit
            WHERE s.idunit = '07'
              AND k.idperiode = '20252026'
        ");

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
