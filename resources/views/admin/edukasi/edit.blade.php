@extends('layouts.app')

@section('header', 'Edit Artikel')

@section('content')

<div class="max-w-4xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('admin.edukasi.index') }}" 
           class="inline-flex items-center text-sm font-semibold text-gold-600 hover:text-gold-800 transition-all">
           <i class="fa-solid fa-arrow-left mr-2"></i>
           Kembali ke Daftar Edukasi
        </a>
    </div>

    <form action="{{ route('admin.edukasi.update', $edukasi) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            
            <div>
                <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul Artikel</label>
                <input type="text" name="judul" id="judul" value="{{ old('judul', $edukasi->judul) }}" required
                       class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300 text-lg font-semibold">
                @error('judul') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="konten" class="block text-sm font-medium text-gray-700 mb-1">Konten Artikel</label>
                <input id="konten" type="hidden" name="konten" value="{{ old('konten', $edukasi->konten) }}">
                <trix-editor input="konten" class="trix-content w-full min-h-[300px] border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300 bg-white"></trix-editor>
                @error('konten') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Publikasi</label>
                    <select name="status" id="status" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300">
                        <option value="Draft" {{ old('status', $edukasi->status) == 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Published" {{ old('status', $edukasi->status) == 'Published' ? 'selected' : '' }}>Published</option>
                    </select>
                    @error('status') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                <div x-data="{ photoPreviewUrl: '{{ $edukasi->gambar_fitur ? asset('storage/'.$edukasi->gambar_fitur) : null }}', 
                              previewPhoto(event) { 
                                const file = event.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => { this.photoPreviewUrl = e.target.result; };
                                    reader.readAsDataURL(file);
                                }
                            } }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Fitur</label>
                    
                    <label for="gambar_fitur" class="cursor-pointer flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="!photoPreviewUrl">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Ganti Gambar</p>
                        </div>
                        <img x-show="photoPreviewUrl" :src="photoPreviewUrl" class="w-full h-full object-cover rounded-xl">
                        
                        <input @change="previewPhoto($event)" name="gambar_fitur" id="gambar_fitur" type="file" class="hidden" />
                    </label>
                    @error('gambar_fitur') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.edukasi.index') }}" class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-semibold">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-gold-500 text-white rounded-xl shadow-md hover:bg-gold-600 transition-all font-semibold">
                    Update Artikel
                </button>
            </div>
            
        </div>
    </form>
</div>

@endsection