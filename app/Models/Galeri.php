<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- [BARU] Import

class Galeri extends Model
{
    use HasFactory;

    protected $fillable = [
        'album_id', // <-- [BARU] Tambahkan ini
        'judul',
        'tipe',
        'path_file',
        'url_video',
        'tanggal_kegiatan', 
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date', 
    ];

    /**
     * [BARU] Mendefinisikan bahwa item galeri ini MILIK SATU album.
     */
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    // Fungsi embedUrl() Anda (biarkan apa adanya)
    protected function embedUrl(): Attribute
{
    return Attribute::make(
        get: function () {
            if ($this->tipe !== 'video' || !$this->url_video) {
                return null;
            }

            // Pola regex untuk mengekstrak ID video
            preg_match('/(watch\?v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]+)/', $this->url_video, $matches);

            return $matches[2] ? 'https://www.youtube.com/embed/' . $matches[2] : null;
        }
    );
}
}
