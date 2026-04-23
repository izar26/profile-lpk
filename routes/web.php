<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicVerifyController;
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
use App\Http\Controllers\Admin\RefDocumentTypeController;
use App\Http\Controllers\Pegawai\PegawaiAreaController;
use App\Http\Controllers\Siswa\SiswaAreaController;
use App\Http\Controllers\Siswa\SiswaTestimoniController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Public Routes ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/program/{id}', [HomeController::class, 'showProgram'])->name('public.program.show');
Route::get('/artikel/{slug}', [HomeController::class, 'showEdukasi'])->name('public.edukasi.show');
Route::get('/keberangkatan/{id}', [HomeController::class, 'showKeberangkatan'])->name('public.keberangkatan.show');

// Public Verification
Route::controller(PublicVerifyController::class)->group(function () {
    Route::get('/verify/{id}', 'verify')->name('student.verify');
    Route::post('/verify/check', 'check')->name('student.verify.check');
});

Route::controller(EmployeeController::class)->group(function () {
    Route::get('/verify/pegawai/{employee}', 'verification')->name('pegawai.verification.public');
    Route::post('/verify/pegawai/check', 'verificationCheck')->name('pegawai.verification.check');
});

// --- Auth & Dashboard Redirect ---
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


// --- ADMIN Routes ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('document-types', RefDocumentTypeController::class)->names('document-types');

    // LPK Profile
    Route::get('/lpk-profile', [LpkProfileController::class, 'edit'])->name('lpk-profile.edit');
    Route::post('/lpk-profile', [LpkProfileController::class, 'update'])->name('lpk-profile.update');

    // Albums & Media
    Route::resource('albums', AlbumController::class);
    Route::get('/albums/{album}/media', [AlbumMediaController::class, 'index'])->name('albums.media.index');
    Route::post('/albums/{album}/media', [AlbumMediaController::class, 'store'])->name('albums.media.store');
    Route::delete('/galeri-media/{galeri}', [AlbumMediaController::class, 'destroy'])->name('albums.media.destroy');

    // Program & Edukasi
    Route::resource('program-pelatihan', ProgramPelatihanController::class)->names('program-pelatihan');
    Route::resource('edukasi', EdukasiController::class); // Menggunakan resource karena route standard CRUD
    Route::resource('cara-daftar', CaraDaftarController::class);

    // Keberangkatan & Alumni
    Route::resource('keberangkatan', KeberangkatanController::class)->names('keberangkatan')->except(['create', 'show']);
    Route::resource('alumni', AlumniController::class)->names('alumni')->except(['create', 'show']);

    // Students Management
    Route::controller(StudentController::class)->prefix('students')->name('students.')->group(function () {
        // Helper
        Route::get('/next-number', 'getNextNumber')->name('next-number');

        // Exports
        Route::get('/export-excel', 'exportExcel')->name('export-excel');
        Route::get('/export-pdf', 'exportPdf')->name('export-pdf');
        Route::get('/export-id-card', 'exportIdCard')->name('export-id-card');
        Route::get('/{student}/export-biodata', 'exportPdfIndividual')->name('export-biodata');
        Route::get('/{student}/export-agreement', 'exportAgreement')->name('export-agreement');

        // Account & Verification
        Route::post('/{student}/generate-account', 'generateAccount')->name('generate-account');
        Route::get('/{student}/verify', 'verification')->name('verify');
        Route::post('/{student}/verify', 'processVerification')->name('process-verify');
    });
    // Resource Students (ditaruh setelah custom route agar tidak konflik dengan ID)
    Route::resource('students', StudentController::class)->names('students');

    // Employees Management
    Route::controller(EmployeeController::class)->prefix('employees')->name('employees.')->group(function () {
        // Exports
        Route::get('/export/excel', 'exportExcel')->name('export-excel');
        Route::get('/export/pdf', 'exportPdf')->name('export-pdf');
        Route::get('/export/biodata/{employee}', 'exportPdfIndividual')->name('export-biodata');
        Route::get('/export/id-card', 'exportIdCard')->name('export-id-card');

        // Account
        Route::post('/generate-account/{employee}', 'generateAccount')->name('generate-account');

        // Relations CRUD
        Route::post('/{employee}/education', 'storeEducation')->name('education.store');
        Route::delete('/{employee}/education/{id}', 'destroyEducation')->name('education.destroy');

        Route::post('/{employee}/family', 'storeFamily')->name('family.store');
        Route::put('/{employee}/family/{id}', 'updateFamily')->name('family.update');
        Route::delete('/{employee}/family/{id}', 'destroyFamily')->name('family.destroy');

        Route::post('/{employee}/document', 'storeDocument')->name('document.store');
        Route::delete('/{employee}/document/{id}', 'destroyDocument')->name('document.destroy');
    });
    Route::resource('employees', EmployeeController::class)->names('employees');
});


// --- PEGAWAI Routes ---
Route::middleware(['auth', 'role:pegawai'])->prefix('pegawai')->name('pegawai.')->group(function () {

    Route::controller(PegawaiAreaController::class)->group(function() {
        Route::get('/dashboard', 'dashboard')->name('dashboard');

        // Biodata
        Route::get('/biodata', 'editBiodata')->name('biodata.edit');
        Route::put('/biodata', 'updateBiodata')->name('biodata.update');
        Route::get('/biodata/print', 'printBiodata')->name('biodata.print');

        // Dokumen
        Route::post('/document', 'storeDocument')->name('document.store');
        Route::delete('/document/{id}', 'destroyDocument')->name('document.destroy');

        // Pendidikan
        Route::post('/education', 'storeEducation')->name('education.store');
        Route::delete('/education/{id}', 'destroyEducation')->name('education.destroy');

        // Keluarga
        Route::post('/family', 'storeFamily')->name('family.store');
        Route::put('/family/{id}', 'updateFamily')->name('family.update');
        Route::delete('/family/{id}', 'destroyFamily')->name('family.destroy');
    });
});


// --- SISWA Routes ---
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {

    Route::controller(SiswaAreaController::class)->group(function() {
        Route::get('/dashboard', 'dashboard')->name('dashboard');

        // Biodata
        Route::get('/biodata', 'editBiodata')->name('biodata.edit');
        Route::put('/biodata', 'updateBiodata')->name('biodata.update');
        Route::get('/biodata/print', 'printBiodata')->name('biodata.print');

        // Formulir
        Route::get('/formulir', 'showFormulir')->name('formulir.show');
        Route::put('/formulir', 'updateFormulir')->name('formulir.update');
    });

    // Testimoni
    Route::get('/testimoni', [SiswaTestimoniController::class, 'index'])->name('testimoni.index');
    Route::post('/testimoni', [SiswaTestimoniController::class, 'store'])->name('testimoni.store');
});


// --- General Profile Routes ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
