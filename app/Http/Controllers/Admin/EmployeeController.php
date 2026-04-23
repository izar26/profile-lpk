<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\LpkProfile;
use App\Models\EmployeeEducation;
use App\Models\EmployeeFamily;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesExport;

class EmployeeController extends Controller
{
    /**
     * Menampilkan daftar pegawai.
     */
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

        // Ambil daftar jabatan unik untuk Dropdown Filter
        $listJabatan = Employee::whereNotNull('jabatan')
                                ->distinct()
                                ->orderBy('jabatan', 'asc')
                                ->pluck('jabatan');

        if ($request->ajax()) {
            return view('admin.employees.partials.table', compact('employees'))->render();
        }

        return view('admin.employees.index', compact('employees', 'listJabatan'));
    }

    /**
     * Menyimpan data pegawai baru (Data Dasar Saja).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|unique:employees,nip',
            'nomor_ktp' => 'nullable|string|max:16|unique:employees,nomor_ktp',
            'jabatan' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email|unique:users,email',
            'foto' => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('employee_foto', 'public');
            }

            // 1. Buat User Login
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make('12345678'),
                'role' => 'pegawai',
                'foto' => $fotoPath,
            ]);

            // 2. Buat Data Employee
            $data = $request->except(['foto', '_token', '_method']);
            $data['user_id'] = $user->id;
            $data['foto'] = $fotoPath;

            Employee::create($data);
        });

        return redirect()->back()->with('success', 'Pegawai ditambahkan & Akun dibuat (Pass: 12345678).');
    }

    /**
     * Update data pegawai (Hanya Data Dasar).
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'nama'              => 'required|string|max:255',
            'nip'               => 'nullable|string|unique:employees,nip,' . $employee->id,
            'jabatan'           => 'required|string|max:100',
            'nomor_ktp'         => 'nullable|string|max:16|unique:employees,nomor_ktp,' . $employee->id,
            'nomor_kk'          => 'nullable|string|max:16',
            'nomor_npwp'        => 'nullable|string|max:20',
            'tempat_lahir'      => 'nullable|string|max:100',
            'tanggal_lahir'     => 'nullable|date',
            'jenis_kelamin'     => 'nullable|in:L,P',
            'golongan_darah'    => 'nullable|string|max:5',
            'agama'             => 'nullable|string|max:50',
            'status_pernikahan' => 'nullable|string|max:50',
            'tinggi_badan'      => 'nullable|numeric',
            'berat_badan'       => 'nullable|numeric',

            // Alamat & Kontak
            'alamat_ktp'        => 'nullable|string',
            'kota_ktp'          => 'nullable|string|max:100',
            'provinsi_ktp'      => 'nullable|string|max:100',
            'alamat_domisili'   => 'nullable|string',
            'telepon'           => 'nullable|string|max:20',
            'no_hp_keluarga_darurat' => 'nullable|string|max:20',
            'email'             => 'required|email|unique:employees,email,' . $employee->id,
            'instagram'         => 'nullable|string|max:255',

            // Foto
            'foto'              => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request, $employee) {
            $data = $request->except(['foto', '_token', '_method']);

            if ($request->hasFile('foto')) {
                if ($employee->foto) Storage::disk('public')->delete($employee->foto);
                $data['foto'] = $request->file('foto')->store('employee_foto', 'public');

                if ($employee->user) {
                    $employee->user->update(['foto' => $data['foto']]);
                }
            }

            $employee->update($data);

            if ($employee->user) {
                $employee->user->update([
                    'name' => $request->nama,
                    'email' => $request->email
                ]);
            }
        });

        return redirect()->back()->with('success', 'Data pegawai diperbarui.');
    }

    /**
     * Hapus pegawai.
     */
    public function destroy(Employee $employee)
    {
        DB::transaction(function () use ($employee) {
            if ($employee->foto) Storage::disk('public')->delete($employee->foto);

            // Hapus file fisik dokumen (database row terhapus otomatis via cascade)
            foreach ($employee->documents as $doc) {
                if (Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }
            }

            if ($employee->user) $employee->user->delete();
            $employee->delete();
        });
        return redirect()->back()->with('success', 'Pegawai dihapus.');
    }

    /**
     * Menampilkan Detail Pegawai.
     * Admin hanya MELIHAT dokumen yang diupload pegawai.
     */
    public function show(Employee $employee)
    {
        // Tetap load 'documents' agar Admin bisa lihat list dokumennya
        $employee->load(['user', 'educations', 'families', 'documents']);

        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Tampilkan form edit pegawai.
     */
    public function edit(Employee $employee)
    {
        $employee->load(['educations', 'families', 'documents']);
        return view('admin.employees.edit', compact('employee'));
    }

    // ==================== RELATIONS CRUD ====================

    public function storeEducation(Request $request, Employee $employee)
    {
        $request->validate([
            'jenjang' => 'required|string',
            'nama_sekolah' => 'required|string',
            'tahun_lulus' => 'required|numeric',
        ]);

        EmployeeEducation::create([
            'employee_id' => $employee->id,
            'jenjang' => $request->jenjang,
            'nama_sekolah' => $request->nama_sekolah,
            'jurusan' => $request->jurusan,
            'tahun_masuk' => $request->tahun_masuk,
            'tahun_lulus' => $request->tahun_lulus,
            'nilai_akhir' => $request->nilai_akhir,
        ]);

        return back()->with('success', 'Riwayat pendidikan ditambahkan.');
    }

    public function destroyEducation(Employee $employee, $id)
    {
        $edu = EmployeeEducation::where('id', $id)->where('employee_id', $employee->id)->firstOrFail();
        $edu->delete();
        return back()->with('success', 'Data pendidikan dihapus.');
    }

    public function storeFamily(Request $request, Employee $employee)
    {
        $request->validate([
            'nama_lengkap' => 'required|string',
            'hubungan' => 'required|string',
        ]);

        EmployeeFamily::create([
            'employee_id' => $employee->id,
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

    public function updateFamily(Request $request, Employee $employee, $id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string',
            'hubungan' => 'required|string',
        ]);

        $family = EmployeeFamily::where('id', $id)
                    ->where('employee_id', $employee->id)
                    ->firstOrFail();

        $family->update([
            'nama_lengkap' => $request->nama_lengkap,
            'hubungan' => $request->hubungan,
            'nik' => $request->nik,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'pekerjaan' => $request->pekerjaan,
            'no_hp' => $request->no_hp,
        ]);

        return back()->with('success', 'Data keluarga diperbarui.');
    }

    public function destroyFamily(Employee $employee, $id)
    {
        $fam = EmployeeFamily::where('id', $id)->where('employee_id', $employee->id)->firstOrFail();
        $fam->delete();
        return back()->with('success', 'Data keluarga dihapus.');
    }

    public function storeDocument(Request $request, Employee $employee)
    {
        $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'file_dokumen' => 'required|file|max:5120',
        ]);

        DB::transaction(function () use ($request, $employee) {
            $path = $request->file('file_dokumen')->store('employee_documents', 'public');

            EmployeeDocument::create([
                'employee_id' => $employee->id,
                'nama_dokumen' => $request->nama_dokumen,
                'file_path' => $path,
            ]);
        });

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function destroyDocument(Employee $employee, $id)
    {
        $doc = EmployeeDocument::where('id', $id)->where('employee_id', $employee->id)->firstOrFail();

        DB::transaction(function () use ($doc) {
            if (Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
            $doc->delete();
        });

        return back()->with('success', 'Dokumen dihapus.');
    }

    /**
     * Generate Akun Login Manual.
     */
    public function generateAccount(Employee $employee)
    {
        if ($employee->user_id && !$employee->user) {
            $employee->update(['user_id' => null]);
            $employee->refresh();
        }

        if ($employee->user_id) return back()->with('error', 'Pegawai ini sudah memiliki akun login.');
        if (User::where('email', $employee->email)->exists()) return back()->with('error', 'Email ini sudah digunakan user lain.');

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

        return back()->with('success', 'Akun pegawai berhasil dibuat (Pass: 12345678).');
    }

    // ==================== FITUR EXPORT & CETAK ====================

    public function exportExcel(Request $request)
    {
        $ids = $request->ids ? explode(',', $request->ids) : null;
        $filename = 'laporan-pegawai-lpk-' . date('d-m-Y-H-i') . '.xlsx';
        return Excel::download(new EmployeesExport($ids), $filename);
    }

    public function exportPdf(Request $request)
    {
        $ids = $request->ids ? explode(',', $request->ids) : null;
        $query = Employee::query();
        if ($ids) $query->whereIn('id', $ids);
        $employees = $query->get();

        $profile = LpkProfile::first();

        $pdf = Pdf::loadView('admin.employees.pdf_view', compact('employees', 'profile'))
                  ->setPaper('a4', 'landscape'); // A4 Landscape untuk tabel lebar

        return $pdf->download('laporan-pegawai.pdf');
    }

    public function exportPdfIndividual(Employee $employee)
    {
        // Load semua relasi agar tercetak lengkap di biodata
        $employee->load(['educations', 'families', 'documents']);
        $profile = LpkProfile::first();

        $pdf = Pdf::loadView('admin.employees.pdf_biodata', compact('employee', 'profile'))
                  ->setPaper('a4', 'portrait'); // PASTIKAN A4 Portrait

        return $pdf->download('biodata-'.$employee->nama.'.pdf');
    }

    public function exportIdCard(Request $request)
    {
        $query = Employee::query();
        $profile = LpkProfile::first();

        if ($request->has('ids')) {
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        }
        elseif ($request->query('mode') == 'all') {
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

        $pdf = Pdf::loadView('admin.employees.pdf_id_card', compact('employees', 'profile'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Kartu_Pegawai_LPK.pdf');
    }

    // ==================== HALAMAN VERIFIKASI PUBLIK ====================
    // Ini tetap ada karena untuk akses publik (scan QR), bukan Admin.

    public function verification(Employee $employee)
    {
        $profile = LpkProfile::first();
        return view('admin.employees.verify_public', compact('employee', 'profile'));
    }

    public function verificationCheck(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'verifikasi_key' => 'required',
        ]);

        $employee = Employee::findOrFail($request->employee_id);

        $dobKey = $employee->tanggal_lahir ? $employee->tanggal_lahir->format('dmY') : null;

        if ($dobKey && $request->verifikasi_key === $dobKey) {
            $profile = LpkProfile::first();
            return view('admin.employees.verified_success', compact('employee', 'profile'));
        }

        return back()->with('error', 'Kode akses salah. Gunakan Tanggal Lahir (DDMMYYYY).');
    }
}