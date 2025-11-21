<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LpkProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LpkProfileController extends Controller
{
    /**
     * Tampilkan form edit.
     * Kita gunakan 'firstOrCreate' untuk memastikan 
     * baris data (dengan ID 1) selalu ada.
     */
    public function edit()
    {
        $profile = LpkProfile::firstOrCreate(['id' => 1]);
        return view('admin.lpk-profile.edit', compact('profile'));
    }

    /**
     * Update data LPK.
     */
    public function update(Request $request)
    {
        $profile = LpkProfile::findOrFail(1);

        $request->validate([
            'nama_lpk' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'deskripsi_singkat' => 'nullable|string',
            'tagline' => 'nullable|string|max:255', // <-- BARU
            'alamat' => 'nullable|string',
            'google_map_embed' => 'nullable|string', // <-- BARU
            'email_lpk' => 'nullable|email',
            'website_url' => 'nullable|url|max:255', // <-- BARU (pakai rule 'url')
            'telepon_lpk' => 'nullable|string|max:20',
            'nomor_wa' => 'nullable|string|max:20', // <-- BARU
            'visi' => 'nullable|string',
            'misi' => 'nullable|string',
            'facebook_url' => 'nullable|string|max:255', // <-- BARU
            'instagram_url' => 'nullable|string|max:255', // <-- BARU
            'tiktok_url' => 'nullable|string|max:255', // <-- BARU
            'youtube_url' => 'nullable|string|max:255', // <-- BARU
        ]);

        // Ambil semua data yang divalidasi
        $data = $request->except('logo');

        // Logika Upload Logo (Sama seperti foto profil user)
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            // Simpan logo baru di 'storage/app/public/logo_lpk'
            $path = $request->file('logo')->store('logo_lpk', 'public');
            $data['logo'] = $path;
        }

        // Logika update() ini sudah otomatis menangani semua field baru
        $profile->update($data); 

        return redirect()->route('admin.lpk-profile.edit')->with('success', 'Profil LPK berhasil diperbarui.');
    }
}