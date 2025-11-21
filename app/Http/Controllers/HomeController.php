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
                    $query->where('tipe', 'foto')->latest()->take(12);
                }])
                ->withCount(['galeris' => function($query) {
                    $query->where('tipe', 'foto');
                }])
                ->latest()
                ->take(8)
                ->get();

    // ... (kode artikel, alumni, dll tetap sama) ...
    $artikels = Edukasi::where('status', 'Published')->with('author')->latest()->take(3)->get();
    $alumnis = Alumni::latest()->take(6)->get();
    $alurDaftar = CaraDaftar::orderBy('urutan', 'asc')->get();
    $keberangkatans = Keberangkatan::latest('tanggal_berangkat')->take(5)->get();
    $employees = Employee::latest()->take(4)->get();

    return view('welcome', compact(
        'profile', 'programs', 'albums', 'artikels', 
        'alumnis', 'alurDaftar', 'keberangkatans', 'employees'
    ));
}
}