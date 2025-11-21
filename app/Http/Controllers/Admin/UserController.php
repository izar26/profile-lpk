<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- 1. TAMBAHKAN IMPORT INI

class UserController extends Controller
{
    /**
     * Tampilkan daftar user.
     */
    public function index()
    {
        // 1. Ambil data user seperti biasa
        $users = User::latest()->paginate(10);
        
        // 2. Ambil ID user yang sedang online (aturan 5 menit)
        $timestamp = now()->subMinutes(5)->getTimestamp();

        $onlineUserIds = DB::table('sessions')
            ->where('user_id', '!=', null) // Pastikan user login
            ->where('last_activity', '>', $timestamp) // Cek aktivitas terakhir
            ->distinct() // Hindari duplikat jika user punya >1 sesi
            ->pluck('user_id') // Ambil hanya ID-nya
            ->toArray(); // Ubah menjadi array [1, 5, 12]

        // 3. Kirim kedua data ke view
        return view('admin.users.index', compact('users', 'onlineUserIds'));
    }

    /**
     * Form tambah user (opsional, karena kita pakai modal).
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Simpan user baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,pegawai,siswa',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Edit user. (Untuk modal fetch)
     */
    public function edit(User $user)
    {
        return response()->json($user);
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,pegawai,siswa',
        ]);

        $user->update($request->only('name', 'email', 'role'));

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }


    /**
     * Hapus user.
     */
    public function destroy(User $user)
    {
        // Cegah admin menghapus dirinya sendiri
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}