<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alumni;
use Faker\Factory as Faker;

class AlumniSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i = 0; $i < 20; $i++) {
            Alumni::create([
                'nama' => $faker->name,
                'kerja_dimana' => 'Toyota, ' . $faker->city, // Contoh: Toyota, Osaka
                'angkatan' => 'Angkatan ' . rand(2018, 2023),
                'testimoni' => "Alhamdulillah berkat LPK ini saya bisa mewujudkan mimpi bekerja di Jepang. Prosesnya transparan dan pelatihannya sangat disiplin. Gaji di sini sangat memuaskan!",
                'foto' => null,
            ]);
        }
    }
}