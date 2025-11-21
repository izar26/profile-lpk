<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ADMIN (Akun Anda)
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@lpk.com',
            'password' => Hash::make('password'), // Password login
            'role' => 'admin',
        ]);

        // Pegawai dan Siswa akan dibuat otomatis di seeder masing-masing
        // agar relasinya rapi.
    }
}