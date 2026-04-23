<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ProgramPelatihan;
use App\Models\User;
use App\Models\LpkProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;
use App\Models\RefDocumentType;

class StudentController extends Controller
{
    /**
     * Tampilkan daftar siswa.
     */
    public function index(Request $request)
    {
        $query = Student::with(['program', 'user'])->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // [UPDATED] Field 'nama_lengkap' dan 'nomor_ktp' sesuai migrasi
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_ktp', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != 'Semua') {
            $query->where('status', $request->status);
        }

        $students = $query->paginate(10);

        if ($request->ajax()) {
            return view('admin.students.partials.table', compact('students'))->render();
        }

        $programs = ProgramPelatihan::all();

        return view('admin.students.index', compact('students', 'programs'));
    }

    public function getNextNumber()
    {
        $now = now();
        $month = $now->format('m');
        $year = $now->format('Y');

        // Cari semua nomor peserta yang memiliki akhiran bulan & tahun ini
        // Format standar: HGS/{seq}/{month}/{year}
        // Kita cari yang mengandung /month/year di akhir
        $suffix = "/{$month}/{$year}";

        $existingSequences = Student::where('participant_number', 'like', "%{$suffix}")
                             ->pluck('participant_number')
                             ->map(function ($number) {
                                 // Parse: HGS/001/10/2025 -> ambil 001
                                 $parts = explode('/', $number);
                                 // Pastikan format minimal ada 4 bagian (HGS, Seq, Month, Year)
                                 // dan bagian ke-2 adalah angka
                                 if (count($parts) >= 4 && is_numeric($parts[1])) {
                                     return intval($parts[1]);
                                 }
                                 return 0;
                             })
                             ->filter(fn($seq) => $seq > 0)
                             ->toArray();
        
        // Algoritma Gap Filling (Cari angka terkecil yang kosong)
        $sequence = 1;
        while (in_array($sequence, $existingSequences)) {
            $sequence++;
        }
        
        return response()->json([
            'seq' => sprintf('%03d', $sequence),
            'month' => $month,
            'year' => $year
        ]);
    }

    /**
     * Simpan siswa baru & buat akun otomatis.
     */
    public function store(Request $request)
    {
        // MERGE INPUT: Gabungkan HGS/ + Seq + / + Month + / + Year
        if ($request->filled(['p_seq', 'p_month', 'p_year'])) {
            $fullNumber = sprintf('HGS/%s/%s/%s', $request->p_seq, $request->p_month, $request->p_year);
            $request->merge(['participant_number' => $fullNumber]);
        }

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nomor_ktp' => 'nullable|string|unique:students,nomor_ktp',
            'program_pelatihan_id' => 'nullable|exists:program_pelatihans,id',
            'email' => 'required|email|unique:students,email|unique:users,email',
            'no_hp_peserta' => 'nullable|string|max:20',
            'status' => 'required',
            // Validasi tetap ke participant_number (hasil merge)
            'participant_number' => ['nullable', 'string', 'unique:students,participant_number', 'regex:/^HGS/'],
            'alamat_domisili' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ], [
            'email.unique' => 'Email ini sudah terdaftar (sebagai siswa atau user lain).',
            'participant_number.regex' => 'Nomor Peserta wajib diawali dengan "HGS".',
            'participant_number.unique' => 'Nomor Peserta ini sudah terpakai.',
        ]);

        DB::transaction(function () use ($request) {

            // 1. PROSES FOTO (JIKA ADA)
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('foto_siswa', 'public');
            }

            // 2. BUAT AKUN USER OTOMATIS
            $user = User::create([
                'name' => $request->nama_lengkap, // Gunakan nama_lengkap
                'email' => $request->email,
                'password' => Hash::make('12345678'), // Password Default
                'role' => 'siswa',
                'foto' => $fotoPath,
            ]);

            // 3. BUAT DATA SISWA
            $data = $request->only([
                'nama_lengkap', 'nomor_ktp', 'program_pelatihan_id', 'email',
                'no_hp_peserta', 'status', 'alamat_domisili', 'participant_number'
            ]);

            $data['user_id'] = $user->id;
            $data['foto'] = $fotoPath;

            // Opsional: Isi alamat KTP dengan alamat domisili agar tidak null (untuk data awal)
            $data['alamat_ktp'] = $request->alamat_domisili;

            Student::create($data);
        });

        return redirect()->back()->with('success', 'Siswa berhasil ditambahkan & Akun Login dibuat (Pass: 12345678).');
    }

    /**
     * Ambil data siswa (JSON) untuk modal edit.
     */
    public function edit(Student $student)
    {
        $student->load(['program', 'educations', 'families', 'experiences', 'documents']);
        $programs = ProgramPelatihan::all();
        $documentTypes = RefDocumentType::where('is_active', true)->orderBy('id', 'asc')->get();
        $uploadedDocuments = $student->documents->pluck('file_path', 'document_type_id')->toArray();

        return view('admin.students.edit', compact('student', 'programs', 'documentTypes', 'uploadedDocuments'));
    }

    /**
     * Update data siswa & sinkronisasi user.
     */
    public function update(Request $request, Student $student)
    {
        // 1. QUICK UPDATE STATUS (Dari show.blade.php)
        if (!$request->has('is_full_edit')) {
            $request->validate([
                'status' => 'required|string',
            ]);

            $student->update(['status' => $request->status]);
            return redirect()->back()->with('success', 'Status siswa berhasil diperbarui.');
        }

        // 2. FULL EDIT (Dari edit.blade.php)
        // MERGE INPUT: Gabungkan HGS/ + Seq + / + Month + / + Year
        if ($request->filled(['p_seq', 'p_month', 'p_year'])) {
            $fullNumber = sprintf('HGS/%s/%s/%s', $request->p_seq, $request->p_month, $request->p_year);
            $request->merge(['participant_number' => $fullNumber]);
        }
        
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nomor_ktp' => 'nullable|string|unique:students,nomor_ktp,' . $student->id,
            'nomor_kk' => 'nullable|string',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'program_pelatihan_id' => 'nullable|exists:program_pelatihans,id',
            'participant_number' => ['nullable', 'string', 'regex:/^HGS/', 'unique:students,participant_number,' . $student->id],
            'foto' => 'nullable|image|max:2048',
            'signature_base64' => 'nullable|string',
            'documents.*' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'participant_number.regex' => 'Nomor Peserta wajib diawali dengan "HGS".',
            'participant_number.unique' => 'Nomor Peserta ini sudah terpakai.',
        ]);

        DB::transaction(function () use ($request, $student) {

            // A. Data Diri
            $data = $request->only([
                'nama_lengkap', 'nomor_ktp', 'nomor_kk', 'program_pelatihan_id', 'email',
                'no_hp_peserta', 'status', 'alamat_domisili', 'participant_number',
                'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'tinggi_badan', 
                'berat_badan', 'golongan_darah', 'agama', 'status_pernikahan', 
                'nomor_paspor', 'nomor_npwp', 'no_hp_ortu', 'alamat_ktp',
                'kota_ktp', 'provinsi_ktp', 'kota_pembuatan'
            ]);
            $data['pernah_bekerja'] = $request->has('pernah_bekerja');
            
            // B. Foto
            if ($request->hasFile('foto')) {
                if ($student->foto && Storage::disk('public')->exists($student->foto)) {
                    Storage::disk('public')->delete($student->foto);
                }
                $path = $request->file('foto')->store('foto_siswa', 'public');
                $data['foto'] = $path;

                // Sinkronisasi ke user
                if ($student->user) {
                    if ($student->user->foto && $student->user->foto != $student->foto && Storage::disk('public')->exists($student->user->foto)) {
                        Storage::disk('public')->delete($student->user->foto);
                    }
                    $student->user->update(['foto' => $path]);
                }
            }

            // C. Tanda Tangan
            if ($request->filled('signature_base64')) {
                if ($student->signature && Storage::disk('public')->exists($student->signature)) {
                    Storage::disk('public')->delete($student->signature);
                }
                $image_parts = explode(";base64,", $request->signature_base64);
                if (count($image_parts) == 2) {
                    $image_base64 = base64_decode($image_parts[1]);
                    $fileName = 'signatures/sign_' . $student->id . '_' . time() . '.png';
                    Storage::disk('public')->put($fileName, $image_base64);
                    $data['signature'] = $fileName;
                }
            }

            $student->update($data);

            // Sinkronisasi user
            if ($student->user) {
                $student->user->update([
                    'name' => $request->nama_lengkap,
                    'email' => $request->email,
                ]);
            }

            // D. Pendidikan
            $student->educations()->delete();
            if ($request->has('pendidikan')) {
                foreach ($request->pendidikan as $edu) {
                    if (!empty($edu['nama_institusi'])) $student->educations()->create($edu);
                }
            }

            // E. Keluarga
            $student->families()->delete();
            if ($request->has('keluarga')) {
                foreach ($request->keluarga as $fam) {
                    if (!empty($fam['nama'])) $student->families()->create($fam);
                }
            }

            // F. Pengalaman
            $student->experiences()->delete();
            if ($data['pernah_bekerja'] && $request->has('pengalaman')) {
                foreach ($request->pengalaman as $exp) {
                    if (!empty($exp['nama_instansi'])) $student->experiences()->create($exp);
                }
            }

            // G. Dokumen
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $typeId => $file) {
                    $oldDoc = $student->documents()->where('document_type_id', $typeId)->first();
                    if ($oldDoc && Storage::disk('public')->exists($oldDoc->file_path)) {
                        Storage::disk('public')->delete($oldDoc->file_path);
                    }
                    $pathDoc = $file->store('dokumen_siswa', 'public');
                    \App\Models\StudentDocument::updateOrCreate(
                        ['student_id' => $student->id, 'document_type_id' => $typeId],
                        ['file_path' => $pathDoc]
                    );
                }
            }
        });

        return redirect()->route('admin.students.show', $student)->with('success', 'Data siswa berhasil diperbarui secara penuh.');
    }

    /**
     * Hapus siswa & usernya.
     */
    public function destroy(Student $student)
    {
        DB::transaction(function () use ($student) {
            // Hapus Foto
            if ($student->foto) Storage::disk('public')->delete($student->foto);

            // Hapus Akun User
            if ($student->user) {
                $student->user->delete();
            }

            // Hapus Data Siswa (Cascade delete relation educations/families akan otomatis jalan jika DB di-set cascade)
            $student->delete();
        });

        return redirect()->back()->with('success', 'Data siswa & akun login dihapus.');
    }

    public function show(Student $student)
    {
        // 1. Load relasi documents
        $student->load(['program', 'educations', 'families', 'experiences', 'documents.type']);

        // 2. Ambil Master Data Dokumen (Gunakan 'id' karena kolom 'urutan' belum ada)
        $documentTypes = \App\Models\RefDocumentType::where('is_active', true)->orderBy('id', 'asc')->get();

        return view('admin.students.show', compact('student', 'documentTypes'));
    }

    /**
     * Buat akun user baru untuk siswa yang belum punya akun.
     */
    public function generateAccount(Student $student)
{
    // 1. CEK DATA YATIM (Broken Reference)
    // Jika user_id terisi, TAPI relasi user-nya null (tidak ditemukan di tabel users)
    if ($student->user_id && !$student->user) {
        // Bersihkan user_id agar bisa dibuatkan akun baru
        $student->update(['user_id' => null]);

        // Refresh model student agar user_id di memory jadi null
        $student->refresh();
    }

    // 2. CEK NORMAL (Jika user_id valid dan user-nya memang ada)
    if ($student->user_id) {
        return redirect()->back()->with('error', 'Siswa ini sudah memiliki akun login yang aktif.');
    }

    // --- Validasi Email (Tetap sama) ---
    if (empty($student->email)) {
        return redirect()->back()->with('error', 'Email siswa kosong. Harap isi email terlebih dahulu.');
    }

    // Cek apakah email sudah dipakai user lain (selain 'user hantu' yg mungkin sudah dihapus)
    if (User::where('email', $student->email)->exists()) {
        return redirect()->back()->with('error', 'Email ini sudah terdaftar di sistem sebagai user lain.');
    }

    // --- Proses Pembuatan Akun (Tetap sama) ---
    DB::transaction(function () use ($student) {
        $user = User::create([
            'name' => $student->nama_lengkap,
            'email' => $student->email,
            'password' => Hash::make('12345678'),
            'role' => 'siswa',
            'foto' => $student->foto,
        ]);

        $student->update(['user_id' => $user->id]);
    });

    return redirect()->back()->with('success', 'Akun login berhasil dibuat! Password default: 12345678');
}

    // --- FITUR EXPORT EXCEL ---
    public function exportExcel(Request $request)
    {
        // 1. Tangkap ID jika ada (dari checklist)
        $ids = $request->ids ? explode(',', $request->ids) : null;

        // 2. Tentukan nama file
        $filename = 'laporan-siswa-lpk-' . date('d-m-Y-H-i') . '.xlsx';

        // 3. Download file Excel menggunakan class StudentsExport
        return Excel::download(new StudentsExport($ids), $filename);
    }

    // --- FITUR EXPORT PDF (List Laporan) ---
    public function exportPdf(Request $request)
    {
        $ids = $request->ids ? explode(',', $request->ids) : null;

        $query = Student::with('program');
        if ($ids) {
            $query->whereIn('id', $ids);
        }
        $students = $query->get();

        $profile = \App\Models\LpkProfile::first();

    // Kirim variable 'profile' ke view
    $pdf = Pdf::loadView('admin.students.pdf_view', compact('students', 'profile'))
              ->setPaper('a4', 'landscape'); // Landscape biar muat banyak kolom

    return $pdf->download('laporan-siswa-lpk.pdf');
    }

    // --- FITUR EXPORT PDF PERORANGAN (Biodata) ---
    public function exportPdfIndividual(Student $student)
    {
        // 1. Load relasi siswa
        $student->load(['program', 'educations', 'families', 'experiences']);

        // 2. Ambil Data Profil LPK (Ambil data pertama)
        $profile = LpkProfile::first();

        // 3. Kirim kedua variabel ($student dan $profile) ke View
        $pdf = Pdf::loadView('admin.students.pdf_biodata', compact('student', 'profile'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('biodata-siswa-'. \Str::slug($student->nama_lengkap) .'.pdf');
    }

    // --- LOGIKA VERIFIKASI ---

    public function verification(Student $student)
    {
        // 1. Load relasi 'documents' dan 'documents.type' (untuk ambil nama file)
        $student->load(['program', 'educations', 'families', 'experiences', 'documents.type']);

        // 2. Ambil Master Data Dokumen untuk acuan pengecekan (Looping label)
$documentTypes = RefDocumentType::where('is_active', true)->orderBy('id', 'asc')->get();
        return view('admin.students.verify', compact('student', 'documentTypes'));
    }

    public function processVerification(Request $request, Student $student)
    {
        $request->validate([
            'action' => 'required|in:terima,revisi,tolak',
            'admin_note' => 'nullable|string',
        ]);

        if (in_array($request->action, ['revisi', 'tolak']) && empty($request->admin_note)) {
            return back()->withErrors(['admin_note' => 'Wajib memberikan catatan alasan untuk Revisi atau Penolakan.']);
        }

        DB::transaction(function () use ($request, $student) {
            if ($request->action == 'terima') {
                $student->update([
                    'status' => 'Wawancara',
                    'verified_at' => now(),
                    'admin_note' => null,
                ]);
            }
            elseif ($request->action == 'revisi') {
                $student->update([
                    'status' => 'Perlu Revisi',
                    'admin_note' => $request->admin_note,
                ]);
            }
            elseif ($request->action == 'tolak') {
                $student->update([
                    'status' => 'Ditolak',
                    'verified_at' => now(),
                    'admin_note' => $request->admin_note,
                ]);
            }
        });

        return redirect()->route('admin.students.index')->with('success', 'Verifikasi siswa ' . $student->nama_lengkap . ' berhasil diproses.');
    }

    // Tambahkan import Carbon untuk format tanggal bahasa Indonesia


// ... code sebelumnya ...

    /**
     * Export Surat Perjanjian (PDF)
     */
    public function exportAgreement(Student $student)
    {
        // 1. Ambil Profil LPK (Pihak Pertama)
        $profile = LpkProfile::first();

        // 2. Format Tanggal Surat (Contoh: Cianjur, 03 Desember 2025)
        // Pastikan setting locale ID di AppServiceProvider atau .env sudah 'id'
        Carbon::setLocale('id');
        $tanggalSurat = Carbon::now()->translatedFormat('d F Y');

        // 3. Load View PDF
        $pdf = Pdf::loadView('admin.students.pdf_agreement', compact('student', 'profile', 'tanggalSurat'))
                  ->setPaper('a4', 'portrait');

        // 4. Download atau Stream (Preview)
        // Gunakan stream() agar admin bisa baca dulu sebelum download
        return $pdf->stream('Surat_Perjanjian_' . str_replace(' ', '_', $student->nama_lengkap) . '.pdf');
    }

    public function exportIdCard(Request $request)
    {
        $query = Student::with(['program', 'user']);
        $profile = LpkProfile::first(); // Untuk Kop/Logo di Kartu

        // LOGIKA FILTER DATA
        if ($request->has('ids')) {
            // Opsi 1 & 3: Perorangan atau Pilihan Checkbox
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        }
        elseif ($request->query('mode') == 'all') {
            // Opsi 2: Cetak Semua (Sesuai Filter Index)
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nomor_ktp', 'like', "%{$search}%");
                });
            }
            if ($request->has('status') && $request->status != 'Semua') {
                $query->where('status', $request->status);
            }
        } else {
            // Default fallback jika akses langsung tanpa param
            abort(404);
        }

        $students = $query->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'Tidak ada data siswa untuk dicetak.');
        }

        // Load View PDF Kartu
        $pdf = Pdf::loadView('admin.students.pdf_id_card', compact('students', 'profile'))
                  ->setPaper('a4', 'portrait'); // A4 Portrait, nanti diatur CSS gridnya

        return $pdf->stream('Kartu_Siswa_LPK.pdf');
    }
}
