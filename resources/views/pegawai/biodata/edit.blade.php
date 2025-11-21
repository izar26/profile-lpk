@extends('layouts.app')

@section('header', 'Edit Biodata Saya')

@section('content')

{{-- Pesan Sukses --}}
@if (session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm flex items-center">
        <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
@endif

{{-- [BARU] Toolbar Atas (Tombol Cetak) --}}
<div class="flex justify-end mb-6">
    <a href="{{ route('siswa.biodata.print') }}" target="_blank" 
       class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-xl hover:bg-red-100 transition font-bold shadow-sm">
       <i class="fa-solid fa-file-pdf mr-2"></i> Cetak Biodata PDF
    </a>
</div>

<form action="{{ route('siswa.biodata.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1 space-y-6">
            
            <div class="bg-white p-6 rounded-2xl border border-gray-200 text-center shadow-sm"
                 x-data="{ photoPreview: '{{ $student->foto ? asset('storage/'.$student->foto) : null }}' }">
                
                <label class="block text-sm font-bold text-gray-700 mb-4 uppercase tracking-wide">Foto Profil</label>
                
                <div class="relative w-40 h-40 mx-auto mb-4">
                    <img :src="photoPreview ?? 'https://ui-avatars.com/api/?background=random&name={{ urlencode($student->nama) }}'" 
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
                <p class="text-xs text-gray-500">Format JPG/PNG, Maks 2MB.</p>
            </div>

            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">NIK (Nomor Induk Kependudukan)</label>
                    <input type="text" value="{{ $student->NIK ?? '-' }}" readonly 
                           class="w-full bg-white border-gray-200 rounded-lg text-gray-500 font-mono text-sm cursor-not-allowed focus:ring-0">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Program Pelatihan</label>
                    <input type="text" value="{{ $student->program->judul ?? '-' }}" readonly 
                           class="w-full bg-white border-gray-200 rounded-lg text-gray-500 font-medium text-sm cursor-not-allowed focus:ring-0">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Email Akun</label>
                    <input type="text" value="{{ $student->email ?? '-' }}" readonly 
                           class="w-full bg-white border-gray-200 rounded-lg text-gray-500 text-sm cursor-not-allowed focus:ring-0">
                </div>
                <div class="text-xs text-gray-400 italic mt-2">
                    *Hubungi admin jika ada kesalahan pada data di atas.
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-8">
            
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                <h3 class="text-lg font-bold text-gold-600 mb-4 border-b border-gray-100 pb-2 flex items-center">
                    <i class="fa-solid fa-user mr-2"></i> Data Pribadi
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama', $student->nama) }}" required
                               class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $student->tempat_lahir) }}" 
                                   class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($student->tanggal_lahir)->format('Y-m-d')) }}" 
                                   class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                                <option value="">-- Pilih --</option>
                                <option value="L" {{ old('jenis_kelamin', $student->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $student->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Golongan Darah</label>
                            <select name="golongan_darah" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                                <option value="">-- Pilih --</option>
                                <option value="A" {{ old('golongan_darah', $student->golongan_darah) == 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ old('golongan_darah', $student->golongan_darah) == 'B' ? 'selected' : '' }}>B</option>
                                <option value="AB" {{ old('golongan_darah', $student->golongan_darah) == 'AB' ? 'selected' : '' }}>AB</option>
                                <option value="O" {{ old('golongan_darah', $student->golongan_darah) == 'O' ? 'selected' : '' }}>O</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Agama</label>
                            <select name="agama" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                                <option value="">-- Pilih --</option>
                                <option value="Islam" {{ old('agama', $student->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('agama', $student->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ old('agama', $student->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ old('agama', $student->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Buddha" {{ old('agama', $student->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                <option value="Konghucu" {{ old('agama', $student->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon / WA</label>
                            <input type="text" name="telepon" value="{{ old('telepon', $student->telepon) }}" 
                                   class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                <h3 class="text-lg font-bold text-gold-600 mb-4 border-b border-gray-100 pb-2 flex items-center">
                    <i class="fa-solid fa-location-dot mr-2"></i> Alamat Lengkap
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Jalan / RT / RW</label>
                        <textarea name="alamat" rows="2" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">{{ old('alamat', $student->alamat) }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kota / Kabupaten</label>
                            <input type="text" name="kota" value="{{ old('kota', $student->kota) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                            <input type="text" name="provinsi" value="{{ old('provinsi', $student->provinsi) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                            <input type="text" name="kode_pos" value="{{ old('kode_pos', $student->kode_pos) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                <h3 class="text-lg font-bold text-gold-600 mb-4 border-b border-gray-100 pb-2 flex items-center">
                    <i class="fa-solid fa-users mr-2"></i> Keluarga & Pendidikan
                </h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah</label>
                            <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $student->nama_ayah) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Ayah</label>
                            <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $student->pekerjaan_ayah) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu</label>
                            <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $student->nama_ibu) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Ibu</label>
                            <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $student->pekerjaan_ibu) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP Orang Tua / Wali</label>
                            <input type="text" name="no_hp_ortu" value="{{ old('no_hp_ortu', $student->no_hp_ortu) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                    </div>

                    <hr class="border-gray-100 my-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sekolah Asal</label>
                            <input type="text" name="sekolah_asal" value="{{ old('sekolah_asal', $student->sekolah_asal) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500" placeholder="Contoh: SMAN 1 Jakarta">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Lulus</label>
                            <input type="text" name="tahun_lulus" value="{{ old('tahun_lulus', $student->tahun_lulus) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 sticky bottom-4 z-10">
                <button type="submit" class="px-8 py-3 bg-gold-500 text-white font-bold rounded-xl shadow-lg hover:bg-gold-600 hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-save mr-2"></i> Simpan Semua Data
                </button>
            </div>

        </div>
    </div>
</form>
@endsection