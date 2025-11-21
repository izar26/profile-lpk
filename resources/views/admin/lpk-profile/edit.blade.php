@extends('layouts.app')

@section('header', 'Pengaturan Profil LPK')

@section('content')

{{-- Tampilkan pesan sukses --}}
@if (session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('admin.lpk-profile.update') }}" enctype="multipart/form-data">
    @csrf
    
    <div class="max-w-4xl mx-auto space-y-6">

        <div>
            <label for="nama_lpk" class="block text-sm font-medium text-gray-700 mb-1">Nama LPK</label>
            <input id="nama_lpk" name="nama_lpk" type="text" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300" value="{{ old('nama_lpk', $profile->nama_lpk) }}" required>
            @error('nama_lpk') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>
        
        <div x-data="{
                logoUrl: '{{ $profile->logo ? asset('storage/' . $profile->logo) : 'https://via.placeholder.com/150x150.png?text=Logo+LPK' }}',
                logoFilename: '',
                previewLogo(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.logoFilename = file.name;
                        const reader = new FileReader();
                        reader.onload = (e) => { this.logoUrl = e.target.result; };
                        reader.readAsDataURL(file);
                    } else {
                        this.logoFilename = '';
                        this.logoUrl = '{{ $profile->logo ? asset('storage/' . $profile->logo) : 'https://via.placeholder.com/150x150.png?text=Logo+LPK' }}';
                    }
                }
             }">
            <label class="block text-sm font-medium text-gray-700 mb-1">Logo LPK</label>
            <div class="flex items-center gap-4">
                <img :src="logoUrl" alt="Preview Logo" class="h-24 w-24 rounded-lg object-cover border-2 border-gold-200 bg-gray-100">
                <div>
                    <label for="logo" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gold-50 text-gold-700 rounded-lg shadow-sm hover:bg-gold-100 transition-all duration-200">
                        <i class="fa-solid fa-upload mr-2 text-gold-600"></i>
                        Pilih Logo...
                    </label>
                    <input @change="previewLogo($event)" name="logo" type="file" id="logo" class="hidden"/>
                    <span x-text="logoFilename" class="ml-3 text-sm text-gray-600"></span>
                    <p class="text-xs text-gray-500 mt-1">PNG, JPG (Maks. 2MB).</p>
                    @error('logo') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div>
            <label for="deskripsi_singkat" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat</label>
            <textarea id="deskripsi_singkat" name="deskripsi_singkat" rows="3" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300">{{ old('deskripsi_singkat', $profile->deskripsi_singkat) }}</textarea>
            @error('deskripsi_singkat') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>
        
        <div>
            <label for="tagline" class="block text-sm font-medium text-gray-700 mb-1">Tagline / Slogan</label>
            <input id="tagline" name="tagline" type="text" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300" value="{{ old('tagline', $profile->tagline) }}" placeholder="Contoh: Profesional, Terampil, Siap Kerja!">
            @error('tagline') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>

        <hr class="border-gray-200 my-4">

        <h3 class="text-lg font-serif font-bold text-gray-800">Lokasi & Kontak</h3>

        <div>
            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
            <textarea id="alamat" name="alamat" rows="3" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300">{{ old('alamat', $profile->alamat) }}</textarea>
            @error('alamat') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="google_map_embed" class="block text-sm font-medium text-gray-700 mb-1">Kode Embed Google Maps</label>
            <textarea id="google_map_embed" name="google_map_embed" rows="4" class="w-full font-mono text-sm border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300" placeholder="Salin kode 'Embed a map' dari Google Maps ke sini...">{{ old('google_map_embed', $profile->google_map_embed) }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Buka Google Maps, cari lokasi LPK, klik "Share", lalu "Embed a map", dan salin kode HTML-nya ke sini.</p>
            @error('google_map_embed') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="telepon_lpk" class="block text-sm font-medium text-gray-700 mb-1">Telepon LPK (Kantor)</label>
                <input id="telepon_lpk" name="telepon_lpk" type="text" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300" value="{{ old('telepon_lpk', $profile->telepon_lpk) }}">
                @error('telepon_lpk') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="nomor_wa" class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp (Admin)</label>
                <input id="nomor_wa" name="nomor_wa" type="text" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300" value="{{ old('nomor_wa', $profile->nomor_wa) }}" placeholder="Contoh: 08123456789">
                @error('nomor_wa') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="email_lpk" class="block text-sm font-medium text-gray-700 mb-1">Email LPK</label>
                <input id="email_lpk" name="email_lpk" type="email" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300" value="{{ old('email_lpk', $profile->email_lpk) }}">
                @error('email_lpk') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="website_url" class="block text-sm font-medium text-gray-700 mb-1">Alamat Website</label>
                <input id="website_url" name="website_url" type="url" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300" value="{{ old('website_url', $profile->website_url) }}" placeholder="https://lpk-anda.com">
                @error('website_url') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <hr class="border-gray-200 my-4">

        <h3 class="text-lg font-serif font-bold text-gray-800">Visi, Misi & Media Sosial</h3>

        <div>
            <label for="visi" class="block text-sm font-medium text-gray-700 mb-1">Visi</label>
            <textarea id="visi" name="visi" rows="4" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300">{{ old('visi', $profile->visi) }}</textarea>
            @error('visi') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="misi" class="block text-sm font-medium text-gray-700 mb-1">Misi</label>
            <textarea id="misi" name="misi" rows="4" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300">{{ old('misi', $profile->misi) }}</textarea>
            @error('misi') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-1">Facebook URL</label>
                <input id="facebook_url" name="facebook_url" type="text" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300" value="{{ old('facebook_url', $profile->facebook_url) }}" placeholder="https://facebook.com/lpkanda">
                @error('facebook_url') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="instagram_url" class="block text-sm font-medium text-gray-700 mb-1">Instagram URL</label>
                <input id="instagram_url" name="instagram_url" type="text" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300" value="{{ old('instagram_url', $profile->instagram_url) }}" placeholder="https://instagram.com/lpkanda">
                @error('instagram_url') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="tiktok_url" class="block text-sm font-medium text-gray-700 mb-1">TikTok URL</label>
                <input id="tiktok_url" name="tiktok_url" type="text" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300" value="{{ old('tiktok_url', $profile->tiktok_url) }}" placeholder="https://tiktok.com/@lpkanda">
                @error('tiktok_url') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="youtube_url" class="block text-sm font-medium text-gray-700 mb-1">YouTube URL</label>
                <input id="youtube_url" name="youtube_url" type="text" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300" value="{{ old('youtube_url', $profile->youtube_url) }}" placeholder="https://youtube.com/@lpkanda">
                @error('youtube_url') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="px-5 py-2.5 bg-gold-500 text-white rounded-xl shadow-md hover:bg-gold-600 transition-all font-semibold">
                Simpan Perubahan
            </button>
        </div>

    </div>
</form>

@endsection