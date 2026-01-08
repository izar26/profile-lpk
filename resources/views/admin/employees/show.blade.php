@extends('layouts.app')

@section('header')
    Detail Pegawai: {{ $employee->nama }}
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- KOLOM KIRI: FOTO & DATA SINGKAT --}}
    <div class="space-y-6">

        {{-- KARTU PROFIL --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="h-24 bg-gradient-to-r from-gold-400 to-gold-600"></div>
            <div class="px-6 pb-6 relative text-center">
                <div class="relative inline-block -mt-12 mb-4">
                    @if($employee->foto)
                        <img src="{{ asset('storage/' . $employee->foto) }}" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
                    @else
                        <div class="w-24 h-24 rounded-full bg-white border-4 border-gray-100 flex items-center justify-center text-gray-400 text-3xl font-bold shadow-md">
                            {{ substr($employee->nama, 0, 1) }}
                        </div>
                    @endif
                </div>

                <h2 class="text-xl font-bold text-gray-900">{{ $employee->nama }}</h2>
                <p class="text-sm text-gray-500 mb-4">{{ $employee->jabatan }} | {{ $employee->status_kepegawaian }}</p>

                <div class="flex justify-center gap-2 mb-4">
                    <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs rounded-full font-semibold border border-blue-100">
                        NIP: {{ $employee->nip ?? '-' }}
                    </span>
                </div>

                {{-- TOMBOL AKSI --}}
                <div class="flex flex-col gap-2">
                    <a href="{{ route('admin.employees.export-biodata', $employee) }}" target="_blank"
                       class="w-full py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium flex items-center justify-center">
                        <i class="fa-solid fa-file-pdf mr-2 text-red-500"></i> Cetak Biodata
                    </a>
                    <a href="{{ route('admin.employees.index') }}"
                       class="w-full py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        {{-- KARTU KONTAK CEPAT --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2">Kontak</h3>
            <ul class="space-y-3 text-sm">
                <li class="flex items-start">
                    <i class="fa-solid fa-envelope mt-1 mr-3 text-gray-400 w-4"></i>
                    <span class="text-gray-700 break-all">{{ $employee->email }}</span>
                </li>
                <li class="flex items-start">
                    <i class="fa-brands fa-whatsapp mt-1 mr-3 text-green-500 w-4"></i>
                    <span class="text-gray-700">{{ $employee->telepon ?? '-' }}</span>
                </li>
                <li class="flex items-start">
                    <i class="fa-solid fa-phone-medical mt-1 mr-3 text-red-400 w-4"></i>
                    <div>
                        <span class="text-xs text-gray-400 block">Darurat</span>
                        <span class="text-gray-700">{{ $employee->no_hp_keluarga_darurat ?? '-' }}</span>
                    </div>
                </li>
            </ul>
        </div>

        {{-- KARTU AKUN --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2">Akun Sistem</h3>
            @if($employee->user)
                <div class="flex items-center gap-3 bg-green-50 p-3 rounded-lg border border-green-100">
                    <div class="w-10 h-10 rounded-full bg-green-200 flex items-center justify-center text-green-700">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-green-800">Aktif</p>
                        <p class="text-xs text-green-600">{{ $employee->user->email }}</p>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-sm text-gray-500 mb-3">Belum ada akun login.</p>
                    <form action="{{ route('admin.employees.generate-account', $employee) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-xs hover:bg-blue-700 font-bold">
                            Buat Akun Otomatis
                        </button>
                    </form>
                </div>
            @endif
        </div>

    </div>

    {{-- KOLOM KANAN: DETAIL DATA --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- 1. DATA PRIBADI --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="flex items-center text-lg font-bold text-gray-800 mb-4">
                <span class="w-1 h-6 bg-gold-500 rounded-full mr-3"></span> Biodata Diri
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                <div>
                    <label class="block text-gray-400 text-xs uppercase mb-1">NIK (KTP)</label>
                    <p class="font-medium text-gray-800">{{ $employee->nomor_ktp ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 text-xs uppercase mb-1">Nomor KK</label>
                    <p class="font-medium text-gray-800">{{ $employee->nomor_kk ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 text-xs uppercase mb-1">Tempat, Tgl Lahir</label>
                    <p class="font-medium text-gray-800">
                        {{ $employee->tempat_lahir }}, {{ $employee->tanggal_lahir ? $employee->tanggal_lahir->format('d M Y') : '-' }}
                    </p>
                </div>
                <div>
                    <label class="block text-gray-400 text-xs uppercase mb-1">Jenis Kelamin</label>
                    <p class="font-medium text-gray-800">{{ $employee->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 text-xs uppercase mb-1">Status Pernikahan</label>
                    <p class="font-medium text-gray-800">{{ $employee->status_pernikahan ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 text-xs uppercase mb-1">Agama</label>
                    <p class="font-medium text-gray-800">{{ $employee->agama ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-gray-400 text-xs uppercase mb-1">NPWP</label>
                    <p class="font-medium text-gray-800">{{ $employee->nomor_npwp ?? '-' }}</p>
                </div>
                <div class="flex gap-4">
                    <div>
                        <label class="block text-gray-400 text-xs uppercase mb-1">Gol. Darah</label>
                        <p class="font-medium text-gray-800">{{ $employee->golongan_darah ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs uppercase mb-1">Tinggi / Berat</label>
                        <p class="font-medium text-gray-800">{{ $employee->tinggi_badan ?? '-' }} cm / {{ $employee->berat_badan ?? '-' }} kg</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. ALAMAT --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="flex items-center text-lg font-bold text-gray-800 mb-4">
                <span class="w-1 h-6 bg-blue-500 rounded-full mr-3"></span> Alamat
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-bold text-gray-700 mb-2 border-b border-gray-200 pb-1">Alamat Sesuai KTP</h4>
                    <p class="text-gray-600 mb-2">{{ $employee->alamat_ktp ?? '-' }}</p>
                    <p class="text-gray-500 text-xs">
                        {{ $employee->kota_ktp ?? '' }} {{ $employee->provinsi_ktp ? ', '.$employee->provinsi_ktp : '' }}
                    </p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-bold text-gray-700 mb-2 border-b border-gray-200 pb-1">Alamat Domisili</h4>
                    <p class="text-gray-600">{{ $employee->alamat_domisili ?? '(Sama dengan KTP)' }}</p>
                </div>
            </div>
        </div>

        {{-- 3. RIWAYAT PENDIDIKAN --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="flex items-center text-lg font-bold text-gray-800 mb-4">
                <span class="w-1 h-6 bg-purple-500 rounded-full mr-3"></span> Riwayat Pendidikan
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 rounded-tl-lg">Jenjang</th>
                            <th class="px-4 py-3">Nama Sekolah</th>
                            <th class="px-4 py-3">Jurusan</th>
                            <th class="px-4 py-3 text-center">Lulus</th>
                            <th class="px-4 py-3 rounded-tr-lg text-center">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($employee->educations as $edu)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $edu->jenjang }}</td>
                                <td class="px-4 py-3">{{ $edu->nama_sekolah }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $edu->jurusan ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">{{ $edu->tahun_lulus }}</td>
                                <td class="px-4 py-3 text-center">{{ $edu->nilai_akhir ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400">
                                    Belum ada data pendidikan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 4. DATA KELUARGA --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="flex items-center text-lg font-bold text-gray-800 mb-4">
                <span class="w-1 h-6 bg-pink-500 rounded-full mr-3"></span> Data Keluarga
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 rounded-tl-lg">Hubungan</th>
                            <th class="px-4 py-3">Nama Lengkap</th>
                            <th class="px-4 py-3">Pekerjaan</th>
                            <th class="px-4 py-3 rounded-tr-lg">Kontak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($employee->families as $fam)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 bg-pink-50 text-pink-700 rounded text-xs font-semibold">
                                        {{ $fam->hubungan }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $fam->nama_lengkap }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $fam->pekerjaan ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $fam->no_hp ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-400">
                                    Belum ada data keluarga.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 5. DOKUMEN (READ ONLY) --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="flex items-center text-lg font-bold text-gray-800 mb-4">
                <span class="w-1 h-6 bg-teal-500 rounded-full mr-3"></span> Berkas Dokumen
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($employee->documents as $doc)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-teal-300 transition group">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-10 h-10 rounded bg-white flex items-center justify-center text-teal-600 shadow-sm shrink-0">
                                <i class="fa-solid fa-file-lines text-xl"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-gray-800 truncate text-sm" title="{{ $doc->nama_dokumen }}">
                                    {{ $doc->nama_dokumen }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $doc->created_at->format('d M Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                               class="p-2 text-gray-500 hover:text-blue-600 hover:bg-white rounded-full transition" title="Lihat">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ asset('storage/' . $doc->file_path) }}" download
                               class="p-2 text-gray-500 hover:text-green-600 hover:bg-white rounded-full transition" title="Download">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center border-2 border-dashed border-gray-200 rounded-xl">
                        <i class="fa-solid fa-folder-open text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500 text-sm">Belum ada dokumen yang diunggah pegawai.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
