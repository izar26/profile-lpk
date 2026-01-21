@extends('layouts.app')

@section('header', 'Edit Biodata Saya')

@section('content')

{{-- Alert Success --}}
@if (session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm flex items-center animate-pulse">
        <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
@endif

{{-- Alert Error --}}
@if ($errors->any())
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

{{-- Toolbar Atas --}}
<div class="flex justify-end mb-6">
    <a href="{{ route('pegawai.biodata.print') }}" target="_blank"
       class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-xl hover:bg-red-100 transition font-bold shadow-sm">
       <i class="fa-solid fa-file-pdf mr-2"></i> Preview Cetak Biodata
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ activeTab: 'biodata' }">

    {{-- ================= KOLOM KIRI (FOTO & MENU) ================= --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- Foto Profil (Form Terpisah di dalam Main Form nanti) --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-200 text-center shadow-sm">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">FOTO PROFIL</h3>

            <div class="relative w-40 h-40 mx-auto mb-4 group">
                @if($employee->foto)
                    <img src="{{ asset('storage/'.$employee->foto) }}" class="w-40 h-40 rounded-full object-cover border-4 border-gold-100 shadow-md">
                @else
                    <div class="w-40 h-40 rounded-full bg-gray-100 border-4 border-gray-200 flex items-center justify-center text-gray-400 text-4xl">
                        <i class="fa-solid fa-user"></i>
                    </div>
                @endif
            </div>

            <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 text-xs text-blue-700">
                <i class="fa-solid fa-circle-info mr-1"></i>
                Untuk mengganti foto, silakan upload di <strong>Tab Biodata Diri</strong>.
            </div>
        </div>

        {{-- Menu Navigasi Vertical (Untuk Mobile/Desktop) --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <button @click="activeTab = 'biodata'"
                :class="activeTab === 'biodata' ? 'bg-gold-50 text-gold-700 border-l-4 border-gold-500' : 'text-gray-600 hover:bg-gray-50'"
                class="w-full text-left px-5 py-4 font-bold text-sm flex items-center transition-all">
                <i class="fa-solid fa-id-card w-6 text-center mr-3"></i> Biodata Diri
            </button>

            <button @click="activeTab = 'alamat'"
                :class="activeTab === 'alamat' ? 'bg-gold-50 text-gold-700 border-l-4 border-gold-500' : 'text-gray-600 hover:bg-gray-50'"
                class="w-full text-left px-5 py-4 font-bold text-sm flex items-center transition-all border-t border-gray-100">
                <i class="fa-solid fa-map-location-dot w-6 text-center mr-3"></i> Alamat & Kontak
            </button>

            <button @click="activeTab = 'pendidikan'"
                :class="activeTab === 'pendidikan' ? 'bg-gold-50 text-gold-700 border-l-4 border-gold-500' : 'text-gray-600 hover:bg-gray-50'"
                class="w-full text-left px-5 py-4 font-bold text-sm flex items-center transition-all border-t border-gray-100">
                <i class="fa-solid fa-graduation-cap w-6 text-center mr-3"></i> Riwayat Pendidikan
            </button>

            <button @click="activeTab = 'keluarga'"
                :class="activeTab === 'keluarga' ? 'bg-gold-50 text-gold-700 border-l-4 border-gold-500' : 'text-gray-600 hover:bg-gray-50'"
                class="w-full text-left px-5 py-4 font-bold text-sm flex items-center transition-all border-t border-gray-100">
                <i class="fa-solid fa-people-roof w-6 text-center mr-3"></i> Data Keluarga
            </button>

            <button @click="activeTab = 'dokumen'"
                :class="activeTab === 'dokumen' ? 'bg-gold-50 text-gold-700 border-l-4 border-gold-500' : 'text-gray-600 hover:bg-gray-50'"
                class="w-full text-left px-5 py-4 font-bold text-sm flex items-center transition-all border-t border-gray-100">
                <i class="fa-solid fa-file-contract w-6 text-center mr-3"></i> Dokumen Pendukung
            </button>
        </div>

    </div>

    {{-- ================= KOLOM KANAN (ISI FORM) ================= --}}
    <div class="lg:col-span-2">

        {{-- FORM UTAMA (HANYA UNTUK TAB BIODATA & ALAMAT) --}}
        <form action="{{ route('pegawai.biodata.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            {{-- === TAB 1: BIODATA DIRI === --}}
            <div x-show="activeTab === 'biodata'" class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm animate-fade-in">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-3 mb-5">Biodata Diri</h3>

                <div class="space-y-4">
                    {{-- Upload Foto Input --}}
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mb-4">
                        <label class="block text-sm font-medium text-blue-800 mb-1">Ganti Foto Profil</label>
                        <input type="file" name="foto" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200"/>
                        <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengganti foto.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ old('nama', $employee->nama) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor KTP (NIK)</label>
                            <input type="text" name="nomor_ktp" value="{{ old('nomor_ktp', $employee->nomor_ktp) }}" maxlength="16" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor KK</label>
                            <input type="text" name="nomor_kk" value="{{ old('nomor_kk', $employee->nomor_kk) }}" maxlength="16" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor NPWP</label>
                            <input type="text" name="nomor_npwp" value="{{ old('nomor_npwp', $employee->nomor_npwp) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $employee->tempat_lahir) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($employee->tanggal_lahir)->format('Y-m-d')) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                            <select name="jenis_kelamin" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                                <option value="L" {{ $employee->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ $employee->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gol. Darah</label>
                            <select name="golongan_darah" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                                <option value="">-</option>
                                @foreach(['A','B','AB','O'] as $gd)
                                    <option value="{{ $gd }}" {{ $employee->golongan_darah == $gd ? 'selected' : '' }}>{{ $gd }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi (cm)</label>
                            <input type="number" name="tinggi_badan" value="{{ old('tinggi_badan', $employee->tinggi_badan) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Berat (kg)</label>
                            <input type="number" name="berat_badan" value="{{ old('berat_badan', $employee->berat_badan) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Agama</label>
                            <select name="agama" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                                <option value="">- Pilih -</option>
                                @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $agm)
                                    <option value="{{ $agm }}" {{ $employee->agama == $agm ? 'selected' : '' }}>{{ $agm }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Pernikahan</label>
                            <select name="status_pernikahan" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                                <option value="">- Pilih -</option>
                                @foreach(['Belum Menikah','Menikah','Cerai Hidup','Cerai Mati'] as $stt)
                                    <option value="{{ $stt }}" {{ $employee->status_pernikahan == $stt ? 'selected' : '' }}>{{ $stt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-gold-500 hover:bg-gold-600 text-white font-bold rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                        Simpan Perubahan
                    </button>
                </div>
            </div>

            {{-- === TAB 2: ALAMAT & KONTAK === --}}
            <div x-show="activeTab === 'alamat'" class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm animate-fade-in" style="display: none;">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-3 mb-5">Alamat & Kontak</h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Sesuai KTP</label>
                            <textarea name="alamat_ktp" rows="3" class="w-full border-gray-300 rounded-lg focus:border-gold-500">{{ old('alamat_ktp', $employee->alamat_ktp) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kota / Kabupaten</label>
                            <input type="text" name="kota_ktp" value="{{ old('kota_ktp', $employee->kota_ktp) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                            <input type="text" name="provinsi_ktp" value="{{ old('provinsi_ktp', $employee->provinsi_ktp) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Domisili (Jika beda dengan KTP)</label>
                            <textarea name="alamat_domisili" rows="2" class="w-full border-gray-300 rounded-lg focus:border-gold-500" placeholder="Kosongkan jika sama dengan KTP">{{ old('alamat_domisili', $employee->alamat_domisili) }}</textarea>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 my-4"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $employee->email) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP / WhatsApp</label>
                            <input type="text" name="telepon" value="{{ old('telepon', $employee->telepon) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP Darurat (Keluarga)</label>
                            <input type="text" name="no_hp_keluarga_darurat" value="{{ old('no_hp_keluarga_darurat', $employee->no_hp_keluarga_darurat) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instagram (Opsional)</label>
                            <input type="text" name="instagram" value="{{ old('instagram', $employee->instagram) }}" class="w-full border-gray-300 rounded-lg focus:border-gold-500" placeholder="@username">
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-gold-500 hover:bg-gold-600 text-white font-bold rounded-lg shadow-md transition transform hover:-translate-y-0.5">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

        {{-- === TAB 3: RIWAYAT PENDIDIKAN (CRUD MANDIRI) === --}}
        <div x-show="activeTab === 'pendidikan'" class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm animate-fade-in" style="display: none;">
            <div class="flex justify-between items-center mb-5 border-b pb-3">
                <h3 class="text-lg font-bold text-gray-800">Riwayat Pendidikan</h3>
                <button onclick="openModal('modalPendidikan')" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition">
                    <i class="fa-solid fa-plus mr-1"></i> Tambah
                </button>
            </div>

            <div class="space-y-4">
                @forelse($employee->educations as $edu)
                    <div class="flex justify-between items-start p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-blue-700">{{ $edu->jenjang }}</span>
                                <span class="text-gray-400">|</span>
                                <span class="font-bold text-gray-800">{{ $edu->nama_sekolah }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Jurusan: {{ $edu->jurusan ?? '-' }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $edu->tahun_masuk }} - {{ $edu->tahun_lulus }}
                                @if($edu->nilai_akhir) • Nilai: {{ $edu->nilai_akhir }} @endif
                            </p>
                        </div>
                        <form action="{{ route('pegawai.education.destroy', $edu->id) }}" method="POST" onsubmit="return confirm('Hapus data pendidikan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 p-2"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-6">Belum ada data pendidikan.</p>
                @endforelse
            </div>
        </div>

        {{-- === TAB 4: DATA KELUARGA (CRUD MANDIRI) === --}}
        <div x-show="activeTab === 'keluarga'" class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm animate-fade-in" style="display: none;">
            <div class="flex justify-between items-center mb-5 border-b pb-3">
                <h3 class="text-lg font-bold text-gray-800">Data Keluarga</h3>
                <button onclick="openModal('modalKeluarga')" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition">
                    <i class="fa-solid fa-plus mr-1"></i> Tambah
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">Hubungan</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Pekerjaan</th>
                            <th class="px-4 py-3 rounded-r-lg text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($employee->families as $fam)
                            <tr>
                                <td class="px-4 py-3 font-semibold">{{ $fam->hubungan }}</td>
                                <td class="px-4 py-3">{{ $fam->nama_lengkap }}</td>
                                <td class="px-4 py-3">{{ $fam->pekerjaan ?? '-' }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <button type="button" onclick='openDetailKeluarga(@json($fam))' class="text-blue-500 hover:text-blue-700 mr-3" title="Detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button type="button" onclick='openEditKeluarga(@json($fam))' class="text-yellow-500 hover:text-yellow-700 mr-3" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('pegawai.family.destroy', $fam->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data keluarga ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-6 text-gray-400">Belum ada data keluarga.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- === TAB 5: DOKUMEN PENDUKUNG (CRUD MANDIRI) === --}}
        <div x-show="activeTab === 'dokumen'" class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm animate-fade-in" style="display: none;">
            <div class="flex justify-between items-center mb-5 border-b pb-3">
                <h3 class="text-lg font-bold text-gray-800">Dokumen Pendukung</h3>
            </div>

            {{-- Form Upload Dokumen --}}
            <form action="{{ route('pegawai.document.store') }}" method="POST" enctype="multipart/form-data" class="bg-blue-50 p-4 rounded-xl border border-blue-100 mb-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-blue-800 mb-1">Nama Dokumen</label>
                        <input type="text" name="nama_dokumen" class="w-full border-blue-200 rounded-lg text-sm" placeholder="Contoh: KTP, Ijazah S1" required>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-blue-800 mb-1">File (PDF/JPG, Max 5MB)</label>
                        <input type="file" name="file_dokumen" class="w-full text-sm text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-200 file:text-blue-800 hover:file:bg-blue-300" required>
                    </div>
                    <div>
                        <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg transition">Upload</button>
                    </div>
                </div>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @forelse($employee->documents as $doc)
                    <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg hover:border-gold-300 transition shadow-sm">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-gray-500 shrink-0">
                                <i class="fa-solid fa-file"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-gray-800 truncate text-sm" title="{{ $doc->nama_dokumen }}">{{ $doc->nama_dokumen }}</p>
                                <p class="text-[10px] text-gray-400">{{ $doc->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-blue-500 hover:text-blue-700"><i class="fa-solid fa-eye"></i></a>
                            <form action="{{ route('pegawai.document.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Hapus dokumen ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="col-span-full text-center py-6 text-gray-400">Belum ada dokumen diupload.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- MODAL TAMBAH PENDIDIKAN --}}
<div id="modalPendidikan" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold mb-4">Tambah Pendidikan</h3>
        <form action="{{ route('pegawai.education.store') }}" method="POST">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Jenjang</label>
                    <select name="jenjang" class="w-full border-gray-300 rounded-lg" required>
                        @foreach(['SD','SMP','SMA/SMK','D3','S1','S2','S3'] as $j) <option value="{{ $j }}">{{ $j }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Sekolah / Univ</label>
                    <input type="text" name="nama_sekolah" class="w-full border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Jurusan (Opsional)</label>
                    <input type="text" name="jurusan" class="w-full border-gray-300 rounded-lg">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1">Thn Masuk</label>
                        <input type="number" name="tahun_masuk" class="w-full border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Thn Lulus</label>
                        <input type="number" name="tahun_lulus" class="w-full border-gray-300 rounded-lg" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nilai Akhir / IPK</label>
                    <input type="text" name="nilai_akhir" class="w-full border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalPendidikan')" class="px-4 py-2 bg-gray-200 rounded-lg text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL TAMBAH KELUARGA --}}
<div id="modalKeluarga" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold mb-4">Tambah Keluarga</h3>
        <form action="{{ route('pegawai.family.store') }}" method="POST">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Hubungan</label>
                    <select name="hubungan" class="w-full border-gray-300 rounded-lg" required>
                        @foreach(['Suami','Istri','Anak','Ayah','Ibu','Saudara'] as $h) <option value="{{ $h }}">{{ $h }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="w-full border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">NIK (Opsional)</label>
                    <input type="text" name="nik" maxlength="16" class="w-full border-gray-300 rounded-lg">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="w-full border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tgl Lahir</label>
                        <input type="date" name="tanggal_lahir" class="w-full border-gray-300 rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Pekerjaan</label>
                    <input type="text" name="pekerjaan" class="w-full border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">No. HP (Opsional)</label>
                    <input type="text" name="no_hp" class="w-full border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalKeluarga')" class="px-4 py-2 bg-gray-200 rounded-lg text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

        </form>
    </div>
</div>

{{-- MODAL DETAIL KELUARGA --}}
<div id="modalDetailKeluarga" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold mb-4 border-b pb-2">Detail Data Keluarga</h3>
        <div class="space-y-3 text-sm">
            <div class="grid grid-cols-3 gap-2">
                <div class="text-gray-500">Hubungan</div>
                <div class="col-span-2 font-bold" id="detail_hubungan"></div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div class="text-gray-500">Nama Lengkap</div>
                <div class="col-span-2 font-bold" id="detail_nama"></div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div class="text-gray-500">NIK</div>
                <div class="col-span-2" id="detail_nik"></div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div class="text-gray-500">TTL</div>
                <div class="col-span-2" id="detail_ttl"></div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div class="text-gray-500">Pekerjaan</div>
                <div class="col-span-2" id="detail_pekerjaan"></div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div class="text-gray-500">No. HP</div>
                <div class="col-span-2" id="detail_nohp"></div>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button type="button" onclick="closeModal('modalDetailKeluarga')" class="px-4 py-2 bg-gray-200 rounded-lg text-sm font-bold hover:bg-gray-300 transition">Tutup</button>
        </div>
    </div>
</div>

{{-- MODAL EDIT KELUARGA --}}
<div id="modalEditKeluarga" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold mb-4">Edit Data Keluarga</h3>
        <form id="formEditKeluarga" method="POST">
            @csrf @method('PUT')
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Hubungan</label>
                    <select name="hubungan" id="edit_hubungan" class="w-full border-gray-300 rounded-lg" required>
                        @foreach(['Suami','Istri','Anak','Ayah','Ibu','Saudara'] as $h) <option value="{{ $h }}">{{ $h }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="edit_nama" class="w-full border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">NIK (Opsional)</label>
                    <input type="text" name="nik" id="edit_nik" maxlength="16" class="w-full border-gray-300 rounded-lg">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" id="edit_tempat_lahir" class="w-full border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tgl Lahir</label>
                        <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" class="w-full border-gray-300 rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Pekerjaan</label>
                    <input type="text" name="pekerjaan" id="edit_pekerjaan" class="w-full border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">No. HP (Opsional)</label>
                    <input type="text" name="no_hp" id="edit_no_hp" class="w-full border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalEditKeluarga')" class="px-4 py-2 bg-gray-200 rounded-lg text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gold-500 hover:bg-gold-600 text-white rounded-lg text-sm font-bold">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function openDetailKeluarga(data) {
        document.getElementById('detail_hubungan').innerText = data.hubungan;
        document.getElementById('detail_nama').innerText = data.nama_lengkap;
        document.getElementById('detail_nik').innerText = data.nik || '-';
        
        let ttl = (data.tempat_lahir || '') + (data.tempat_lahir && data.tanggal_lahir ? ', ' : '') + (data.tanggal_lahir || '');
        document.getElementById('detail_ttl').innerText = ttl || '-';
        
        document.getElementById('detail_pekerjaan').innerText = data.pekerjaan || '-';
        document.getElementById('detail_nohp').innerText = data.no_hp || '-';
        
        openModal('modalDetailKeluarga');
    }

    function openEditKeluarga(data) {
        // Set Action URL
        let url = "{{ route('pegawai.family.update', ':id') }}";
        url = url.replace(':id', data.id);
        document.getElementById('formEditKeluarga').action = url;

        // Fill Data
        document.getElementById('edit_hubungan').value = data.hubungan;
        document.getElementById('edit_nama').value = data.nama_lengkap;
        document.getElementById('edit_nik').value = data.nik || '';
        document.getElementById('edit_tempat_lahir').value = data.tempat_lahir || '';
        document.getElementById('edit_tanggal_lahir').value = data.tanggal_lahir ? data.tanggal_lahir.split('T')[0] : ''; // Handle datetime format if any
        document.getElementById('edit_pekerjaan').value = data.pekerjaan || '';
        document.getElementById('edit_no_hp').value = data.no_hp || '';

        openModal('modalEditKeluarga');
    }
</script>

@endsection
