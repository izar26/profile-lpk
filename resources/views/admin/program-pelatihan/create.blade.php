@extends('layouts.app')

@section('header', 'Tambah Program Pelatihan')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('admin.program-pelatihan.index') }}" class="inline-flex items-center text-sm font-semibold text-gold-600 hover:text-gold-800 transition-all">
           <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.program-pelatihan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="space-y-6 bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Program</label>
                <input type="text" name="judul" value="{{ old('judul') }}" required class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-500 focus:ring-gold-500">
                @error('judul') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat</label>
                <textarea name="deskripsi_singkat" rows="2" required class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-500 focus:ring-gold-500" placeholder="Untuk tampilan di kartu depan...">{{ old('deskripsi_singkat') }}</textarea>
                @error('deskripsi_singkat') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Lengkap (Kurikulum, Syarat, Biaya, dll)</label>
                <input id="deskripsi_lengkap" type="hidden" name="deskripsi_lengkap" value="{{ old('deskripsi_lengkap') }}">
                <trix-editor input="deskripsi_lengkap" class="trix-content w-full min-h-[300px] border-gray-300 rounded-xl shadow-sm focus:border-gold-500 focus:ring-gold-500"></trix-editor>
                @error('deskripsi_lengkap') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Program</label>
                    <select name="status" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-500 focus:ring-gold-500">
                        <option value="Akan Datang">Akan Datang</option>
                        <option value="Buka Pendaftaran">Buka Pendaftaran</option>
                        <option value="Berjalan">Berjalan</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>

                <div x-data="{ photoPreviewUrl: null, previewPhoto(event) { 
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (e) => { this.photoPreviewUrl = e.target.result; };
                            reader.readAsDataURL(file);
                        }
                    } }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Fitur</label>
                    
                    <label class="cursor-pointer flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50 hover:bg-gray-100 transition-all relative overflow-hidden">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="!photoPreviewUrl">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Klik untuk upload gambar</p>
                        </div>
                        <img x-show="photoPreviewUrl" :src="photoPreviewUrl" class="absolute inset-0 w-full h-full object-cover" style="display: none;">
                        <input @change="previewPhoto($event)" name="gambar_fitur" type="file" class="hidden" />
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="px-8 py-3 bg-gold-500 text-white font-bold rounded-xl shadow-lg hover:bg-gold-600 transition-all">
                    Simpan Program
                </button>
            </div>
        </div>
    </form>
</div>
@endsection