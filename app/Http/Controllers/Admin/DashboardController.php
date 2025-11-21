<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Alumni;
use App\Models\ProgramPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Utama (Dari tabel masing-masing agar akurat)
        $totalSiswa = Student::count();
        $totalPegawai = Employee::count();
        $totalAlumni = Alumni::count();
        $totalProgram = ProgramPelatihan::count();

        // 2. User Online (Logic 5 Menit)
        $timestamp = now()->subMinutes(5)->getTimestamp();
        
        $onlineUsers = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->where('sessions.user_id', '!=', null)
            ->where('sessions.last_activity', '>', $timestamp)
            ->select('users.*', 'sessions.last_activity')
            ->distinct('users.id') // Hindari duplikat session
            ->get()
            ->map(function ($user) {
                $user->last_seen = Carbon::createFromTimestamp($user->last_activity)->diffForHumans();
                return $user;
            });

        return view('admin.dashboard', compact(
            'totalSiswa', 
            'totalPegawai', 
            'totalAlumni', 
            'totalProgram', 
            'onlineUsers'
        ));
    }
}