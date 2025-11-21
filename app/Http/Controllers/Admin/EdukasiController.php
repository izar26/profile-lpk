<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EdukasiController extends Controller
{
    public function index()
    {
        $edukasi = Edukasi::with('author')->latest()->paginate(10);
        return view('admin.edukasi.index', compact('edukasi'));
    }

    // [BARU] Menampilkan halaman form tambah
    public function create()
    {
        return view('admin.edukasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'gambar_fitur' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:Published,Draft',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($request->judul) . '-' . time();

        if ($request->hasFile('gambar_fitur')) {
            $path = $request->file('gambar_fitur')->store('edukasi_fitur', 'public');
            $data['gambar_fitur'] = $path;
        }

        Edukasi::create($data);

        return redirect()->route('admin.edukasi.index')->with('success', 'Artikel edukasi berhasil ditambahkan.');
    }

    // [UBAH] Menampilkan halaman form edit
    public function edit(Edukasi $edukasi)
    {
        return view('admin.edukasi.edit', compact('edukasi'));
    }

    public function update(Request $request, Edukasi $edukasi)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'gambar_fitur' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:Published,Draft',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->judul) . '-' . $edukasi->id;

        if ($request->hasFile('gambar_fitur')) {
            if ($edukasi->gambar_fitur) {
                Storage::disk('public')->delete($edukasi->gambar_fitur);
            }
            $path = $request->file('gambar_fitur')->store('edukasi_fitur', 'public');
            $data['gambar_fitur'] = $path;
        }

        $edukasi->update($data);

        return redirect()->route('admin.edukasi.index')->with('success', 'Artikel edukasi berhasil diperbarui.');
    }

    public function destroy(Edukasi $edukasi)
    {
        if ($edukasi->gambar_fitur) {
            Storage::disk('public')->delete($edukasi->gambar_fitur);
        }
        $edukasi->delete();
        
        return redirect()->route('admin.edukasi.index')->with('success', 'Artikel edukasi berhasil dihapus.');
    }
}