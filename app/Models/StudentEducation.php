<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentEducation extends Model
{
    use HasFactory;

    protected $table = 'student_educations';

    protected $fillable = [
        'student_id', 
        'kategori',      // Formal / Non-Formal
        'tingkat',       // SD/SMP/Kursus
        'nama_institusi',
        'lokasi', 
        'jurusan',
        'tahun_masuk',   // Mapping: Tahun Dari / Durasi Waktu
        'tahun_lulus',   // Mapping: Tahun Sampai
        'nilai_rata_rata'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}