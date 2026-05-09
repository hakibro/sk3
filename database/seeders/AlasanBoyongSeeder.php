<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlasanBoyongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // database/seeders/AlasanBoyongSeeder.php
    public function run(): void
    {
        $alasans = [
            ['nama_alasan' => 'Lulus / Tamat Belajar'],
            ['nama_alasan' => 'Pindah Sekolah (Mutasi)'],
            ['nama_alasan' => 'Permintaan Sendiri (Berhenti)'],
            ['nama_alasan' => 'Dikeluarkan (Pelanggaran Berat)'],
            ['nama_alasan' => 'Sakit / Kesehatan'],
        ];

        foreach ($alasans as $alasan) {
            \App\Models\AlasanBoyong::create($alasan);
        }
    }
}
