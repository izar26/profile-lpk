<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ProgramPelatihan;
// [BARU] Import Model untuk Dokumen Dinamis
use App\Models\RefDocumentType;
use App\Models\StudentDocument;

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

        // Pastikan view PDF ini ada di folder resources/views/admin/students/
        $pdf = Pdf::loadView('admin.students.pdf_biodata', compact('student'));
        
        return $pdf->download('biodata-' . \Str::slug($student->nama_lengkap) . '.pdf');
    }

    public function showFormulir()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect()->route('siswa.dashboard')->with('error', 'Akun Anda belum terhubung dengan data siswa. Hubungi Admin.');
        }
        
        // [UPDATE] Load relasi 'documents' juga
        $student->load(['families', 'educations', 'experiences', 'documents']);

        $programs = ProgramPelatihan::all();
        
        // [BARU] Ambil Master Data Dokumen yang Aktif
        $documentTypes = RefDocumentType::where('is_active', true)->orderBy('id', 'asc')->get();

        // [BARU] Mapping dokumen yang SUDAH diupload siswa biar gampang dicek di View
        // Hasilnya array: [ id_tipe_dokumen => 'path/file.jpg', ... ]
        $uploadedDocuments = $student->documents->pluck('file_path', 'document_type_id')->toArray();

        return view('siswa.formulir.wizard', compact('student', 'programs', 'documentTypes', 'uploadedDocuments'));
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

        // 2. VALIDASI DATA UMUM
        $request->validate([
            'program_pelatihan_id' => 'required',
            'nama_lengkap'         => 'required|string|max:255',
            'tempat_lahir'         => 'required|string',
            'tanggal_lahir'        => 'required|date',
            'jenis_kelamin'        => 'required|in:Laki-laki,Perempuan',
            'nomor_ktp'            => 'required|numeric',
            'email'                => 'required|email',
            'no_hp_peserta'        => 'required',
            'foto'                 => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi Pas Foto
            'documents.*'          => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',   // Validasi Dokumen Pendukung
        ], [
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi.',
            'nomor_ktp.required'    => 'Nomor KTP wajib diisi.',
            'program_pelatihan_id.required' => 'Silakan pilih program pelatihan.',
        ]);

        // 3. MULAI PROSES PENYIMPANAN
        DB::beginTransaction();

        try {
            // --- A. SIMPAN DATA PRIBADI ---
            // Kita exclude kolom file lama (file_ktp, file_kk, dll) dari sini
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
            $student->save(); 

            // --- B. SIMPAN PENDIDIKAN (Reset Strategy) ---
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

            // --- E. UPLOAD PAS FOTO (Tetap di tabel students) ---
            if ($request->hasFile('foto')) {
                if ($student->foto) {
                    Storage::disk('public')->delete($student->foto);
                }
                $student->foto = $request->file('foto')->store('foto_siswa', 'public');
                
                // Sinkron ke tabel User (opsional)
                if($student->user) {
                    $student->user->update(['foto' => $student->foto]);
                }
                $student->save();
            }

            // --- F. UPLOAD DOKUMEN DINAMIS (LOGIKA BARU) ---
            
            // 1. Cek Dokumen Wajib
            // Ambil semua dokumen yang wajib & aktif
            $requiredDocs = RefDocumentType::where('is_active', true)->where('is_required', true)->get();

            foreach ($requiredDocs as $doc) {
                // Cek apakah di DB sudah ada?
                $alreadyHas = $student->documents()->where('document_type_id', $doc->id)->exists();
                // Cek apakah user sedang upload file ini sekarang?
                $isUploadingNow = $request->hasFile("documents.{$doc->id}");

                // Jika tidak ada di DB DAN tidak diupload sekarang -> Error
                if (!$alreadyHas && !$isUploadingNow) {
                     // Kita lempar error agar ditangkap catch di bawah
                    throw new \Exception("Dokumen wajib belum diisi: " . $doc->nama);
                }
            }

            // 2. Proses Simpan File Looping
            if ($request->has('documents')) {
                foreach ($request->file('documents') as $typeId => $file) {
                    
                    // a. Hapus file lama fisik jika ada (Opsional, untuk hemat storage)
                    $oldDoc = $student->documents()->where('document_type_id', $typeId)->first();
                    if ($oldDoc && Storage::disk('public')->exists($oldDoc->file_path)) {
                        Storage::disk('public')->delete($oldDoc->file_path);
                    }

                    // b. Upload file baru
                    // Path: storage/app/public/dokumen_siswa/namafile.pdf
                    $path = $file->store('dokumen_siswa', 'public');

                    // c. Simpan/Update data ke tabel 'student_documents'
                    StudentDocument::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'document_type_id' => $typeId
                        ],
                        [
                            'file_path' => $path
                        ]
                    );
                }
            }

            // --- G. UPDATE STATUS ---
            $student->update(['status' => 'Menunggu Verifikasi']);

            DB::commit(); // Simpan permanen

            return redirect()->back()->with('success', 'Formulir berhasil dikirim! Data sedang diverifikasi.');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua perubahan jika error
            
            return back()
                ->withInput() 
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
}