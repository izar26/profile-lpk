@extends('layouts.app')

@section('header', 'Dashboard Admin')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <div class="relative p-6 bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-blue-500 transition-transform duration-300 hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Siswa</p>
                <h5 class="font-serif mt-1 text-3xl font-bold text-gray-800">{{ $totalSiswa }}</h5>
            </div>
            <div class="p-3 bg-blue-50 rounded-full text-blue-600">
                <i class="fa-solid fa-users-rectangle text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="relative p-6 bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-gold-500 transition-transform duration-300 hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Pegawai</p>
                <h5 class="font-serif mt-1 text-3xl font-bold text-gray-800">{{ $totalPegawai }}</h5>
            </div>
            <div class="p-3 bg-gold-50 rounded-full text-gold-600">
                <i class="fa-solid fa-id-card-clip text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="relative p-6 bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-purple-500 transition-transform duration-300 hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Jejak Alumni</p>
                <h5 class="font-serif mt-1 text-3xl font-bold text-gray-800">{{ $totalAlumni }}</h5>
            </div>
            <div class="p-3 bg-purple-50 rounded-full text-purple-600">
                <i class="fa-solid fa-user-graduate text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="relative p-6 bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-green-500 transition-transform duration-300 hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Program</p>
                <h5 class="font-serif mt-1 text-3xl font-bold text-gray-800">{{ $totalProgram }}</h5>
            </div>
            <div class="p-3 bg-green-50 rounded-full text-green-600">
                <i class="fa-solid fa-book-open-reader text-2xl"></i>
            </div>
        </div>
    </div>

</div> 

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-2">
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
            <div class="p-8">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-gold-100 rounded-full flex items-center justify-center text-gold-600">
                        <i class="fa-solid fa-hand-sparkles text-2xl"></i>
                    </div>
                    <h3 class="font-serif text-2xl font-bold text-gray-900">Selamat Datang, Admin!</h3>
                </div>
                <p class="text-gray-600 leading-relaxed">
                    Anda berada di panel kontrol utama <strong>{{ config('app.name', 'LPK Profile') }}</strong>. 
                    Dari sini Anda dapat mengelola seluruh data akademik, administrasi pegawai, serta konten website publik.
                </p>
                
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('admin.students.create') }}" onclick="event.preventDefault(); openModal('modalTambahSiswa')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200 transition">
                        + Siswa Baru
                    </a>
                    <a href="{{ route('admin.employees.create') }}" onclick="event.preventDefault(); openModal('modalTambahPegawai')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200 transition">
                        + Pegawai Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 h-full">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h4 class="font-bold text-gray-800">User Online</h4>
                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full animate-pulse">
                    {{ $onlineUsers->count() }} Aktif
                </span>
            </div>
            <div class="p-4">
                @if($onlineUsers->isEmpty())
                    <div class="text-center py-6 text-gray-400">
                        <i class="fa-solid fa-user-clock text-3xl mb-2"></i>
                        <p class="text-sm">Tidak ada user aktif.</p>
                    </div>
                @else
                    <ul class="space-y-4">
                        @foreach($onlineUsers as $user)
                        <li class="flex items-center gap-3">
                            <div class="relative">
                                @if($user->foto)
                                    <img src="{{ asset('storage/' . $user->foto) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gold-100 flex items-center justify-center text-gold-600 font-bold text-xs">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                @endif
                                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ ucfirst($user->role) }}</p>
                            </div>
                            <div class="text-xs text-gray-400 whitespace-nowrap">
                                {{ $user->last_seen }}
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

</div>

<script>
    function openModal(id) {
        if(id === 'modalTambahSiswa') window.location.href = "{{ route('admin.students.index') }}";
        if(id === 'modalTambahPegawai') window.location.href = "{{ route('admin.employees.index') }}";
    }
</script>

@endsection