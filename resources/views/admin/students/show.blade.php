@extends('layouts.app')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.students.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <span>Detail Siswa: {{ $student->nama_lengkap }}</span>
    </div>
@endsection

@section('content')
<div class="space-y-8 pb-20">
    
    {{-- HEADER PROFILE --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
        <div class="flex items-center gap-6">
            <div class="relative">
                @if($student->foto)
                    <img src="{{ asset('storage/' . $student->foto) }}" class="w-24 h-24 rounded-full object-cover border-4 border-gold-100 shadow-sm">
                    <button onclick="window.open('{{ asset('storage/' . $student->foto) }}', '_blank')" class="absolute bottom-0 right-0 bg-gray-800 text-white p-1.5 rounded-full text-xs hover:bg-black transition" title="Lihat Foto Full"><i class="fa-solid fa-expand"></i></button>
                @else
                    <div class="w-24 h-24 rounded-full bg-gold-100 flex items-center justify-center text-gold-600 text-3xl font-bold">{{ substr($student->nama_lengkap, 0, 1) }}</div>
                @endif
            </div>
            <div>
                <h2 class="text-3xl font-bold text-gray-900 font-serif">{{ $student->nama_lengkap }}</h2>
                <div class="flex flex-wrap items-center gap-3 mt-2">
                    <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-md text-sm font-bold border border-blue-100">
                        {{ $student->program->judul ?? 'Belum Pilih Program' }}
                    </span>
                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-md text-sm border border-gray-200">
                        No. KTP: {{ $student->nomor_ktp ?? '-' }}
                    </span>
                    @if($student->pernah_bekerja)
                        <span class="bg-purple-50 text-purple-700 px-3 py-1 rounded-md text-sm border border-purple-100 font-semibold">
                            Berpengalaman Kerja
                        </span>
                    @endif
                </div>
                
                {{-- PROGRESS BAR DATA --}}
                @if(isset($student->data_completion))
                <div class="mt-3 flex items-center gap-2">
                    <div class="w-32 bg-gray-200 rounded-full h-2.5">
                        <div class="bg-gold-500 h-2.5 rounded-full" style="width: {{ $student->data_completion['percentage'] }}%"></div>
                    </div>
                    <span class="text-xs font-bold {{ $student->data_completion['is_complete'] ? 'text-green-600' : 'text-orange-500' }}">
                        {{ $student->data_completion['percentage'] }}% Lengkap
                    </span>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 min-w-[300px]">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Aksi Verifikasi</h4>
            <form action="{{ route('admin.students.update', $student) }}" method="POST" class="flex gap-2">
                @csrf @method('PUT')
                
                <input type="hidden" name="nama_lengkap" value="{{ $student->nama_lengkap }}">
                <input type="hidden" name="email" value="{{ $student->email }}">
                
                <select name="status" class="flex-1 rounded-lg border-gray-300 text-sm focus:border-gold-500 focus:ring-gold-500">
                    <option value="Mendaftar" {{ $student->status == 'Mendaftar' ? 'selected' : '' }}>Mendaftar</option>
                    <option value="Menunggu Verifikasi" {{ $student->status == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="Perlu Revisi" {{ $student->status == 'Perlu Revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                    <option value="Wawancara" {{ $student->status == 'Wawancara' ? 'selected' : '' }}>Wawancara</option>
                    <option value="Pelatihan" {{ $student->status == 'Pelatihan' ? 'selected' : '' }}>Pelatihan</option>
                    <option value="Magang" {{ $student->status == 'Magang' ? 'selected' : '' }}>Magang</option>
                    <option value="Kerja" {{ $student->status == 'Kerja' ? 'selected' : '' }}>Kerja</option>
                    <option value="Alumni" {{ $student->status == 'Alumni' ? 'selected' : '' }}>Alumni</option>
                    <option value="Keluar" {{ $student->status == 'Keluar' ? 'selected' : '' }}>Keluar</option>
                    <option value="Ditolak" {{ $student->status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <button type="submit" class="bg-gold-500 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-gold-600 transition">
                    Update
                </button>
            </form>
            <div class="mt-3 flex justify-end gap-2">
                <a href="{{ route('admin.students.verify', $student->id) }}" class="text-xs font-bold text-gold-600 hover:underline flex items-center">
                     <i class="fa-solid fa-clipboard-check mr-1"></i> Ke Ruang Verifikasi
                </a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('admin.students.export-biodata', $student) }}" target="_blank" class="text-xs font-bold text-red-600 hover:underline flex items-center">
                    <i class="fa-solid fa-file-pdf mr-1"></i> Download PDF
                </a>
            </div>
        </div>
    </div>

    {{-- GRID INFO --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- CARD 1: FISIK --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 md:col-span-1">
            <h3 class="font-bold text-lg text-gray-900 mb-4 border-b pb-2">Fisik & Kesehatan</h3>
            <ul class="space-y-3 text-sm">
                <li class="flex justify-between"><span class="text-gray-500">Tinggi Badan</span> <span class="font-medium">{{ $student->tinggi_badan ?? '-' }} cm</span></li>
                <li class="flex justify-between"><span class="text-gray-500">Berat Badan</span> <span class="font-medium">{{ $student->berat_badan ?? '-' }} kg</span></li>
                <li class="flex justify-between"><span class="text-gray-500">Gol. Darah</span> <span class="font-medium">{{ $student->golongan_darah ?? '-' }}</span></li>
                <li class="flex justify-between"><span class="text-gray-500">Jenis Kelamin</span> <span class="font-medium">{{ $student->jenis_kelamin }}</span></li>
            </ul>
        </div>

        {{-- CARD 2: KONTAK & IDENTITAS --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 md:col-span-2">
            <h3 class="font-bold text-lg text-gray-900 mb-4 border-b pb-2">Kontak & Identitas</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 uppercase">Email</p>
                    <p class="font-medium text-gray-800">{{ $student->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">No. HP Peserta (WA)</p>
                    <p class="font-medium text-gray-800">{{ $student->no_hp_peserta ?? '-' }}</p>
                </div>
                
                <div>
                    <p class="text-xs text-gray-400 uppercase">Nomor KTP</p>
                    <p class="font-medium text-gray-800">{{ $student->nomor_ktp ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">Nomor KK</p>
                    <p class="font-medium text-gray-800">{{ $student->nomor_kk ?? '-' }}</p>
                </div>
                
                <div>
                    <p class="text-xs text-gray-400 uppercase">Tempat, Tgl Lahir</p>
                    <p class="font-medium text-gray-800">{{ $student->tempat_lahir }}, {{ $student->tanggal_lahir ? $student->tanggal_lahir->format('d F Y') : '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">Status Pernikahan</p>
                    <p class="font-medium text-gray-800">{{ $student->status_pernikahan ?? '-' }}</p>
                </div>

                 <div>
                    <p class="text-xs text-gray-400 uppercase">Paspor / NPWP</p>
                    <p class="font-medium text-gray-800">
                        {{ $student->nomor_paspor ? 'Paspor: '.$student->nomor_paspor : '' }} <br>
                        {{ $student->nomor_npwp ? 'NPWP: '.$student->nomor_npwp : '' }}
                        {{ !$student->nomor_paspor && !$student->nomor_npwp ? '-' : '' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">No. HP Orang Tua</p>
                    <p class="font-medium text-gray-800">{{ $student->no_hp_ortu ?? '-' }}</p>
                </div>

                <div class="sm:col-span-2 border-t pt-2">
                    <p class="text-xs text-gray-400 uppercase">Alamat KTP</p>
                    <p class="font-medium text-gray-800 mb-2">
                        {{ $student->alamat_ktp ?? '-' }} 
                        @if($student->kota_ktp || $student->provinsi_ktp)
                            <br><span class="text-xs text-gray-500">{{ $student->kota_ktp ?? '' }} {{ $student->provinsi_ktp ?? '' }}</span>
                        @endif
                    </p>
                    
                    <p class="text-xs text-gray-400 uppercase">Alamat Domisili</p>
                    <p class="font-medium text-gray-800">{{ $student->alamat_domisili ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- [BARU] TABEL DOKUMEN --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800"><i class="fa-solid fa-file-contract mr-2 text-gold-500"></i> Dokumen Pendukung</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {{-- Loop Master Data agar terlihat mana yang kosong --}}
            @if(isset($documentTypes) && $documentTypes->count() > 0)
                @foreach($documentTypes as $docType)
                    @php
                        // Cek apakah user punya dokumen ini
                        $uploadedDoc = $student->documents->firstWhere('document_type_id', $docType->id);
                        $isUploaded = !empty($uploadedDoc);
                    @endphp

                    <div class="flex items-center justify-between p-3 border rounded-lg {{ $isUploaded ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                        <div class="flex-1 min-w-0 pr-2">
                            <span class="text-sm font-semibold block truncate {{ $isUploaded ? 'text-green-800' : 'text-gray-500' }}">
                                {{ $docType->nama }}
                                @if($docType->is_required) <span class="text-red-500">*</span> @endif
                            </span>
                            @if($isUploaded)
                                <span class="text-[10px] text-green-600 block">Diupload: {{ $uploadedDoc->updated_at->format('d M Y') }}</span>
                            @else
                                <span class="text-[10px] text-gray-400 block italic">Belum diupload</span>
                            @endif
                        </div>

                        @if($isUploaded)
                            <a href="{{ asset('storage/'.$uploadedDoc->file_path) }}" target="_blank" class="flex-shrink-0 text-xs bg-white border border-green-300 px-3 py-1 rounded-full text-green-600 hover:bg-green-600 hover:text-white transition shadow-sm">
                                <i class="fa-solid fa-eye mr-1"></i> Lihat
                            </a>
                        @else
                            <span class="flex-shrink-0 text-gray-300 text-lg"><i class="fa-solid fa-file-circle-xmark"></i></span>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="col-span-3 text-center py-4 text-gray-500 italic">
                    Master Data Dokumen belum disetting. Silakan hubungi admin sistem.
                </div>
            @endif
        </div>
    </div>

    {{-- TABEL PENDIDIKAN --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800"><i class="fa-solid fa-graduation-cap mr-2 text-gold-500"></i> Riwayat Pendidikan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3">Tingkat</th>
                        <th class="px-6 py-3">Nama Institusi</th>
                        <th class="px-6 py-3">Jurusan</th>
                        <th class="px-6 py-3">Periode / Durasi</th>
                        <th class="px-6 py-3">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($student->educations as $edu)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $edu->tingkat }} <span class="text-xs text-gray-500">({{ $edu->kategori }})</span></td>
                        <td class="px-6 py-4">{{ $edu->nama_institusi }}</td>
                        <td class="px-6 py-4">{{ $edu->jurusan ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($edu->kategori == 'Formal')
                                {{ $edu->tahun_masuk ?? '?' }} - {{ $edu->tahun_lulus ?? '?' }}
                            @else
                                {{ $edu->tahun_masuk }} {{-- Ini isinya Durasi Waktu untuk non-formal --}}
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $edu->nilai_rata_rata ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">Data pendidikan belum diisi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TABEL KELUARGA --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800"><i class="fa-solid fa-users mr-2 text-gold-500"></i> Data Keluarga</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3">Hubungan</th>
                        <th class="px-6 py-3">Nama Lengkap</th>
                        <th class="px-6 py-3">L/P</th>
                        <th class="px-6 py-3">Usia</th>
                        <th class="px-6 py-3">Pekerjaan</th>
                        <th class="px-6 py-3">Penghasilan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($student->families as $fam)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $fam->hubungan }}</td>
                        <td class="px-6 py-4">{{ $fam->nama }}</td>
                        <td class="px-6 py-4">{{ $fam->jenis_kelamin }}</td>
                        <td class="px-6 py-4">{{ $fam->usia ?? '-' }} Thn</td>
                        <td class="px-6 py-4">{{ $fam->pekerjaan ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $fam->penghasilan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500 italic">Data keluarga belum diisi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TABEL PENGALAMAN --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800"><i class="fa-solid fa-briefcase mr-2 text-gold-500"></i> Pengalaman Kerja</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3">Perusahaan/Instansi</th>
                        <th class="px-6 py-3">Posisi</th>
                        <th class="px-6 py-3">Periode</th>
                        <th class="px-6 py-3">Gaji Akhir</th>
                        <th class="px-6 py-3">Alasan Berhenti</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($student->experiences as $exp)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $exp->nama_instansi }}
                            @if($exp->tipe == 'Organisasi') <span class="text-xs bg-gray-200 px-1 rounded ml-1">Org</span> @endif
                        </td>
                        <td class="px-6 py-4">{{ $exp->posisi }}</td>
                        <td class="px-6 py-4">
                            {{ $exp->tanggal_mulai ? \Carbon\Carbon::parse($exp->tanggal_mulai)->format('M Y') : '-' }} s/d 
                            {{ $exp->tanggal_selesai ? \Carbon\Carbon::parse($exp->tanggal_selesai)->format('M Y') : 'Sekarang' }}
                        </td>
                        <td class="px-6 py-4">{{ $exp->gaji_akhir ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-500 italic">{{ $exp->alasan_berhenti ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">Data pengalaman belum diisi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection