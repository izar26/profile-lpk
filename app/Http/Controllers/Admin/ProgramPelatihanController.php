<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramPelatihan; // <-- DIUBAH
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProgramPelatihanController extends Controller // <-- DIUBAH
{
    // Menampilkan halaman utama
    public function index()
    {
        $programs = ProgramPelatihan::latest()->paginate(10); // <-- DIUBAH
        // Arahkan ke view baru
        return view('admin.program-pelatihan.index', compact('programs')); // <-- DIUBAH
    }

    // Menyimpan program baru
    public function store(Request $request)
    {
        // (Logika validasi tidak berubah, tapi kita tambahkan session)
        try {
            $validatedData = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi_singkat' => 'required|string|max:255',
                'deskripsi_lengkap' => 'nullable|string',
                'gambar_fitur' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'status' => 'required|in:Akan Datang,Buka Pendaftaran,Berjalan,Selesai',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput()->with('openModalTambah', true);
        }

        $data = $validatedData;

        if ($request->hasFile('gambar_fitur')) {
            $path = $request->file('gambar_fitur')->store('program_pelatihan', 'public'); // <-- Ganti nama folder
            $data['gambar_fitur'] = $path;
        }

        ProgramPelatihan::create($data); // <-- DIUBAH

        return redirect()->route('admin.program-pelatihan.index')->with('success', 'Program baru berhasil ditambahkan.'); // <-- DIUBAH
    }

    // Mengambil data untuk modal "Edit"
    public function edit(ProgramPelatihan $program) // <-- DIUBAH
    {
        return response()->json($program);
    }

    // Update program
    public function update(Request $request, ProgramPelatihan $program) // <-- DIUBAH
    {
        try {
            $validatedData = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi_singkat' => 'required|string|max:255',
                'deskripsi_lengkap' => 'nullable|string',
                'gambar_fitur' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'status' => 'required|in:Akan Datang,Buka Pendaftaran,Berjalan,Selesai',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // [PENTING] Ganti 'editProgramId' agar cocok dengan script
            return redirect()->back()->withErrors($e->errors())->withInput()->with('openModalEdit', true)->with('editProgramId', $program->id);
        }

        $data = $validatedData;

        if ($request->hasFile('gambar_fitur')) {
            if ($program->gambar_fitur) {
                Storage::disk('public')->delete($program->gambar_fitur);
            }
            $path = $request->file('gambar_fitur')->store('program_pelatihan', 'public'); // <-- Ganti nama folder
            $data['gambar_fitur'] = $path;
        }

        $program->update($data);

        return redirect()->route('admin.program-pelatihan.index')->with('success', 'Program berhasil diperbarui.'); // <-- DIUBAH
    }

    // Menghapus program
    public function destroy(ProgramPelatihan $program) // <-- DIUBAH
    {
        if ($program->gambar_fitur) {
            Storage::disk('public')->delete($program->gambar_fitur);
        }

        $program->delete();

        return redirect()->route('admin.program-pelatihan.index')->with('success', 'Program berhasil dihapus.'); // <-- DIUBAH
    }
}