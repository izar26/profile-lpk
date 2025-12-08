<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student; // [PENTING] Import Model Student
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB; // [PENTING] Import DB

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'telepon' => ['required', 'string', 'max:20'], // Validasi No WA
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Gunakan Transaction agar data konsisten
        DB::transaction(function () use ($request) {
            
            // 2. Buat Akun User (Role: Siswa)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'siswa', // Default role
            ]);

            // 3. Buat Data Siswa (Status: Mendaftar)
            // Kita simpan Nama, Email, dan Telepon dari form registrasi ke tabel students
            Student::create([
    'user_id' => $user->id,
    'nama_lengkap' => $request->name,
    'email' => $request->email,
    'no_hp_peserta' => $request->telepon,
    'status' => 'Mendaftar',
]);


            // Trigger Event Registered (Bawaan Laravel)
            event(new Registered($user));

            // 4. Login Otomatis
            Auth::login($user);
        });

        // 5. Redirect ke Dashboard (Sesuai role di web.php)
        return redirect(route('dashboard', absolute: false));
    }
}