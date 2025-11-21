<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LpkProfileSeeder::class,
            UserSeeder::class, // Admin dibuat di sini
            ProgramPelatihanSeeder::class,
            EmployeeSeeder::class, // Pegawai + User Pegawai
            StudentSeeder::class, // Siswa + User Siswa
            AlbumGaleriSeeder::class,
            EdukasiSeeder::class,
            CaraDaftarSeeder::class,
            KeberangkatanSeeder::class,
            AlumniSeeder::class,
        ]);
    }
}