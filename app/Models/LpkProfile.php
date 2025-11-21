<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpkProfile extends Model
{
    use HasFactory;

    // Izinkan semua kolom ini diisi
    protected $fillable = [
        'nama_lpk',
        'logo',
        'deskripsi_singkat',
        'tagline', // <-- BARU
        'alamat',
        'google_map_embed', // <-- BARU
        'email_lpk',
        'website_url', // <-- BARU
        'telepon_lpk',
        'nomor_wa', // <-- BARU
        'visi',
        'misi',
        'facebook_url', // <-- BARU
        'instagram_url', // <-- BARU
        'tiktok_url', // <-- BARU
        'youtube_url', // <-- BARU
    ];
}