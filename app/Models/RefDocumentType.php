<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefDocumentType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Helper untuk mengubah nama jadi slug otomatis saat create/update
    public function setNamaAttribute($value)
    {
        $this->attributes['nama'] = $value;
        $this->attributes['slug'] = \Str::slug($value);
    }
}