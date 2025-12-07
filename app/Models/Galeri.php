<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Galeri extends Model
{
    use HasFactory;

    protected $fillable = [
        'album_id', 'judul', 'tipe', 'path_file', 'url_video', 'tanggal_kegiatan', 
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date', 
    ];

    // [BARU] Tambahkan ini agar atribut di bawah otomatis terbawa ke JSON/JavaScript
    protected $appends = ['embed_url', 'thumbnail_url'];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    // [UPDATE] Pastikan nama fungsinya get...Attribute untuk accessor lama
    // Atau gunakan style baru Laravel seperti di bawah ini:

    protected function embedUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->tipe !== 'video' || !$this->url_video) return null;
                preg_match('/(watch\?v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]+)/', $this->url_video, $matches);
                return isset($matches[2]) ? 'https://www.youtube.com/embed/' . $matches[2] : null;
            }
        );
    }

    // [BARU] Accessor untuk Thumbnail YouTube
    protected function getThumbnailUrlAttribute()
    {
        if ($this->tipe !== 'video' || !$this->url_video) return null;
        preg_match('/(watch\?v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]+)/', $this->url_video, $matches);
        return isset($matches[2]) ? "https://img.youtube.com/vi/{$matches[2]}/hqdefault.jpg" : null;
    }
}