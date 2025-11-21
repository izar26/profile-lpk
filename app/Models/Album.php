<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_album',
        'deskripsi_album',
    ];

    /**
     * Mendefinisikan bahwa satu Album memiliki BANYAK item galeri.
     */
    public function galeris(): HasMany
    {
        return $this->hasMany(Galeri::class);
    }
}