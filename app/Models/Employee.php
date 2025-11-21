<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nip', 'nama', 'tempat_lahir', 'tanggal_lahir', 
        'jenis_kelamin', 'agama', 'pendidikan_terakhir', 'jabatan', 
        'status_kepegawaian', 'email', 'telepon', 'alamat', 
        'kota', 'provinsi', 'kode_pos', 'foto', 'linkedin', 'instagram'
    ];

    protected $casts = ['tanggal_lahir' => 'date'];

    public function user() { return $this->belongsTo(User::class); }

    // Hitung Kelengkapan Data
    public function getDataCompletionAttribute()
    {
        $fields = ['nip', 'nama', 'email', 'telepon', 'alamat', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'jabatan', 'foto'];
        $total = count($fields);
        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) $filled++;
        }
        $percentage = ($filled / $total) * 100;

        return [
            'filled' => $filled, 'total' => $total, 
            'percentage' => round($percentage), 
            'is_complete' => $filled === $total, 
            'text' => "$filled / $total"
        ];
    }
}