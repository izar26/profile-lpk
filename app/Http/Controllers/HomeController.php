<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LpkProfile;
use App\Models\ProgramPelatihan;
use App\Models\Edukasi;
use App\Models\Alumni;
use App\Models\CaraDaftar;
use App\Models\Keberangkatan;
use App\Models\Employee; // [BARU]
use App\Models\Album;    // [BARU]

class HomeController extends Controller
{
    // app/Http/Controllers/HomeController.php

public function index()
{
    // ... (kode profile dan program tetap sama) ...
    $profile = LpkProfile::find(1);
    if (!$profile) $profile = new LpkProfile();
    $programs = ProgramPelatihan::where('status', '!=', 'Selesai')->latest()->take(6)->get();

    // [UPDATE] Ambil Album dengan 12 foto terbarunya
    $albums = Album::with(['galeris' => function($query) {
                        $query->latest()->take(12); 
                    }])
                    ->withCount('galeris') // Hitung semua item (foto + video)
                    ->latest()
                    ->take(8)
                    ->get();

    // ... (kode artikel, alumni, dll tetap sama) ...
    $artikels = Edukasi::where('status', 'Published')->with('author')->latest()->take(3)->get();
    $alumnis = Alumni::with('student')
                        ->where('is_published', true)
                        ->latest()
                        ->take(6)
                        ->get();
    $alurDaftar = CaraDaftar::orderBy('urutan', 'asc')->get();
    $keberangkatans = Keberangkatan::latest('tanggal_berangkat')->take(5)->get();
    $employees = Employee::latest()->take(4)->get();

    return view('welcome', compact(
        'profile', 'programs', 'albums', 'artikels', 
        'alumnis', 'alurDaftar', 'keberangkatans', 'employees'
    ));
}
public function showProgram($id)
    {
        // 1. Ambil data program berdasarkan ID
        $program = ProgramPelatihan::findOrFail($id);

        // 2. Ambil Profil LPK untuk Navbar/Footer
        $profile = LpkProfile::find(1);
        if (!$profile) $profile = new LpkProfile();

        // 3. Kirim ke view
        return view('program.show', compact('program', 'profile'));
    }

    public function showEdukasi($slug)
    {
        // 1. Ambil data edukasi berdasarkan SLUG dan pastikan statusnya Published
        $edukasi = Edukasi::where('slug', $slug)
                          ->where('status', 'Published')
                          ->firstOrFail();

        // 2. Ambil Profil LPK untuk Navbar/Footer
        $profile = LpkProfile::find(1);
        if (!$profile) $profile = new LpkProfile();

        // 3. (Opsional) Ambil 3 artikel terbaru lainnya untuk sidebar/rekomendasi
        $artikelLain = Edukasi::where('status', 'Published')
                              ->where('id', '!=', $edukasi->id)
                              ->latest()
                              ->take(3)
                              ->get();

        // 4. Kirim ke view
        return view('edukasi.show', compact('edukasi', 'profile', 'artikelLain'));
    }

    // App/Http/Controllers/HomeController.php

public function showKeberangkatan($id)
{
    // 1. Ambil data keberangkatan
    $keberangkatan = Keberangkatan::findOrFail($id);

    // 2. Ambil Profil LPK
    $profile = LpkProfile::find(1);
    if (!$profile) $profile = new LpkProfile();

    // 3. Ambil keberangkatan lain untuk sidebar (kecuali yang sedang dilihat)
    $lainnya = Keberangkatan::where('id', '!=', $id)
                            ->latest('tanggal_berangkat')
                            ->take(3)
                            ->get();

    // 4. Kirim ke view
    return view('keberangkatan.show', compact('keberangkatan', 'profile', 'lainnya'));
}
}