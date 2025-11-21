@extends('layouts.app')

@section('header')
    Kelola Media Album: {{ $album->nama_album }}
@endsection

@section('content')

{{-- Pesan Sukses/Error --}}
@if (session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif
@if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm">
        <p class="font-bold">Oops! Ada kesalahan:</p>
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Tombol Kembali ke Daftar Album --}}
<div class="mb-6">
    <a href="{{ route('admin.albums.index') }}" 
       class="inline-flex items-center text-sm font-semibold text-gold-600 hover:text-gold-800 transition-all">
       <i class="fa-solid fa-arrow-left mr-2"></i>
       Kembali ke Daftar Album
    </a>
</div>

{{-- Form Tambah Galeri (Action diubah) --}}
<div class="mb-8" x-data="{ 
        tipe: '{{ old('tipe', 'foto') }}',
        photoPreviewUrl: null,
        photoFilename: '',
        previewPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                this.photoFilename = file.name;
                const reader = new FileReader();
                reader.onload = (e) => { this.photoPreviewUrl = e.target.result; };
                reader.readAsDataURL(file);
            } else {
                this.photoFilename = '';
                this.photoPreviewUrl = null;
            }
        }
    }">
    <h3 class="font-serif text-xl font-bold text-gold-500 mb-4">Tambah Media ke Album Ini</h3>

    <form action="{{ route('admin.albums.media.store', $album) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="space-y-4">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul / Keterangan (Opsional)</label>
                    <input type="text" name="judul" id="judul" value="{{ old('judul') }}"
                           class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300">
                </div>

                <div>
                    <label for="tanggal_kegiatan" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kegiatan</label>
                    <input type="date" name="tanggal_kegiatan" id="tanggal_kegiatan" 
                           value="{{ old('tanggal_kegiatan', now()->format('Y-m-d')) }}" required
                           class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Media</label>
                <div class="flex gap-4">
                    <label class="flex items-center p-3 border border-gray-300 rounded-xl has-[:checked]:bg-gold-50 has-[:checked]:border-gold-400">
                        <input type="radio" name="tipe" value="foto" x-model="tipe" class="text-gold-600 focus:ring-gold-500">
                        <span class="ml-3 font-medium">Upload Foto</span>
                    </label>
                    <label class="flex items-center p-3 border border-gray-300 rounded-xl has-[:checked]:bg-gold-50 has-[:checked]:border-gold-400">
                        <input type="radio" name="tipe" value="video" x-model="tipe" class="text-gold-600 focus:ring-gold-500">
                        <span class="ml-3 font-medium">Link YouTube</span>
                    </label>
                </div>
            </div>

            <div x-show="tipe === 'foto'" x-transition>
                {{-- (Input foto tidak berubah) --}}
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Foto</label>
                <div class="flex items-center">
                    <label for="path_file" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gold-50 text-gold-700 rounded-lg shadow-sm hover:bg-gold-100 transition-all duration-200">
                        <i class="fa-solid fa-upload mr-2 text-gold-600"></i>
                        Pilih Foto...
                    </label>
                    <input @change="previewPhoto($event)" name="path_file" type="file" id="path_file" class="hidden"/>
                    <span x-text="photoFilename" class="ml-3 text-sm text-gray-600"></span>
                </div>
                <div x-show="photoPreviewUrl" class="mt-4">
                    <p class="text-sm font-medium text-gray-700 mb-1">Preview:</p>
                    <img :src="photoPreviewUrl" class="h-32 w-auto rounded-lg object-cover border-2 border-gold-200">
                </div>
            </div>

            <div x-show="tipe === 'video'" x-transition>
                {{-- (Input video tidak berubah) --}}
                <label for="url_video" class="block text-sm font-medium text-gray-700 mb-1">Link Video YouTube</label>
                <input type="url" name="url_video" id="url_video" value="{{ old('url_video') }}" 
                       placeholder="Contoh: https://www.youtube.com/watch?v=..."
                       class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300">
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-5 py-2.5 bg-gold-500 text-white rounded-xl shadow-md hover:bg-gold-600 transition-all font-semibold">
                    Simpan ke Album
                </button>
            </div>
        </div>
    </form>
</div>


<hr class="border-gray-200 my-8">

