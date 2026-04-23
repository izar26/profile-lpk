@extends('layouts.app')

@php
    $isAccepted = in_array($student->status ?? '', ['Wawancara', 'Pelatihan', 'Magang', 'Kerja', 'Alumni']);
@endphp
@section('header', $isAccepted ? 'Data Profil Siswa' : 'Formulir Pendaftaran Siswa')

@section('content')

    {{-- --- BAGIAN NOTIFIKASI --- --}}
    <div class="max-w-5xl mx-auto mb-6">
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex items-center animate-fade-in-down">
                <div class="text-green-500 text-2xl mr-4"><i class="fa-solid fa-circle-check"></i></div>
                <div>
                    <h4 class="font-bold text-green-800">Berhasil!</h4>
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex items-center animate-fade-in-down">
                <div class="text-red-500 text-2xl mr-4"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div>
                    <h4 class="font-bold text-red-800">Gagal Menyimpan</h4>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm shadow-sm">
                <p class="font-bold mb-1"><i class="fa-solid fa-list-check mr-2"></i>Mohon perbaiki isian berikut:</p>
                <ul class="list-disc list-inside ml-2 text-xs sm:text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- --- CEK STATUS READ ONLY --- --}}
    @php
        $isEditable = !in_array($student->status, ['Menunggu Verifikasi', 'Ditolak']);
        $isAccepted = in_array($student->status, ['Wawancara', 'Pelatihan', 'Magang', 'Kerja', 'Alumni']);
    @endphp

    @if(!$isEditable)
        <div
            class="max-w-5xl mx-auto mb-8 bg-slate-800 text-white px-6 py-4 rounded-xl shadow-lg flex flex-col md:flex-row items-center justify-between border-l-4 border-gold-500">
            <div class="flex items-center mb-3 md:mb-0">
                <i class="fa-solid fa-lock text-3xl mr-4 text-gold-500"></i>
                <div>
                    <h3 class="font-bold text-lg">Mode Baca Saja (Read-Only)</h3>
                    <p class="text-slate-300 text-sm">Data Anda sedang dalam proses verifikasi atau sudah diterima.</p>
                </div>
            </div>
            <span class="bg-white text-slate-900 px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow">
                Status: {{ $student->status }}
            </span>
        </div>
    @endif

    {{-- --- WIZARD FORM UTAMA --- --}}
    <div x-data="wizardForm()" class="max-w-5xl mx-auto pb-24">

        {{-- 1. STEPPER INDICATOR --}}
        <div class="mb-10 px-4">
            <div class="relative flex items-center justify-between w-full">
                <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-200 -z-10 rounded"></div>
                <div class="absolute left-0 top-1/2 transform -translate-y-1/2 h-1 bg-gold-500 -z-10 rounded transition-all duration-500"
                    :style="'width: ' + ((step - 1) / 4 * 100) + '%'"></div>

                <template x-for="i in 5">
                    <div class="flex flex-col items-center cursor-pointer group" @click="goToStep(i)">
                        <div :class="step >= i ? 'bg-gold-500 text-white border-gold-500 shadow-gold transform scale-110' : 'bg-white text-gray-400 border-gray-300 hover:border-gold-300'"
                            class="w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center font-bold border-2 transition-all duration-300 z-10 shadow-sm">

                            <span x-show="step > i"><i class="fa-solid fa-check"></i></span>
                            <span x-show="step <= i" x-text="i"></span>
                        </div>

                        <span
                            class="text-[10px] md:text-xs mt-2 font-bold uppercase tracking-wide transition-colors duration-300 hidden sm:block"
                            :class="step >= i ? 'text-gold-600' : 'text-gray-400'" x-text="getStepName(i)"></span>
                    </div>
                </template>
            </div>
            <div class="text-center mt-4 sm:hidden">
                <span class="text-sm font-bold text-gold-600 uppercase border-b-2 border-gold-500 pb-1"
                    x-text="getStepName(step)"></span>
            </div>
        </div>

        {{-- 2. FORM AREA --}}
        <form action="{{ route('siswa.formulir.update') }}" method="POST" enctype="multipart/form-data"
            class="bg-white p-6 sm:p-10 rounded-3xl shadow-xl border border-gray-100 relative min-h-[500px]">
            @csrf
            @method('PUT')

            <fieldset {{ !$isEditable ? 'disabled' : '' }}>

                {{-- === STEP 1: DATA PRIBADI === --}}
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="flex items-center justify-between border-b pb-4 mb-6">
                        <h3 class="text-2xl font-serif font-bold text-gray-800">Data Pribadi</h3>
                        <span class="text-xs font-medium text-gray-400 bg-gray-100 px-2 py-1 rounded">Langkah 1 dari
                            5</span>
                    </div>

                    {{-- Program Pelatihan --}}
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-5 rounded-r-lg mb-8 shadow-sm">
                        <label class="block text-sm font-bold text-blue-800 mb-2">Program Pelatihan <span
                                class="text-red-500">*</span></label>
                        <select name="program_pelatihan_id" required
                            class="w-full rounded-lg border-blue-200 focus:border-gold-500 focus:ring-gold-500 bg-white py-3 px-4 text-gray-700 font-medium transition cursor-pointer hover:border-blue-300">
                            <option value="">-- Pilih Program yang Diminati --</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ old('program_pelatihan_id', $student->program_pelatihan_id) == $program->id ? 'selected' : '' }}>
                                    {{ $program->judul }}
                                </option>
                            @endforeach
                        </select>
                        @error('program_pelatihan_id') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Grid Data Diri --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="col-span-1 md:col-span-2">
                            <x-form-input label="Nama Lengkap (Sesuai KTP)" name="nama_lengkap"
                                :value="$student->nama_lengkap" required="true" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <x-form-input label="Tinggi (cm)" name="tinggi_badan" :value="$student->tinggi_badan"
                                type="number" />
                            <x-form-input label="Berat (kg)" name="berat_badan" :value="$student->berat_badan"
                                type="number" />
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Jenis Kelamin <span
                                    class="text-red-500">*</span></label>
                            <select name="jenis_kelamin" required
                                class="form-select w-full rounded-lg border-gray-300 focus:border-gold-500 focus:ring-gold-500">
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin', $student->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin', $student->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Status Pernikahan <span
                                    class="text-red-500">*</span></label>
                            <select name="status_pernikahan" required
                                class="form-select w-full rounded-lg border-gray-300 focus:border-gold-500 focus:ring-gold-500">
                                <option value="Belum Menikah" {{ old('status_pernikahan', $student->status_pernikahan) == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah
                                </option>
                                <option value="Menikah" {{ old('status_pernikahan', $student->status_pernikahan) == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                <option value="Janda/Duda" {{ old('status_pernikahan', $student->status_pernikahan) == 'Janda/Duda' ? 'selected' : '' }}>Janda/Duda</option>
                            </select>
                        </div>

                        <x-form-input label="Tempat Lahir" name="tempat_lahir" :value="$student->tempat_lahir"
                            required="true" />
                        <x-form-input label="Tanggal Lahir" name="tanggal_lahir"
                            :value="optional($student->tanggal_lahir)->format('Y-m-d')" type="date" required="true" />
                        <x-form-input label="Agama" name="agama" :value="$student->agama" required="true" />
                    </div>

                    {{-- Grid Kontak --}}
                    <h4 class="font-bold text-gray-800 text-lg mb-4 mt-8 border-b pb-2 flex items-center">
                        <i class="fa-solid fa-address-card mr-2 text-gold-500"></i> Kontak & Identitas
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <x-form-input label="Nomor KTP (16 Digit)" name="nomor_ktp" :value="$student->nomor_ktp"
                            required="true" maxlength="16" />
                        <x-form-input label="Email Aktif" name="email" :value="$student->email" type="email"
                            required="true" />
                        <x-form-input label="No. HP Peserta (WhatsApp)" name="no_hp_peserta"
                            :value="$student->no_hp_peserta" required="true" />
                        <x-form-input label="No. HP Orang Tua" name="no_hp_ortu" :value="$student->no_hp_ortu"
                            required="true" />
                        <x-form-input label="Golongan Darah" name="golongan_darah" :value="$student->golongan_darah" />
                        <x-form-input label="Nomor Paspor (Opsional)" name="nomor_paspor" :value="$student->nomor_paspor" />
                        <x-form-input label="Nomor NPWP (Opsional)" name="nomor_npwp" :value="$student->nomor_npwp" />
                    </div>

                    {{-- Grid Alamat --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-xl border">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Sesuai KTP <span
                                    class="text-red-500">*</span></label>
                            <textarea name="alamat_ktp" rows="3"
                                class="w-full rounded-lg border-gray-300 focus:border-gold-500"
                                required>{{ old('alamat_ktp', $student->alamat_ktp) }}</textarea>
                            <div class="grid grid-cols-2 gap-2 mt-2">
                                <input type="text" name="kota_ktp" value="{{ old('kota_ktp', $student->kota_ktp) }}"
                                    placeholder="Kota/Kab"
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-gold-500" required>
                                <input type="text" name="provinsi_ktp"
                                    value="{{ old('provinsi_ktp', $student->provinsi_ktp) }}" placeholder="Provinsi"
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-gold-500" required>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl border">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Domisili <span
                                    class="text-red-500">*</span></label>
                            <textarea name="alamat_domisili" rows="3"
                                class="w-full rounded-lg border-gray-300 focus:border-gold-500"
                                required>{{ old('alamat_domisili', $student->alamat_domisili) }}</textarea>
                            <p class="text-xs text-gray-400 mt-2 italic">*Isi jika tempat tinggal berbeda dengan KTP</p>
                        </div>
                    </div>
                </div>

                {{-- === STEP 2: PENDIDIKAN (REPEATER) === --}}
                <div x-show="step === 2" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="flex items-center justify-between border-b pb-4 mb-6">
                        <h3 class="text-2xl font-serif font-bold text-gray-800">Riwayat Pendidikan</h3>
                        <span class="text-xs font-medium text-gray-400 bg-gray-100 px-2 py-1 rounded">Langkah 2 dari
                            5</span>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(edu, index) in educations" :key="index">
                            <div
                                class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm relative group hover:border-gold-300 transition-colors">
                                {{-- Delete Button --}}
                                @if($isEditable)
                                    <button type="button" @click="removeEducation(index)"
                                        class="absolute top-3 right-3 text-gray-300 hover:text-red-500 hover:bg-red-50 w-8 h-8 rounded-full flex items-center justify-center transition"
                                        title="Hapus Data">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                @endif

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                    {{-- Baris 1 --}}
                                    <div class="md:col-span-3">
                                        <label class="text-xs font-bold text-gray-500 uppercase">Tingkat</label>
                                        <select :name="'pendidikan['+index+'][tingkat]'" x-model="edu.tingkat"
                                            class="w-full rounded border-gray-300 text-sm focus:border-gold-500">
                                            <option value="SD">SD</option>
                                            <option value="SMP">SMP</option>
                                            <option value="SMA/SMK">SMA/SMK</option>
                                            <option value="D3">D3</option>
                                            <option value="S1">S1</option>
                                            <option value="Kursus">Kursus</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-5">
                                        <label class="text-xs font-bold text-gray-500 uppercase">Nama Sekolah /
                                            Institusi</label>
                                        <input type="text" :name="'pendidikan['+index+'][nama_institusi]'"
                                            x-model="edu.nama_institusi"
                                            class="w-full rounded border-gray-300 text-sm focus:border-gold-500"
                                            placeholder="Contoh: SMA Negeri 1...">
                                    </div>
                                    <div class="md:col-span-4">
                                        <label class="text-xs font-bold text-gray-500 uppercase">Jurusan</label>
                                        <input type="text" :name="'pendidikan['+index+'][jurusan]'" x-model="edu.jurusan"
                                            class="w-full rounded border-gray-300 text-sm focus:border-gold-500"
                                            placeholder="IPA/IPS/Teknik...">
                                    </div>

                                    {{-- Baris 2 --}}
                                    <div class="md:col-span-2">
                                        <label class="text-xs font-bold text-gray-500 uppercase">Masuk (Thn)</label>
                                        <input type="number" :name="'pendidikan['+index+'][tahun_masuk]'"
                                            x-model="edu.tahun_masuk"
                                            class="w-full rounded border-gray-300 text-sm focus:border-gold-500">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-xs font-bold text-gray-500 uppercase">Lulus (Thn)</label>
                                        <input type="number" :name="'pendidikan['+index+'][tahun_lulus]'"
                                            x-model="edu.tahun_lulus"
                                            class="w-full rounded border-gray-300 text-sm focus:border-gold-500">
                                    </div>
                                    <div class="md:col-span-4">
                                        <label class="text-xs font-bold text-gray-500 uppercase">Lokasi (Kota)</label>
                                        <input type="text" :name="'pendidikan['+index+'][lokasi]'" x-model="edu.lokasi"
                                            class="w-full rounded border-gray-300 text-sm focus:border-gold-500">
                                    </div>
                                    <div class="md:col-span-4">
                                        <label class="text-xs font-bold text-gray-500 uppercase">Nilai Rata-rata /
                                            IPK</label>
                                        <input type="text" :name="'pendidikan['+index+'][nilai_rata_rata]'"
                                            x-model="edu.nilai_rata_rata"
                                            class="w-full rounded border-gray-300 text-sm focus:border-gold-500">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    @if($isEditable)
                        <button type="button" @click="addEducation()"
                            class="mt-4 w-full py-4 border-2 border-dashed border-gold-300 text-gold-600 rounded-xl hover:bg-gold-50 font-bold transition flex items-center justify-center gap-2 group">
                            <i class="fa-solid fa-plus-circle group-hover:scale-110 transition"></i> Tambah Riwayat Pendidikan
                        </button>
                    @endif
                </div>

                {{-- === STEP 3: KELUARGA (REPEATER) === --}}
                <div x-show="step === 3" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="flex items-center justify-between border-b pb-4 mb-6">
                        <h3 class="text-2xl font-serif font-bold text-gray-800">Data Keluarga</h3>
                        <span class="text-xs font-medium text-gray-400 bg-gray-100 px-2 py-1 rounded">Langkah 3 dari
                            5</span>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(fam, index) in families" :key="index">
                            <div
                                class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm relative group hover:border-gold-300 transition-colors">
                                @if($isEditable)
                                    <button type="button" @click="removeFamily(index)"
                                        class="absolute top-3 right-3 text-gray-300 hover:text-red-500 hover:bg-red-50 w-8 h-8 rounded-full flex items-center justify-center transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                @endif

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="text-xs font-bold text-gray-500 uppercase">Hubungan</label>
                                        <select :name="'keluarga['+index+'][hubungan]'" x-model="fam.hubungan"
                                            class="w-full rounded border-gray-300 text-sm focus:border-gold-500">
                                            <option value="Ayah">Ayah</option>
                                            <option value="Ibu">Ibu</option>
                                            <option value="Pasangan">Pasangan</option>
                                            <option value="Anak">Anak</option>
                                            <option value="Saudara">Saudara</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-xs font-bold text-gray-500 uppercase">Nama Lengkap</label>
                                        <input type="text" :name="'keluarga['+index+'][nama]'" x-model="fam.nama"
                                            class="w-full rounded border-gray-300 text-sm focus:border-gold-500">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-500 uppercase">L/P</label>
                                        <select :name="'keluarga['+index+'][jenis_kelamin]'" x-model="fam.jenis_kelamin"
                                            class="w-full rounded border-gray-300 text-sm focus:border-gold-500">
                                            <option value="L">L</option>
                                            <option value="P">P</option>
                                        </select>
                                    </div>

                                    {{-- Hitung Usia Otomatis --}}
                                    <div>
                                        <label class="text-xs font-bold text-gray-500 uppercase">Tgl Lahir</label>
                                        <input type="date" :name="'keluarga['+index+'][tanggal_lahir]'"
                                            x-model="fam.tanggal_lahir" @change="calculateAge(index)"
                                            class="w-full rounded border-gray-300 text-sm focus:border-gold-500">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-500 uppercase">Usia (Thn)</label>
                                        <input type="number" :name="'keluarga['+index+'][usia]'" x-model="fam.usia" readonly
                                            class="w-full rounded border-gray-300 text-sm bg-gray-100 text-gray-500">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-500 uppercase">Pekerjaan</label>
                                        <input type="text" :name="'keluarga['+index+'][pekerjaan]'" x-model="fam.pekerjaan"
                                            class="w-full rounded border-gray-300 text-sm focus:border-gold-500">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-500 uppercase">Penghasilan / Bulan</label>
                                        <input type="text" :name="'keluarga['+index+'][penghasilan]'"
                                            x-model="fam.penghasilan"
                                            class="w-full rounded border-gray-300 text-sm focus:border-gold-500"
                                            placeholder="Rp ...">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    @if($isEditable)
                        <button type="button" @click="addFamily()"
                            class="mt-4 w-full py-4 border-2 border-dashed border-gold-300 text-gold-600 rounded-xl hover:bg-gold-50 font-bold transition flex items-center justify-center gap-2 group">
                            <i class="fa-solid fa-plus-circle group-hover:scale-110 transition"></i> Tambah Anggota Keluarga
                        </button>
                    @endif
                </div>

                {{-- === STEP 4: PENGALAMAN (TOGGLE + REPEATER) === --}}
                <div x-show="step === 4" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="flex items-center justify-between border-b pb-4 mb-6">
                        <h3 class="text-2xl font-serif font-bold text-gray-800">Pengalaman Kerja</h3>
                        <span class="text-xs font-medium text-gray-400 bg-gray-100 px-2 py-1 rounded">Langkah 4 dari
                            5</span>
                    </div>

                    {{-- Toggle --}}
                    <div class="mb-6 bg-yellow-50 p-4 rounded-xl border border-yellow-200 flex items-center cursor-pointer transition hover:bg-yellow-100"
                        @click="pernahBekerja = !pernahBekerja">
                        <div class="relative inline-block w-12 mr-2 align-middle select-none">
                            <input type="checkbox" name="pernah_bekerja" x-model="pernahBekerja"
                                class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer transition-all duration-300"
                                :class="pernahBekerja ? 'right-0 border-gold-500' : 'left-0 border-gray-300'" />
                            <label
                                class="toggle-label block overflow-hidden h-6 rounded-full cursor-pointer transition-colors duration-300"
                                :class="pernahBekerja ? 'bg-gold-500' : 'bg-gray-300'"></label>
                        </div>
                        <label class="ml-3 font-bold text-gray-700 cursor-pointer select-none">
                            Saya memiliki pengalaman kerja / magang / organisasi
                        </label>
                    </div>

                    <div x-show="pernahBekerja" x-transition>
                        <div class="space-y-4">
                            <template x-for="(exp, index) in experiences" :key="index">
                                <div
                                    class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm relative group hover:border-gold-300 transition-colors">
                                    @if($isEditable)
                                        <button type="button" @click="removeExperience(index)"
                                            class="absolute top-3 right-3 text-gray-300 hover:text-red-500 hover:bg-red-50 w-8 h-8 rounded-full flex items-center justify-center transition">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endif

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs font-bold text-gray-500 uppercase">Nama Instansi /
                                                Perusahaan</label>
                                            <input type="text" :name="'pengalaman['+index+'][nama_instansi]'"
                                                x-model="exp.nama_instansi"
                                                class="w-full rounded border-gray-300 text-sm focus:border-gold-500">
                                        </div>
                                        <div>
                                            <label class="text-xs font-bold text-gray-500 uppercase">Posisi /
                                                Jabatan</label>
                                            <input type="text" :name="'pengalaman['+index+'][posisi]'" x-model="exp.posisi"
                                                class="w-full rounded border-gray-300 text-sm focus:border-gold-500">
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div><label class="text-xs font-bold text-gray-500 uppercase">Mulai
                                                    (Tgl)</label><input type="date"
                                                    :name="'pengalaman['+index+'][tanggal_mulai]'"
                                                    x-model="exp.tanggal_mulai"
                                                    class="w-full rounded border-gray-300 text-sm focus:border-gold-500">
                                            </div>
                                            <div><label class="text-xs font-bold text-gray-500 uppercase">Selesai
                                                    (Tgl)</label><input type="date"
                                                    :name="'pengalaman['+index+'][tanggal_selesai]'"
                                                    x-model="exp.tanggal_selesai"
                                                    class="w-full rounded border-gray-300 text-sm focus:border-gold-500">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-xs font-bold text-gray-500 uppercase">Gaji Terakhir
                                                (Opsional)</label>
                                            <input type="text" :name="'pengalaman['+index+'][gaji_akhir]'"
                                                x-model="exp.gaji_akhir"
                                                class="w-full rounded border-gray-300 text-sm focus:border-gold-500">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        @if($isEditable)
                            <button type="button" @click="addExperience()"
                                class="mt-4 w-full py-4 border-2 border-dashed border-gold-300 text-gold-600 rounded-xl hover:bg-gold-50 font-bold transition flex items-center justify-center gap-2 group">
                                <i class="fa-solid fa-plus-circle group-hover:scale-110 transition"></i> Tambah Pengalaman
                            </button>
                        @endif
                    </div>

                    <div x-show="!pernahBekerja"
                        class="text-center text-gray-400 italic py-10 border-2 border-dashed rounded-xl bg-gray-50">
                        <i class="fa-solid fa-briefcase-slash text-4xl mb-2 text-gray-300"></i>
                        <p>Tidak ada pengalaman kerja.</p>
                    </div>
                </div>

                {{-- === STEP 5: DOKUMEN & TANDA TANGAN === --}}
                <div x-show="step === 5" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="flex items-center justify-between border-b pb-4 mb-6">
                        <h3 class="text-2xl font-serif font-bold text-gray-800">Dokumen & Tanda Tangan</h3>
                        <span class="text-xs font-medium text-gray-400 bg-gray-100 px-2 py-1 rounded">Langkah 5 dari
                            5</span>
                    </div>

                    {{-- Section Foto --}}
                    <div
                        class="bg-white border-2 border-dashed border-blue-300 p-6 rounded-2xl mb-8 flex flex-col md:flex-row items-center gap-6 relative overflow-hidden">
                        <div
                            class="absolute top-0 right-0 bg-blue-500 text-white text-xs px-2 py-1 rounded-bl-lg font-bold">
                            Wajib</div>

                        {{-- Preview Foto --}}
                        <div
                            class="w-32 h-40 bg-gray-100 rounded-lg flex-shrink-0 border flex items-center justify-center overflow-hidden relative group">
                            @if($student->foto)
                                <img src="{{ asset('storage/' . $student->foto) }}" class="w-full h-full object-cover">
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center text-white text-xs font-bold opacity-0 group-hover:opacity-100 transition">
                                    Ganti Foto</div>
                            @else
                                <i class="fa-solid fa-user text-4xl text-gray-300"></i>
                            @endif
                        </div>

                        <div class="flex-1 text-center md:text-left">
                            <label class="font-bold text-gray-800 text-lg mb-1 block">Pas Foto (3x4)</label>
                            <p class="text-xs text-gray-500 mb-4">Pastikan wajah terlihat jelas, latar belakang polos
                                (Merah/Biru), format JPG/PNG, Max 2MB.</p>

                            <div x-data="{ fotoName: '' }" class="mt-2">
                                <label
                                    class="cursor-pointer inline-flex items-center justify-center bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-100 px-5 py-2.5 rounded-full text-sm font-bold transition shadow-sm">
                                    <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Pilih File Foto
                                    <input type="file" name="foto" accept="image/*" class="sr-only" tabindex="-1"
                                        @change="fotoName = $event.target.files[0]?.name || ''">
                                </label>
                                <div x-show="fotoName" style="display: none;"
                                    class="text-xs text-green-700 mt-3 font-semibold bg-green-50 p-2 rounded border border-green-200 inline-block">
                                    <i class="fa-solid fa-circle-check mr-1"></i> File: <span x-text="fotoName"></span>
                                </div>
                            </div>
                            @error('foto') <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Grid Dokumen Dinamis --}}
                    <h4 class="font-bold text-gray-700 mb-4">Dokumen Pendukung</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                        @foreach($documentTypes as $doc)
                            <div
                                class="border p-5 rounded-xl bg-gray-50 hover:bg-white transition shadow-sm hover:shadow-md relative group @error('documents.' . $doc->id) border-red-500 ring-1 ring-red-200 bg-red-50 @enderror">

                                <div class="flex justify-between items-start mb-2">
                                    <label class="font-bold text-gray-800 text-sm">{{ $doc->nama }}</label>
                                    @if($doc->is_required)
                                        <span
                                            class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded border border-red-200 uppercase tracking-wide">Wajib</span>
                                    @else
                                        <span
                                            class="bg-green-100 text-green-600 text-[10px] font-bold px-2 py-0.5 rounded border border-green-200 uppercase tracking-wide">Opsional</span>
                                    @endif
                                </div>

                                @php $filePath = $uploadedDocuments[$doc->id] ?? null; @endphp

                                @if($filePath)
                                    <div class="flex items-center justify-between bg-white border p-2 rounded mb-3 shadow-sm">
                                        <span class="text-xs text-green-600 font-bold flex items-center">
                                            <i class="fa-solid fa-circle-check mr-1 text-lg"></i> Terupload
                                        </span>
                                        <a href="{{ asset('storage/' . $filePath) }}" target="_blank"
                                            class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded hover:bg-blue-200 font-bold transition">
                                            <i class="fa-solid fa-eye mr-1"></i> Lihat
                                        </a>
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400 italic mb-3 flex items-center"><i
                                            class="fa-solid fa-circle-xmark mr-1"></i> Belum ada file</div>
                                @endif

                                <div x-data="{ docName: '' }" class="mt-2">
                                    <label
                                        class="cursor-pointer flex items-center justify-center w-full bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400 px-4 py-2 rounded-lg text-xs font-bold transition shadow-sm group">
                                        <i
                                            class="fa-solid fa-cloud-arrow-up mr-2 text-gray-400 group-hover:text-gold-500 transition"></i>
                                        Pilih File Dokumen
                                        <input type="file" name="documents[{{ $doc->id }}]" accept=".jpg,.jpeg,.png,.pdf"
                                            class="sr-only" tabindex="-1" @change="docName = $event.target.files[0]?.name || ''"
                                            {{ $doc->is_required && !$filePath ? 'required' : '' }}>
                                    </label>
                                    <div x-show="docName" style="display: none;"
                                        class="text-xs text-green-700 mt-2 font-semibold bg-green-50 p-2 rounded border border-green-200 truncate">
                                        <i class="fa-solid fa-circle-check mr-1"></i> File: <span x-text="docName"></span>
                                    </div>
                                </div>

                                @error('documents.' . $doc->id)
                                    <p class="text-red-500 text-xs mt-2 font-bold flex items-center"><i
                                            class="fa-solid fa-triangle-exclamation mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    {{-- Tanda Tangan Digital --}}
                    <div class="border-t pt-8 mt-8">
                        <h4 class="font-bold text-gray-800 mb-6 text-center">Tanda Tangan Elektronik</h4>

                        <div class="flex flex-col items-center">
                            <div class="mb-4 text-center">
                                <span class="text-gray-500 text-sm">Dibuat di:</span>
                                <input type="text" name="kota_pembuatan"
                                    value="{{ old('kota_pembuatan', $student->kota_pembuatan) }}"
                                    placeholder="Nama Kota (Mis: Jakarta)"
                                    class="border-b-2 border-gray-300 text-center focus:border-gold-500 focus:outline-none py-1 px-2 w-48 font-bold text-gray-800 placeholder-gray-300"
                                    required>
                                <div class="text-xs text-gray-400 mt-1">{{ now()->format('d F Y') }}</div>
                            </div>

                            <div class="relative group">
                                {{-- Container Canvas --}}
                                <div class="border-2 border-dashed border-gray-400 rounded-xl bg-white shadow-inner relative overflow-hidden"
                                    style="width: 320px; height: 180px;">
                                    <canvas id="signature-pad"
                                        class="w-full h-full cursor-crosshair z-10 relative"></canvas>

                                    {{-- Placeholder Text --}}
                                    <div
                                        class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-20">
                                        <span class="text-gray-400 text-sm font-bold">Area Tanda Tangan</span>
                                    </div>

                                    {{-- Jika ada Signature Lama --}}
                                    @if($student->signature)
                                        <img src="{{ asset('storage/' . $student->signature) }}"
                                            class="absolute top-0 left-0 w-full h-full object-contain opacity-40 pointer-events-none p-4"
                                            title="Tanda tangan tersimpan">
                                    @endif
                                </div>

                                {{-- Tombol Clear --}}
                                <button type="button" id="clear-signature"
                                    class="absolute top-3 right-3 bg-white text-gray-400 hover:text-red-500 border hover:border-red-300 shadow-sm rounded-full w-8 h-8 flex items-center justify-center transition z-20"
                                    title="Hapus & Ulangi">
                                    <i class="fa-solid fa-eraser"></i>
                                </button>
                            </div>

                            <input type="hidden" name="signature_base64" id="signature_base64">
                            @error('signature_base64') <p class="text-red-500 text-xs mt-2 font-bold animate-pulse">
                            {{ $message }}</p> @enderror

                            <p class="font-bold uppercase mt-4 text-lg text-gray-800">
                                {{ $student->nama_lengkap ?? '(Nama Lengkap)' }}</p>
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">Peserta Pelatihan</p>
                        </div>
                    </div>

                    {{-- Disclaimer & Tombol Simpan --}}
                    @if($isEditable)
                        <div class="mt-12 bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center">
                            <p class="text-sm text-yellow-800">
                                <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                                Dengan menekan tombol simpan, saya menyatakan data yang saya isi adalah benar dan dapat
                                dipertanggungjawabkan.
                            </p>
                        </div>

                    @if($isEditable)
                        <div class="mt-12 bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center">
                            <p class="text-sm text-yellow-800">
                                <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                                Dengan menekan tombol simpan, saya menyatakan data yang saya isi adalah benar dan dapat
                                dipertanggungjawabkan.
                            </p>
                        </div>
                    @endif
                </div>

            </fieldset>

            {{-- 3. BOTTOM NAVIGATION --}}
            <div class="flex flex-wrap items-center justify-between mt-12 pt-6 border-t border-gray-100 gap-4">
                <button type="button" x-show="step > 1" @click="prevStep()"
                    class="flex items-center text-gray-500 hover:text-gray-800 font-bold px-6 py-3 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Sebelumnya
                </button>
                <div x-show="step === 1" class="w-32 hidden sm:block"></div>

                @if($isEditable)
                    <div class="flex-1 flex justify-center order-last sm:order-none w-full sm:w-auto mt-4 sm:mt-0" 
                         x-show="isAccepted || (!isAccepted && step === 5)">
                        {{-- Spinner Loading (Alpine) --}}
                        <div x-show="isSubmitting" class="flex flex-col items-center">
                            <svg class="animate-spin h-6 w-6 text-gold-600 mb-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-gray-500 font-bold text-xs">Menyimpan...</span>
                        </div>

                        {{-- Tombol Submit --}}
                        <button type="submit" x-show="!isSubmitting" @click="submitForm($event)"
                            class="bg-gradient-to-r from-gold-500 to-gold-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl hover:scale-105 transition transform flex items-center w-full justify-center sm:w-auto">
                            <i class="fa-solid fa-save mr-2"></i> SIMPAN FORMULIR
                        </button>
                    </div>
                @else
                    <div class="flex-1"></div>
                @endif

                <button type="button" x-show="step < 5" @click="nextStep()"
                    class="flex items-center justify-center bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg hover:shadow-blue-500/30">
                    Selanjutnya <i class="fa-solid fa-arrow-right ml-2"></i>
                </button>
                <div x-show="step === 5" class="w-32 hidden sm:block"></div>
            </div>
        </form>
    </div>

    {{-- COMPONENT INPUT HELPER (Optional, biar kode lebih rapi) --}}
    @once
        @verbatim
            <script type="text/template" id="helper-input">
                    </script>
        @endverbatim
    @endonce

@endsection

@section('scripts')
    {{-- Load Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        function wizardForm() {
            return {
                // --- LOGIKA INITIAL STEP BERDASARKAN ERROR ---
                // Jika ada error validasi backend, otomatis lompat ke step terkait
                step: {{ $errors->any() ? (
        $errors->has('nama_lengkap') || $errors->has('nomor_ktp') ? 1 :
        ($errors->has('pendidikan*') ? 2 :
            ($errors->has('keluarga*') ? 3 :
                ($errors->has('pengalaman*') ? 4 : 5)))
    ) : 1 }},

                pernahBekerja: {{ old('pernah_bekerja', $student->pernah_bekerja) ? 'true' : 'false' }},
                isAccepted: {{ $isAccepted ? 'true' : 'false' }},
                isSubmitting: false,
                signaturePad: null,

                // --- DATA ARRAYS (Dengan Data Persistence 'old') ---
                // Trik: Gunakan old() untuk mengambil data form yang gagal, fallback ke data database
                educations: @json(old('pendidikan', $student->educations ?? [])),
                families: @json(old('keluarga', $student->families ?? [])),
                experiences: @json(old('pengalaman', $student->experiences ?? [])),

                init() {
                    // Init Signature Pad
                    const canvas = document.getElementById('signature-pad');
                    if (canvas) {
                        // Fix Blurry Canvas on High DPI Screens
                        const ratio = Math.max(window.devicePixelRatio || 1, 1);
                        canvas.width = canvas.offsetWidth * ratio;
                        canvas.height = canvas.offsetHeight * ratio;
                        canvas.getContext("2d").scale(ratio, ratio);

                        this.signaturePad = new SignaturePad(canvas, {
                            backgroundColor: 'rgba(255, 255, 255, 0)', // Transparan
                            penColor: 'rgb(0, 0, 0)'
                        });

                        // Clear Button Logic
                        document.getElementById('clear-signature').addEventListener('click', () => {
                            this.signaturePad.clear();
                            document.getElementById('signature_base64').value = '';
                        });
                    }

                    // Notifikasi Toast jika baru load dan ada error
                    @if($errors->any())
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Mohon periksa inputan yang ditandai merah.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000
                        });
                    @endif

                    // Inisialisasi data kosong jika array masih kosong (untuk UX)
                    if (this.educations.length === 0) this.addEducation();
                    if (this.families.length === 0) this.addFamily();
                },

                getStepName(i) {
                    return ['Data Diri', 'Pendidikan', 'Keluarga', 'Pengalaman', 'Dokumen'][i - 1];
                },

                goToStep(targetStep) {
                    if (this.isAccepted) {
                        this.step = targetStep;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        return;
                    }

                    // Hanya boleh lompat ke step yang sudah dilewati atau next step (+1)
                    // Tapi untuk kemudahan edit, kita izinkan klik step sebelumnya kapan saja
                    if (targetStep < this.step) {
                        this.step = targetStep;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else if (targetStep === this.step + 1) {
                        this.nextStep();
                    }
                },

                // --- VALIDASI FRONTEND ---
                validateCurrentStep() {
                    const currentDiv = document.querySelector(`[x-show="step === ${this.step}"]`);
                    if (!currentDiv) return true;

                    // Cari input required yang VISIBLE saja
                    const inputs = currentDiv.querySelectorAll('input[required]:not([type="hidden"]), select[required], textarea[required]');
                    let isValid = true;
                    let firstError = null;

                    inputs.forEach(el => {
                        // Reset
                        el.classList.remove('ring-2', 'ring-red-500', 'border-red-500');

                        if (!el.value || el.value.trim() === '') {
                            isValid = false;
                            el.classList.add('ring-2', 'ring-red-500', 'border-red-500');
                            if (!firstError) firstError = el;
                        }
                    });

                    if (!isValid) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data Belum Lengkap',
                            text: 'Mohon lengkapi field bertanda bintang (*) sebelum melanjutkan.',
                            toast: true,
                            position: 'top-end',
                            timer: 3000,
                            showConfirmButton: false
                        });
                        if (firstError) firstError.focus();
                        return false;
                    }
                    return true;
                },

                nextStep() {
                    if (this.validateCurrentStep()) {
                        this.step++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                prevStep() {
                    this.step--;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                // --- FUNGSI SUBMIT ---
                submitForm(e) {
                    e.preventDefault();

                    // 1. Validasi Dokumen & Foto (Frontend Check)
                    // Kita cek manual input file yg required
                    if (!this.validateCurrentStep()) return;

                    // 2. Cek Signature
                    const hasExistingSignature = {{ $student->signature ? 'true' : 'false' }};
                    if (this.signaturePad.isEmpty() && !hasExistingSignature) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tanda Tangan Diperlukan',
                            text: 'Silakan tanda tangan pada kotak yang tersedia di bagian bawah.',
                            confirmButtonColor: '#d97706'
                        });
                        return;
                    }

                    // 3. Simpan Canvas ke Hidden Input
                    if (!this.signaturePad.isEmpty()) {
                        const data = this.signaturePad.toDataURL('image/png');
                        document.getElementById('signature_base64').value = data;
                    }

                    // 4. Konfirmasi SweetAlert
                    Swal.fire({
                        title: 'Simpan Formulir?',
                        html: "Pastikan data Anda sudah benar.<br>Data akan masuk ke proses verifikasi.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#d97706',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Kirim Data!',
                        cancelButtonText: 'Batal, Cek Lagi'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.isSubmitting = true;
                            e.target.closest('form').submit();
                        }
                    });
                },

                // --- HELPER LOGIC ---
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

                // --- REPEATER FUNCTIONS ---
                addEducation() {
                    this.educations.push({ tingkat: 'SMA/SMK', nama_institusi: '', jurusan: '', tahun_masuk: '', tahun_lulus: '', lokasi: '', nilai_rata_rata: '' });
                },
                removeEducation(i) {
                    this.educations.splice(i, 1);
                    if (this.educations.length === 0) this.addEducation(); // Min 1 row
                },

                addFamily() {
                    this.families.push({ hubungan: 'Ayah', jenis_kelamin: 'L', nama: '', pekerjaan: '', pendidikan: '', tanggal_lahir: '', usia: '', penghasilan: '' });
                },
                removeFamily(i) {
                    this.families.splice(i, 1);
                    if (this.families.length === 0) this.addFamily(); // Min 1 row
                },

                addExperience() {
                    this.experiences.push({ nama_instansi: '', posisi: '', tanggal_mulai: '', tanggal_selesai: '', gaji_akhir: '' });
                },
                removeExperience(i) { this.experiences.splice(i, 1); }
            }
        }
    </script>
@endsection