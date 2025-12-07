<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpkProfile extends Model
{
    use HasFactory;

    // Izinkan semua kolom ini diisi
    protected $fillable = [
    'nama_lpk', 'nama_pimpinan', 'nomor_sk', // [BARU]
    'logo', 'gambar_hero', 'gambar_tentang',
    'gambar_auth', 'background_kartu',
    'deskripsi_singkat', 'tagline',
    'alamat', 'google_map_embed',
    'email_lpk', 'website_url',
    'telepon_lpk', 'nomor_wa',
    'visi', 'misi',
    'facebook_url', 'instagram_url', 'tiktok_url', 'youtube_url',
];
}