@extends('layouts.app')

@section('header', 'Dashboard Siswa')

@section('content')

    {{-- 
        BAGIAN 1: NOTIFIKASI KELENGKAPAN DATA 
        Hanya muncul jika user sudah terhubung dengan data siswa 
        TAPI datanya belum lengkap (is_complete = false)
    --}}
    @if(Auth::user()->student && !Auth::user()->student->data_completion['is_complete'])
        <div class="bg-orange-50 border-l-4 border-orange-400 p-4 mb-6 rounded-r-lg shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-circle-exclamation text-orange-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-orange-700 font-bold">
                        Data Diri Belum Lengkap
                    </p>
                    <p class="text-xs text-orange-600 mt-1">
                        Baru terisi <strong>{{ Auth::user()->student->data_completion['text'] }}</strong> ({{ Auth::user()->student->data_completion['percentage'] }}%). 
                        Mohon lengkapi data untuk keperluan administrasi dan sertifikat.
                    </p>
                </div>
            </div>
            <div class="ml-auto pl-3">
                <a href="{{ route('siswa.biodata.edit') }}" 
                   class="whitespace-nowrap px-4 py-2 bg-orange-100 text-orange-700 text-xs font-bold rounded-lg hover:bg-orange-200 transition-colors">
                    Lengkapi Sekarang <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- BAGIAN 2: KARTU PROFIL SINGKAT --}}
        <div class="md:col-span-1">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 text-center h-full">
                <div class="relative w-24 h-24 mx-auto mb-4">
                    {{-- Cek apakah data siswa dan foto ada --}}
                    @if(Auth::user()->student && Auth::user()->student->foto)
                        <img src="{{ asset('storage/' . Auth::user()->student->foto) }}" 
                             class="w-24 h-24 rounded-full object-cover border-4 border-gold-100 shadow-sm">
                    @else
                        {{-- Avatar Default (Inisial Nama) --}}
                        <div class="w-24 h-24 rounded-full bg-gold-50 flex items-center justify-center text-gold-500 text-3xl font-bold border-4 border-gold-100 shadow-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                
                <h3 class="text-lg font-bold text-gray-900">{{ Auth::user()->name }}</h3>
                
                {{-- Cek NIK --}}
                <p class="text-sm text-gray-500 mt-1">
                    {{ Auth::user()->student->NIK ?? 'NIK Belum Terhubung' }}
                </p>
                
                <div class="mt-6">
                    {{-- Badge Status --}}
                    @if(Auth::user()->student)
                        <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide
                            @if(Auth::user()->student->status == 'Mendaftar') bg-gray-100 text-gray-600
                            @elseif(Auth::user()->student->status == 'Pelatihan') bg-blue-100 text-blue-700
                            @elseif(Auth::user()->student->status == 'Magang') bg-purple-100 text-purple-700
                            @elseif(Auth::user()->student->status == 'Kerja') bg-green-100 text-green-700
                            @elseif(Auth::user()->student->status == 'Alumni') bg-gold-100 text-gold-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ Auth::user()->student->status }}
                        </span>
                    @else
                        <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                            Data Belum Terhubung
                        </span>
                    @endif
                </div>

                <div class="mt-6 border-t border-gray-100 pt-4">
                    <a href="{{ route('siswa.biodata.edit') }}" class="text-sm text-gold-600 hover:text-gold-800 font-semibold hover:underline">
                        Edit Profil Saya
                    </a>
                </div>
            </div>
        </div>

        {{-- BAGIAN 3: KARTU INFORMASI PROGRAM --}}
        <div class="md:col-span-2">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 h-full flex flex-col">
                <h3 class="text-lg font-serif font-bold text-gold-500 mb-6 border-b border-gray-100 pb-2">
                    Program Pelatihan Saya
                </h3>
                
                {{-- Logika Pengecekan Program --}}
                @if(Auth::user()->student && Auth::user()->student->program)
                    
                    <div class="flex flex-col sm:flex-row items-start gap-6">
                        {{-- Gambar Program --}}
                        @if(Auth::user()->student->program->gambar_fitur)
                            <img src="{{ asset('storage/' . Auth::user()->student->program->gambar_fitur) }}" 
                                 class="w-full sm:w-32 h-32 rounded-xl object-cover shadow-sm border border-gray-100">
                        @else
                            <div class="w-full sm:w-32 h-32 rounded-xl bg-gray-50 flex items-center justify-center text-gray-300 border border-gray-100">
                                <i class="fa-solid fa-image text-3xl"></i>
                            </div>
                        @endif

                        <div class="flex-1">
                            <h4 class="text-xl font-bold text-gray-900 mb-2">
                                {{ Auth::user()->student->program->judul }}
                            </h4>
                            <p class="text-gray-600 text-sm leading-relaxed mb-4">
                                {{ Auth::user()->student->program->deskripsi_singkat }}
                            </p>
                            
                            <div class="p-3 bg-blue-50 rounded-lg border border-blue-100 inline-flex items-start gap-2">
                                <i class="fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
                                <p class="text-xs text-blue-700">
                                    <strong>Info:</strong> Hubungi admin jika terjadi kesalahan pemilihan program atau ingin mengajukan perpindahan jurusan.
                                </p>
                            </div>
                        </div>
                    </div>

                @elseif(Auth::user()->student)
                    {{-- Case: Data siswa ada, tapi program_id null --}}
                    <div class="flex-1 flex flex-col items-center justify-center text-center py-10">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <i class="fa-solid fa-chalkboard-user text-gray-300 text-3xl"></i>
                        </div>
                        <h4 class="text-gray-900 font-bold">Belum Memilih Program</h4>
                        <p class="text-gray-500 text-sm mt-1">Anda belum terdaftar di program pelatihan manapun.</p>
                    </div>

                @else
                    {{-- Case: Data siswa belum dihubungkan sama sekali --}}
                    <div class="flex-1 flex flex-col items-center justify-center text-center py-10 bg-red-50 rounded-xl border border-red-100 border-dashed">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm">
                            <i class="fa-solid fa-link-slash text-red-400 text-3xl"></i>
                        </div>
                        <h4 class="text-lg font-bold text-red-800 mb-2">Akun Belum Terhubung</h4>
                        <p class="text-sm text-red-600 max-w-md mx-auto px-4">
                            Halo <strong>{{ Auth::user()->name }}</strong>, akun login Anda belum dihubungkan dengan data siswa di database kami. 
                            <br><br>
                            Silakan hubungi Admin LPK untuk verifikasi data.
                        </p>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection