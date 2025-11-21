<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Pakai data Indonesia
        $jabatan = ['Instruktur Bahasa', 'Instruktur Fisik', 'Staff Admin', 'Marketing', 'Kepala LPK'];

        for ($i = 0; $i < 10; $i++) {
            $nama = $faker->name;
            $email = $faker->unique()->safeEmail;
            
            // Buat Akun User
            $user = User::create([
                'name' => $nama,
                'email' => $email,
                'password' => Hash::make('12345678'),
                'role' => 'pegawai',
            ]);

            // Buat Data Pegawai
            Employee::create([
                'user_id' => $user->id,
                'nip' => $faker->unique()->numerify('PEG-####'),
                'nama' => $nama,
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->date('Y-m-d', '2000-01-01'),
                'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                'agama' => 'Islam',
                'pendidikan_terakhir' => $faker->randomElement(['S1', 'D3', 'SMA']),
                'jabatan' => $faker->randomElement($jabatan),
                'status_kepegawaian' => $faker->randomElement(['Tetap', 'Kontrak']),
                'email' => $email,
                'telepon' => $faker->phoneNumber,
                'alamat' => $faker->address,
                'kota' => $faker->city,
                'provinsi' => $faker->state,
                'kode_pos' => $faker->postcode,
                'linkedin' => 'https://linkedin.com',
            ]);
        }
    }
}