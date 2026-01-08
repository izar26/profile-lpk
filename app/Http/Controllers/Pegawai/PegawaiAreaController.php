<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\EmployeeEducation;
use App\Models\EmployeeFamily;
use App\Models\EmployeeDocument;
use App\Models\LpkProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PegawaiAreaController extends Controller
{
    /**
     * Menampilkan Dashboard Pegawai
     */
    public function dashboard()
    {
        // Load data pegawai beserta relasinya untuk ditampilkan di dashboard
        $employee = Auth::user()->employee;

        if ($employee) {
            $employee->load(['educations', 'families', 'documents']);
        }

        return view('pegawai.dashboard', compact('employee'));
    }

    /**
     * Menampilkan Form Edit Biodata
     */
    public function editBiodata()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return redirect()->route('pegawai.dashboard')->with('error', 'Data kepegawaian tidak ditemukan. Hubungi Admin.');
        }

        // Load relasi agar bisa ditampilkan list-nya di form edit
        $employee->load(['educations', 'families', 'documents']);

        return view('pegawai.biodata.edit', compact('employee'));
    }

    /**
     * Memproses Update Biodata Utama (Data Diri, Alamat, Kontak)
     */
    public function updateBiodata(Request $request)
    {
        $employee = Auth::user()->employee;

        // 1. Validasi Sesuai Kolom Database Baru
        $request->validate([
            'nama'              => 'required|string|max:255',
            'nomor_ktp'         => 'required|string|max:16|unique:employees,nomor_ktp,' . $employee->id,
            'nomor_kk'          => 'nullable|string|max:16',
            'nomor_npwp'        => 'nullable|string|max:20',
            'tempat_lahir'      => 'required|string|max:100',
            'tanggal_lahir'     => 'required|date',
            'jenis_kelamin'     => 'required|in:L,P',
            'golongan_darah'    => 'nullable|string|max:5',
            'agama'             => 'required|string|max:50',
            'status_pernikahan' => 'nullable|string|max:50',
            'tinggi_badan'      => 'nullable|numeric',
            'berat_badan'       => 'nullable|numeric',

            // Alamat & Kontak
            'alamat_ktp'        => 'required|string',
            'kota_ktp'          => 'required|string|max:100',
            'provinsi_ktp'      => 'required|string|max:100',
            'alamat_domisili'   => 'nullable|string',
            'telepon'           => 'required|string|max:20',
            'no_hp_keluarga_darurat' => 'nullable|string|max:20',
            'email'             => 'required|email|unique:employees,email,' . $employee->id,
            'instagram'         => 'nullable|string|max:255',

            // Foto
            'foto'              => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request, $employee) {

            $data = $request->except(['foto', '_token', '_method']);

            // 2. Handle Upload Foto
            if ($request->hasFile('foto')) {
                // Hapus foto lama
                if ($employee->foto && Storage::disk('public')->exists($employee->foto)) {
                    Storage::disk('public')->delete($employee->foto);
                }

                $path = $request->file('foto')->store('employee_foto', 'public');
                $data['foto'] = $path;

                // Sync ke User Auth
                Auth::user()->update(['foto' => $path]);
            }

            // 3. Update Tabel Employees
            $employee->update($data);

            // 4. Sync Nama & Email ke User Auth
            Auth::user()->update([
                'name' => $request->nama,
                'email' => $request->email
            ]);
        });

        return redirect()->back()->with('success', 'Biodata utama berhasil diperbarui.');
    }

    // =========================================================================
    // FITUR: MANAJEMEN PENDIDIKAN (RIWAYAT)
    // =========================================================================

    public function storeEducation(Request $request)
    {
        $request->validate([
            'jenjang' => 'required|string',
            'nama_sekolah' => 'required|string',
            'tahun_lulus' => 'required|numeric',
        ]);

        EmployeeEducation::create([
            'employee_id' => Auth::user()->employee->id,
            'jenjang' => $request->jenjang,
            'nama_sekolah' => $request->nama_sekolah,
            'jurusan' => $request->jurusan,
            'tahun_masuk' => $request->tahun_masuk,
            'tahun_lulus' => $request->tahun_lulus,
            'nilai_akhir' => $request->nilai_akhir,
        ]);

        return back()->with('success', 'Riwayat pendidikan ditambahkan.');
    }

    public function destroyEducation($id)
    {
        $edu = EmployeeEducation::where('id', $id)->where('employee_id', Auth::user()->employee->id)->firstOrFail();
        $edu->delete();
        return back()->with('success', 'Data pendidikan dihapus.');
    }

    // =========================================================================
    // FITUR: MANAJEMEN KELUARGA
    // =========================================================================

    public function storeFamily(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string',
            'hubungan' => 'required|string',
        ]);

        EmployeeFamily::create([
            'employee_id' => Auth::user()->employee->id,
            'nama_lengkap' => $request->nama_lengkap,
            'hubungan' => $request->hubungan,
            'nik' => $request->nik,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'pekerjaan' => $request->pekerjaan,
            'no_hp' => $request->no_hp,
        ]);

        return back()->with('success', 'Data keluarga ditambahkan.');
    }

    public function destroyFamily($id)
    {
        $fam = EmployeeFamily::where('id', $id)->where('employee_id', Auth::user()->employee->id)->firstOrFail();
        $fam->delete();
        return back()->with('success', 'Data keluarga dihapus.');
    }

    // =========================================================================
    // FITUR: UPLOAD DOKUMEN MANDIRI
    // =========================================================================

    public function storeDocument(Request $request)
    {
        $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'file_dokumen' => 'required|file|max:5120', // Max 5MB
        ]);

        DB::transaction(function () use ($request) {
            $path = $request->file('file_dokumen')->store('employee_documents', 'public');

            EmployeeDocument::create([
                'employee_id' => Auth::user()->employee->id,
                'nama_dokumen' => $request->nama_dokumen,
                'file_path' => $path,
            ]);
        });

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function destroyDocument($id)
    {
        // Pastikan hanya bisa menghapus dokumen milik sendiri
        $doc = EmployeeDocument::where('id', $id)->where('employee_id', Auth::user()->employee->id)->firstOrFail();

        DB::transaction(function () use ($doc) {
            if (Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
            $doc->delete();
        });

        return back()->with('success', 'Dokumen dihapus.');
    }

    // =========================================================================
    // FITUR: CETAK BIODATA
    // =========================================================================

    public function printBiodata()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return back()->with('error', 'Data pegawai tidak ditemukan.');
        }

        // Load semua relasi agar tercetak di PDF
        $employee->load(['educations', 'families', 'documents']);

        $profile = LpkProfile::first();

        // Menggunakan view PDF yang sama dengan Admin (Reusability)
        $pdf = Pdf::loadView('admin.employees.pdf_biodata', compact('employee', 'profile'));

        return $pdf->download('Biodata-' . Str::slug($employee->nama) . '.pdf');
    }
}
