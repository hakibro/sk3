<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    public function run(): void
    {
        AppSetting::setValue('kop_nama_pesantren', 'PONDOK PESANTREN NGALAH');
        AppSetting::setValue('kop_nspp', '510035140166');
        AppSetting::setValue('kop_alamat', 'Jl. Pesantren Ngalah No. 16 Sengonagung Purwosari Pasuruan 67162');
    }
}
