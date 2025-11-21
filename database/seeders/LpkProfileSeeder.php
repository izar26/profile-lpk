<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LpkProfile;

class LpkProfileSeeder extends Seeder
{
    public function run(): void
    {
        LpkProfile::create([
            'id' => 1,
            'nama_lpk' => 'LPK Global Sukses Mandiri',
            'tagline' => 'Jembatan Pasti Menuju Karir Internasional di Jepang',
            'deskripsi_singkat' => 'Lembaga Pelatihan Kerja terakreditasi yang fokus mencetak tenaga kerja profesional untuk ditempatkan di perusahaan-perusahaan ternama di Jepang.',
            'alamat' => 'Jl. Raya Pendidikan No. 123, Kota Bandung, Jawa Barat',
            'email_lpk' => 'info@lpkglobalsukses.com',
            'telepon_lpk' => '(022) 1234567',
            'nomor_wa' => '6281234567890',
            'website_url' => 'https://lpkglobalsukses.com',
            'visi' => 'Menjadi LPK terdepan dalam mencetak SDM unggul, berkarakter, dan berdaya saing global.',
            'misi' => "1. Menyelenggarakan pelatihan bahasa dan budaya Jepang yang intensif.\n2. Membangun karakter disiplin dan etos kerja tinggi.\n3. Menjalin kerjasama luas dengan perusahaan penerima di Jepang.",
            'facebook_url' => 'https://facebook.com',
            'instagram_url' => 'https://instagram.com',
            'youtube_url' => 'https://youtube.com',
            'google_map_embed' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126748.56347862248!2d107.57311705231137!3d-6.903444341687889!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e6398252477f%3A0x146a1f93d3e815b2!2sBandung%2C%20Bandung%20City%2C%20West%20Java!5e0!3m2!1sen!2sid!4v1710000000000!5m2!1sen!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
        ]);
    }
}