<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        for ($i = 0; $i < 50; $i++) {
            $nama = $faker->name;
            $email = $faker->unique()->safeEmail;
            
            // Buat Akun User
            $user = User::create([
                'name' => $nama,
                'email' => $email,
                'password' => Hash::make('12345678'),
                'role' => 'siswa',
            ]);

            // Buat Data Siswa
            Student::create([
                'user_id' => $user->id,
                'program_pelatihan_id' => $faker->numberBetween(1, 3), // Asumsi ID program 1-3
                'NIK' => $faker->unique()->numerify('32#############'),
                'nama' => $nama,
                'email' => $email,
                'telepon' => $faker->phoneNumber,
                'alamat' => $faker->address,
                'kota' => $faker->city,
                'provinsi' => $faker->state,
                'kode_pos' => $faker->postcode,
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->date('Y-m-d', '2005-01-01'),
                'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                'agama' => 'Islam',
                'golongan_darah' => $faker->randomElement(['A', 'B', 'AB', 'O']),
                'nama_ayah' => $faker->name('male'),
                'nama_ibu' => $faker->name('female'),
                'pekerjaan_ayah' => $faker->jobTitle,
                'no_hp_ortu' => $faker->phoneNumber,
                'sekolah_asal' => 'SMK ' . $faker->city,
                'tahun_lulus' => $faker->year,
                'status' => $faker->randomElement(['Mendaftar', 'Wawancara', 'Pelatihan', 'Magang']),
            ]);
        }
    }
}