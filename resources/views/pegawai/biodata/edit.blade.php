@extends('layouts.app')

@section('header', 'Edit Biodata Saya')

@section('content')

{{-- Pesan Sukses --}}
@if (session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm flex items-center">
        <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
@endif

{{-- Toolbar Atas (Tombol Cetak) --}}
<div class="flex justify-end mb-6">
    <a href="{{ route('pegawai.biodata.print') }}" target="_blank" 
       class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-xl hover:bg-red-100 transition font-bold shadow-sm">
       <i class="fa-solid fa-file-pdf mr-2"></i> Cetak Biodata PDF
    </a>
</div>

{{-- FORM WRAPPER UTAMA --}}
<form action="{{ route('pegawai.biodata.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- ================= KOLOM KIRI (FOTO & INFO STATIS) ================= --}}
        {{-- Bagian ini Tetap Muncul Walaupun Tab Berpindah --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- Foto Profil --}}
            <div class="bg-white p-6 rounded-2xl border border-gray-200 text-center shadow-sm"
                 x-data="{ photoPreview: '{{ $employee->foto ? asset('storage/'.$employee->foto) : null }}' }">
                
                <label class="block text-sm font-bold text-gray-700 mb-4 uppercase tracking-wide">Foto Profil</label>
                
                <div class="relative w-40 h-40 mx-auto mb-4">
                    <img :src="photoPreview ?? 'https://ui-avatars.com/api/?background=random&name={{ urlencode($employee->nama) }}'" 
                         class="w-40 h-40 rounded-full object-cover border-4 border-gold-100 shadow-md">
                    
                    <label for="foto_upload" class="absolute bottom-0 right-0 bg-gold-500 text-white p-2.5 rounded-full shadow-lg cursor-pointer hover:bg-gold-600 transition-all">
                        <i class="fa-solid fa-camera"></i>
                        <input type="file" id="foto_upload" name="foto" class="hidden"
                               @change="const file = $event.target.files[0]; 
                                        const reader = new FileReader(); 
                                        reader.onload = (e) => { photoPreview = e.target.result }; 
                                        reader.readAsDataURL(file)">
                    </label>
                </div>
                <p class="text-xs text-gray-500">Klik ikon kamera untuk ubah.<br>Format JPG/PNG, Maks 2MB.</p>
            </div>

            {{-- Info Kepegawaian (Readonly) --}}
            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">NIP</label>
                    <input type="text" value="{{ $employee->nip ?? '-' }}" readonly 
                           class="w-full bg-white border-gray-200 rounded-lg text-gray-500 font-mono text-sm cursor-not-allowed focus:ring-0">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Jabatan</label>
                    <input type="text" value="{{ $employee->jabatan ?? '-' }}" readonly 
                           class="w-full bg-white border-gray-200 rounded-lg text-gray-500 font-medium text-sm cursor-not-allowed focus:ring-0">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Status</label>
                    <input type="text" value="{{ $employee->status_kepegawaian ?? '-' }}" readonly 
                           class="w-full bg-white border-gray-200 rounded-lg text-gray-500 text-sm cursor-not-allowed focus:ring-0">
                </div>
            </div>
        </div>

        {{-- ================= KOLOM KANAN (TABS CONTENT) ================= --}}
        {{-- Menggunakan AlpineJS untuk Tab Switching --}}
        <div class="lg:col-span-2" x-data="{ activeTab: 'pribadi' }">
            
            {{-- NAVIGASI TAB --}}
            <div class="bg-white rounded-t-2xl border border-gray-200 border-b-0 p-2 flex overflow-x-auto gap-2 shadow-sm">
                
                <button type="button" @click="activeTab = 'pribadi'"
                    :class="activeTab === 'pribadi' ? 'bg-gold-500 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-50'"
                    class="flex-1 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 whitespace-nowrap">
                    <i class="fa-solid fa-user mr-2"></i> Data Pribadi
                </button>

                <button type="button" @click="activeTab = 'alamat'"
                    :class="activeTab === 'alamat' ? 'bg-gold-500 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-50'"
                    class="flex-1 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 whitespace-nowrap">
                    <i class="fa-solid fa-map-location-dot mr-2"></i> Alamat & Kontak
                </button>

                <button type="button" @click="activeTab = 'sosmed'"
                    :class="activeTab === 'sosmed' ? 'bg-gold-500 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-50'"
                    class="flex-1 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 whitespace-nowrap">
                    <i class="fa-solid fa-share-nodes mr-2"></i> Media Sosial
                </button>

            </div>

            {{-- KONTEN TAB --}}
            <div class="bg-white p-6 rounded-b-2xl border border-gray-200 shadow-sm min-h-[400px]">

                {{-- TAB 1: DATA PRIBADI --}}
                <div x-show="activeTab === 'pribadi'" x-transition.opacity.duration.300ms class="space-y-5">
                    <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Informasi Personal</h3>
                    
                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama', $employee->nama) }}" required
                               class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                    </div>

                    {{-- TTL --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $employee->tempat_lahir) }}" 
                                   class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($employee->tanggal_lahir)->format('Y-m-d')) }}" 
                                   class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                    </div>

                    {{-- Gender & Agama --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                                <option value="">-- Pilih --</option>
                                <option value="L" {{ old('jenis_kelamin', $employee->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $employee->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Agama</label>
                            <select name="agama" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                                <option value="">-- Pilih --</option>
                                @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'] as $agama)
                                    <option value="{{ $agama }}" {{ old('agama', $employee->agama) == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Pendidikan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                        <select name="pendidikan_terakhir" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                            <option value="">-- Pilih --</option>
                            @foreach(['SD', 'SMP', 'SMA/SMK', 'D1', 'D3', 'S1', 'S2', 'S3'] as $edu)
                                <option value="{{ $edu }}" {{ old('pendidikan_terakhir', $employee->pendidikan_terakhir) == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- TAB 2: ALAMAT & KONTAK --}}
                <div x-show="activeTab === 'alamat'" x-transition.opacity.duration.300ms class="space-y-5" style="display: none;">
                    <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Lokasi & Kontak</h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Domisili Lengkap</label>
                        <textarea name="alamat" rows="3" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500" placeholder="Nama Jalan, Gg, No. Rumah, RT/RW">{{ old('alamat', $employee->alamat) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kota / Kabupaten</label>
                            <input type="text" name="kota" value="{{ old('kota', $employee->kota) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                            <input type="text" name="provinsi" value="{{ old('provinsi', $employee->provinsi) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                            <input type="text" name="kode_pos" value="{{ old('kode_pos', $employee->kode_pos) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon / WhatsApp</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-brands fa-whatsapp text-green-500"></i>
                                </div>
                                <input type="text" name="telepon" value="{{ old('telepon', $employee->telepon) }}" 
                                       class="pl-10 w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 3: MEDIA SOSIAL --}}
                <div x-show="activeTab === 'sosmed'" x-transition.opacity.duration.300ms class="space-y-5" style="display: none;">
                    <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Jejaring Sosial</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn Profile</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-brands fa-linkedin text-blue-700 text-lg"></i>
                            </div>
                            <input type="text" name="linkedin" value="{{ old('linkedin', $employee->linkedin) }}" 
                                   class="pl-10 w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500" 
                                   placeholder="https://www.linkedin.com/in/username">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instagram Profile</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-brands fa-instagram text-pink-600 text-lg"></i>
                            </div>
                            <input type="text" name="instagram" value="{{ old('instagram', $employee->instagram) }}" 
                                   class="pl-10 w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500" 
                                   placeholder="https://www.instagram.com/username">
                        </div>
                    </div>
                </div>

            </div>

            {{-- TOMBOL SIMPAN (FIXED DI BAWAH TABS) --}}
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-gold-500 text-white font-bold rounded-xl shadow-lg hover:bg-gold-600 hover:shadow-xl transition-all transform hover:-translate-y-0.5 flex items-center">
                    <i class="fa-solid fa-save mr-2"></i> Simpan Semua Data
                </button>
            </div>

        </div>
    </div>
</form>
@endsection