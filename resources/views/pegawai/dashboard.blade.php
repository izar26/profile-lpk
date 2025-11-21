@extends('layouts.app')

@section('header', 'Dashboard Pegawai')

@section('content')

    {{-- NOTIFIKASI KELENGKAPAN DATA --}}
    @if(Auth::user()->employee && !Auth::user()->employee->data_completion['is_complete'])
        <div class="bg-orange-50 border-l-4 border-orange-400 p-4 mb-6 rounded-r-lg shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-circle-exclamation text-orange-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-orange-700 font-bold">
                        Data Kepegawaian Belum Lengkap
                    </p>
                    <p class="text-xs text-orange-600 mt-1">
                        Baru terisi <strong>{{ Auth::user()->employee->data_completion['text'] }}</strong> ({{ Auth::user()->employee->data_completion['percentage'] }}%). 
                        Mohon lengkapi data untuk arsip HRD.
                    </p>
                </div>
            </div>
            <div class="ml-auto pl-3">
                <a href="{{ route('pegawai.biodata.edit') }}" 
                   class="whitespace-nowrap px-4 py-2 bg-orange-100 text-orange-700 text-xs font-bold rounded-lg hover:bg-orange-200 transition-colors">
                    Lengkapi Sekarang <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- KARTU PROFIL --}}
        <div class="md:col-span-1">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 text-center h-full">
                <div class="relative w-24 h-24 mx-auto mb-4">
                    @if(Auth::user()->employee && Auth::user()->employee->foto)
                        <img src="{{ asset('storage/' . Auth::user()->employee->foto) }}" 
                             class="w-24 h-24 rounded-full object-cover border-4 border-gold-100 shadow-sm">
                    @else
                        <div class="w-24 h-24 rounded-full bg-gold-50 flex items-center justify-center text-gold-500 text-3xl font-bold border-4 border-gold-100 shadow-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                
                <h3 class="text-lg font-bold text-gray-900">{{ Auth::user()->name }}</h3>
                <p class="text-sm text-gray-500 mt-1">
                    {{ Auth::user()->employee->nip ?? 'NIP Belum Diatur' }}
                </p>
                
                <div class="mt-6 space-y-2">
                    @if(Auth::user()->employee)
                        <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 inline-block">
                            {{ Auth::user()->employee->jabatan }}
                        </span>
                        <div class="block pt-2">
                             <span class="px-3 py-1 rounded-full text-xs font-medium border border-gray-300 text-gray-600">
                                {{ Auth::user()->employee->status_kepegawaian }}
                            </span>
                        </div>
                    @else
                        <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                            Data Belum Terhubung
                        </span>
                    @endif
                </div>

                <div class="mt-6 border-t border-gray-100 pt-4">
                    <a href="{{ route('pegawai.biodata.edit') }}" class="text-sm text-gold-600 hover:text-gold-800 font-semibold hover:underline">
                        Edit Profil Saya
                    </a>
                </div>
            </div>
        </div>

        {{-- KARTU DETAIL & AKSI --}}
        <div class="md:col-span-2">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 h-full flex flex-col">
                <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                    <h3 class="text-lg font-serif font-bold text-gold-500">
                        Informasi Kepegawaian
                    </h3>
                    @if(Auth::user()->employee)
                        <a href="{{ route('pegawai.biodata.print') }}" target="_blank" 
                           class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 text-xs font-bold rounded-lg hover:bg-red-100 transition">
                            <i class="fa-solid fa-file-pdf mr-2"></i> Cetak Biodata
                        </a>
                    @endif
                </div>
                
                @if(Auth::user()->employee)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Email</p>
                            <p class="text-gray-800 font-medium">{{ Auth::user()->employee->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Telepon</p>
                            <p class="text-gray-800 font-medium">{{ Auth::user()->employee->telepon ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Pendidikan Terakhir</p>
                            <p class="text-gray-800 font-medium">{{ Auth::user()->employee->pendidikan_terakhir ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Sosial Media</p>
                            <div class="flex gap-3">
                                @if(Auth::user()->employee->linkedin)
                                    <a href="{{ Auth::user()->employee->linkedin }}" target="_blank" class="text-blue-600 hover:text-blue-800"><i class="fa-brands fa-linkedin text-xl"></i></a>
                                @endif
                                @if(Auth::user()->employee->instagram)
                                    <a href="{{ Auth::user()->employee->instagram }}" target="_blank" class="text-pink-600 hover:text-pink-800"><i class="fa-brands fa-instagram text-xl"></i></a>
                                @endif
                                @if(!Auth::user()->employee->linkedin && !Auth::user()->employee->instagram)
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 mt-auto">
                        <p class="text-xs text-gray-500 uppercase font-bold mb-2">Alamat Lengkap</p>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            {{ Auth::user()->employee->alamat ?? '-' }}
                            <br>
                            {{ Auth::user()->employee->kota }} {{ Auth::user()->employee->provinsi }} {{ Auth::user()->employee->kode_pos }}
                        </p>
                    </div>

                @else
                    <div class="flex-1 flex flex-col items-center justify-center text-center py-10">
                        <i class="fa-solid fa-user-slash text-gray-300 text-4xl mb-3"></i>
                        <h4 class="text-gray-900 font-bold">Data Belum Dihubungkan</h4>
                        <p class="text-gray-500 text-sm mt-1">Silakan hubungi Admin untuk menghubungkan akun Anda dengan data kepegawaian.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection