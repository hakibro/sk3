<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Super Admin
        User::updateOrCreate(['email' => 'admin@test.com'], [
            'name' => 'Administrator Sistem',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'lembaga' => null,
        ]);

        // 2. Akun Pengurus Pusat
        User::updateOrCreate(['email' => 'pusat@test.com'], [
            'name' => 'H. Ahmad Pusat',
            'password' => Hash::make('password'),
            'role' => 'pengurus_pusat',
            'lembaga' => null,
        ]);

        // 3. Akun Pengurus Asrama (Contoh: Asrama Sunan Ampel)
        User::updateOrCreate(['email' => 'asrama@test.com'], [
            'name' => 'Ustadz Asrama',
            'password' => Hash::make('password'),
            'role' => 'pengurus_asrama',
            'lembaga' => 'Sunan Ampel', // Pastikan ini sesuai dengan data di v_siswa
        ]);
    }
}
