<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentFamily extends Model
{
    use HasFactory;

    protected $table = 'student_families';

    protected $fillable = [
        'student_id', 
        'hubungan', 
        'jenis_kelamin', 
        'nama', 
        'tanggal_lahir',
        'usia',       // [NEW] Sesuai formulir kolom "Usia"
        'pendidikan', 
        'pekerjaan', 
        'penghasilan'
    ];
    
    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}