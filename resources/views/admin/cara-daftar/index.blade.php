@extends('layouts.app')

@section('header', 'Manajemen Cara Daftar')

@section('content')

{{-- Success Message --}}
@if (session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Error Message --}}
@if ($errors->any() && !session('openModalEdit') && !session('openModalTambah'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Header Button --}}
<div class="flex justify-end mb-4">
    <button onclick="openModal('modalTambah')"
            class="px-4 py-2 bg-gold-500 text-white rounded-xl shadow hover:bg-gold-600 transition-all font-semibold">
        + Tambah Langkah
    </button>
</div>

{{-- Table --}}
<div class="bg-white shadow-md rounded-xl overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 table-fixed">
        <thead class="bg-gray-50">
            <tr>
                <th class="w-16 px-6 py-3 text-left text-xs text-gray-500 uppercase">Urutan</th>
                <th class="w-24 px-6 py-3 text-left text-xs text-gray-500 uppercase">Ikon</th>
                <th class="w-1/4 px-6 py-3 text-left text-xs text-gray-500 uppercase">Judul</th>
                <th class="w-1/3 px-6 py-3 text-left text-xs text-gray-500 uppercase">Deskripsi</th>
                <th class="w-auto px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($langkahs as $langkah)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gold-100 text-gold-600 font-bold text-sm border border-gold-200">
                            {{ $langkah->urutan }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($langkah->gambar)
                            <img src="{{ asset('storage/' . $langkah->gambar) }}" alt="Icon" class="h-10 w-10 object-contain">
                        @else
                            <div class="h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-400">
                                <i class="fa-solid fa-image"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-gray-900">{{ $langkah->judul }}</p>
                    </td>
                    <td class="px-6 py-4 break-words">
                        <p class="text-sm text-gray-600">{{ $langkah->deskripsi }}</p>
                    </td>
                    <td class="px-6 py-4 text-right text-sm space-x-3 whitespace-nowrap">
                        <button onclick="loadEditLangkah({{ $langkah->id }})"
                                class="text-indigo-600 hover:text-indigo-900 transition">
                            Edit
                        </button>
                        <button type="button" 
                                onclick="siapkanHapusLangkah('{{ route('admin.cara-daftar.destroy', $langkah) }}', '{{ $langkah->judul }}')"
                                class="text-red-600 hover:text-red-800 transition">
                            Hapus
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                        Belum ada langkah pendaftaran yang diatur.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


{{-- ============================= MODAL TAMBAH ============================== --}}
<div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
    <div class="modal-content bg-white w-full max-w-md rounded-lg shadow-lg scale-90 opacity-0 transition-all duration-300">
        <h2 class="text-xl font-bold text-gray-900 p-6 border-b border-gray-200">Tambah Langkah Baru</h2>
        <form action="{{ route('admin.cara-daftar.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-4">
                
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-1">
                        <label class="block text-sm mb-1 font-medium text-gray-700">Urutan</label>
                        <input type="number" name="urutan" value="{{ old('urutan', $langkahs->count() + 1) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm mb-1 font-medium text-gray-700">Judul Langkah</label>
                        <input type="text" name="judul" value="{{ old('judul') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300" placeholder="Contoh: Isi Formulir">
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">{{ old('deskripsi') }}</textarea>
                </div>

                <div x-data="{ photoPreviewUrl: null, photoFilename: '', previewPhoto(event) { 
                        const file = event.target.files[0];
                        if (file) {
                            this.photoFilename = file.name;
                            const reader = new FileReader();
                            reader.onload = (e) => { this.photoPreviewUrl = e.target.result; };
                            reader.readAsDataURL(file);
                        }
                    } }">
                    <label class="block text-sm mb-1 font-medium text-gray-700">Ikon / Gambar (Opsional)</label>
                    <label class="cursor-pointer flex items-center gap-3 px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                        <i class="fa-solid fa-upload text-gray-500"></i>
                        <span class="text-sm text-gray-600">Pilih Ikon...</span>
                        <input @change="previewPhoto($event)" name="gambar" type="file" class="hidden"/>
                    </label>
                    <div x-show="photoPreviewUrl" class="mt-3">
                        <img :src="photoPreviewUrl" class="h-16 w-16 object-contain border rounded-md p-1">
                    </div>
                </div>

            </div>
            <div class="flex justify-end p-6 bg-gray-50 border-t border-gray-200 rounded-b-lg space-x-2">
                <button type="button" onclick="closeModal('modalTambah')" class="px-4 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition text-gray-700 font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-xl hover:bg-gold-600 transition font-semibold">Simpan</button>
            </div>
        </form>
    </div>
