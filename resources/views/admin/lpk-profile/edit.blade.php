@extends('layouts.app')

@section('header', 'Pengaturan Profil LPK')

@section('content')

{{-- Menampilkan Pesan Sukses --}}
@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
         class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm flex items-center justify-between transition-opacity duration-500">
        <div class="flex items-center">
            <i class="fa-solid fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button @click="show = false" class="text-green-700 hover:text-green-900"><i class="fa-solid fa-times"></i></button>
    </div>
@endif

{{-- Menampilkan Alert Error Global --}}
@if ($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm animate-pulse">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-bold text-red-800">Gagal Menyimpan Perubahan</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('admin.lpk-profile.update') }}" enctype="multipart/form-data" 
      x-data="{ activeTab: 'utama' }"> 
    @csrf
    
    <div class="max-w-6xl mx-auto">

        {{-- Tab Navigation --}}
        <div class="flex overflow-x-auto border-b border-gray-200 mb-6 bg-white rounded-t-xl shadow-sm sticky top-0 z-20">
            <button type="button" @click="activeTab = 'utama'" 
                :class="activeTab === 'utama' ? 'border-gold-500 text-gold-600 bg-gold-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-all focus:outline-none whitespace-nowrap group">
                <i class="fa-solid fa-circle-info mr-2 group-hover:scale-110 transition-transform"></i> Info Utama
            </button>

            <button type="button" @click="activeTab = 'visual'" 
                :class="activeTab === 'visual' ? 'border-gold-500 text-gold-600 bg-gold-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-all focus:outline-none whitespace-nowrap group">
                <i class="fa-solid fa-images mr-2 group-hover:scale-110 transition-transform"></i> Visual & Media
            </button>

            <button type="button" @click="activeTab = 'kontak'" 
                :class="activeTab === 'kontak' ? 'border-gold-500 text-gold-600 bg-gold-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-all focus:outline-none whitespace-nowrap group">
                <i class="fa-solid fa-address-book mr-2 group-hover:scale-110 transition-transform"></i> Kontak & Lokasi
            </button>

            <button type="button" @click="activeTab = 'visi'" 
                :class="activeTab === 'visi' ? 'border-gold-500 text-gold-600 bg-gold-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-all focus:outline-none whitespace-nowrap group">
                <i class="fa-solid fa-bullseye mr-2 group-hover:scale-110 transition-transform"></i> Visi Misi
            </button>
        </div>

        <div class="bg-white p-8 rounded-b-xl shadow-sm border border-gray-100 space-y-6 min-h-[400px]">

            {{-- TAB UTAMA --}}
            <div x-show="activeTab === 'utama'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama LPK <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lpk" value="{{ old('nama_lpk', $profile->nama_lpk) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gold-500 focus:ring-gold-500 transition-colors">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Pimpinan</label>
                            <input type="text" name="nama_pimpinan" value="{{ old('nama_pimpinan', $profile->nama_pimpinan) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gold-500 focus:ring-gold-500" placeholder="Contoh: Budi Santoso, S.Pd.">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nomor SK / Izin</label>
                            <input type="text" name="nomor_sk" value="{{ old('nomor_sk', $profile->nomor_sk) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gold-500 focus:ring-gold-500" placeholder="Contoh: KEP.123/DISNAKER/2023">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tagline / Slogan</label>
                        <input type="text" name="tagline" value="{{ old('tagline', $profile->tagline) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gold-500 focus:ring-gold-500">
                        <p class="text-xs text-gray-400 mt-1">Ditampilkan di halaman login dan beranda.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Singkat</label>
                        <textarea name="deskripsi_singkat" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gold-500 focus:ring-gold-500">{{ old('deskripsi_singkat', $profile->deskripsi_singkat) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- TAB VISUAL & MEDIA --}}
            <div x-show="activeTab === 'visual'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    {{-- 1. Logo --}}
                    <div x-data="{ preview: '{{ $profile->logo ? asset('storage/'.$profile->logo) : null }}', handleFile(e){ const f = e.target.files[0]; if(f){ const r = new FileReader(); r.onload = (evt) => this.preview = evt.target.result; r.readAsDataURL(f); } } }">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Logo LPK</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl h-64 flex flex-col items-center justify-center bg-gray-50 relative overflow-hidden group hover:border-gold-400 transition-all">
                            
                            <div x-show="!preview" class="text-gray-300 mb-3"><i class="fa-solid fa-image text-5xl"></i></div>
                            
                            <img x-show="preview" :src="preview" class="absolute inset-0 w-full h-full object-contain p-4 z-0 transition-transform duration-500 group-hover:scale-105">
                            
                            <div x-show="preview" class="absolute inset-0 bg-black/40 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                            <div class="relative z-20">
                                <label class="cursor-pointer px-5 py-2.5 bg-white border border-gray-200 rounded-lg shadow-md text-sm font-bold text-gray-700 hover:text-gold-600 hover:border-gold-400 hover:shadow-lg transition-all flex items-center gap-2 transform group-hover:-translate-y-1">
                                    <i class="fa-solid fa-upload"></i> <span x-text="preview ? 'Ganti Logo' : 'Pilih Logo'"></span>
                                    <input type="file" name="logo" class="hidden" @change="handleFile($event)" accept="image/*">
                                </label>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 text-center">Format PNG/JPG, Transparan lebih baik.</p>
                    </div>

                    {{-- 2. Background Auth --}}
                    <div x-data="{ preview: '{{ $profile->gambar_auth ? asset('storage/'.$profile->gambar_auth) : null }}', handleFile(e){ const f = e.target.files[0]; if(f){ const r = new FileReader(); r.onload = (evt) => this.preview = evt.target.result; r.readAsDataURL(f); } } }">
                        <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center justify-between">
                            Background Login & Register 
                            <span class="bg-gold-100 text-gold-700 text-[10px] px-2 py-0.5 rounded-full font-extrabold uppercase tracking-wide">Baru</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl h-64 flex flex-col items-center justify-center bg-gray-50 relative overflow-hidden group hover:border-gold-400 transition-all">
                            
                            <div x-show="!preview" class="text-gray-300 mb-3"><i class="fa-solid fa-image text-5xl"></i></div>
                            
                            <img x-show="preview" :src="preview" class="absolute inset-0 w-full h-full object-cover z-0 transition-transform duration-700 group-hover:scale-110">
                            
                            <div x-show="preview" class="absolute inset-0 bg-black/40 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                            <div class="relative z-20">
                                <label class="cursor-pointer px-5 py-2.5 bg-white border border-gray-200 rounded-lg shadow-md text-sm font-bold text-gray-700 hover:text-gold-600 hover:border-gold-400 hover:shadow-lg transition-all flex items-center gap-2 transform group-hover:-translate-y-1">
                                    <i class="fa-solid fa-upload"></i> <span x-text="preview ? 'Ganti Background' : 'Pilih Background'"></span>
                                    <input type="file" name="gambar_auth" class="hidden" @change="handleFile($event)" accept="image/*">
                                </label>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 text-center">Disarankan Portrait (1080x1920). Max 4MB.</p>
                    </div>

                    {{-- 3. Hero Image --}}
                    <div x-data="{ preview: '{{ $profile->gambar_hero ? asset('storage/'.$profile->gambar_hero) : null }}', handleFile(e){ const f = e.target.files[0]; if(f){ const r = new FileReader(); r.onload = (evt) => this.preview = evt.target.result; r.readAsDataURL(f); } } }">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Gambar Hero (Beranda Utama)</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl h-64 flex flex-col items-center justify-center bg-gray-50 relative overflow-hidden group hover:border-gold-400 transition-all">
                            
                            <div x-show="!preview" class="text-gray-300 mb-3"><i class="fa-solid fa-image text-5xl"></i></div>
                            
                            <img x-show="preview" :src="preview" class="absolute inset-0 w-full h-full object-cover z-0 transition-transform duration-700 group-hover:scale-110">
                            
                            <div x-show="preview" class="absolute inset-0 bg-black/40 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                            <div class="relative z-20">
                                <label class="cursor-pointer px-5 py-2.5 bg-white border border-gray-200 rounded-lg shadow-md text-sm font-bold text-gray-700 hover:text-gold-600 hover:border-gold-400 hover:shadow-lg transition-all flex items-center gap-2 transform group-hover:-translate-y-1">
                                    <i class="fa-solid fa-upload"></i> <span x-text="preview ? 'Ganti Hero' : 'Pilih Hero'"></span>
                                    <input type="file" name="gambar_hero" class="hidden" @change="handleFile($event)" accept="image/*">
                                </label>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 text-center">Format Landscape (1920x1080).</p>
                    </div>

                    {{-- 4. Tentang Kami Image --}}
                    <div x-data="{ preview: '{{ $profile->gambar_tentang ? asset('storage/'.$profile->gambar_tentang) : null }}', handleFile(e){ const f = e.target.files[0]; if(f){ const r = new FileReader(); r.onload = (evt) => this.preview = evt.target.result; r.readAsDataURL(f); } } }">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Gambar 'Tentang Kami'</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl h-64 flex flex-col items-center justify-center bg-gray-50 relative overflow-hidden group hover:border-gold-400 transition-all">
                            
                            <div x-show="!preview" class="text-gray-300 mb-3"><i class="fa-solid fa-image text-5xl"></i></div>
                            
                            <img x-show="preview" :src="preview" class="absolute inset-0 w-full h-full object-cover z-0 transition-transform duration-700 group-hover:scale-110">
                            
                            <div x-show="preview" class="absolute inset-0 bg-black/40 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                            <div class="relative z-20">
                                <label class="cursor-pointer px-5 py-2.5 bg-white border border-gray-200 rounded-lg shadow-md text-sm font-bold text-gray-700 hover:text-gold-600 hover:border-gold-400 hover:shadow-lg transition-all flex items-center gap-2 transform group-hover:-translate-y-1">
                                    <i class="fa-solid fa-upload"></i> <span x-text="preview ? 'Ganti Gambar' : 'Pilih Gambar'"></span>
                                    <input type="file" name="gambar_tentang" class="hidden" @change="handleFile($event)" accept="image/*">
                                </label>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 text-center">Format Portrait (800x1000).</p>
                    </div>

                </div>

                {{-- SECTION KHUSUS: Desain Kartu Siswa --}}
                <div class="border-t pt-6 mt-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fa-solid fa-id-card text-gold-500 mr-2"></i> Desain Kartu Siswa
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Input Background Kartu -->
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Background ID Card (Custom)
                            </label>
                            
                            <div class="flex items-start space-x-4">
                                {{-- Preview Gambar Saat Ini --}}
                                <div class="shrink-0">
                                    @if($profile->background_kartu)
                                        <div class="relative group">
                                            <img src="{{ asset('storage/' . $profile->background_kartu) }}" 
                                                 class="h-32 w-20 object-cover rounded-lg border border-gray-300 shadow-sm" 
                                                 alt="Background Kartu">
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition rounded-lg"></div>
                                        </div>
                                        <p class="text-[10px] text-gray-500 mt-1 text-center font-semibold">Saat Ini</p>
                                    @else
                                        <div class="h-32 w-20 bg-red-800 rounded-lg border border-gray-300 flex items-center justify-center shadow-sm">
                                            <span class="text-[10px] text-white text-center px-1 leading-tight">Default<br>(Merah)</span>
                                        </div>
                                        <p class="text-[10px] text-gray-500 mt-1 text-center font-semibold">Default</p>
                                    @endif
                                </div>

                                {{-- Input File --}}
                                <div class="flex-1">
                                    <input type="file" name="background_kartu" accept="image/*"
                                           class="block w-full text-sm text-gray-500
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-full file:border-0
                                                  file:text-xs file:font-semibold
                                                  file:bg-gold-50 file:text-gold-700
                                                  hover:file:bg-gold-100 transition cursor-pointer mb-2">
                                    
                                    <p class="text-xs text-gray-500 leading-relaxed">
                                        <i class="fa-solid fa-circle-info mr-1 text-blue-500"></i>
                                        Format: <strong>JPG/PNG</strong>. Disarankan rasio Portrait (54mm x 86mm).<br>
                                        Gambar ini akan menggantikan warna merah default pada ID Card Siswa.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB KONTAK --}}
            <div x-show="activeTab === 'kontak'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea name="alamat" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gold-500 focus:ring-gold-500">{{ old('alamat', $profile->alamat) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Embed Google Maps (HTML)</label>
                        <textarea name="google_map_embed" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gold-500 font-mono text-xs bg-gray-50">{{ old('google_map_embed', $profile->google_map_embed) }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Copy "Embed a map" dari Google Maps dan paste di sini (iframe).</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div><label class="block text-sm font-bold text-gray-700">Email</label><input type="email" name="email_lpk" value="{{ old('email_lpk', $profile->email_lpk) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gold-500"></div>
                        <div><label class="block text-sm font-bold text-gray-700">Website</label><input type="text" name="website_url" value="{{ old('website_url', $profile->website_url) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gold-500"></div>
                        <div><label class="block text-sm font-bold text-gray-700">Telepon Kantor</label><input type="text" name="telepon_lpk" value="{{ old('telepon_lpk', $profile->telepon_lpk) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gold-500"></div>
                        <div><label class="block text-sm font-bold text-gray-700">WhatsApp Admin</label><input type="text" name="nomor_wa" value="{{ old('nomor_wa', $profile->nomor_wa) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gold-500"></div>
                    </div>

                    <hr class="my-6 border-gray-200">
                    <h4 class="font-bold text-gray-700 uppercase text-xs tracking-wider mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-share-nodes text-gold-500"></i> Media Sosial
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center gap-2 group">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-focus-within:bg-blue-100 transition"><i class="fa-brands fa-facebook text-blue-600"></i></div>
                            <input type="text" name="facebook_url" value="{{ old('facebook_url', $profile->facebook_url) }}" class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="URL Facebook">
                        </div>
                        <div class="flex items-center gap-2 group">
                            <div class="w-8 h-8 rounded-full bg-pink-50 flex items-center justify-center group-focus-within:bg-pink-100 transition"><i class="fa-brands fa-instagram text-pink-600"></i></div>
                            <input type="text" name="instagram_url" value="{{ old('instagram_url', $profile->instagram_url) }}" class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-500" placeholder="URL Instagram">
                        </div>
                        <div class="flex items-center gap-2 group">
                            <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center group-focus-within:bg-gray-200 transition"><i class="fa-brands fa-tiktok text-black"></i></div>
                            <input type="text" name="tiktok_url" value="{{ old('tiktok_url', $profile->tiktok_url) }}" class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-gray-500 focus:ring-gray-500" placeholder="URL TikTok">
                        </div>
                        <div class="flex items-center gap-2 group">
                            <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center group-focus-within:bg-red-100 transition"><i class="fa-brands fa-youtube text-red-600"></i></div>
                            <input type="text" name="youtube_url" value="{{ old('youtube_url', $profile->youtube_url) }}" class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="URL YouTube">
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB VISI --}}
            <div x-show="activeTab === 'visi'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Visi</label>
                        <textarea name="visi" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gold-500 focus:ring-gold-500">{{ old('visi', $profile->visi) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Misi</label>
                        <textarea name="misi" rows="8" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gold-500 focus:ring-gold-500" placeholder="Gunakan enter untuk poin baru">{{ old('misi', $profile->misi) }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-gold-600 to-gold-500 text-white rounded-xl font-bold shadow-lg hover:from-gold-700 hover:to-gold-600 transition-all transform hover:-translate-y-0.5 hover:shadow-xl focus:ring-2 focus:ring-offset-2 focus:ring-gold-500">
                <i class="fa-solid fa-save mr-2"></i> Simpan Semua Perubahan
            </button>
        </div>

    </div>
</form>
@endsection