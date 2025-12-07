<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\LpkProfile;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PublicVerifyController extends Controller
{
    /**
     * Menampilkan halaman verifikasi awal.
     * URL ini yang ditanam di QR Code (misal: /verify/123)
     */
    public function verify($id)
    {
        // Cari siswa berdasarkan ID, jika tidak ada -> 404
        $student = Student::findOrFail($id);
        $profile = LpkProfile::first();

        // Tampilkan halaman "Gate" (Minta Input)
        return view('public.verify.gate', compact('student', 'profile'));
    }

    /**
     * Memproses Input Verifikasi (Password / Tanggal Lahir)
     */
    public function check(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'verifikasi_key' => 'required|string', // Bisa Tanggal Lahir (DDMMYYYY) atau Password Akun
        ]);

        $student = Student::with(['program', 'educations', 'families', 'experiences'])->findOrFail($request->student_id);
        $profile = LpkProfile::first();

        // LOGIKA VERIFIKASI
        // Opsi 1: Cek pakai Tanggal Lahir (Format: DDMMYYYY) -> Lebih user friendly untuk publik/perusahaan
        // Contoh: Lahir 17 Agustus 1945 -> Input: 17081945
        $tglLahirInput = $request->verifikasi_key;
        $tglLahirSiswa = Carbon::parse($student->tanggal_lahir)->format('dmY');

        if ($tglLahirInput === $tglLahirSiswa) {
            // Jika Benar, Tampilkan Data Lengkap
            return view('public.verify.result', compact('student', 'profile'));
        }

        // Jika Salah
        return back()->with('error', 'Kode Verifikasi Salah. Masukkan Tanggal Lahir (Format: DDMMYYYY).');
    }
}