</div>


{{-- ============================= MODAL EDIT ============================== --}}
<div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
    <div class="modal-content bg-white w-full max-w-md rounded-lg shadow-lg scale-90 opacity-0 transition-all duration-300">
        <h2 class="text-xl font-bold text-gray-900 p-6 border-b border-gray-200">Edit Langkah</h2>
        
        <div id="edit-loading" class="hidden text-center py-10">
            <div class="loader mx-auto mb-3"></div>
            <p class="text-gray-600">Mengambil data...</p>
        </div>

        <form id="formEdit" method="POST" enctype="multipart/form-data" class="hidden">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-1">
                        <label class="block text-sm mb-1 font-medium text-gray-700">Urutan</label>
                        <input type="number" id="edit_urutan" name="urutan" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm mb-1 font-medium text-gray-700">Judul Langkah</label>
                        <input type="text" id="edit_judul" name="judul" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Deskripsi</label>
                    <textarea id="edit_deskripsi" name="deskripsi" rows="3" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300"></textarea>
                </div>

                <div x-data="{ photoPreviewUrl: null, photoFilename: '', previewPhoto(event, previewElementId) { 
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (e) => { 
                                this.photoPreviewUrl = e.target.result; 
                                const img = document.getElementById(previewElementId);
                                img.src = e.target.result;
                                img.classList.remove('hidden');
                            };
                            reader.readAsDataURL(file);
                        }
                    } }">
                    <label class="block text-sm mb-1 font-medium text-gray-700">Ikon / Gambar (Opsional)</label>
                    
                    <img id="edit_preview_image" src="" class="h-16 w-16 object-contain border rounded-md p-1 mb-3 hidden">

                    <label class="cursor-pointer flex items-center gap-3 px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                        <i class="fa-solid fa-upload text-gray-500"></i>
                        <span class="text-sm text-gray-600">Ganti Ikon...</span>
                        <input @change="previewPhoto($event, 'edit_preview_image')" name="gambar" type="file" class="hidden"/>
                    </label>
                </div>

            </div>
            <div class="flex justify-end p-6 bg-gray-50 border-t border-gray-200 rounded-b-lg space-x-2">
                <button type="button" onclick="closeModal('modalEdit')" class="px-4 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition text-gray-700 font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-xl hover:bg-gold-600 transition font-semibold">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- ============================= MODAL HAPUS ============================== --}}
<div id="modalHapus" class="modal hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50">
    <div class="modal-content bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 scale-90 opacity-0 transition-all duration-300">
        <div class="flex items-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-xl font-bold mb-1 text-gray-900">Konfirmasi Hapus</h2>
                <p class="text-sm text-gray-600">Anda yakin ingin menghapus langkah ini?</p>
            </div>
        </div>
        <div class="mt-4 bg-gray-50 p-3 rounded-lg">
            <p class="text-sm text-gray-700">Langkah: <strong id="itemHapusNama" class="font-semibold">...</strong></p>
        </div>
        <form id="formHapus" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex justify-end mt-6 space-x-2">
                <button type="button" onclick="closeModal('modalHapus')" class="px-4 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition font-semibold text-gray-700">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition font-semibold">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function loadEditLangkah(id) {
        openModal('modalEdit');
        document.getElementById('edit-loading').classList.remove('hidden');
        document.getElementById('formEdit').classList.add('hidden');

        fetch(`/admin/cara-daftar/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('edit_judul').value = data.judul;
                document.getElementById('edit_urutan').value = data.urutan;
                document.getElementById('edit_deskripsi').value = data.deskripsi;
                
                const imgPreview = document.getElementById('edit_preview_image');
                if(data.gambar) {
                    imgPreview.src = `/storage/${data.gambar}`;
                    imgPreview.classList.remove('hidden');
                } else {
                    imgPreview.classList.add('hidden');
                }

                document.getElementById('formEdit').action = `/admin/cara-daftar/${id}`;

                setTimeout(() => {
                    document.getElementById('edit-loading').classList.add('hidden');
                    document.getElementById('formEdit').classList.remove('hidden');
                }, 400);
            });
    }

    function siapkanHapusLangkah(url, nama) {
        document.getElementById('formHapus').action = url;
        document.getElementById('itemHapusNama').textContent = nama;
        openModal('modalHapus');
    }

    // Logika error modal
    document.addEventListener('DOMContentLoaded', function() {
        @if ($errors->any())
            @if (session('openModalTambah'))
                openModal('modalTambah');
            @elseif (session('openModalEdit'))
                 // Logika reopen edit modal jika perlu
            @endif
        @endif
    });
</script>
@endsection