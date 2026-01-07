<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ProgramPelatihan;
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

        // Pastikan file view PDF ini ada
        $pdf = Pdf::loadView('admin.students.pdf_biodata', compact('student'));

        return $pdf->download('biodata-' . \Str::slug($student->nama_lengkap) . '.pdf');
    }

    public function showFormulir()
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('siswa.dashboard')->with('error', 'Akun Anda belum terhubung dengan data siswa. Hubungi Admin.');
        }

        // Load relasi documents agar kita tahu file apa yang sudah diupload
        $student->load(['families', 'educations', 'experiences', 'documents']);

        $programs = ProgramPelatihan::all();

        // Ambil Jenis Dokumen dari Master Data (Inilah yang membuatnya FLEKSIBEL)
        // Admin bisa tambah "Kartu Keluarga", "Ijazah", dll di panel admin tanpa ubah kodingan ini.
        $documentTypes = RefDocumentType::where('is_active', true)->orderBy('id', 'asc')->get();

        // Mapping dokumen yang SUDAH diupload siswa biar gampang dicek di View
        // Hasilnya array: [ id_tipe_dokumen => 'path/file.jpg', ... ]
        $uploadedDocuments = $student->documents->pluck('file_path', 'document_type_id')->toArray();

        return view('siswa.formulir.wizard', compact('student', 'programs', 'documentTypes', 'uploadedDocuments'));
    }

    /**
     * Menyimpan/Update Data Formulir Lengkap.
     */
    /**
     * Menyimpan/Update Data Formulir Lengkap dengan Validasi Ketat.
     */
    public function updateFormulir(Request $request)
    {
        $student = Auth::user()->student;

        if (!$student) return back()->with('error', 'Data siswa tidak ditemukan.');

        // Cek Status Lock
        if (!in_array($student->status, ['Mendaftar', 'Perlu Revisi'])) {
            return back()->with('error', 'Formulir terkunci. Status: ' . $student->status);
        }

        // --- 1. VALIDASI ---
        $rules = [
            'program_pelatihan_id' => 'required|exists:program_pelatihans,id',
            'nama_lengkap'         => 'required|string|max:255',
            'tempat_lahir'         => 'required|string',
            'tanggal_lahir'        => 'required|date',
            'jenis_kelamin'        => 'required|in:Laki-laki,Perempuan',
            'nomor_ktp'            => 'required|numeric|digits:16',
            'email'                => 'required|email',
            'no_hp_peserta'        => 'required|numeric',
            'kota_pembuatan'       => 'required|string',

            // Validasi Foto
            'foto'                 => $student->foto ? 'nullable|image|max:2048' : 'required|image|max:2048',

            // Validasi Tanda Tangan (Wajib jika belum punya)
            'signature_base64'     => $student->signature ? 'nullable|string' : 'required|string',

            // Validasi Dokumen
            'documents.*'          => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        $messages = [
            'required' => ':attribute wajib diisi.',
            'signature_base64.required' => 'Mohon bubuhkan tanda tangan Anda pada kotak yang tersedia.',
            'numeric'  => ':attribute harus berupa angka.',
            'digits'   => ':attribute harus 16 digit.',
            'email'    => 'Email tidak valid.',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);

        // Validasi Dokumen Wajib Custom
        $validator->after(function ($validator) use ($student, $request) {
            $requiredDocs = RefDocumentType::where('is_active', true)->where('is_required', true)->get();
            foreach ($requiredDocs as $doc) {
                $alreadyInDb = $student->documents()->where('document_type_id', $doc->id)->exists();
                $isUploadingNow = $request->hasFile("documents.{$doc->id}");
                if (!$alreadyInDb && !$isUploadingNow) {
                    $validator->errors()->add("documents.{$doc->id}", "Dokumen {$doc->nama} wajib diupload.");
                }
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Mohon lengkapi data yang berwarna merah.');
        }

        // --- 2. SIMPAN DATA ---
        DB::beginTransaction();
        try {
            // A. Data Diri
            $data = $request->only([
                'program_pelatihan_id', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir',
                'jenis_kelamin', 'tinggi_badan', 'berat_badan', 'golongan_darah',
                'agama', 'status_pernikahan', 'nomor_ktp', 'nomor_paspor', 'nomor_npwp',
                'email', 'no_hp_peserta', 'no_hp_ortu', 'alamat_domisili', 'alamat_ktp',
                'kota_ktp', 'provinsi_ktp', 'kota_pembuatan'
            ]);
            $data['pernah_bekerja'] = $request->has('pernah_bekerja');
            $student->fill($data);
            $student->save();

            // B. Pendidikan
            $student->educations()->delete();
            if ($request->has('pendidikan')) {
                foreach ($request->pendidikan as $edu) {
                    if (!empty($edu['nama_institusi'])) $student->educations()->create($edu);
                }
            }

            // C. Keluarga
            $student->families()->delete();
            if ($request->has('keluarga')) {
                foreach ($request->keluarga as $fam) {
                    if (!empty($fam['nama'])) $student->families()->create($fam);
                }
            }

            // D. Pengalaman
            $student->experiences()->delete();
            if ($data['pernah_bekerja'] && $request->has('pengalaman')) {
                foreach ($request->pengalaman as $exp) {
                    if (!empty($exp['nama_instansi'])) $student->experiences()->create($exp);
                }
            }

            // E. Foto
            if ($request->hasFile('foto')) {
                if ($student->foto && Storage::disk('public')->exists($student->foto)) {
                    Storage::disk('public')->delete($student->foto);
                }
                $student->foto = $request->file('foto')->store('foto_siswa', 'public');
                if($student->user) $student->user->update(['foto' => $student->foto]);
            }

            // F. TANDA TANGAN DIGITAL (BARU)
            if ($request->filled('signature_base64')) {
                // Hapus tanda tangan lama jika ada
                if ($student->signature && Storage::disk('public')->exists($student->signature)) {
                    Storage::disk('public')->delete($student->signature);
                }

                // Decode Base64
                $image_parts = explode(";base64,", $request->signature_base64);
                $image_base64 = base64_decode($image_parts[1]);

                // Simpan sebagai file PNG
                $fileName = 'signatures/sign_' . $student->id . '_' . time() . '.png';
                Storage::disk('public')->put($fileName, $image_base64);

                // Update DB
                $student->signature = $fileName;
            }
            $student->save();

            // G. Dokumen
            if ($request->has('documents')) {
                foreach ($request->file('documents') as $typeId => $file) {
                    $oldDoc = $student->documents()->where('document_type_id', $typeId)->first();
                    if ($oldDoc && Storage::disk('public')->exists($oldDoc->file_path)) {
                        Storage::disk('public')->delete($oldDoc->file_path);
                    }
                    $pathDoc = $file->store('dokumen_siswa', 'public');
                    StudentDocument::updateOrCreate(
                        ['student_id' => $student->id, 'document_type_id' => $typeId],
                        ['file_path' => $pathDoc]
                    );
                }
            }

            $student->update(['status' => 'Menunggu Verifikasi']);
            DB::commit();

            return back()->with('success', 'Formulir berhasil dikirim! Tanda tangan dan dokumen telah tersimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
