<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW v_siswa AS 
            SELECT 
                p.idperson, 
                p.nama, 
                s.idkelas, 
                k.idtingkat, 
                k.keterangan as kelas,
                d.title as unit_formal -- Tambahan agar query Controller Anda jalan
            FROM daruttaqwa_person.tbl_person p 
            JOIN daruttaqwa_sisda.tbl_siswa s ON p.idperson = s.idperson 
            JOIN daruttaqwa_sisda.tbl_kelas k ON k.idkelas = s.idkelas 
            JOIN daruttaqwa_referensi.tbl_departemen d ON d.idunit = k.idunit 
            WHERE s.status = 1 
              AND d.idunit = '07' 
              AND k.idperiode = '20252026'
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_siswa");
    }
};