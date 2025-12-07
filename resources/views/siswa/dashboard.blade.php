@extends('layouts.app')

@section('header', 'Dashboard Siswa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- HEADER SAMBUTAN --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Halo, {{ Auth::user()->name }}! 👋</h2>
            <p class="text-gray-500">Selamat datang di portal pendaftaran LPK Hachimitsu.</p>
        </div>
        @if(Auth::user()->student && Auth::user()->student->foto)
            <img src="{{ asset('storage/' . Auth::user()->student->foto) }}" class="w-16 h-16 rounded-full object-cover border-2 border-gold-200">
        @else
            <div class="w-16 h-16 bg-gold-100 rounded-full flex items-center justify-center text-gold-600 text-xl font-bold">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
        @endif
    </div>

    {{-- LOGIKA ALERT STATUS (FEEDBACK LOOP) --}}
    @php
        $student = Auth::user()->student;
        $status = $student ? $student->status : 'Kosong';
    @endphp

    {{-- 1. JIKA BELUM MENGISI SAMA SEKALI --}}
    @if(!$student)
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0"><i class="fa-solid fa-circle-exclamation text-red-500"></i></div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-red-800">Anda belum melengkapi data diri!</h3>
                    <p class="text-sm text-red-700 mt-1">Silakan klik menu <strong>Formulir Pendaftaran</strong> untuk mulai mengisi data.</p>
                    <a href="{{ route('siswa.formulir.show') }}" class="mt-3 inline-block bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-700">
                        Isi Formulir Sekarang
                    </a>
                </div>
            </div>
        </div>

    {{-- 2. STATUS: MENDAFTAR (DRAFT) --}}
    @elseif($status == 'Mendaftar')
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0"><i class="fa-solid fa-pen-to-square text-blue-500"></i></div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-blue-800">Pendaftaran Belum Selesai</h3>
                    <p class="text-sm text-blue-700 mt-1">Anda masih dalam tahap pengisian formulir. Segera lengkapi dan kirim data Anda.</p>
                    <a href="{{ route('siswa.formulir.show') }}" class="mt-3 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700">
                        Lanjutkan Pengisian
                    </a>
                </div>
            </div>
        </div>

    {{-- 3. STATUS: MENUNGGU VERIFIKASI (LOCK) --}}
    @elseif($status == 'Menunggu Verifikasi')
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-r shadow-sm text-center">
            <i class="fa-solid fa-clock text-4xl text-yellow-500 mb-3"></i>
            <h3 class="text-lg font-bold text-yellow-800">Data Sedang Diverifikasi</h3>
            <p class="text-yellow-700 mt-2">Terima kasih telah mengirim formulir. Tim Admin kami sedang memeriksa kelengkapan data Anda.<br>Mohon tunggu 1x24 jam. Kami akan memberitahu Anda jika ada update.</p>
            <div class="mt-4">
                <a href="{{ route('siswa.formulir.show') }}" class="text-yellow-800 font-bold hover:underline">Lihat Formulir Saya (Read Only)</a>
            </div>
        </div>

    {{-- 4. STATUS: PERLU REVISI (URGENT) --}}
    @elseif($status == 'Perlu Revisi')
        <div class="bg-orange-50 border-l-4 border-orange-500 p-6 rounded-r shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0"><i class="fa-solid fa-triangle-exclamation text-3xl text-orange-600 mt-1"></i></div>
                <div class="ml-4 w-full">
                    <h3 class="text-lg font-bold text-orange-800">Formulir Perlu Perbaikan!</h3>
                    <p class="text-orange-700 mt-1">Admin telah memeriksa data Anda dan menemukan beberapa hal yang perlu diperbaiki.</p>
                    
                    {{-- CATATAN ADMIN (PENTING) --}}
                    @if($student->admin_note)
                        <div class="mt-4 bg-white p-4 rounded-lg border border-orange-200">
                            <p class="text-xs text-gray-500 font-bold uppercase mb-1">Pesan dari Admin:</p>
                            <p class="text-gray-800 font-medium"><i class="fa-solid fa-quote-left text-orange-300 mr-2"></i>{{ $student->admin_note }}</p>
                        </div>
                    @endif

                    <div class="mt-5">
                        <a href="{{ route('siswa.formulir.show') }}" class="inline-block bg-orange-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-orange-700 shadow-md transition transform hover:-translate-y-1">
                            <i class="fa-solid fa-wrench mr-2"></i> Perbaiki Formulir Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>

    {{-- 5. STATUS: WAWANCARA (LOLOS ADMIN) --}}
    @elseif($status == 'Wawancara')
        <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-r shadow-sm text-center">
            <i class="fa-solid fa-check-circle text-4xl text-green-500 mb-3"></i>
            <h3 class="text-lg font-bold text-green-800">Selamat! Anda Lolos Seleksi Administrasi</h3>
            <p class="text-green-700 mt-2">Data Anda telah terverifikasi. Anda berhak melanjutkan ke tahap Wawancara.</p>
            <div class="mt-4 p-4 bg-white rounded-lg border border-green-200 inline-block text-left">
                <p class="text-sm text-gray-600"><i class="fa-solid fa-calendar mr-2"></i> Jadwal Wawancara: <strong>Menunggu Info Admin</strong></p>
                <p class="text-sm text-gray-600 mt-1"><i class="fa-solid fa-shirt mr-2"></i> Pakaian: <strong>Bebas Rapi / Kemeja</strong></p>
            </div>
            <div class="mt-6">
                <a href="{{ route('siswa.biodata.print') }}" class="bg-green-600 text-white px-6 py-2 rounded-full font-bold hover:bg-green-700 transition">
                    <i class="fa-solid fa-print mr-2"></i> Cetak Kartu Bukti
                </a>
            </div>
        </div>

    {{-- 6. STATUS: DITOLAK --}}
    @elseif($status == 'Ditolak')
        <div class="bg-gray-100 border-l-4 border-gray-500 p-6 rounded-r shadow-sm text-center grayscale">
            <i class="fa-solid fa-circle-xmark text-4xl text-gray-500 mb-3"></i>
            <h3 class="text-lg font-bold text-gray-800">Mohon Maaf</h3>
            <p class="text-gray-600 mt-2">Berdasarkan hasil verifikasi, kualifikasi Anda belum sesuai dengan standar LPK Hachimitsu saat ini.</p>
            @if($student->admin_note)
                <p class="text-sm text-gray-500 mt-2 bg-white p-2 rounded border border-gray-200 inline-block">Alasan: {{ $student->admin_note }}</p>
            @endif
            <p class="text-gray-500 mt-4 text-sm">Jangan patah semangat dan coba lagi di kesempatan berikutnya.</p>
        </div>
        
    @else
        {{-- STATUS LAIN (Pelatihan, Magang, dll) --}}
        <div class="bg-gold-50 border border-gold-200 p-6 rounded-xl text-center">
            <h3 class="text-xl font-bold text-gold-800">Status Anda: {{ $status }}</h3>
            <p class="text-gold-600 mt-2">Semoga sukses mengikuti program pelatihan!</p>
        </div>
    @endif

</div>
@endsection