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
     */
    public function edit()
    {
        // Pastikan data ID 1 selalu ada
        $profile = LpkProfile::firstOrCreate(['id' => 1], [
            'nama_lpk' => 'LPK Baru',
            // Default value lain jika perlu
        ]);
        
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
            'nama_pimpinan' => 'nullable|string|max:255',
            'nomor_sk' => 'nullable|string|max:255',
            
            // Validasi Gambar
            'logo' => 'nullable|image|max:2048',
            'gambar_hero' => 'nullable|image|max:4096',
            'gambar_tentang' => 'nullable|image|max:2048',
            'gambar_auth' => 'nullable|image|max:4096',
            
            // [BARU] Validasi Background Kartu (Max 4MB)
            'background_kartu' => 'nullable|image|max:4096',
            
            'deskripsi_singkat' => 'nullable|string',
            'tagline' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'google_map_embed' => 'nullable|string',
            'email_lpk' => 'nullable|email',
            'website_url' => 'nullable|url|max:255',
            'telepon_lpk' => 'nullable|string|max:20',
            'nomor_wa' => 'nullable|string|max:20',
            'visi' => 'nullable|string',
            'misi' => 'nullable|string',
            'facebook_url' => 'nullable|string|max:255',
            'instagram_url' => 'nullable|string|max:255',
            'tiktok_url' => 'nullable|string|max:255',
            'youtube_url' => 'nullable|string|max:255',
        ]);

        // Kecualikan semua input file dari $data awal
        $data = $request->except(['logo', 'gambar_hero', 'gambar_tentang', 'gambar_auth', 'background_kartu']);

        // Helper function untuk upload
        $uploadImage = function($field, $folder) use ($request, $profile, &$data) {
            if ($request->hasFile($field)) {
                // Hapus file lama jika ada
                if ($profile->$field) {
                    Storage::disk('public')->delete($profile->$field);
                }
                // Simpan file baru
                $data[$field] = $request->file($field)->store($folder, 'public');
            }
        };

        // Jalankan upload untuk setiap gambar
        $uploadImage('logo', 'logo_lpk');
        $uploadImage('gambar_hero', 'logo_lpk');    
        $uploadImage('gambar_tentang', 'logo_lpk');
        $uploadImage('gambar_auth', 'logo_lpk'); 
        
        // [BARU] Upload Background Kartu
        $uploadImage('background_kartu', 'logo_lpk'); 

        $profile->update($data);

        return redirect()->route('admin.lpk-profile.edit')->with('success', 'Profil LPK dan aset gambar berhasil diperbarui.');
    }
}