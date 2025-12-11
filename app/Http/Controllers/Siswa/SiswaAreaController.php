<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ProgramPelatihan;
use Illuminate\Support\Facades\DB;

class SiswaAreaController extends Controller
{
    public function dashboard()
    {
        return view('siswa.dashboard');
    }

    public function printBiodata()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return back()->with('error', 'Data biodata belum tersedia.');
        }

        // Pastikan view ini ada. Jika belum, gunakan view sederhana dulu.
        $pdf = Pdf::loadView('admin.students.pdf_biodata', compact('student'));
        
        return $pdf->download('biodata-' . \Str::slug($student->nama_lengkap) . '.pdf');
    }

    public function showFormulir()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect()->route('siswa.dashboard')->with('error', 'Akun Anda belum terhubung dengan data siswa. Hubungi Admin.');
        }
        
        // Load relasi untuk ditampilkan di form wizard
        $student->load(['families', 'educations', 'experiences']);

        $programs = ProgramPelatihan::all();
        
        return view('siswa.formulir.wizard', compact('student', 'programs'));
    }

    /**
     * Menyimpan/Update Data Formulir Lengkap.
     */
    public function updateFormulir(Request $request)
{
    $student = Auth::user()->student;

    if (!$student) {
        return back()->with('error', 'Data siswa tidak ditemukan.');
    }

    // 1. CEK STATUS LOCK
    if (!in_array($student->status, ['Mendaftar', 'Perlu Revisi'])) {
        return back()->with('error', 'Formulir terkunci. Status: ' . $student->status);
    }

    // 2. VALIDASI DATA (PENTING: Agar tidak crash jika ada yang kosong)
    $request->validate([
        'program_pelatihan_id' => 'required',
        'nama_lengkap'         => 'required|string|max:255',
        'tempat_lahir'         => 'required|string',
        'tanggal_lahir'        => 'required|date',
        'jenis_kelamin'        => 'required|in:Laki-laki,Perempuan',
        'nik'                  => 'nullable|numeric', // Sesuaikan dengan nama field di form (nomor_ktp)
        'nomor_ktp'            => 'required|numeric',
        'email'                => 'required|email',
        'no_hp_peserta'        => 'required',
        'foto'                 => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi foto
        // Tambahkan validasi lain sesuai kebutuhan...
    ], [
        // Custom Pesan Error (Opsional)
        'nama_lengkap.required' => 'Nama Lengkap wajib diisi.',
        'nomor_ktp.required'    => 'Nomor KTP wajib diisi.',
        'program_pelatihan_id.required' => 'Silakan pilih program pelatihan.',
    ]);

    // 3. MULAI PROSES PENYIMPANAN DENGAN TRY-CATCH
    DB::beginTransaction(); // Mulai transaksi database

    try {
        // --- A. SIMPAN DATA PRIBADI ---
        $data = $request->only([
            'program_pelatihan_id',
            'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
            'tinggi_badan', 'berat_badan', 'golongan_darah',
            'agama', 'status_pernikahan',
            'nomor_ktp', 'nomor_kk', 'nomor_paspor', 'nomor_npwp',
            'email', 'no_hp_peserta', 'no_hp_ortu',    
            'alamat_domisili', 'alamat_ktp', 'kota_ktp', 'provinsi_ktp',
            'kota_pembuatan'
        ]);

        $data['pernah_bekerja'] = $request->has('pernah_bekerja') ? true : false;
        $student->fill($data);
        $student->save(); // Simpan Data Diri dulu

        // --- B. SIMPAN PENDIDIKAN ---
        // Kita hapus dulu yang lama, lalu buat baru (Reset Strategy)
        $student->educations()->delete(); 
        if ($request->has('pendidikan')) {
            foreach ($request->pendidikan as $edu) {
                if (!empty($edu['nama_institusi'])) {
                    $student->educations()->create($edu);
                }
            }
        }

        // --- C. SIMPAN KELUARGA ---
        $student->families()->delete();
        if ($request->has('keluarga')) {
            foreach ($request->keluarga as $fam) {
                if (!empty($fam['nama'])) {
                    $student->families()->create($fam);
                }
            }
        }

        // --- D. SIMPAN PENGALAMAN ---
        $student->experiences()->delete();
        if ($request->has('pengalaman')) {
            foreach ($request->pengalaman as $exp) {
                if (!empty($exp['nama_instansi'])) {
                    $student->experiences()->create($exp);
                }
            }
        }

        // --- E. UPLOAD FOTO ---
        if ($request->hasFile('foto')) {
            if ($student->foto) {
                Storage::disk('public')->delete($student->foto);
            }
            $student->foto = $request->file('foto')->store('foto_siswa', 'public');
            
            // Sinkron ke tabel User
            if($student->user) {
                $student->user->update(['foto' => $student->foto]);
            }
            $student->save(); // Simpan path foto baru
        }

        // --- F. UPLOAD DOKUMEN ---
        $documents = ['file_ktp', 'file_kk', 'file_ijazah', 'file_sertifikat_jlpt', 'file_rekomendasi_sekolah', 'file_izin_ortu'];
        foreach ($documents as $doc) {
            if ($request->hasFile($doc)) {
                if ($student->$doc) {
                    Storage::disk('public')->delete($student->$doc);
                }
                $student->$doc = $request->file($doc)->store('dokumen_siswa', 'public');
            }
        }
        $student->save(); // Simpan path dokumen

        // --- G. UPDATE STATUS ---
        $student->update(['status' => 'Menunggu Verifikasi']);

        DB::commit(); // Semua sukses? Simpan permanen.

        return redirect()->back()->with('success', 'Formulir berhasil dikirim! Data sedang diverifikasi.');

    } catch (\Exception $e) {
        DB::rollBack(); // Ada error? Batalkan semua perubahan.
        
        // Return error ke user (bukan halaman crash)
        // Log error asli untuk developer: \Log::error($e->getMessage());
        return back()
            ->withInput() // Kembalikan input user agar tidak ngetik ulang
            ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
    }
}
}