<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProgramPelatihanController extends Controller
{
    public function index()
    {
        $programs = ProgramPelatihan::latest()->paginate(10);
        return view('admin.program-pelatihan.index', compact('programs'));
    }

    // [BARU] Halaman Tambah
    public function create()
    {
        return view('admin.program-pelatihan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi_singkat' => 'required|string|max:255',
            'deskripsi_lengkap' => 'nullable|string', // Ini akan berisi HTML dari Trix
            'gambar_fitur' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:Akan Datang,Buka Pendaftaran,Berjalan,Selesai',
        ]);

        $data = $request->except('gambar_fitur');

        if ($request->hasFile('gambar_fitur')) {
            $path = $request->file('gambar_fitur')->store('program_penelitian', 'public');
            $data['gambar_fitur'] = $path;
        }

        ProgramPelatihan::create($data);

        return redirect()->route('admin.program-pelatihan.index')->with('success', 'Program baru berhasil ditambahkan.');
    }

    // [UBAH] Halaman Edit (Bukan JSON lagi)
    public function edit(ProgramPelatihan $program_pelatihan) 
    {
        // Note: Laravel resource binding mungkin menggunakan nama variabel $program_pelatihan 
        // atau $program tergantung route list. Kita gunakan variabel umum $program di view.
        return view('admin.program-pelatihan.edit', ['program' => $program_pelatihan]);
    }

    public function update(Request $request, ProgramPelatihan $program_pelatihan)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi_singkat' => 'required|string|max:255',
            'deskripsi_lengkap' => 'nullable|string',
            'gambar_fitur' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:Akan Datang,Buka Pendaftaran,Berjalan,Selesai',
        ]);

        $data = $request->except('gambar_fitur');

        if ($request->hasFile('gambar_fitur')) {
            if ($program_pelatihan->gambar_fitur) {
                Storage::disk('public')->delete($program_pelatihan->gambar_fitur);
            }
            $path = $request->file('gambar_fitur')->store('program_penelitian', 'public');
            $data['gambar_fitur'] = $path;
        }

        $program_pelatihan->update($data);

        return redirect()->route('admin.program-pelatihan.index')->with('success', 'Program berhasil diperbarui.');
    }

    public function destroy(ProgramPelatihan $program_pelatihan)
    {
        if ($program_pelatihan->gambar_fitur) {
            Storage::disk('public')->delete($program_pelatihan->gambar_fitur);
        }
        
        $program_pelatihan->delete();

        return redirect()->route('admin.program-pelatihan.index')->with('success', 'Program berhasil dihapus.');
    }
}