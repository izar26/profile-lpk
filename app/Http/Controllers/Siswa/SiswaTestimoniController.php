<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SiswaTestimoniController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;

        // Keamanan: Cek apakah benar-benar alumni
        if ($student->status !== 'Alumni') {
            return redirect()->route('siswa.dashboard')->with('error', 'Menu ini khusus untuk Alumni.');
        }

        // Ambil data testimoni jika sudah ada
        $testimoni = Alumni::where('student_id', $student->id)->first();

        return view('siswa.testimoni.index', compact('student', 'testimoni'));
    }

    public function store(Request $request)
    {
        $student = Auth::user()->student;

        if ($student->status !== 'Alumni') {
            abort(403);
        }

        $request->validate([
            'kerja_dimana' => 'required|string|max:255',
            'angkatan'     => 'nullable|string|max:50',
            'testimoni'    => 'required|string',
            'foto'         => 'nullable|image|max:2048',
        ]);

        // Cek data lama
        $alumni = Alumni::where('student_id', $student->id)->first();
        
        $data = $request->only(['kerja_dimana', 'angkatan', 'testimoni']);
        
        // LOGIC PENTING:
        // Setiap kali siswa update/simpan, status kembali ke Draft (unpublish)
        // agar admin bisa review ulang kata-katanya.
        $data['is_published'] = false; 
        $data['student_id'] = $student->id;

        if ($request->hasFile('foto')) {
            if ($alumni && $alumni->foto) {
                Storage::disk('public')->delete($alumni->foto);
            }
            $data['foto'] = $request->file('foto')->store('alumni_foto', 'public');
        }

        // Update or Create
        if ($alumni) {
            $alumni->update($data);
            $message = 'Testimoni berhasil diperbarui. Menunggu persetujuan Admin untuk tampil.';
        } else {
            Alumni::create($data);
            $message = 'Testimoni berhasil dikirim. Menunggu persetujuan Admin untuk tampil.';
        }

        return redirect()->back()->with('success', $message);
    }
}