<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Galeri; // <-- [PENTING] Kita pakai model Galeri di sini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlbumMediaController extends Controller
{
    /**
     * Menampilkan halaman "Kelola Media" untuk album tertentu.
     */
    public function index(Album $album)
    {
        // Ambil semua item galeri yang HANYA milik album ini
        $galeriItems = $album->galeris()->latest('tanggal_kegiatan')->get();

        // Ambil HANYA FOTO untuk lightbox slider
        $fotoItems = $galeriItems->where('tipe', 'foto')->values();

        return view('admin.albums.media', compact('album', 'galeriItems', 'fotoItems'));
    }

    /**
     * Menyimpan item media (foto/video) BARU ke dalam album ini.
     */
    public function store(Request $request, Album $album)
    {
        $request->validate([
            'judul' => 'nullable|string|max:255',
            'tipe' => 'required|in:foto,video',
            'path_file' => 'required_if:tipe,foto|nullable|image|mimes:jpeg,png,jpg|max:2048',
            'url_video' => 'required_if:tipe,video|nullable|url',
            'tanggal_kegiatan' => 'nullable|date',
        ], [
            'path_file.required_if' => 'File foto wajib di-upload jika tipe adalah Foto.',
            'url_video.required_if' => 'Link video YouTube wajib diisi jika tipe adalah Video.',
        ]);

        $data = $request->only('judul', 'tipe', 'tanggal_kegiatan');

        // [WAJIB] Set koneksi ke album
        $data['album_id'] = $album->id;

        if (empty($data['tanggal_kegiatan'])) {
            $data['tanggal_kegiatan'] = now();
        }

        if ($request->tipe == 'foto') {
            $path = $request->file('path_file')->store('galeri_foto', 'public');
            $data['path_file'] = $path;
        } else {
            $data['url_video'] = $request->url_video;
        }

        Galeri::create($data);

        return redirect()->route('admin.albums.media.index', $album)
                         ->with('success', 'Item galeri berhasil ditambahkan.');
    }

    /**
     * Menghapus item media (foto/video).
     * Perhatikan kita menerima $galeri, bukan $album.
     */
    public function destroy(Galeri $galeri)
    {
        // Ambil album_id untuk redirect kembali ke halaman yang benar
        $albumId = $galeri->album_id;

        if ($galeri->tipe == 'foto' && $galeri->path_file) {
            Storage::disk('public')->delete($galeri->path_file);
        }

        $galeri->delete();

        return redirect()->route('admin.albums.media.index', $albumId)
                         ->with('success', 'Item galeri berhasil dihapus.');
    }
}