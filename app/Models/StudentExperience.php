<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentExperience extends Model
{
    use HasFactory;

    protected $table = 'student_experiences';

    protected $fillable = [
        'student_id', 
        'tipe',             // Pekerjaan / Organisasi
        'nama_instansi',
        'jenis_usaha',      // Mapping: Bidang (Organisasi)
        'alamat_instansi',  // Mapping: Tempat (Organisasi)
        'posisi',           // Mapping: Jabatan (Organisasi)
        'tanggal_mulai', 
        'tanggal_selesai',
        'gaji_awal',        // Nullable (Only Pekerjaan)
        'gaji_akhir',       // Nullable (Only Pekerjaan)
        'alasan_berhenti'   // Nullable (Only Pekerjaan)
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}