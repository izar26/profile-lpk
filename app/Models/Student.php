<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    // Tambahkan semua field baru ke $fillable
    protected $fillable = [
        'user_id', 'program_pelatihan_id', 'NIK', 'nama', 'email', 'telepon', 'alamat', 
        'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'status', 'foto',
        // Field Baru:
        'agama', 'golongan_darah', 'nama_ayah', 'nama_ibu', 'pekerjaan_ayah', 'pekerjaan_ibu',
        'no_hp_ortu', 'sekolah_asal', 'tahun_lulus', 'kota', 'provinsi', 'kode_pos'
    ];

    protected $casts = ['tanggal_lahir' => 'date'];

    public function program(): BelongsTo { return $this->belongsTo(ProgramPelatihan::class, 'program_pelatihan_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    // ============================================================
    // 🧠 LOGIKA HITUNG KELENGKAPAN DATA (ACCESSOR)
    // ============================================================
    public function getDataCompletionAttribute()
    {
        // Daftar field yang dianggap "Wajib Dilengkapi" agar dianggap 100%
        $fieldsToCheck = [
            'NIK', 'nama', 'email', 'telepon', 'alamat', 
            'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 
            'agama', 'nama_ibu', 'nama_ayah', 'sekolah_asal'
        ];

        $total = count($fieldsToCheck);
        $filled = 0;

        foreach ($fieldsToCheck as $field) {
            if (!empty($this->$field)) {
                $filled++;
            }
        }

        // Hitung Persentase
        $percentage = ($filled / $total) * 100;

        return [
            'filled' => $filled,
            'total' => $total,
            'percentage' => round($percentage),
            'is_complete' => $filled === $total,
            'text' => "$filled / $total"
        ];
    }
}