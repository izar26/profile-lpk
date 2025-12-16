<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDocument extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi balik ke Jenis Dokumen (untuk ambil nama dokumennya nanti)
    public function type()
    {
        return $this->belongsTo(RefDocumentType::class, 'document_type_id');
    }
}