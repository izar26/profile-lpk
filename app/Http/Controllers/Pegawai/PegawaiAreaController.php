<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf; // [BARU] Import PDF

class PegawaiAreaController extends Controller
{
    public function dashboard()
    {
        return view('pegawai.dashboard');
    }

    public function editBiodata()
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            // Jika admin belum menghubungkan data
            return redirect()->route('pegawai.dashboard')->with('error', 'Data kepegawaian belum dihubungkan.');
        }

        return view('pegawai.biodata.edit', compact('employee'));
    }

    public function updateBiodata(Request $request)
    {
        $employee = Auth::user()->employee;

        // 1. Validasi Lengkap
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:50',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'linkedin' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = collect($validated)->except('foto')->toArray();

        // 2. Upload Foto
        if ($request->hasFile('foto')) {
            if ($employee->foto) Storage::disk('public')->delete($employee->foto);
            
            $path = $request->file('foto')->store('employee_foto', 'public');
            $data['foto'] = $path;

            // Sync ke User Avatar
            if(Auth::user()->foto && Auth::user()->foto != $path) {
                 Storage::disk('public')->delete(Auth::user()->foto);
            }
            Auth::user()->update(['foto' => $path]);
        }

        $employee->update($data);
        
        // Sync Nama User
        Auth::user()->update(['name' => $request->nama]);

        return redirect()->back()->with('success', 'Biodata pegawai diperbarui.');
    }

    // [BARU] Fitur Cetak Biodata Sendiri
    public function printBiodata()
    {
        $employee = Auth::user()->employee;
        if (!$employee) return back();

        // Kita gunakan view yang SAMA dengan milik Admin agar efisien
        $pdf = Pdf::loadView('admin.employees.pdf_biodata', compact('employee'));
        return $pdf->download('biodata-saya.pdf');
    }
}