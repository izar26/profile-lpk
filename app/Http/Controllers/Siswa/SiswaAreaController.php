<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class SiswaAreaController extends Controller
{
    public function dashboard()
    {
        return view('siswa.dashboard');
    }

    public function editBiodata()
    {
        // Ambil data siswa yang terhubung dengan user yang sedang login
        $student = Auth::user()->student;

        // Jika data belum di-link admin, tampilkan error atau form kosong
        if (!$student) {
            return redirect()->route('siswa.dashboard')->with('error', 'Data biodata belum dihubungkan oleh Admin.');
        }

        return view('siswa.biodata.edit', compact('student'));
    }

    public function updateBiodata(Request $request)
    {
        $student = Auth::user()->student;

        // 1. Validasi SEMUA field baru
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'golongan_darah' => 'nullable|string|max:5',
            'agama' => 'nullable|string|max:50',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'nama_ayah' => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:255',
            'no_hp_ortu' => 'nullable|string|max:20',
            'sekolah_asal' => 'nullable|string|max:255',
            'tahun_lulus' => 'nullable|string|max:4',
            'foto' => 'nullable|image|max:2048',
        ]);

        // 2. Ambil semua data yang divalidasi KECUALI foto (karena butuh proses upload)
        $data = collect($validated)->except('foto')->toArray();

        // 3. Proses Upload Foto
        if ($request->hasFile('foto')) {
            // Hapus foto lama di tabel students
            if ($student->foto) {
                Storage::disk('public')->delete($student->foto);
            }
            
            // Simpan foto baru
            $path = $request->file('foto')->store('student_foto', 'public');
            $data['foto'] = $path;

            // [OPSIONAL] Update juga foto di tabel Users (Avatar) agar sinkron
            if(Auth::user()->foto && Auth::user()->foto != $path) {
                 Storage::disk('public')->delete(Auth::user()->foto);
            }
            Auth::user()->update(['foto' => $path]);
        }

        // 4. Update Data Siswa ke Database
        $student->update($data);

        // 5. Sinkronisasi Nama ke tabel Users agar nama di pojok kanan atas ikut berubah
        Auth::user()->update(['name' => $request->nama]);

        return redirect()->back()->with('success', 'Biodata lengkap berhasil diperbarui.');
    }

    public function printBiodata()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return back()->with('error', 'Data biodata belum tersedia.');
        }

        // Kita REUSE (Gunakan kembali) view PDF milik admin agar konsisten
        $pdf = Pdf::loadView('admin.students.pdf_biodata', compact('student'));
        
        return $pdf->download('biodata-saya.pdf');
    }
}