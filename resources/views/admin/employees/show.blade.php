@extends('layouts.app')

@section('header')
    Detail Pegawai: {{ $employee->nama }}
@endsection

@section('content')
<div class="space-y-6">
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center gap-4">
            @if($employee->foto)
                <img src="{{ asset('storage/' . $employee->foto) }}" class="w-20 h-20 rounded-full object-cover border-4 border-gold-100">
            @else
                <div class="w-20 h-20 rounded-full bg-gold-100 flex items-center justify-center text-gold-600 text-2xl font-bold">{{ substr($employee->nama, 0, 1) }}</div>
            @endif
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $employee->nama }}</h2>
                <p class="text-gray-500">{{ $employee->jabatan ?? '-' }}</p>
                <div class="mt-2 flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $employee->data_completion['is_complete'] ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                        Data: {{ $employee->data_completion['text'] }} ({{ $employee->data_completion['percentage'] }}%)
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                        Status: {{ $employee->status_kepegawaian }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="flex gap-3">
            <a href="{{ route('admin.employees.export-biodata', $employee) }}" target="_blank" 
               class="px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 transition flex items-center font-semibold">
               <i class="fa-solid fa-file-pdf mr-2"></i> Cetak Biodata
            </a>
            <a href="{{ route('admin.employees.index') }}" 
               class="px-4 py-2 bg-gray-100 text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-200 transition font-medium">
               Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gold-600 border-b pb-2 mb-4">Data Pribadi</h3>
            <div class="space-y-3">
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">NIP</span> <span class="col-span-2 font-medium">{{ $employee->nip ?? '-' }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">TTL</span> <span class="col-span-2 font-medium">{{ $employee->tempat_lahir }}, {{ $employee->tanggal_lahir ? $employee->tanggal_lahir->format('d M Y') : '-' }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Gender</span> <span class="col-span-2 font-medium">{{ $employee->jenis_kelamin == 'L' ? 'Laki-laki' : ($employee->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Agama</span> <span class="col-span-2 font-medium">{{ $employee->agama ?? '-' }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Email</span> <span class="col-span-2 font-medium">{{ $employee->email ?? '-' }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Telepon</span> <span class="col-span-2 font-medium">{{ $employee->telepon ?? '-' }}</span></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gold-600 border-b pb-2 mb-4">Detail Pekerjaan</h3>
            <div class="space-y-3">
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Jabatan</span> <span class="col-span-2 font-medium">{{ $employee->jabatan ?? '-' }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Status</span> <span class="col-span-2 font-medium">{{ $employee->status_kepegawaian ?? '-' }}</span></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Pendidikan</span> <span class="col-span-2 font-medium">{{ $employee->pendidikan_terakhir ?? '-' }}</span></div>
                <div class="my-2 border-t border-gray-100"></div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">LinkedIn</span> 
                    <span class="col-span-2 font-medium">
                        @if($employee->linkedin) <a href="{{ $employee->linkedin }}" target="_blank" class="text-blue-600 hover:underline">Link Profil</a> @else - @endif
                    </span>
                </div>
                <div class="grid grid-cols-3"><span class="text-gray-500 text-sm">Instagram</span>
                    <span class="col-span-2 font-medium">
                        @if($employee->instagram) <a href="{{ $employee->instagram }}" target="_blank" class="text-pink-600 hover:underline">Link Profil</a> @else - @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 md:col-span-2">
            <h3 class="text-lg font-bold text-gold-600 border-b pb-2 mb-4">Alamat Lengkap</h3>
            <p class="text-gray-700 mb-4">{{ $employee->alamat ?? '-' }}</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 border-t border-gray-50">
                <div><span class="block text-xs text-gray-500">Kota/Kab</span> <span class="font-medium">{{ $employee->kota ?? '-' }}</span></div>
                <div><span class="block text-xs text-gray-500">Provinsi</span> <span class="font-medium">{{ $employee->provinsi ?? '-' }}</span></div>
                <div><span class="block text-xs text-gray-500">Kode Pos</span> <span class="font-medium">{{ $employee->kode_pos ?? '-' }}</span></div>
            </div>
        </div>

    </div>
</div>
@endsection