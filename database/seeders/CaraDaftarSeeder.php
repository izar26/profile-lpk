<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CaraDaftar;

class CaraDaftarSeeder extends Seeder
{
    public function run(): void
    {
        $steps = [
            ['judul' => 'Isi Formulir', 'deskripsi' => 'Lengkapi data diri Anda melalui formulir pendaftaran online atau datang langsung ke kantor kami.', 'urutan' => 1],
            ['judul' => 'Seleksi Berkas', 'deskripsi' => 'Tim kami akan memverifikasi kelengkapan dokumen administrasi Anda.', 'urutan' => 2],
            ['judul' => 'Wawancara & Tes', 'deskripsi' => 'Ikuti tes fisik, matematika dasar, dan wawancara dengan instruktur.', 'urutan' => 3],
            ['judul' => 'Mulai Pelatihan', 'deskripsi' => 'Setelah lulus seleksi, Anda resmi menjadi siswa dan siap mengikuti pelatihan intensif.', 'urutan' => 4],
        ];

        foreach ($steps as $step) {
            CaraDaftar::create($step);
        }
    }
}