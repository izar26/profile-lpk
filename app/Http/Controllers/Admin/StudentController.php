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

    /**
     * Simpan siswa baru & buat akun otomatis.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nomor_ktp' => 'nullable|string|unique:students,nomor_ktp',
            'program_pelatihan_id' => 'nullable|exists:program_pelatihans,id',
            'email' => 'required|email|unique:students,email|unique:users,email', 
            'no_hp_peserta' => 'nullable|string|max:20', 
            'status' => 'required',
            'alamat_domisili' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ], [
            'email.unique' => 'Email ini sudah terdaftar (sebagai siswa atau user lain).'
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
                'no_hp_peserta', 'status', 'alamat_domisili'
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
        return response()->json($student);
    }

    /**
     * Update data siswa & sinkronisasi user.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nomor_ktp' => 'nullable|string|unique:students,nomor_ktp,' . $student->id,
            'email' => 'required|email|unique:students,email,' . $student->id,
            'program_pelatihan_id' => 'nullable|exists:program_pelatihans,id',
            'foto' => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request, $student) {
            
            // Ambil field yang diizinkan update dari Modal Admin (Quick Edit)
            $data = $request->only([
                'nama_lengkap', 'nomor_ktp', 'program_pelatihan_id', 'email', 
                'no_hp_peserta', 'status', 'alamat_domisili'
            ]);

            // Update Foto
            if ($request->hasFile('foto')) {
                if ($student->foto) Storage::disk('public')->delete($student->foto);
                
                $path = $request->file('foto')->store('foto_siswa', 'public');
                $data['foto'] = $path;

                // [SINKRONISASI FOTO KE USER]
                if ($student->user) {
                    if ($student->user->foto && $student->user->foto != $student->foto) {
                         if(Storage::disk('public')->exists($student->user->foto)) {
                            Storage::disk('public')->delete($student->user->foto);
                         }
                    }
                    $student->user->update(['foto' => $path]);
                }
            }

            $student->update($data);

            // [SINKRONISASI NAMA & EMAIL KE USER]
            if ($student->user) {
                $student->user->update([
                    'name' => $request->nama_lengkap, // Sync nama_lengkap
                    'email' => $request->email,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Data siswa & akun berhasil diperbarui.');
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
        return view('admin.students.show', compact('student'));
    }

    /**
     * Buat akun user baru untuk siswa yang belum punya akun.
     */
    public function generateAccount(Student $student)
    {
        if ($student->user_id) {
            return redirect()->back()->with('error', 'Siswa ini sudah memiliki akun login.');
        }

        if (empty($student->email)) {
            return redirect()->back()->with('error', 'Email siswa kosong. Harap isi email terlebih dahulu.');
        }

        if (User::where('email', $student->email)->exists()) {
            return redirect()->back()->with('error', 'Email ini sudah digunakan user lain.');
        }

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

        $pdf = Pdf::loadView('admin.students.pdf_view', compact('students'))
                  ->setPaper('a4', 'landscape');
        
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
        
        return $pdf->download('Bukti-Seleksi-'. \Str::slug($student->nama_lengkap) .'.pdf');
    }

    // --- LOGIKA VERIFIKASI ---

    public function verification(Student $student)
    {
        $student->load(['program', 'educations', 'families', 'experiences']);
        return view('admin.students.verify', compact('student'));
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