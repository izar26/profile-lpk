<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Edukasi;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class EdukasiSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        // Pastikan ada user admin ID 1
        $adminId = 1; 

        for ($i = 0; $i < 10; $i++) {
            $judul = $faker->sentence(6);
            Edukasi::create([
                'user_id' => $adminId,
                'judul' => $judul,
                'slug' => Str::slug($judul),
                'konten' => '<div>' . implode('</div><div>', $faker->paragraphs(3)) . '</div>', // Simulasi HTML Trix
                'status' => 'Published',
                'gambar_fitur' => null,
            ]);
        }
    }
}