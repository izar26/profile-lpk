<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Exports\EmployeesExport; // Export Excel
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf; // Export PDF

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('user')->latest();

        // 1. Logika Search (Tetap)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        // 2. Logika Filter Jabatan (Tetap)
        if ($request->has('jabatan') && $request->jabatan != 'Semua') {
            $query->where('jabatan', $request->jabatan);
        }

        $employees = $query->paginate(10);

        // [BARU] Ambil daftar jabatan unik dari database untuk Dropdown
        // distinct() artinya ambil data unik (tidak kembar)
        // pluck('jabatan') artinya hanya ambil kolom jabatannya saja
        $listJabatan = Employee::whereNotNull('jabatan')
                        ->distinct()
                        ->orderBy('jabatan', 'asc')
                        ->pluck('jabatan');

        // AJAX Response (Tetap)
        if ($request->ajax()) {
            return view('admin.employees.partials.table', compact('employees'))->render();
        }

        // [UPDATE] Kirim variabel $listJabatan ke view
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

            // 1. Buat User (Role Pegawai)
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

    // Generate Akun untuk Pegawai Lama
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

    // Export Excel
    public function exportExcel(Request $request)
    {
        $ids = $request->ids ? explode(',', $request->ids) : null;
        
        $query = Employee::latest();
        if ($ids) {
            $query->whereIn('id', $ids);
        }
        $employees = $query->get();

        $filename = "data-pegawai-lpk.csv";
        $handle = fopen('php://output', 'w');

        // Header Download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Header Kolom CSV
        fputcsv($handle, [
            'Nama Lengkap', 'NIP', 'Jabatan', 'Status Kepegawaian', 
            'Email', 'Telepon', 'Alamat', 'Kota', 'Provinsi'
        ]);

        // Isi Data
        foreach ($employees as $emp) {
            fputcsv($handle, [
                $emp->nama,
                "'".$emp->nip, // Kasih kutip biar tidak jadi scientific number di excel
                $emp->jabatan,
                $emp->status_kepegawaian,
                $emp->email,
                $emp->telepon,
                $emp->alamat,
                $emp->kota,
                $emp->provinsi
            ]);
        }

        fclose($handle);
        exit;
    }

    // Export PDF
    public function exportPdf(Request $request)
    {
        $ids = $request->ids ? explode(',', $request->ids) : null;
        $query = Employee::query();
        if ($ids) $query->whereIn('id', $ids);
        $employees = $query->get();

        $pdf = Pdf::loadView('admin.employees.pdf_view', compact('employees'))->setPaper('a4', 'landscape');
        return $pdf->download('laporan-pegawai.pdf');
    }

    public function exportPdfIndividual(Employee $employee)
    {
        $pdf = Pdf::loadView('admin.employees.pdf_biodata', compact('employee'));
        return $pdf->download('biodata-'.$employee->nama.'.pdf');
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