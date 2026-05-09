<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat 1 User Admin / Utama
        User::create([
            'name' => 'Admin Ganteng',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'), // Passwordnya: password123
        ]);

        // Membuat 10 user tambahan secara acak (menggunakan Factory bawaan Laravel)
        User::factory(10)->create();
    }
}