<div x-data="galleryLightbox()">

    <div x-data="{ tab: 'semua' }">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-serif text-xl font-bold text-gold-500">Isi Album</h3>
            <div class="flex bg-gray-100 p-1 rounded-lg">
                <button @click="tab = 'semua'" :class="tab === 'semua' ? 'bg-gold-500 text-white shadow-md' : 'text-gray-600 hover:text-gold-600'" class="px-4 py-1.5 rounded-md font-semibold text-sm transition-all">Semua</button>
                <button @click="tab = 'foto'" :class="tab === 'foto' ? 'bg-gold-500 text-white shadow-md' : 'text-gray-600 hover:text-gold-600'" class="px-4 py-1.5 rounded-md font-semibold text-sm transition-all">Foto</button>
                <button @click="tab = 'video'" :class="tab === 'video' ? 'bg-gold-500 text-white shadow-md' : 'text-gray-600 hover:text-gold-600'" class="px-4 py-1.5 rounded-md font-semibold text-sm transition-all">Video</button>
            </div>
        </div>

        @if($galeriItems->isEmpty())
            <p class="text-gray-500">Belum ada media (foto/video) yang ditambahkan ke album ini.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                @foreach($galeriItems as $item)
                <div x-show="tab === 'semua' || tab === '{{ $item->tipe }}'" x-transition:enter.duration.300ms
                     class="relative group bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">

                    @if($item->tipe == 'foto')
                        <button type="button" @click="open({{ $item->id }})" class="w-full">
                            <img src="{{ asset('storage/'. $item->path_file) }}" alt="{{ $item->judul }}" 
                                 class="w-full h-48 object-cover cursor-pointer transition-transform duration-300 group-hover:scale-105">
                        </button>
                    @elseif($item->tipe == 'video' && $item->embed_url)
                        <iframe src="{{ $item->embed_url }}" frameborder="0" allowfullscreen class="w-full h-48"></iframe>
                    @endif

                    <div class="p-4">
                        <p class="font-semibold text-gray-800 truncate">{{ $item->judul ?? 'Tanpa Judul' }}</p>
                        <span class="text-xs text-gray-500">
                            {{ ($item->tanggal_kegiatan ?? $item->created_at)->format('d M Y') }}
                        </span>
                    </div>

                    <button type="button" 
                            onclick="siapkanHapusMedia('{{ route('admin.albums.media.destroy', $item) }}', '{{ $item->judul ?? 'Item ini' }}')"
                            class="absolute top-2 right-2 w-8 h-8 bg-red-600 text-white rounded-full shadow-lg 
                                   flex items-center justify-center 
                                   opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                </div>
                @endforeach

            </div>
        @endif
    </div>

    <div id="modalLightbox" 
         class="modal hidden fixed inset-0 bg-black/80 flex justify-center items-center z-50 p-4" 
         @keydown.window.arrow-right.prevent="next()" 
         @keydown.window.arrow-left.prevent="prev()" 
         @keydown.window.escape.prevent="close()">

        <div class="absolute inset-0" @click="close()"></div>
        <button type="button" @click="close()" class="absolute top-4 right-4 text-white/70 hover:text-white transition-all z-[60]">
            <i class="fa-solid fa-times text-4xl"></i>
        </button>
        <button type="button" @click.stop="prev()" 
                class="absolute left-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white 
                       transition-all z-[60] bg-black/30 p-2 rounded-full">
            <i class="fa-solid fa-chevron-left text-3xl w-8 h-8 flex items-center justify-center"></i>
        </button>
        <button type="button" @click.stop="next()" 
                class="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white 
                       transition-all z-[60] bg-black/30 p-2 rounded-full">
            <i class="fa-solid fa-chevron-right text-3xl w-8 h-8 flex items-center justify-center"></i>
        </button>
        <div class="modal-content scale-90 opacity-0 transition-all duration-300 relative" @click.stop>
            <img :src="currentUrl" alt="Preview Gambar" 
                 class="w-auto h-auto object-contain rounded-lg shadow-lg" 
                 style="max-width: 90vw; max-height: 85vh;">
            <div class="mt-3 text-center">
                <h3 x-text="currentTitle" class="text-lg font-semibold text-white">...</h3>
            </div>
        </div>
    </div>
</div>


<div id="modalHapusMedia" class="modal hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50">
    <div class="modal-content bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 scale-90 opacity-0 transition-all duration-300">
        <div class="flex items-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-xl font-bold mb-1 text-gray-900">Konfirmasi Hapus</h2>
                <p class="text-sm text-gray-600">Anda yakin ingin menghapus item media ini?</p>
            </div>
        </div>
        <div class="mt-4 bg-gray-50 p-3 rounded-lg">
            <p class="text-sm text-gray-700">Item: <strong id="itemHapusNamaMedia" class="font-semibold">...</strong></p>
        </div>
        <form id="formHapusMedia" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex justify-end mt-6 space-x-2">
                <button type="button" onclick="closeModal('modalHapusMedia')" class="px-4 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition font-semibold text-gray-700">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition font-semibold">
                    Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


@section('scripts')
<script>
    // Fungsi baru untuk modal hapus media
    function siapkanHapusMedia(url, nama) {
        document.getElementById('formHapusMedia').action = url;
        document.getElementById('itemHapusNamaMedia').textContent = nama;
        openModal('modalHapusMedia');
    }

    // Alpine.js Component untuk Lightbox
    function galleryLightbox() {
        return {
            currentIndex: 0,
            currentTitle: '',
            currentUrl: '',
            // [PENTING] Data foto diambil dari variabel baru
            galleryPhotos: @json($fotoItems), 

            open(itemId) {
                const index = this.galleryPhotos.findIndex(p => p.id === itemId);
                if (index === -1) return;

                this.currentIndex = index;
                this.updateModalContent();
                openModal('modalLightbox');
            },
            close() {
                closeModal('modalLightbox');
            },
            next() {
                this.currentIndex = (this.currentIndex + 1) % this.galleryPhotos.length;
                this.updateModalContent();
            },
            prev() {
                this.currentIndex = (this.currentIndex - 1 + this.galleryPhotos.length) % this.galleryPhotos.length;
                this.updateModalContent();
            },
            updateModalContent() {
                if (!this.galleryPhotos[this.currentIndex]) return;

                let item = this.galleryPhotos[this.currentIndex];
                this.currentTitle = item.judul ?? 'Tanpa Judul';
                this.currentUrl = `/storage/${item.path_file}`;
            }
        };
    }
</script>
@endsection