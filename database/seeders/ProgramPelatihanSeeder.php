<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProgramPelatihan;

class ProgramPelatihanSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            [
                'judul' => 'Pemagangan (Ginou Jisshusei)',
                'deskripsi_singkat' => 'Program magang teknis selama 3 tahun di berbagai industri manufaktur dan konstruksi.',
                'deskripsi_lengkap' => 'Program ini dirancang untuk lulusan SMA/SMK yang ingin meningkatkan keterampilan teknis sambil bekerja di Jepang. Fasilitas asrama, pelatihan bahasa intensif N4, dan penempatan kerja terjamin.',
                'status' => 'Buka Pendaftaran'
            ],
            [
                'judul' => 'Tokutei Ginou (Specified Skilled Worker)',
                'deskripsi_singkat' => 'Program pekerja berketerampilan khusus untuk Anda yang sudah memiliki pengalaman atau eks-magang.',
                'deskripsi_lengkap' => 'Visa kerja 5 tahun dengan gaji setara warga Jepang. Bidang pekerjaan meliputi Pengolahan Makanan, Pertanian, Konstruksi, dan Perhotelan.',
                'status' => 'Berjalan'
            ],
            [
                'judul' => 'Kaigo (Caregiver)',
                'deskripsi_singkat' => 'Pelatihan khusus perawat lansia dengan permintaan tinggi di Jepang.',
                'deskripsi_lengkap' => 'Mempersiapkan tenaga perawat profesional. Syarat utama adalah kesabaran dan kemampuan bahasa Jepang setara N4/N3. Gaji sangat kompetitif.',
                'status' => 'Akan Datang'
            ],
        ];

        foreach ($programs as $prog) {
            ProgramPelatihan::create($prog);
        }
    }
}