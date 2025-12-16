@extends('layouts.app')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.students.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <span>Ruang Verifikasi: {{ $student->nama_lengkap }}</span>
    </div>
@endsection

@section('content')

{{-- ALERT ERROR JIKA VALIDASI GAGAL --}}
@if($errors->any())
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start pb-20">
    
    {{-- KOLOM KIRI: DATA SISWA (READ ONLY) --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- KARTU DATA DIRI --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 border-b pb-2 mb-4 flex items-center justify-between">
                <span><i class="fa-solid fa-user text-gold-500 mr-2"></i> Data Diri & Kontak</span>
                {{-- Tampilkan Foto Profil Kecil --}}
                @if($student->foto)
                    <a href="{{ asset('storage/'.$student->foto) }}" target="_blank">
                         <img src="{{ asset('storage/'.$student->foto) }}" alt="Foto" class="w-10 h-12 object-cover rounded border border-gray-300 hover:scale-150 transition">
                    </a>
                @endif
            </h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                {{-- Baris 1: Nama & Program --}}
                <div class="col-span-2 md:col-span-1"><span class="text-gray-500 block text-xs">Nama Lengkap</span> <span class="font-bold text-lg text-gray-800">{{ $student->nama_lengkap }}</span></div>
                <div class="col-span-2 md:col-span-1"><span class="text-gray-500 block text-xs">Program Pelatihan</span> <span class="font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">{{ $student->program->judul ?? 'Belum Memilih' }}</span></div>

                {{-- Baris 2: Identitas Kependudukan --}}
                <div><span class="text-gray-500 block text-xs">Nomor KTP</span> <span class="font-medium">{{ $student->nomor_ktp ?? '-' }}</span></div>
                <div><span class="text-gray-500 block text-xs">Nomor KK</span> <span class="font-medium">{{ $student->nomor_kk ?? '-' }}</span></div>

                {{-- Baris 3: Fisik --}}
                <div><span class="text-gray-500 block text-xs">TTL</span> <span class="font-medium">{{ $student->tempat_lahir }}, {{ $student->tanggal_lahir ? $student->tanggal_lahir->format('d M Y') : '-' }}</span></div>
                <div><span class="text-gray-500 block text-xs">Jenis Kelamin</span> <span class="font-medium">{{ $student->jenis_kelamin }}</span></div>

                {{-- Baris 4: Kontak --}}
                <div><span class="text-gray-500 block text-xs">Email</span> <span class="font-medium">{{ $student->email }}</span></div>
                <div><span class="text-gray-500 block text-xs">No. HP Peserta (WA)</span> <span class="font-medium text-green-600">{{ $student->no_hp_peserta ?? '-' }}</span></div>

                {{-- Baris 5: Tambahan --}}
                <div><span class="text-gray-500 block text-xs">Status Nikah</span> <span class="font-medium">{{ $student->status_pernikahan ?? '-' }}</span></div>
                <div><span class="text-gray-500 block text-xs">Pernah Bekerja?</span> 
                    <span class="font-bold px-2 py-0.5 rounded text-xs {{ $student->pernah_bekerja ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $student->pernah_bekerja ? 'YA' : 'TIDAK' }}
                    </span>
                </div>

                {{-- Baris 6: Alamat --}}
                <div class="col-span-2 border-t pt-2 mt-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-gray-500 block text-xs">Alamat Sesuai KTP</span> 
                            <span class="font-medium block mb-2">{{ $student->alamat_ktp ?? '-' }} <br>
                                <span class="text-xs text-gray-400">{{ $student->kota_ktp ?? '' }} {{ $student->provinsi_ktp ?? '' }}</span>
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-xs">Alamat Domisili</span> 
                            <span class="font-medium block">{{ $student->alamat_domisili ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KARTU DOKUMEN (VERSI DINAMIS) --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 border-b pb-2 mb-4 flex items-center">
                <i class="fa-solid fa-file-contract text-blue-500 mr-2"></i> Kelengkapan Dokumen
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                {{-- 1. FOTO PROFIL (Masih dari tabel students) --}}
                <div class="flex items-center justify-between p-3 border rounded-lg {{ $student->foto ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                    <span class="text-sm font-semibold {{ $student->foto ? 'text-green-800' : 'text-red-800' }}">
                        Pas Foto 3x4 <span class="text-xs text-red-500">*</span>
                    </span>
                    @if($student->foto)
                        <a href="{{ asset('storage/'.$student->foto) }}" target="_blank" class="text-xs bg-white border border-green-300 px-3 py-1 rounded-full text-green-600 hover:bg-green-600 hover:text-white transition">
                            <i class="fa-solid fa-eye mr-1"></i> Cek
                        </a>
                    @else
                        <span class="text-xs text-red-500 italic"><i class="fa-solid fa-times-circle"></i> Kosong</span>
                    @endif
                </div>

                {{-- 2. DOKUMEN DINAMIS (Looping dari Master Data) --}}
                @foreach($documentTypes as $docType)
                    @php
                        // Cari apakah siswa sudah upload dokumen jenis ini?
                        $uploadedDoc = $student->documents->firstWhere('document_type_id', $docType->id);
                        $isUploaded = !empty($uploadedDoc);
                    @endphp

                    <div class="flex items-center justify-between p-3 border rounded-lg {{ $isUploaded ? 'bg-green-50 border-green-200' : ($docType->is_required ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200') }}">
                        <div>
                            <span class="text-sm font-semibold block {{ $isUploaded ? 'text-green-800' : ($docType->is_required ? 'text-red-800' : 'text-gray-600') }}">
                                {{ $docType->nama }}
                                @if($docType->is_required) <span class="text-red-500">*</span> @endif
                            </span>
                        </div>

                        @if($isUploaded)
                            <a href="{{ asset('storage/'.$uploadedDoc->file_path) }}" target="_blank" class="text-xs bg-white border border-green-300 px-3 py-1 rounded-full text-green-600 hover:bg-green-600 hover:text-white transition">
                                <i class="fa-solid fa-eye mr-1"></i> Cek
                            </a>
                        @else
                            @if($docType->is_required)
                                <span class="text-xs text-red-500 italic"><i class="fa-solid fa-times-circle"></i> Kosong</span>
                            @else
                                <span class="text-xs text-gray-400 italic">-</span>
                            @endif
                        @endif
                    </div>
                @endforeach

            </div>
        </div>

        {{-- KARTU PENDIDIKAN --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 border-b pb-2 mb-4 flex items-center">
                <i class="fa-solid fa-graduation-cap text-purple-500 mr-2"></i> Pendidikan
            </h3>
            <ul class="space-y-3">
                @forelse($student->educations as $edu)
                    <li class="text-sm border-b last:border-0 pb-2">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-bold block text-gray-800">{{ $edu->nama_institusi }}</span>
                                <span class="text-xs bg-purple-50 text-purple-700 px-2 py-0.5 rounded mr-1">{{ $edu->tingkat }}</span>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $edu->kategori }}</span>
                            </div>
                            <div class="text-right text-xs text-gray-500">
                                {{ $edu->tahun_masuk ?? '?' }} - {{ $edu->tahun_lulus ?? 'Sekarang' }}
                            </div>
                        </div>
                        <div class="text-gray-500 text-xs mt-1 grid grid-cols-2 gap-2">
                            <span>Jurusan: {{ $edu->jurusan ?? '-' }}</span>
                            @if($edu->kategori == 'Formal')
                                <span>Nilai Rata-rata: <b>{{ $edu->nilai_rata_rata ?? '-' }}</b></span>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="text-gray-400 italic text-sm text-center py-4 bg-gray-50 rounded">Belum mengisi data pendidikan.</li>
                @endforelse
            </ul>
        </div>
        
        {{-- KARTU KELUARGA --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 border-b pb-2 mb-4 flex items-center">
                <i class="fa-solid fa-users text-orange-500 mr-2"></i> Data Keluarga
            </h3>
            <ul class="space-y-3">
                @forelse($student->families as $fam)
                    <li class="text-sm border-b last:border-0 pb-2">
                        <div class="flex justify-between">
                            <span class="font-bold text-gray-800">{{ $fam->nama }} <span class="text-xs font-normal text-gray-500">({{ $fam->usia }} Thn)</span></span>
                            <span class="text-xs bg-orange-100 text-orange-800 px-2 py-0.5 rounded">{{ $fam->hubungan }}</span>
                        </div>
                        <div class="text-gray-500 text-xs mt-1">
                             Pekerjaan: {{ $fam->pekerjaan ?? '-' }} | Penghasilan: {{ $fam->penghasilan ?? '-' }}
                        </div>
                    </li>
                @empty
                    <li class="text-gray-400 italic text-sm text-center py-4 bg-gray-50 rounded">Belum mengisi data keluarga.</li>
                @endforelse
            </ul>
        </div>

        {{-- KARTU PENGALAMAN (Hanya tampil jika ada) --}}
        @if($student->experiences->count() > 0)
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 border-b pb-2 mb-4 flex items-center">
                <i class="fa-solid fa-briefcase text-teal-500 mr-2"></i> Pengalaman Kerja/Organisasi
            </h3>
            <ul class="space-y-3">
                @foreach($student->experiences as $exp)
                    <li class="text-sm border-b last:border-0 pb-2">
                        <div class="flex justify-between">
                            <span class="font-bold text-gray-800">{{ $exp->nama_instansi }}</span>
                            <span class="text-xs bg-teal-50 text-teal-700 px-2 py-0.5 rounded">{{ $exp->tipe }}</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            <span class="font-semibold">{{ $exp->posisi }}</span> 
                            ({{ $exp->tanggal_mulai ? $exp->tanggal_mulai->format('M Y') : '?' }} - {{ $exp->tanggal_selesai ? $exp->tanggal_selesai->format('M Y') : '?' }})
                        </div>
                        @if($exp->tipe == 'Pekerjaan')
                            <div class="text-xs text-red-400 mt-1 italic">
                                Alasan berhenti: {{ $exp->alasan_berhenti ?? '-' }}
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

    </div>

    {{-- KOLOM KANAN: PANEL KONTROL (STICKY) --}}
    <div class="lg:col-span-1">
        <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-gold-500 sticky top-6">
            <h3 class="font-bold text-lg text-gray-800 mb-4">Panel Keputusan</h3>
            
            {{-- Status Saat Ini --}}
            <div class="mb-6 bg-gray-50 p-3 rounded-lg text-center">
                <span class="text-xs text-gray-500 uppercase tracking-wide">Status Saat Ini</span>
                <div class="font-bold text-xl text-gold-600 mt-1">{{ $student->status }}</div>
            </div>

            <form action="{{ route('admin.students.process-verify', $student->id) }}" method="POST">
                @csrf
                
                {{-- Catatan Admin --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Admin <span class="text-red-500">*</span></label>
                    <textarea name="admin_note" rows="4" class="w-full rounded-lg border-gray-300 focus:border-gold-500 text-sm" placeholder="Tulis catatan jika minta Revisi (misal: Foto KTP buram) atau Alasan Penolakan..."></textarea>
                    <p class="text-xs text-gray-400 mt-1">Wajib diisi jika memilih Revisi atau Tolak.</p>
                </div>

                {{-- Tombol Aksi --}}
                <div class="space-y-3">
                    <button type="submit" name="action" value="terima" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold shadow-md transition flex items-center justify-center group">
                        <i class="fa-solid fa-check-circle mr-2 group-hover:scale-110 transition"></i> TERIMA (Lolos Admin)
                    </button>
                    
                    <button type="submit" name="action" value="revisi" class="w-full py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-bold shadow-md transition flex items-center justify-center">
                        <i class="fa-solid fa-rotate-left mr-2"></i> MINTA REVISI
                    </button>
                    
                    <button type="submit" name="action" value="tolak" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold shadow-md transition flex items-center justify-center" onclick="return confirm('Yakin ingin menolak siswa ini secara permanen?')">
                        <i class="fa-solid fa-ban mr-2"></i> TOLAK SISWA
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection