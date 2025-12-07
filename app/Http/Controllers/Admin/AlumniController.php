<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlumniController extends Controller
{
    public function index()
    {
        // Load relasi student agar nama bisa diambil
        $alumnis = Alumni::with('student')->latest()->paginate(10);
        
        // Ambil student yang statusnya 'Alumni' TAPI belum ada di tabel alumnis
        // Supaya tidak double input testimoni untuk orang yang sama
        $availableStudents = Student::where('status', 'Alumni')
                                    ->doesntHave('alumni')
                                    ->orderBy('nama_lengkap', 'asc')
                                    ->get();

        return view('admin.alumni.index', compact('alumnis', 'availableStudents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'   => 'required|exists:students,id|unique:alumnis,student_id',
            'kerja_dimana' => 'required|string|max:255',
            'angkatan'     => 'nullable|string|max:50',
            'testimoni'    => 'required|string',
            'foto'         => 'nullable|image|max:2048',
            'is_published' => 'nullable|boolean',
        ]);

        $data = $request->all();
        // Default published false jika tidak dicentang
        $data['is_published'] = $request->has('is_published') ? 1 : 0;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('alumni_foto', 'public');
        }

        Alumni::create($data);

        return redirect()->back()->with('success', 'Testimoni alumni berhasil ditambahkan.');
    }

    public function edit(Alumni $alumni)
    {
        // Load relasi student untuk ditampilkan namanya di form edit (readonly)
        $alumni->load('student');
        return response()->json($alumni);
    }

    public function update(Request $request, Alumni $alumni)
    {
        $request->validate([
            'kerja_dimana' => 'required|string|max:255',
            'angkatan'     => 'nullable|string|max:50',
            'testimoni'    => 'required|string',
            'foto'         => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['student_id']); // Student ID tidak boleh diubah
        
        // Handle Toggle Switch dari form (bisa jadi string "on" atau null)
        $data['is_published'] = $request->has('is_published') ? 1 : 0;

        if ($request->hasFile('foto')) {
            if ($alumni->foto) Storage::disk('public')->delete($alumni->foto);
            $data['foto'] = $request->file('foto')->store('alumni_foto', 'public');
        }

        $alumni->update($data);

        return redirect()->back()->with('success', 'Data alumni diperbarui.');
    }

    public function destroy(Alumni $alumni)
    {
        if ($alumni->foto) Storage::disk('public')->delete($alumni->foto);
        $alumni->delete();
        return redirect()->back()->with('success', 'Data alumni dihapus.');
    }
    
    // Method baru untuk Toggle Status via AJAX (Opsional, tapi bagus utk UX)
    public function toggleStatus($id)
    {
        $alumni = Alumni::findOrFail($id);
        $alumni->is_published = !$alumni->is_published;
        $alumni->save();
        
        return response()->json(['message' => 'Status visibilitas berhasil diubah.']);
    }
}