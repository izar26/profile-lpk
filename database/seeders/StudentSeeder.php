<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentEducation;
use App\Models\StudentExperience;
use App\Models\StudentFamily;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        for ($i = 0; $i < 50; $i++) {
            $gender = $faker->randomElement(['Laki-laki', 'Perempuan']);
            $nama = $faker->name($gender == 'Laki-laki' ? 'male' : 'female');
            $email = $faker->unique()->safeEmail;
            
            // 1. Buat Akun User
            $user = User::create([
                'name' => $nama,
                'email' => $email,
                'password' => Hash::make('12345678'),
                'role' => 'siswa',
            ]);

            // 2. Buat Data Siswa (Sesuaikan dengan field Migrasi Terbaru)
            $student = Student::create([
                'user_id' => $user->id,
                'program_pelatihan_id' => $faker->numberBetween(1, 3), 
                'nama_lengkap' => $nama, // [UPDATED]
                'email' => $email,
                
                // Data Fisik
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->date('Y-m-d', '-18 years'),
                'jenis_kelamin' => $gender,
                'tinggi_badan' => $faker->numberBetween(150, 180),
                'berat_badan' => $faker->numberBetween(45, 80),
                'golongan_darah' => $faker->randomElement(['A', 'B', 'AB', 'O']),
                
                // Alamat
                'alamat_ktp' => $faker->address,
                'kota_ktp' => $faker->city,
                'provinsi_ktp' => $faker->state,
                'alamat_domisili' => $faker->address,
                
                // Identitas & Kontak
                'nomor_ktp' => $faker->unique()->numerify('32##############'),
                'no_hp_peserta' => $faker->phoneNumber,
                'no_hp_ortu' => $faker->phoneNumber,
                'status_pernikahan' => $faker->randomElement(['Belum Menikah', 'Menikah', 'Janda/Duda']),
                'agama' => 'Islam',
                
                // Status & System
                'status' => $faker->randomElement(['Mendaftar', 'Menunggu Verifikasi', 'Wawancara', 'Pelatihan', 'Magang']),
                'admin_note' => $faker->optional(0.3)->sentence, 
                'verified_at' => $faker->optional(0.7)->dateTimeThisYear(),
                'pernah_bekerja' => $faker->boolean(40), // 40% chance true
            ]);

            // 3. Buat Data Pendidikan
            $tingkatPendidikan = ['SD', 'SMP', 'SMA/SMK'];
            foreach ($tingkatPendidikan as $index => $tingkat) {
                StudentEducation::create([
                    'student_id' => $student->id,
                    'kategori' => 'Formal',
                    'tingkat' => $tingkat,
                    'nama_institusi' => $tingkat . ' ' . $faker->city,
                    'lokasi' => $faker->city,
                    'jurusan' => $tingkat == 'SMA/SMK' ? $faker->randomElement(['IPA', 'IPS', 'TKJ', 'RPL', 'Otomotif']) : '-',
                    'tahun_masuk' => (string) (2005 + ($index * 3)),
                    'tahun_lulus' => (string) (2008 + ($index * 3)),
                    'nilai_rata_rata' => (string) $faker->randomFloat(2, 75, 95),
                ]);
            }

            // 4. Buat Data Pengalaman (Jika pernah bekerja)
            if ($student->pernah_bekerja) { 
                StudentExperience::create([
                    'student_id' => $student->id,
                    'tipe' => 'Pekerjaan',
                    'nama_instansi' => $faker->company,
                    'jenis_usaha' => $faker->companySuffix,
                    'alamat_instansi' => $faker->address,
                    'posisi' => $faker->jobTitle,
                    'tanggal_mulai' => $faker->date('Y-m-d', '-2 years'),
                    'tanggal_selesai' => $faker->date('Y-m-d', 'now'),
                    'gaji_awal' => 'Rp ' . number_format($faker->numberBetween(1500000, 2500000), 0, ',', '.'),
                    'gaji_akhir' => 'Rp ' . number_format($faker->numberBetween(3000000, 4500000), 0, ',', '.'),
                    'alasan_berhenti' => 'Ingin meningkatkan skill bahasa & bekerja di Jepang',
                ]);
            }

            // 5. Buat Data Keluarga (Ayah)
            $dobAyah = $faker->date('Y-m-d', '1970-01-01');
            StudentFamily::create([
                'student_id' => $student->id,
                'hubungan' => 'Ayah',
                'jenis_kelamin' => 'L',
                'nama' => $faker->name('male'),
                'tanggal_lahir' => $dobAyah,
                'usia' => Carbon::parse($dobAyah)->age, // Hitung usia otomatis
                'pendidikan' => $faker->randomElement(['SMA', 'S1']),
                'pekerjaan' => $faker->jobTitle,
                'penghasilan' => 'Rp ' . number_format($faker->numberBetween(2000000, 8000000), 0, ',', '.'),
            ]);

            // 5. Buat Data Keluarga (Ibu)
            $dobIbu = $faker->date('Y-m-d', '1975-01-01');
            StudentFamily::create([
                'student_id' => $student->id,
                'hubungan' => 'Ibu',
                'jenis_kelamin' => 'P',
                'nama' => $faker->name('female'),
                'tanggal_lahir' => $dobIbu,
                'usia' => Carbon::parse($dobIbu)->age,
                'pendidikan' => $faker->randomElement(['SMP', 'SMA']),
                'pekerjaan' => 'Ibu Rumah Tangga',
                'penghasilan' => 'Tidak berpenghasilan',
            ]);
        }
    }
}