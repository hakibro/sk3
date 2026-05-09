<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create v_asrama (Periode 20252026)
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

        // 2. Create v_siswa (Simplified)
        DB::statement("
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
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_siswa");
        DB::statement("DROP VIEW IF EXISTS v_asrama");
    }
};