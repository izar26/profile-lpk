<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\LpkProfile; // Pastikan Model ini ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesExport;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('user')->latest();

        // 1. Logika Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        // 2. Logika Filter Jabatan
        if ($request->has('jabatan') && $request->jabatan != 'Semua') {
            $query->where('jabatan', $request->jabatan);
        }

        $employees = $query->paginate(10);

        // Ambil daftar jabatan unik untuk Dropdown
        $listJabatan = Employee::whereNotNull('jabatan')
                                ->distinct()
                                ->orderBy('jabatan', 'asc')
                                ->pluck('jabatan');

        if ($request->ajax()) {
            return view('admin.employees.partials.table', compact('employees'))->render();
        }

        return view('admin.employees.index', compact('employees', 'listJabatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|unique:employees,nip',
            'jabatan' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email|unique:users,email',
            'foto' => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('employee_foto', 'public');
            }

            // 1. Buat User
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make('12345678'),
                'role' => 'pegawai',
                'foto' => $fotoPath,
            ]);

            // 2. Buat Employee
            $data = $request->all();
            $data['user_id'] = $user->id;
            $data['foto'] = $fotoPath;
            Employee::create($data);
        });

        return redirect()->back()->with('success', 'Pegawai ditambahkan & Akun dibuat.');
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|unique:employees,nip,' . $employee->id,
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'foto' => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request, $employee) {
            $data = $request->all();
            if ($request->hasFile('foto')) {
                if ($employee->foto) Storage::disk('public')->delete($employee->foto);
                $data['foto'] = $request->file('foto')->store('employee_foto', 'public');
                if ($employee->user) $employee->user->update(['foto' => $data['foto']]);
            }
            $employee->update($data);
            if ($employee->user) {
                $employee->user->update(['name' => $request->nama, 'email' => $request->email]);
            }
        });

        return redirect()->back()->with('success', 'Data pegawai diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        DB::transaction(function () use ($employee) {
            if ($employee->foto) Storage::disk('public')->delete($employee->foto);
            if ($employee->user) $employee->user->delete();
            $employee->delete();
        });
        return redirect()->back()->with('success', 'Pegawai dihapus.');
    }

    public function generateAccount(Employee $employee)
    {
        if ($employee->user_id) return back()->with('error', 'Sudah punya akun.');
        if (User::where('email', $employee->email)->exists()) return back()->with('error', 'Email sudah dipakai user lain.');

        DB::transaction(function () use ($employee) {
            $user = User::create([
                'name' => $employee->nama,
                'email' => $employee->email,
                'password' => Hash::make('12345678'),
                'role' => 'pegawai',
                'foto' => $employee->foto,
            ]);
            $employee->update(['user_id' => $user->id]);
        });

        return back()->with('success', 'Akun pegawai berhasil dibuat.');
    }

    // --- FITUR EXPORT EXCEL ---
    public function exportExcel(Request $request)
    {
        // 1. Tangkap ID jika ada (dari checklist pilihan)
        $ids = $request->ids ? explode(',', $request->ids) : null;
        
        // 2. Buat nama file dengan timestamp agar unik
        $filename = 'laporan-pegawai-lpk-' . date('d-m-Y-H-i') . '.xlsx';

        // 3. Download file Excel menggunakan class EmployeesExport
        return Excel::download(new EmployeesExport($ids), $filename);
    }

    // --- FITUR EXPORT PDF LAPORAN ---
    public function exportPdf(Request $request)
    {
        $ids = $request->ids ? explode(',', $request->ids) : null;
        $query = Employee::query();
        if ($ids) $query->whereIn('id', $ids);
        $employees = $query->get();

        $pdf = Pdf::loadView('admin.employees.pdf_view', compact('employees'))->setPaper('a4', 'landscape');
        return $pdf->download('laporan-pegawai.pdf');
    }

    // --- FITUR EXPORT BIODATA PERORANGAN ---
    public function exportPdfIndividual(Employee $employee)
    {
        // Ambil profil LPK untuk Header Kop Surat
        $profile = LpkProfile::first();
        
        $pdf = Pdf::loadView('admin.employees.pdf_biodata', compact('employee', 'profile'));
        return $pdf->download('biodata-'.$employee->nama.'.pdf');
    }

    // --- [BARU] FITUR CETAK ID CARD PEGAWAI ---
    public function exportIdCard(Request $request)
    {
        $query = Employee::query();
        $profile = LpkProfile::first(); // Mengambil Logo & Nama LPK

        // LOGIKA FILTER CETAK (Sama dengan Siswa)
        if ($request->has('ids')) {
            // Opsi 1: Cetak Pilihan Checkbox
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        } 
        elseif ($request->query('mode') == 'all') {
            // Opsi 2: Cetak Semua (Sesuai Filter Tabel)
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%")
                      ->orWhere('jabatan', 'like', "%{$search}%");
                });
            }
            if ($request->has('jabatan') && $request->jabatan != 'Semua') {
                $query->where('jabatan', $request->jabatan);
            }
        } else {
            abort(404);
        }

        $employees = $query->get();

        if ($employees->isEmpty()) {
            return back()->with('error', 'Tidak ada data pegawai untuk dicetak.');
        }

        // Load View PDF ID Card
        // Menggunakan kertas A4 Portrait agar muat banyak kartu
        $pdf = Pdf::loadView('admin.employees.pdf_id_card', compact('employees', 'profile'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Kartu_Pegawai_LPK.pdf');
    }

    // --- [BARU] PERSIAPAN HALAMAN VERIFIKASI PUBLIK ---
    // Method ini menampilkan halaman input kode (Tgl Lahir) saat QR Code discan
    public function verification(Employee $employee)
    {
        // Kita gunakan view yang mirip dengan siswa nanti
        // Pastikan Anda sudah membuat view-nya di langkah selanjutnya
        $profile = LpkProfile::first();
        return view('admin.employees.verify_public', compact('employee', 'profile'));
    }
    
    // Method untuk mengecek inputan kode verifikasi
    public function verificationCheck(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'verifikasi_key' => 'required', // Format DDMMYYYY
        ]);

        $employee = Employee::findOrFail($request->employee_id);

        // Ubah tanggal lahir pegawai menjadi string DDMMYYYY
        // Contoh: 1990-12-25 menjadi 25121990
        $dobKey = $employee->tanggal_lahir ? $employee->tanggal_lahir->format('dmY') : null;

        if ($dobKey && $request->verifikasi_key === $dobKey) {
            // Jika Cocok, Tampilkan Halaman "Verified"
            $profile = LpkProfile::first();
            return view('admin.employees.verified_success', compact('employee', 'profile'));
        }

        return back()->with('error', 'Kode akses salah. Gunakan Tanggal Lahir (DDMMYYYY).');
    }

    public function show(Employee $employee)
    {
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return response()->json($employee);
    }
}