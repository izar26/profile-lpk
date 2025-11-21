<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Nama class diubah
class ProgramPelatihan extends Model
{
    use HasFactory;

    // Nama tabel 'program_pelatihans' (plural) 
    // akan otomatis terdeteksi oleh Laravel, jadi kita tidak perlu ubah apa-apa.

    protected $fillable = [
        'judul',
        'deskripsi_singkat',
        'deskripsi_lengkap',
        'gambar_fitur',
        'status',
    ];
}