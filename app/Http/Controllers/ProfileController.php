<?php
// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage; // <-- 1. IMPORT STORAGE

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // 2. Isi data user dari request yang divalidasi
        $request->user()->fill($request->validated());

        // 3. Reset verifikasi email jika email diubah
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // ===== 4. LOGIKA UPLOAD FOTO =====
        if ($request->hasFile('foto')) {
            $user = $request->user();

            // Hapus foto lama jika ada
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }

            // Simpan foto baru di 'storage/app/public/foto_profil'
            // dan simpan path-nya (misal: "foto_profil/namafile.jpg")
            $path = $request->file('foto')->store('foto_profil', 'public');
            
            // Simpan path ke database
            $user->foto = $path;
        }
        // ==================================

        // 5. Simpan semua perubahan
        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Hapus foto profil dari storage saat akun dihapus
        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}