<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Album;
use App\Models\Galeri;
use Faker\Factory as Faker;

class AlbumGaleriSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        $albumNames = [
            'Kegiatan Belajar Bahasa', 
            'Latihan Fisik Pagi', 
            'Pelepasan Angkatan 10', 
            'Fasilitas Asrama', 
            'Kunjungan Industri'
        ];

        foreach ($albumNames as $name) {
            $album = Album::create([
                'nama_album' => $name,
                'deskripsi_album' => $faker->sentence(10),
            ]);

            // Buat 5-8 foto dummy per album
            for ($j = 0; $j < rand(5, 8); $j++) {
                Galeri::create([
                    'album_id' => $album->id,
                    'judul' => $faker->sentence(3),
                    'tipe' => 'foto',
                    'path_file' => null, // Null dulu karena kita tidak punya file fisik
                    'tanggal_kegiatan' => $faker->dateTimeThisYear(),
                ]);
            }
        }
    }
}