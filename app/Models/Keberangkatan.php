<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keberangkatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'tujuan',
        'tanggal_berangkat',
        'jumlah_peserta',
        'deskripsi',
        'foto',
    ];

    protected $casts = [
        'tanggal_berangkat' => 'date',
    ];
}