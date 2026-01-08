<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
    use HasFactory;
    
    protected $table = 'employee_educations';

    protected $fillable = [
        'employee_id',
        'jenjang',        // SD, SMP, SMA, S1, dll
        'nama_sekolah',
        'jurusan',
        'tahun_masuk',
        'tahun_lulus',
        'nilai_akhir',    // IPK / Nilai
    ];

    // Relasi balik ke Pegawai
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
