<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{
    /**
     * Menampilkan halaman daftar album.
     */
    public function index()
{
    // [PENTING] Pastikan Anda sudah menambahkan withCount
    $albums = Album::withCount('galeris')->latest()->paginate(10);
    return view('admin.albums.index', compact('albums'));
}

    /**
     * Menyimpan album baru dari modal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_album' => 'required|string|max:255',
            'deskripsi_album' => 'nullable|string',
        ]);

        Album::create($request->all());

        return redirect()->route('admin.albums.index')->with('success', 'Album baru berhasil dibuat.');
    }

    /**
     * Mengambil data album untuk modal edit (JSON).
     */
    public function edit(Album $album)
    {
        return response()->json($album);
    }

    /**
     * Mengupdate data album.
     */
    public function update(Request $request, Album $album)
    {
        $request->validate([
            'nama_album' => 'required|string|max:255',
            'deskripsi_album' => 'nullable|string',
        ]);

        $album->update($request->all());

        return redirect()->route('admin.albums.index')->with('success', 'Album berhasil diperbarui.');
    }

    /**
     * Menghapus album DAN semua foto/video di dalamnya.
     */
    public function destroy(Album $album)
    {
        // 1. Hapus semua file foto dari storage
        foreach ($album->galeris as $item) {
            if ($item->tipe == 'foto' && $item->path_file) {
                Storage::disk('public')->delete($item->path_file);
            }
        }

        // 2. Hapus album (ini akan otomatis menghapus item galeri 
        //    di database karena kita atur 'onDelete: cascade')
        $album->delete();

        return redirect()->route('admin.albums.index')->with('success', 'Album dan semua isinya berhasil dihapus.');
    }
}