<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Keberangkatan;
use Faker\Factory as Faker;

class KeberangkatanSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i = 0; $i < 5; $i++) {
            Keberangkatan::create([
                'judul' => 'Pelepasan Peserta Magang Batch ' . ($i + 20),
                'tujuan' => $faker->city . ', Jepang',
                'tanggal_berangkat' => $faker->dateTimeBetween('-1 year', '+2 months'),
                'jumlah_peserta' => rand(5, 20),
                'deskripsi' => $faker->sentence(10),
                'foto' => null,
            ]);
        }
    }
}