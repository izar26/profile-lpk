@extends('layouts.app')

@section('header', 'Testimoni Alumni')

@section('content')

{{-- Alert Status --}}
@if (session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm flex items-center">
        <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    {{-- Card Preview Status --}}
    <div class="lg:col-span-1">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 text-center">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Status Testimoni</h3>
            
            @if($testimoni)
                @if($testimoni->is_published)
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 mb-3">
                        <i class="fa-solid fa-earth-asia text-3xl"></i>
                    </div>
                    <p class="text-green-600 font-bold text-lg">Ditayangkan</p>
                    <p class="text-sm text-gray-500 mt-1">Testimoni Anda dapat dilihat oleh publik.</p>
                @else
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-yellow-100 text-yellow-600 mb-3">
                        <i class="fa-solid fa-clock text-3xl"></i>
                    </div>
                    <p class="text-yellow-600 font-bold text-lg">Menunggu Review</p>
                    <p class="text-sm text-gray-500 mt-1">Admin sedang meninjau testimoni Anda.</p>
                @endif

                <div class="mt-6 border-t pt-4 text-left">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-2">Preview Foto</p>
                    @if($testimoni->foto)
                        <img src="{{ asset('storage/' . $testimoni->foto) }}" class="w-full h-48 object-cover rounded-lg border">
                    @else
                        <div class="w-full h-48 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                            <i class="fa-solid fa-image text-3xl"></i>
                        </div>
                    @endif
                </div>
            @else
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-3">
                    <i class="fa-solid fa-pen-to-square text-3xl"></i>
                </div>
                <p class="text-gray-500 font-medium">Belum ada testimoni.</p>
                <p class="text-sm text-gray-400 mt-1">Silakan isi formulir di samping.</p>
            @endif
        </div>
    </div>

    {{-- Form Input --}}
    <div class="lg:col-span-2">
        <form action="{{ route('siswa.testimoni.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-circle-info text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Informasi</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Setiap perubahan yang Anda simpan akan mengubah status menjadi <b>Menunggu Review</b> hingga Admin menyetujuinya kembali.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" value="{{ $student->nama_lengkap }}" disabled class="w-full bg-gray-100 border-gray-300 rounded-lg text-gray-500 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Angkatan</label>
                    <input type="text" name="angkatan" value="{{ old('angkatan', $testimoni->angkatan ?? '') }}" placeholder="Contoh: 2023 / Batch 5" class="w-full border-gray-300 rounded-lg focus:ring-gold-500 focus:border-gold-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Saat ini bekerja di (Perusahaan/Lokasi)</label>
                <input type="text" name="kerja_dimana" value="{{ old('kerja_dimana', $testimoni->kerja_dimana ?? '') }}" required placeholder="Contoh: Toyota Motor Corp, Aichi" class="w-full border-gray-300 rounded-lg focus:ring-gold-500 focus:border-gold-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Kata-kata Testimoni</label>
                <textarea name="testimoni" rows="5" required placeholder="Ceritakan pengalaman Anda selama pelatihan dan bekerja..." class="w-full border-gray-300 rounded-lg focus:ring-gold-500 focus:border-gold-500">{{ old('testimoni', $testimoni->testimoni ?? '') }}</textarea>
                <p class="text-xs text-gray-500 mt-1 text-right">Ceritakan dengan jujur dan sopan.</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Upload Foto Saat Bekerja (Opsional)</label>
                <input type="file" name="foto" accept="image/*" class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-gold-50 file:text-gold-700
                    hover:file:bg-gold-100
                    cursor-pointer border border-gray-300 rounded-lg
                "/>
                <p class="text-xs text-gray-500 mt-1">Format: JPG/PNG, Max: 2MB. Disarankan foto menggunakan seragam kerja / di lokasi kerja.</p>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="bg-gold-500 text-white px-6 py-3 rounded-xl font-bold hover:bg-gold-600 transition shadow-lg flex items-center">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Simpan Testimoni
                </button>
            </div>
        </form>
    </div>
</div>
@endsection