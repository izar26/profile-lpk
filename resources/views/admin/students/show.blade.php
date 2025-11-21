@extends('layouts.app')

@section('header')
    Detail Siswa: {{ $student->nama }}
@endsection

@section('content')
<div class="space-y-6">
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center gap-4">
            @if($student->foto)
                <img src="{{ asset('storage/' . $student->foto) }}" class="w-20 h-20 rounded-full object-cover border-4 border-gold-100">
            @else
                <div class="w-20 h-20 rounded-full bg-gold-100 flex items-center justify-center text-gold-600 text-2xl font-bold">{{ substr($student->nama, 0, 1) }}</div>
            @endif
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $student->nama }}</h2>
                <p class="text-gray-500">{{ $student->program->judul ?? 'Belum ada program' }}</p>
                <div class="mt-2 flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $student->data_completion['is_complete'] ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                        Data: {{ $student->data_completion['text'] }} ({{ $student->data_completion['percentage'] }}%)
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                        Status: {{ $student->status }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="flex gap-3">
            <a href="{{ route('admin.students.export-biodata', $student) }}" target="_blank" 
               class="px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 transition flex items-center font-semibold">
               <i class="fa-solid fa-file-pdf mr-2"></i> Cetak Biodata
            </a>
            
            <a href="{{ route('admin.students.index') }}" 
               class="px-4 py-2 bg-gray-100 text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-200 transition font-medium">
               Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gold-600 border-b pb-2 mb-4">Data Pribadi</h3>
            <div class="space-y-3">
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">NIK</span> <span class="col-span-2 font-medium">{{ $student->NIK ?? '-' }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">TTL</span> <span class="col-span-2 font-medium">{{ $student->tempat_lahir }}, {{ $student->tanggal_lahir ? $student->tanggal_lahir->format('d M Y') : '-' }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Gender</span> <span class="col-span-2 font-medium">{{ $student->jenis_kelamin == 'L' ? 'Laki-laki' : ($student->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Gol. Darah</span> <span class="col-span-2 font-medium">{{ $student->golongan_darah ?? '-' }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Agama</span> <span class="col-span-2 font-medium">{{ $student->agama ?? '-' }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Email</span> <span class="col-span-2 font-medium">{{ $student->email ?? '-' }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Telepon</span> <span class="col-span-2 font-medium">{{ $student->telepon ?? '-' }}</span></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gold-600 border-b pb-2 mb-4">Keluarga & Pendidikan</h3>
            <div class="space-y-3">
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Nama Ayah</span> <span class="col-span-2 font-medium">{{ $student->nama_ayah ?? '-' }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Pekerjaan Ayah</span> <span class="col-span-2 font-medium">{{ $student->pekerjaan_ayah ?? '-' }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Nama Ibu</span> <span class="col-span-2 font-medium">{{ $student->nama_ibu ?? '-' }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Pekerjaan Ibu</span> <span class="col-span-2 font-medium">{{ $student->pekerjaan_ibu ?? '-' }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">No HP Ortu</span> <span class="col-span-2 font-medium">{{ $student->no_hp_ortu ?? '-' }}</span></div>
                <div class="my-2 border-t border-gray-100"></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Sekolah Asal</span> <span class="col-span-2 font-medium">{{ $student->sekolah_asal ?? '-' }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Tahun Lulus</span> <span class="col-span-2 font-medium">{{ $student->tahun_lulus ?? '-' }}</span></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 md:col-span-2">
            <h3 class="text-lg font-bold text-gold-600 border-b pb-2 mb-4">Alamat Lengkap</h3>
            <p class="text-gray-700 mb-4">{{ $student->alamat ?? '-' }}</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 border-t border-gray-50">
                <div><span class="block text-xs text-gray-500">Kota/Kab</span> <span class="font-medium">{{ $student->kota ?? '-' }}</span></div>
                <div><span class="block text-xs text-gray-500">Provinsi</span> <span class="font-medium">{{ $student->provinsi ?? '-' }}</span></div>
                <div><span class="block text-xs text-gray-500">Kode Pos</span> <span class="font-medium">{{ $student->kode_pos ?? '-' }}</span></div>
            </div>
        </div>

    </div>
</div>
@endsection