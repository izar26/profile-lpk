<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alumni;
use App\Models\Student;
use Faker\Factory as Faker;

class AlumniSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Ambil Student yang ada, atau buat jika kosong
        $students = Student::limit(10)->get();

        // Jika database student kosong, kita buat data student dummy on-the-fly
        // (Ini hanya untuk development agar seeder tidak error)
        if ($students->count() < 5) {
            $this->command->info('Data Student sedikit/kosong. Membuat 5 dummy student status Alumni...');
            
            for ($i = 0; $i < 5; $i++) {
                $student = Student::create([
                    'user_id' => null, // Atau buat user jika perlu
                    'program_pelatihan_id' => 1, // Pastikan ada ID 1 di tabel program
                    'nama_lengkap' => $faker->name,
                    'tempat_lahir' => $faker->city,
                    'tanggal_lahir' => $faker->date('Y-m-d', '2005-01-01'),
                    'jenis_kelamin' => $faker->randomElement(['Laki-laki', 'Perempuan']),
                    'alamat_ktp' => $faker->address,
                    'status' => 'Alumni', // Penting: Status Alumni
                    'nomor_ktp' => $faker->unique()->nik,
                    'email' => $faker->unique()->safeEmail,
                ]);
                $students->push($student);
            }
        }

        // 2. Loop student yang ada untuk dibuatkan testimoni
        foreach ($students as $student) {
            // Pastikan student ini belum punya testimoni (supaya tidak error unique constraint)
            if (!$student->alumni) {
                
                // Update status student jadi Alumni biar logis
                $student->update(['status' => 'Alumni']);

                Alumni::create([
                    'student_id'   => $student->id, // Relasi ke ID student
                    'kerja_dimana' => $faker->randomElement(['Toyota, Aichi', 'Honda, Saitama', 'Nissan, Yokohama', 'Perkebunan Ibaraki', 'Konstruksi Osaka']),
                    'angkatan'     => 'Angkatan ' . $faker->numberBetween(2019, 2024),
                    
                    // Random kata-kata testimoni
                    'testimoni'    => $faker->randomElement([
                        "Alhamdulillah berkat LPK ini saya bisa mewujudkan mimpi bekerja di Jepang. Prosesnya transparan dan pelatihannya sangat disiplin.",
                        "Sensei-nya sangat ramah tapi tegas dalam mengajar. Fasilitas asrama juga sangat nyaman.",
                        "Gaji di Jepang sangat memuaskan, bisa bantu renovasi rumah orang tua di kampung. Terima kasih LPK!",
                        "Awalnya ragu karena bahasa Jepang susah, tapi berkat metode pengajaran di sini saya jadi cepat paham.",
                        "Sangat merekomendasikan LPK ini bagi yang ingin mengubah nasib. Kuncinya sabar dan ikuti arahan Sensei."
                    ]),
                    
                    'foto'         => null, // Null agar menggunakan foto dari profil student (jika ada)
                    
                    // 80% kemungkinan data dipublish, 20% draft
                    'is_published' => $faker->boolean(80), 
                ]);
            }
        }

        $this->command->info('Alumni Seeder berhasil dijalankan.');
    }
}