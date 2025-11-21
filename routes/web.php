<?php
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LpkProfileController;
use App\Http\Controllers\Admin\AlbumController;
use App\Http\Controllers\Admin\AlbumMediaController;
use App\Http\Controllers\Admin\ProgramPelatihanController;
use App\Http\Controllers\Admin\EdukasiController;
use App\Http\Controllers\Admin\CaraDaftarController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\KeberangkatanController;
use App\Http\Controllers\Admin\AlumniController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Siswa\SiswaAreaController;
use App\Http\Controllers\Pegawai\PegawaiAreaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rute Publik (Landing Page, Profil LPK)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rute yang HANYA bisa diakses setelah login (auth)
// Rute 'dashboard' ini akan mengarahkan user berdasarkan role mereka
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isPegawai()) {
        return redirect()->route('pegawai.dashboard');
    } else {
        return redirect()->route('siswa.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');


// Grup Rute untuk ADMIN
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Panggil Controller yang baru kita buat
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); // <-- INI YANG BARU

    Route::resource('users', UserController::class);

    Route::get('/lpk-profile', [LpkProfileController::class, 'edit'])->name('lpk-profile.edit');
    Route::post('/lpk-profile', [LpkProfileController::class, 'update'])->name('lpk-profile.update');
   
    Route::get('/albums', [AlbumController::class, 'index'])->name('albums.index');
Route::post('/albums', [AlbumController::class, 'store'])->name('albums.store');
Route::get('/albums/{album}/edit', [AlbumController::class, 'edit'])->name('albums.edit');
Route::put('/albums/{album}', [AlbumController::class, 'update'])->name('albums.update');
Route::delete('/albums/{album}', [AlbumController::class, 'destroy'])->name('albums.destroy');

Route::get('/albums/{album}/media', [AlbumMediaController::class, 'index'])->name('albums.media.index');
Route::post('/albums/{album}/media', [AlbumMediaController::class, 'store'])->name('albums.media.store');

// Rute Hapus Galeri (item media)
// Perhatikan: kita tidak perlu /albums/{album} di URL karena ID galeri sudah unik
Route::delete('/galeri-media/{galeri}', [AlbumMediaController::class, 'destroy'])->name('albums.media.destroy');

    Route::get('/program-pelatihan', [ProgramPelatihanController::class, 'index'])->name('program-pelatihan.index'); // <-- DIUBAH
Route::post('/program-pelatihan', [ProgramPelatihanController::class, 'store'])->name('program-pelatihan.store'); // <-- DIUBAH
Route::get('/program-pelatihan/{program}/edit', [ProgramPelatihanController::class, 'edit'])->name('program-pelatihan.edit'); // <-- DIUBAH
Route::put('/program-pelatihan/{program}', [ProgramPelatihanController::class, 'update'])->name('program-pelatihan.update'); // <-- DIUBAH
Route::delete('/program-pelatihan/{program}', [ProgramPelatihanController::class, 'destroy'])->name('program-pelatihan.destroy'); // <-- DIUBAH

Route::get('/edukasi/create', [EdukasiController::class, 'create'])->name('edukasi.create'); // Tambahkan ini
Route::get('/edukasi', [EdukasiController::class, 'index'])->name('edukasi.index');
Route::post('/edukasi', [EdukasiController::class, 'store'])->name('edukasi.store');
Route::get('/edukasi/{edukasi}/edit', [EdukasiController::class, 'edit'])->name('edukasi.edit');
Route::put('/edukasi/{edukasi}', [EdukasiController::class, 'update'])->name('edukasi.update'); 
Route::delete('/edukasi/{edukasi}', [EdukasiController::class, 'destroy'])->name('edukasi.destroy');

Route::get('/cara-daftar', [CaraDaftarController::class, 'index'])->name('cara-daftar.index');
Route::post('/cara-daftar', [CaraDaftarController::class, 'store'])->name('cara-daftar.store');
Route::get('/cara-daftar/{caraDaftar}/edit', [CaraDaftarController::class, 'edit'])->name('cara-daftar.edit');
Route::put('/cara-daftar/{caraDaftar}', [CaraDaftarController::class, 'update'])->name('cara-daftar.update');
Route::delete('/cara-daftar/{caraDaftar}', [CaraDaftarController::class, 'destroy'])->name('cara-daftar.destroy');

// 1. Export Excel & PDF
    Route::get('/students/export-excel', [StudentController::class, 'exportExcel'])->name('students.export-excel');
    Route::get('/students/export-pdf', [StudentController::class, 'exportPdf'])->name('students.export-pdf');
    Route::get('/students/{student}/export-biodata', [StudentController::class, 'exportPdfIndividual'])->name('students.export-biodata');

    // 2. Generate Akun
    Route::post('/students/{student}/generate-account', [StudentController::class, 'generateAccount'])->name('students.generate-account');

    // 3. Resource (CRUD Bawaan) - TARUH PALING BAWAH
    Route::resource('students', StudentController::class)->names('students');

Route::resource('keberangkatan', KeberangkatanController::class)
    ->names('keberangkatan')
    ->except(['create', 'show']);

    Route::resource('alumni', AlumniController::class)->names('alumni')->except(['create', 'show']);

    Route::get('/employees/export-excel', [EmployeeController::class, 'exportExcel'])->name('employees.export-excel');
Route::get('/employees/export-pdf', [EmployeeController::class, 'exportPdf'])->name('employees.export-pdf');
Route::get('/employees/{employee}/export-biodata', [EmployeeController::class, 'exportPdfIndividual'])->name('employees.export-biodata');
Route::post('/employees/{employee}/generate-account', [EmployeeController::class, 'generateAccount'])->name('employees.generate-account');

// Resource Pegawai
Route::resource('employees', EmployeeController::class)->names('employees');
});

Route::middleware(['auth', 'role:pegawai'])->prefix('pegawai')->name('pegawai.')->group(function () {
    
    // Dashboard Pegawai
    Route::get('/dashboard', [PegawaiAreaController::class, 'dashboard'])->name('dashboard');
    
    // Edit Biodata Pegawai
    Route::get('/biodata', [PegawaiAreaController::class, 'editBiodata'])->name('biodata.edit');
    Route::put('/biodata', [PegawaiAreaController::class, 'updateBiodata'])->name('biodata.update');
    Route::get('/biodata/print', [PegawaiAreaController::class, 'printBiodata'])->name('biodata.print');
});


// --- [BARU] Grup Rute untuk SISWA ---
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    
    // Dashboard Siswa
    Route::get('/dashboard', [SiswaAreaController::class, 'dashboard'])->name('dashboard');
    
    // Edit Biodata Siswa
    Route::get('/biodata', [SiswaAreaController::class, 'editBiodata'])->name('biodata.edit');
    Route::put('/biodata', [SiswaAreaController::class, 'updateBiodata'])->name('biodata.update');

    Route::get('/biodata/print', [SiswaAreaController::class, 'printBiodata'])->name('biodata.print');
});


// Rute profil bawaan Breeze (bisa diakses semua role yang login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php'; // Rute login, register, dll.