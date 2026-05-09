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
        User::create([
            'name' => 'Administrator Sistem',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'lembaga' => null,
        ]);

        // 2. Akun Pengurus Pusat
        User::create([
            'name' => 'H. Ahmad Pusat',
            'email' => 'pusat@test.com',
            'password' => Hash::make('password'),
            'role' => 'pengurus_pusat',
            'lembaga' => 'Kantor Pusat',
        ]);

        // 3. Akun Pengurus Asrama (Contoh: Asrama Sunan Ampel)
        User::create([
            'name' => 'Ustadz Asrama',
            'email' => 'asrama@test.com',
            'password' => Hash::make('password'),
            'role' => 'pengurus_asrama',
            'lembaga' => 'Sunan Ampel', // Pastikan ini sesuai dengan data di v_siswa
        ]);
    }
}