<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ProgramPelatihan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Hash; 
use App\Exports\StudentsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('NIK', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('status') && $request->status != 'Semua') {
            $query->where('status', $request->status);
        }

        $students = $query->paginate(10);

        // [BARU] Jika request AJAX (dari search/pagination), kembalikan tabelnya saja
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
            'nama' => 'required|string|max:255',
            'NIK' => 'nullable|string|unique:students,NIK',
            'program_pelatihan_id' => 'nullable|exists:program_pelatihans,id',
            // Email wajib unik di students DAN users
            'email' => 'required|email|unique:students,email|unique:users,email', 
            'telepon' => 'nullable|string|max:20',
            'status' => 'required',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ], [
            'email.unique' => 'Email ini sudah terdaftar (sebagai siswa atau user lain).'
        ]);

        DB::transaction(function () use ($request) {
            
            // 1. PROSES FOTO (JIKA ADA)
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('student_foto', 'public');
            }

            // 2. BUAT AKUN USER OTOMATIS
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make('12345678'), // Password Default
                'role' => 'siswa',
                'foto' => $fotoPath, // Sinkronisasi Foto ke User (Avatar)
            ]);

            // 3. BUAT DATA SISWA
            $data = $request->all();
            $data['user_id'] = $user->id; // Hubungkan relasi
            $data['foto'] = $fotoPath; // Simpan path foto ke data siswa (Arsip)

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
            'nama' => 'required|string|max:255',
            // Ignore ID sendiri saat cek unique
            'NIK' => 'nullable|string|unique:students,NIK,' . $student->id,
            'email' => 'required|email|unique:students,email,' . $student->id,
            'program_pelatihan_id' => 'nullable|exists:program_pelatihans,id',
            'foto' => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request, $student) {
            $data = $request->all();

            // Update Foto
            if ($request->hasFile('foto')) {
                // Hapus foto lama siswa dari storage
                if ($student->foto) Storage::disk('public')->delete($student->foto);
                
                // Upload baru
                $path = $request->file('foto')->store('student_foto', 'public');
                $data['foto'] = $path;

                // [SINKRONISASI FOTO KE USER]
                if ($student->user) {
                    // Hapus foto lama user jika ada dan berbeda
                    if ($student->user->foto && $student->user->foto != $student->foto) {
                         // Cek exists dulu biar aman
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
                    'name' => $request->nama,
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
            // Hapus Foto dari Storage
            if ($student->foto) Storage::disk('public')->delete($student->foto);
            
            // Hapus Akun User (Relasi on delete set null di database, jadi harus manual delete user kalau mau bersih)
            if ($student->user) {
                $student->user->delete();
            }

            // Hapus Data Siswa
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
        // 1. Cek apakah siswa sudah punya user_id
        if ($student->user_id) {
            return redirect()->back()->with('error', 'Siswa ini sudah memiliki akun login.');
        }

        // 2. Cek apakah email siswa kosong
        if (empty($student->email)) {
            return redirect()->back()->with('error', 'Email siswa kosong. Harap edit data siswa dan isi email terlebih dahulu.');
        }

        // 3. Cek apakah email sudah dipakai di tabel users oleh orang lain
        if (User::where('email', $student->email)->exists()) {
            return redirect()->back()->with('error', 'Email siswa ini (' . $student->email . ') sudah digunakan oleh user lain. Harap ganti email siswa.');
        }

        DB::transaction(function () use ($student) {
            // 4. Buat User Baru
            $user = User::create([
                'name' => $student->nama,
                'email' => $student->email,
                'password' => Hash::make('12345678'), // Default password
                'role' => 'siswa',
                'foto' => $student->foto, // Sinkronkan foto jika ada
            ]);

            // 5. Update Siswa
            $student->update(['user_id' => $user->id]);
        });

        return redirect()->back()->with('success', 'Akun login berhasil dibuat! Password default: 12345678');
    }

    // --- FITUR EXPORT EXCEL (Semua & Pilihan) ---
    public function exportExcel(Request $request)
    {
        $ids = $request->ids ? explode(',', $request->ids) : null;
        
        $query = Student::with('program');
        if ($ids) {
            $query->whereIn('id', $ids);
        }
        $students = $query->get();

        $filename = "data-siswa-lpk.csv";
        $handle = fopen('php://output', 'w');

        // Header HTTP untuk download file
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Tulis Header Kolom
        fputcsv($handle, ['Nama Lengkap', 'NIK', 'Program', 'Status', 'Email', 'Telepon', 'Alamat']);

        // Tulis Data
        foreach ($students as $student) {
            fputcsv($handle, [
                $student->nama,
                "'".$student->NIK, // Tanda kutip biar excel baca sbg teks
                $student->program->judul ?? '-',
                $student->status,
                $student->email,
                $student->telepon,
                $student->alamat
            ]);
        }

        fclose($handle);
        exit;
    }

    // --- FITUR EXPORT PDF (Semua & Pilihan) ---
    public function exportPdf(Request $request)
    {
        // Ambil IDs dari URL ?ids=1,2,3 (Jika dari Bulk Action)
        $ids = $request->ids ? explode(',', $request->ids) : null;
        
        $query = Student::with('program');

        // Jika ada IDs, filter. Jika TIDAK ADA (tombol "PDF Semua"), dia akan ambil semua.
        if ($ids) {
            $query->whereIn('id', $ids);
        }
        
        $students = $query->get();

        // ... (Load view dan download PDF)
        $pdf = Pdf::loadView('admin.students.pdf_view', compact('students'))
                  ->setPaper('a4', 'landscape');
        
        return $pdf->download('laporan-siswa-lpk.pdf');
    }

    // --- FITUR EXPORT PDF PERORANGAN (Biodata) ---
    public function exportPdfIndividual(Student $student)
    {
        // Load view khusus biodata perorangan
        $pdf = Pdf::loadView('admin.students.pdf_biodata', compact('student'));
        return $pdf->download('biodata-'.$student->nama.'.pdf');
    }
}