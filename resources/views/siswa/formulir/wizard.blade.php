@extends('layouts.app')

@section('header', 'Formulir Pendaftaran')

@section('content')

{{-- ALERT / NOTIFIKASI SUKSES --}}
@if (session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm flex items-center">
        <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
@endif

{{-- ALERT / NOTIFIKASI ERROR --}}
@if (session('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm flex items-center">
        <i class="fa-solid fa-ban mr-2"></i> {{ session('error') }}
    </div>
@endif

{{-- CEK STATUS UNTUK LOCKING FORM --}}
@php
    $isEditable = in_array($student->status, ['Mendaftar', 'Perlu Revisi']);
@endphp

{{-- BANNER INFORMASI MODE BACA --}}
@if(!$isEditable)
    <div class="mb-8 bg-gray-600 text-white px-6 py-4 rounded-xl shadow-lg flex items-center justify-between">
        <div class="flex items-center">
            <i class="fa-solid fa-lock text-2xl mr-4"></i>
            <div>
                <h3 class="font-bold text-lg">Mode Baca Saja (Read-Only)</h3>
                <p class="text-gray-200 text-sm">Data Anda sedang diverifikasi. Anda tidak dapat mengubah data saat ini.</p>
            </div>
        </div>
        <div class="bg-white text-gray-800 px-3 py-1 rounded text-xs font-bold uppercase">
            Status: {{ $student->status }}
        </div>
    </div>
@endif

{{-- AREA CATATAN REVISI (Jika Ada) --}}
@if($student->status == 'Perlu Revisi' && $student->admin_note)
    <div class="mb-8 bg-orange-50 border-l-4 border-orange-500 p-4 rounded shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-circle-exclamation text-orange-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-orange-700 font-bold">Catatan Perbaikan dari Admin:</p>
                <p class="text-sm text-orange-600 mt-1">{{ $student->admin_note }}</p>
            </div>
        </div>
    </div>
@endif

<div x-data="wizardForm()" class="max-w-5xl mx-auto pb-20">

    {{-- STEPPER INDICATOR --}}
    <div class="mb-8">
        <div class="flex items-center justify-between relative">
            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-200 -z-10"></div>
            <template x-for="i in 5">
                <div class="flex flex-col items-center cursor-pointer" @click="step = i">
                    <div :class="step >= i ? 'bg-gold-500 text-white border-gold-500' : 'bg-white text-gray-400 border-gray-300'"
                         class="w-10 h-10 rounded-full flex items-center justify-center font-bold border-2 transition-all duration-300 relative z-10">
                        <span x-text="i"></span>
                    </div>
                    <span class="text-[10px] sm:text-xs mt-2 font-bold uppercase tracking-wide hidden sm:block" 
                          :class="step >= i ? 'text-gold-600' : 'text-gray-400'" 
                          x-text="getStepName(i)"></span>
                </div>
            </template>
        </div>
        <div class="text-center mt-4 sm:hidden">
            <span class="text-sm font-bold text-gold-600 uppercase" x-text="getStepName(step)"></span>
        </div>
    </div>

    <form action="{{ route('siswa.formulir.update') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 sm:p-8 rounded-2xl shadow-lg border border-gray-100">
        @csrf
        @method('PUT')

        <fieldset {{ !$isEditable ? 'disabled' : '' }}>

            {{-- STEP 1: DATA PRIBADI --}}
            <div x-show="step === 1" x-transition.opacity>
                <h3 class="text-xl font-serif font-bold text-gray-900 mb-6 border-b pb-2">Langkah 1: Data Pribadi</h3>
                
                <div class="bg-blue-50 border border-blue-200 p-4 rounded-xl mb-6">
                    <label class="block text-sm font-bold text-blue-800 mb-2">Pilih Program Pelatihan <span class="text-red-500">*</span></label>
                    <select name="program_pelatihan_id" class="w-full rounded-lg border-blue-300 focus:border-gold-500 focus:ring-gold-500 bg-white py-2.5 px-3 text-gray-700 font-medium disabled:bg-gray-100 disabled:text-gray-500">
                        <option value="">-- Pilih Program yang Diminati --</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ $student->program_pelatihan_id == $program->id ? 'selected' : '' }}>
                                {{ $program->judul }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- DATA FISIK & STATUS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- [UPDATE] field nama -> nama_lengkap --}}
                    <div><label class="block text-sm font-medium mb-1">Nama Lengkap (Sesuai KTP)</label><input type="text" name="nama_lengkap" value="{{ $student->nama_lengkap }}" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500" required></div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium mb-1">Tinggi (cm)</label><input type="number" name="tinggi_badan" value="{{ $student->tinggi_badan }}" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500"></div>
                        <div><label class="block text-sm font-medium mb-1">Berat (kg)</label><input type="number" name="berat_badan" value="{{ $student->berat_badan }}" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500"></div>
                    </div>

                    {{-- [NEW] Jenis Kelamin --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500">
                            <option value="">-- Pilih --</option>
                            <option value="Laki-laki" {{ $student->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ $student->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Status Pernikahan</label>
                        <select name="status_pernikahan" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500">
                            <option value="Belum Menikah" {{ $student->status_pernikahan == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                            <option value="Menikah" {{ $student->status_pernikahan == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                            <option value="Janda/Duda" {{ $student->status_pernikahan == 'Janda/Duda' ? 'selected' : '' }}>Janda/Duda</option>
                        </select>
                    </div>

                    <div><label class="block text-sm font-medium mb-1">Tempat Lahir</label><input type="text" name="tempat_lahir" value="{{ $student->tempat_lahir }}" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500"></div>
                    <div><label class="block text-sm font-medium mb-1">Tanggal Lahir</label><input type="date" name="tanggal_lahir" value="{{ optional($student->tanggal_lahir)->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500"></div>
                    
                    {{-- [NEW] Agama --}}
                    <div><label class="block text-sm font-medium mb-1">Agama</label><input type="text" name="agama" value="{{ $student->agama }}" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500"></div>
                </div>

                {{-- IDENTITAS & KONTAK --}}
                <h4 class="font-bold text-gray-700 mb-4 mt-6 border-b pb-2">Identitas & Kontak</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div><label class="block text-sm font-medium mb-1">Nomor KTP</label><input type="text" name="nomor_ktp" value="{{ $student->nomor_ktp }}" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500"></div>
                    <div><label class="block text-sm font-medium mb-1">Nomor KK (Opsional)</label><input type="text" name="nomor_kk" value="{{ $student->nomor_kk }}" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500"></div>
                    
                    <div><label class="block text-sm font-medium mb-1">Alamat Email</label><input type="email" name="email" value="{{ $student->email }}" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500"></div>
                    <div><label class="block text-sm font-medium mb-1">No. HP Peserta (WhatsApp)</label><input type="text" name="no_hp_peserta" value="{{ $student->no_hp_peserta }}" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500"></div>
                    
                    <div><label class="block text-sm font-medium mb-1">No. HP Orang Tua</label><input type="text" name="no_hp_ortu" value="{{ $student->no_hp_ortu }}" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500"></div>
                    <div><label class="block text-sm font-medium mb-1">Golongan Darah</label><input type="text" name="golongan_darah" value="{{ $student->golongan_darah }}" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500"></div>
                    
                    <div><label class="block text-sm font-medium mb-1">Nomor Paspor (Jika ada)</label><input type="text" name="nomor_paspor" value="{{ $student->nomor_paspor }}" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500"></div>
                    <div><label class="block text-sm font-medium mb-1">Nomor NPWP (Jika ada)</label><input type="text" name="nomor_npwp" value="{{ $student->nomor_npwp }}" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500"></div>
                </div>

                {{-- ALAMAT --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-1">Alamat Sesuai KTP</label>
                        <textarea name="alamat_ktp" rows="3" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500">{{ $student->alamat_ktp }}</textarea>
                        <div class="grid grid-cols-2 gap-2 mt-2">
                             <input type="text" name="kota_ktp" value="{{ $student->kota_ktp }}" placeholder="Kota/Kab" class="text-sm rounded border-gray-300 disabled:bg-gray-100">
                             <input type="text" name="provinsi_ktp" value="{{ $student->provinsi_ktp }}" placeholder="Provinsi" class="text-sm rounded border-gray-300 disabled:bg-gray-100">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Alamat Domisili</label>
                        <textarea name="alamat_domisili" rows="3" class="w-full rounded-lg border-gray-300 focus:border-gold-500 disabled:bg-gray-100 disabled:text-gray-500">{{ $student->alamat_domisili }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">*Isi jika berbeda dengan KTP</p>
                    </div>
                </div>
            </div>

            {{-- STEP 2: PENDIDIKAN --}}
            <div x-show="step === 2" x-transition.opacity>
                <h3 class="text-xl font-serif font-bold text-gray-900 mb-6 border-b pb-2">Langkah 2: Riwayat Pendidikan</h3>

                <template x-for="(edu, index) in educations" :key="index">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-4 relative group">
                        @if($isEditable)
                            <button type="button" @click="removeEducation(index)" class="absolute top-2 right-2 text-red-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold text-gray-500">Jenis</label>
                                <select :name="'pendidikan['+index+'][kategori]'" x-model="edu.kategori" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                                    <option value="Formal">Formal (Sekolah)</option>
                                    <option value="Non-Formal">Non-Formal (Kursus)</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500">Tingkat</label>
                                <select :name="'pendidikan['+index+'][tingkat]'" x-model="edu.tingkat" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                                    <option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA/SMK">SMA/SMK</option><option value="D3">D3</option><option value="S1">S1</option><option value="Kursus">Kursus/Pelatihan</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs font-bold text-gray-500">Nama Sekolah/Institusi</label>
                                <input type="text" :name="'pendidikan['+index+'][nama_institusi]'" x-model="edu.nama_institusi" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500">Jurusan / Materi</label>
                                <input type="text" :name="'pendidikan['+index+'][jurusan]'" x-model="edu.jurusan" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500">Lokasi</label>
                                <input type="text" :name="'pendidikan['+index+'][lokasi]'" x-model="edu.lokasi" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-xs font-bold text-gray-500" x-text="edu.kategori == 'Formal' ? 'Masuk (Thn)' : 'Durasi'"></label>
                                    <input type="text" :name="'pendidikan['+index+'][tahun_masuk]'" x-model="edu.tahun_masuk" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                                </div>
                                <div x-show="edu.kategori == 'Formal'">
                                    <label class="text-xs font-bold text-gray-500">Lulus (Thn)</label>
                                    <input type="text" :name="'pendidikan['+index+'][tahun_lulus]'" x-model="edu.tahun_lulus" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                                </div>
                            </div>
                             <div x-show="edu.kategori == 'Formal'">
                                <label class="text-xs font-bold text-gray-500">Nilai Rata-rata</label>
                                <input type="text" :name="'pendidikan['+index+'][nilai_rata_rata]'" x-model="edu.nilai_rata_rata" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                            </div>
                        </div>
                    </div>
                </template>

                @if($isEditable)
                    <button type="button" @click="addEducation()" class="w-full py-3 border-2 border-dashed border-gold-300 text-gold-600 rounded-xl hover:bg-gold-50 font-bold"> + Tambah Pendidikan </button>
                @endif
            </div>

            {{-- STEP 3: KELUARGA --}}
            <div x-show="step === 3" x-transition.opacity>
                <h3 class="text-xl font-serif font-bold text-gray-900 mb-6 border-b pb-2">Langkah 3: Data Keluarga</h3>
                
                <template x-for="(fam, index) in families" :key="index">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-4 relative">
                        @if($isEditable)
                            <button type="button" @click="removeFamily(index)" class="absolute top-2 right-2 text-red-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="text-xs font-bold text-gray-500">Hubungan</label>
                                <select :name="'keluarga['+index+'][hubungan]'" x-model="fam.hubungan" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                                    <option value="Ayah">Ayah</option><option value="Ibu">Ibu</option><option value="Saudara">Saudara</option><option value="Pasangan">Pasangan</option><option value="Anak">Anak</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500">L/P</label>
                                <select :name="'keluarga['+index+'][jenis_kelamin]'" x-model="fam.jenis_kelamin" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                                    <option value="L">L</option><option value="P">P</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs font-bold text-gray-500">Nama</label>
                                <input type="text" :name="'keluarga['+index+'][nama]'" x-model="fam.nama" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                            </div>
                            
                            {{-- LOGIKA: Tgl Lahir trigger Hitung Usia Otomatis --}}
                            <div>
                                <label class="text-xs font-bold text-gray-500">Tgl Lahir</label>
                                <input type="date" :name="'keluarga['+index+'][tanggal_lahir]'" x-model="fam.tanggal_lahir" 
                                       @change="calculateAge(index)"
                                       class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                            </div>
                            
                            {{-- [NEW] Kolom Usia --}}
                            <div>
                                <label class="text-xs font-bold text-gray-500">Usia (Thn)</label>
                                <input type="number" :name="'keluarga['+index+'][usia]'" x-model="fam.usia" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200 bg-white">
                            </div>

                            <div>
                                <label class="text-xs font-bold text-gray-500">Pendidikan</label>
                                <input type="text" :name="'keluarga['+index+'][pendidikan]'" x-model="fam.pendidikan" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500">Pekerjaan</label>
                                <input type="text" :name="'keluarga['+index+'][pekerjaan]'" x-model="fam.pekerjaan" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                            </div>
                            <div class="md:col-span-4">
                                <label class="text-xs font-bold text-gray-500">Penghasilan Rata-rata / Bulan</label>
                                <input type="text" :name="'keluarga['+index+'][penghasilan]'" x-model="fam.penghasilan" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200" placeholder="Contoh: Rp 3.000.000">
                            </div>
                        </div>
                    </div>
                </template>
                
                @if($isEditable)
                    <button type="button" @click="addFamily()" class="w-full py-3 border-2 border-dashed border-gold-300 text-gold-600 rounded-xl hover:bg-gold-50 font-bold"> + Tambah Keluarga </button>
                @endif
            </div>

            {{-- STEP 4: PENGALAMAN --}}
            <div x-show="step === 4" x-transition.opacity>
                <h3 class="text-xl font-serif font-bold text-gray-900 mb-6 border-b pb-2">Langkah 4: Pengalaman Kerja</h3>

                {{-- [NEW] CHECKBOX PERNAH BEKERJA --}}
                <div class="mb-6 bg-yellow-50 p-4 rounded-lg border border-yellow-200 flex items-center">
                    <input type="checkbox" id="pernah_bekerja" name="pernah_bekerja" x-model="pernahBekerja" class="w-5 h-5 text-gold-600 rounded border-gray-300 focus:ring-gold-500">
                    <label for="pernah_bekerja" class="ml-3 font-bold text-gray-700 cursor-pointer">
                        Apakah Anda Pernah Bekerja Sebelumnya?
                    </label>
                </div>

                {{-- LOGIC: HANYA TAMPIL JIKA PERNAH BEKERJA --}}
                <div x-show="pernahBekerja" x-transition>
                    <template x-for="(exp, index) in experiences" :key="index">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-4 relative">
                            @if($isEditable)
                                <button type="button" @click="removeExperience(index)" class="absolute top-2 right-2 text-red-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                            @endif
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                 <div>
                                    <label class="text-xs font-bold text-gray-500">Tipe</label>
                                    <select :name="'pengalaman['+index+'][tipe]'" x-model="exp.tipe" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                                        <option value="Pekerjaan">Pekerjaan</option><option value="Organisasi">Organisasi</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500">Nama Instansi</label>
                                    <input type="text" :name="'pengalaman['+index+'][nama_instansi]'" x-model="exp.nama_instansi" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                                </div>
                                <div x-show="exp.tipe == 'Pekerjaan'">
                                    <label class="text-xs font-bold text-gray-500">Jenis Usaha</label>
                                    <input type="text" :name="'pengalaman['+index+'][jenis_usaha]'" x-model="exp.jenis_usaha" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500">Posisi / Jabatan</label>
                                    <input type="text" :name="'pengalaman['+index+'][posisi]'" x-model="exp.posisi" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                                </div>
                                <div class="md:col-span-2">
                                     <label class="text-xs font-bold text-gray-500">Alamat Tempat</label>
                                     <input type="text" :name="'pengalaman['+index+'][alamat_instansi]'" x-model="exp.alamat_instansi" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                                </div>
                                <div class="md:col-span-2 grid grid-cols-2 gap-4" x-show="exp.tipe == 'Pekerjaan'">
                                    <div><label class="text-xs font-bold text-gray-500">Gaji Awal</label><input type="text" :name="'pengalaman['+index+'][gaji_awal]'" x-model="exp.gaji_awal" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200"></div>
                                    <div><label class="text-xs font-bold text-gray-500">Gaji Akhir</label><input type="text" :name="'pengalaman['+index+'][gaji_akhir]'" x-model="exp.gaji_akhir" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200"></div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 md:col-span-2">
                                    <div><label class="text-xs font-bold text-gray-500">Mulai (Tgl)</label><input type="date" :name="'pengalaman['+index+'][tanggal_mulai]'" x-model="exp.tanggal_mulai" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200"></div>
                                    <div><label class="text-xs font-bold text-gray-500">Selesai (Tgl)</label><input type="date" :name="'pengalaman['+index+'][tanggal_selesai]'" x-model="exp.tanggal_selesai" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200"></div>
                                </div>
                                <div class="md:col-span-2" x-show="exp.tipe == 'Pekerjaan'">
                                    <label class="text-xs font-bold text-gray-500">Alasan Berhenti</label>
                                    <input type="text" :name="'pengalaman['+index+'][alasan_berhenti]'" x-model="exp.alasan_berhenti" class="w-full rounded-md border-gray-300 text-sm disabled:bg-gray-200">
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    @if($isEditable)
                        <button type="button" @click="addExperience()" class="w-full py-3 border-2 border-dashed border-gold-300 text-gold-600 rounded-xl hover:bg-gold-50 font-bold"> + Tambah Pengalaman </button>
                    @endif
                </div>

                <div x-show="!pernahBekerja" class="text-center text-gray-500 italic py-8 border-2 border-dashed rounded-xl">
                    Anda menyatakan tidak memiliki pengalaman kerja sebelumnya. Silakan lanjut ke tahap berikutnya.
                </div>
            </div>

            {{-- STEP 5: UPLOAD DOKUMEN --}}
            <div x-show="step === 5" x-transition.opacity>
                <h3 class="text-xl font-serif font-bold text-gray-900 mb-6 border-b pb-2">Langkah 5: Upload Dokumen</h3>
                
                <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-6 flex items-start">
                    <div class="mr-4 text-blue-500 text-3xl"><i class="fa-solid fa-camera"></i></div>
                    <div class="w-full">
                        <label class="block font-bold text-gray-700 mb-1">Pas Foto Ukuran 3,5 x 4,5 <span class="text-red-500">*</span></label>
                         @if($student->foto)
                            <div class="mb-2 text-xs text-green-600 font-bold"><i class="fa-solid fa-check-circle"></i> Foto tersimpan</div>
                        @endif
                        <input type="file" name="foto" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 disabled:opacity-50">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach([
                        'file_ktp' => 'Scan KTP', 
                        'file_kk' => 'Scan Kartu Keluarga', 
                        'file_ijazah' => 'Scan Ijazah Terakhir',
                        'file_sertifikat_jlpt' => 'Sertifikat JLPT (Jika ada)',
                        'file_rekomendasi_sekolah' => 'Surat Rekomendasi Sekolah',
                        'file_izin_ortu' => 'Surat Izin Orang Tua'
                    ] as $field => $label)
                    <div class="border p-4 rounded-lg bg-gray-50">
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ $label }}</label>
                        @if($student->$field)
                            <div class="mb-2 text-xs text-green-600 font-bold flex items-center">
                                <i class="fa-solid fa-check-circle mr-1"></i> Tersimpan
                                <a href="{{ asset('storage/'.$student->$field) }}" target="_blank" class="ml-2 text-blue-600 hover:underline">Lihat</a>
                            </div>
                        @endif
                        <input type="file" name="{{ $field }}" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gold-50 file:text-gold-700 hover:file:bg-gold-100 disabled:opacity-50">
                    </div>
                    @endforeach
                </div>

                {{-- [NEW] TANDA TANGAN SECTION --}}
                <div class="mt-12 flex justify-end">
                    <div class="text-center w-64">
                         <div class="flex items-center gap-2 mb-4 justify-center">
                            <input type="text" name="kota_pembuatan" value="{{ $student->kota_pembuatan }}" placeholder="Nama Kota..." class="border-b-2 border-gray-400 border-t-0 border-x-0 bg-transparent text-center focus:ring-0 w-32 px-1" required>
                            <span class="text-gray-600">, {{ now()->format('d-m-Y') }}</span>
                         </div>
                         <div class="h-24 border-b border-black mb-2 flex items-end justify-center text-gray-400 text-xs pb-2">
                             (Tanda Tangan)
                         </div>
                         <p class="font-bold uppercase">{{ $student->nama_lengkap ?? '(Nama Lengkap)' }}</p>
                         <p class="text-xs">Peserta</p>
                    </div>
                </div>

                @if($isEditable)
                    <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                        <p class="text-sm text-yellow-800 font-medium text-center">
                            <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                            Dengan menekan tombol simpan, saya menyatakan bahwa informasi tersebut di atas adalah benar. Apabila LPK HACHIMITSU menemukan informasi tidak benar, kepesertaan saya dapat dibatalkan.
                        </p>
                    </div>
                    <div class="text-center mt-8">
                        <button type="submit" class="px-12 py-4 bg-gold-600 text-white text-lg font-bold rounded-full shadow-xl hover:bg-gold-700 transition transform hover:-translate-y-1">
                            SIMPAN & KIRIM FORMULIR
                        </button>
                    </div>
                @else
                    <div class="mt-8 text-center text-gray-500 italic">
                        <p>Tombol simpan dinonaktifkan karena formulir sedang dalam mode baca.</p>
                    </div>
                @endif
            </div>

        </fieldset> {{-- END FIELDSET --}}

        {{-- NAVIGASI BAWAH --}}
        <div class="flex justify-between mt-8 pt-6 border-t border-gray-100">
            <button type="button" x-show="step > 1" @click="step--" class="px-6 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 font-semibold">
                <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
            </button>
            <div class="flex-1"></div> 
            <button type="button" x-show="step < 5" @click="step++" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                Selanjutnya <i class="fa-solid fa-arrow-right ml-2"></i>
            </button>
        </div>

    </form>
</div>

@endsection

@section('scripts')
<script>
    function wizardForm() {
        return {
            step: 1,
            // [NEW] Logic Pernah Bekerja
            pernahBekerja: {{ $student->pernah_bekerja ? 'true' : 'false' }},
            
            // Load existing data from DB
            educations: @json($student->educations ?? []),
            families: @json($student->families ?? []),
            experiences: @json($student->experiences ?? []),

            getStepName(i) {
                const names = ['Data Diri', 'Pendidikan', 'Keluarga', 'Pengalaman', 'Dokumen'];
                return names[i-1];
            },
            
            // [NEW] Logic Hitung Usia
            calculateAge(index) {
                const dob = this.families[index].tanggal_lahir;
                if (dob) {
                    const birthDate = new Date(dob);
                    const today = new Date();
                    let age = today.getFullYear() - birthDate.getFullYear();
                    const m = today.getMonth() - birthDate.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }
                    this.families[index].usia = age;
                }
            },

            addEducation() { 
                this.educations.push({ 
                    kategori: 'Formal', tingkat: 'SMA/SMK', nama_institusi: '', jurusan: '', 
                    tahun_masuk: '', tahun_lulus: '', lokasi: '', nilai_rata_rata: '' 
                }); 
            },
            removeEducation(index) { this.educations.splice(index, 1); },
            
            addFamily() { 
                this.families.push({ 
                    hubungan: 'Ayah', jenis_kelamin: 'L', nama: '', pekerjaan: '', 
                    pendidikan: '', tanggal_lahir: '', usia: '', penghasilan: '' 
                }); 
            },
            removeFamily(index) { this.families.splice(index, 1); },

            addExperience() { 
                this.experiences.push({ 
                    tipe: 'Pekerjaan', nama_instansi: '', jenis_usaha: '', alamat_instansi: '',
                    posisi: '', gaji_awal: '', gaji_akhir: '', alasan_berhenti: '', 
                    tanggal_mulai: '', tanggal_selesai: '' 
                }); 
            },
            removeExperience(index) { this.experiences.splice(index, 1); }
        }
    }
</script>
@endsection