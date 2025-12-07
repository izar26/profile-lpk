<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ProgramPelatihan;

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

        // =========================================================================
        // 1. LOGIC LOCKING (PENGUNCIAN)
        // =========================================================================
        // Hanya izinkan edit jika status 'Mendaftar' (Baru) atau 'Perlu Revisi'.
        if (!in_array($student->status, ['Mendaftar', 'Perlu Revisi'])) {
            return back()->with('error', 'Formulir terkunci karena sedang diproses. Status: ' . $student->status);
        }

        // =========================================================================
        // 2. SIMPAN DATA PRIBADI (STEP 1)
        // =========================================================================
        
        // Ambil data dari request sesuai kolom database terbaru
        $data = $request->only([
            'program_pelatihan_id',
            
            // Data Diri
            'nama_lengkap', // [UPDATED]
            'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
            'tinggi_badan', 'berat_badan', 'golongan_darah',
            'agama', 'status_pernikahan',
            
            // Kependudukan
            'nomor_ktp', 'nomor_kk', 'nomor_paspor', 'nomor_npwp',
            
            // Kontak
            'email', 'no_hp_peserta', 'no_hp_ortu',    
            
            // Alamat
            'alamat_domisili', 
            'alamat_ktp', 'kota_ktp', 'provinsi_ktp', // [UPDATED]
            
            // Tanda Tangan
            'kota_pembuatan' // [UPDATED]
        ]);

        // [LOGIC KHUSUS] Handling Checkbox "Pernah Bekerja"
        // HTML Checkbox tidak mengirim value jika unchecked, jadi kita paksa boolean
        $data['pernah_bekerja'] = $request->has('pernah_bekerja') ? true : false;

        $student->fill($data);

        // =========================================================================
        // 3. SIMPAN PENDIDIKAN (STEP 2)
        // =========================================================================
        // Data dikirim dalam bentuk array: name="pendidikan[0][nama_institusi]" dst.
        if ($request->has('pendidikan')) {
            $student->educations()->delete(); // Hapus data lama (Reset strategy)
            
            foreach ($request->pendidikan as $edu) {
                // Filter baris kosong (jika user menambah row tapi tidak diisi)
                if (!empty($edu['nama_institusi'])) {
                    // Pastikan key array $edu sesuai dengan kolom di tabel student_educations
                    // (kategori, tingkat, nama_institusi, lokasi, jurusan, tahun_masuk, tahun_lulus, nilai_rata_rata)
                    $student->educations()->create($edu);
                }
            }
        }

        // =========================================================================
        // 4. SIMPAN KELUARGA (STEP 3)
        // =========================================================================
        if ($request->has('keluarga')) {
            $student->families()->delete();
            
            foreach ($request->keluarga as $fam) {
                if (!empty($fam['nama'])) {
                    // Pastikan key array $fam sesuai dengan kolom di tabel student_families
                    // (hubungan, jenis_kelamin, nama, tanggal_lahir, usia, pendidikan, pekerjaan, penghasilan)
                    $student->families()->create($fam);
                }
            }
        }

        // =========================================================================
        // 5. SIMPAN PENGALAMAN (STEP 4)
        // =========================================================================
        if ($request->has('pengalaman')) {
            $student->experiences()->delete();
            
            foreach ($request->pengalaman as $exp) {
                if (!empty($exp['nama_instansi'])) {
                    // Pastikan key array $exp sesuai dengan kolom di tabel student_experiences
                    // (tipe, nama_instansi, jenis_usaha, alamat_instansi, posisi, tanggal_mulai, tanggal_selesai, gaji_awal, gaji_akhir, alasan_berhenti)
                    $student->experiences()->create($exp);
                }
            }
        }

        // =========================================================================
        // 6. UPLOAD DOKUMEN & FOTO (STEP 5)
        // =========================================================================
        
        // A. Pas Foto
        if ($request->hasFile('foto')) {
            if ($student->foto) {
                Storage::disk('public')->delete($student->foto);
            }
            $student->foto = $request->file('foto')->store('foto_siswa', 'public');
            
            // Update foto User juga agar sinkron
            if($student->user) {
                $student->user->update(['foto' => $student->foto]);
            }
        }

        // B. Dokumen Lampiran
        $documents = [
            'file_ktp', 
            'file_kk', 
            'file_ijazah', 
            'file_sertifikat_jlpt', 
            'file_rekomendasi_sekolah', 
            'file_izin_ortu'
        ];
        
        foreach ($documents as $doc) {
            if ($request->hasFile($doc)) {
                // Hapus file lama jika ada
                if ($student->$doc) {
                    Storage::disk('public')->delete($student->$doc);
                }
                // Simpan file baru
                $student->$doc = $request->file($doc)->store('dokumen_siswa', 'public');
            }
        }

        // =========================================================================
        // 7. FINALISASI & AUTO-STATUS
        // =========================================================================
        
        // Ubah status jadi "Menunggu Verifikasi" agar Admin mendapat notifikasi/tanda
        $student->status = 'Menunggu Verifikasi';
        
        $student->save();

        return redirect()->back()->with('success', 'Formulir berhasil dikirim! Data sedang diverifikasi oleh Admin.');
    }
}