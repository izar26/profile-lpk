<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PegawaiAreaController extends Controller
{
    /**
     * Menampilkan Dashboard Pegawai
     */
    public function dashboard()
    {
        return view('pegawai.dashboard');
    }

    /**
     * Menampilkan Form Edit Biodata
     */
    public function editBiodata()
    {
        // Mengambil data pegawai dari user yang sedang login
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            // Jika akun user belum terhubung ke data employees (kasus jarang terjadi jika sistem benar)
            return redirect()->route('pegawai.dashboard')->with('error', 'Data kepegawaian tidak ditemukan. Hubungi Admin.');
        }

        // Variabel dikirim sebagai 'employee', BUKAN 'student'
        return view('pegawai.biodata.edit', compact('employee'));
    }

    /**
     * Memproses Update Biodata
     * PERBAIKAN POIN 2: Hanya memvalidasi dan menyimpan kolom yang ada di Database.
     */
    public function updateBiodata(Request $request)
    {
        $employee = Auth::user()->employee;

        // 1. Validasi Data (Sesuai kolom di tabel 'employees')
        // Catatan: Jabatan, NIP, Status Kepegawaian tidak divalidasi disini karena itu wewenang Admin (Readonly di view)
        $validated = $request->validate([
            'nama'                => 'required|string|max:255',
            'tempat_lahir'        => 'nullable|string|max:255',
            'tanggal_lahir'       => 'nullable|date',
            'jenis_kelamin'       => 'nullable|in:L,P',
            'agama'               => 'nullable|string|max:50',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'alamat'              => 'nullable|string',
            'kota'                => 'nullable|string|max:100',
            'provinsi'            => 'nullable|string|max:100',
            'kode_pos'            => 'nullable|string|max:20',
            'telepon'             => 'nullable|string|max:50',
            'linkedin'            => 'nullable|string|max:255',
            'instagram'           => 'nullable|string|max:255',
            'foto'                => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Maks 2MB
        ]);

        // Pisahkan foto dari array data utama untuk diproses terpisah
        $dataToUpdate = collect($validated)->except('foto')->toArray();

        // 2. Logika Upload Foto
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($employee->foto && Storage::disk('public')->exists($employee->foto)) {
                Storage::disk('public')->delete($employee->foto);
            }
            
            // Simpan foto baru
            $path = $request->file('foto')->store('employee_foto', 'public');
            $dataToUpdate['foto'] = $path;

            // Sinkronisasi Foto ke Tabel Users (agar di navbar atas berubah)
            // Cek jika user punya foto lama di tabel users, hapus juga
            if(Auth::user()->foto && Auth::user()->foto != $path) {
                  // Opsional: Hapus foto lama user jika beda path (biasanya sama)
            }
            Auth::user()->update(['foto' => $path]);
        }

        // 3. Update Tabel Employees
        $employee->update($dataToUpdate);
        
        // 4. Sinkronisasi Nama ke Tabel Users (agar nama login ikut berubah)
        if ($request->has('nama')) {
            Auth::user()->update(['name' => $request->nama]);
        }

        return redirect()->back()->with('success', 'Biodata berhasil diperbarui.');
    }

    /**
     * Cetak Biodata Diri Sendiri (PDF)
     */
    public function printBiodata()
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return back()->with('error', 'Data pegawai tidak ditemukan.');
        }

        // Menggunakan view PDF yang sama dengan milik Admin agar efisien
        $pdf = Pdf::loadView('admin.employees.pdf_biodata', compact('employee'));
        
        // Download dengan nama file custom
        return $pdf->download('Biodata-'.str_slug($employee->nama).'.pdf');
    }
}