<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    use HasFactory;

    protected $fillable = [
    'student_id',
    'kerja_dimana',
    'angkatan',
    'testimoni',
    'foto',
    'is_published',
];


    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}