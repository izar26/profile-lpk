<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// HAPUS: use Te7aHoudini\LaravelTrix\Traits\HasTrixRichText;

class Edukasi extends Model
{
    use HasFactory; // <-- HAPUS: HasTrixRichText

    protected $fillable = [
        'judul',
        'slug',
        'konten',
        'gambar_fitur',
        'status',
        'user_id',
    ];

    /**
     * Relasi ke penulis (User).
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}