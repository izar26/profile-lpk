@extends('layouts.app')

@section('header', 'Master Data Dokumen')

@section('content')

{{-- Tombol Tambah --}}
<div class="mb-6 flex justify-between items-center">
    <p class="text-gray-600">Atur jenis dokumen yang wajib/opsional diupload oleh siswa.</p>
    <button onclick="openModal('modal-create')" class="bg-gold-500 hover:bg-gold-600 text-white font-bold py-2 px-4 rounded-lg shadow-gold transition transform hover:-translate-y-1">
        <i class="fa-solid fa-plus mr-2"></i> Tambah Dokumen
    </button>
</div>

{{-- Alert Sukses --}}
@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        {{ session('success') }}
    </div>
@endif

{{-- Tabel Data --}}
<div class="overflow-x-auto relative">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
            <tr>
                <th class="py-3 px-6">Nama Dokumen</th>
                <th class="py-3 px-6 text-center">Wajib?</th>
                <th class="py-3 px-6 text-center">Status</th>
                <th class="py-3 px-6 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $doc)
            <tr class="bg-white border-b hover:bg-gray-50">
                <td class="py-4 px-6 font-medium text-gray-900">
                    {{ $doc->nama }}
                    <div class="text-xs text-gray-400 font-normal mt-1">Slug: {{ $doc->slug }}</div>
                </td>
                <td class="py-4 px-6 text-center">
                    @if($doc->is_required)
                        <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded">Wajib</span>
                    @else
                        <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-0.5 rounded">Opsional</span>
                    @endif
                </td>
                <td class="py-4 px-6 text-center">
                    @if($doc->is_active)
                        <span class="text-green-600 font-bold"><i class="fa-solid fa-circle-check"></i> Aktif</span>
                    @else
                        <span class="text-gray-400 font-bold"><i class="fa-solid fa-circle-xmark"></i> Non-Aktif</span>
                    @endif
                </td>
                <td class="py-4 px-6 text-center">
                    <button onclick="openModal('modal-edit-{{ $doc->id }}')" class="text-blue-600 hover:text-blue-900 mr-3">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </button>
                    <form action="{{ route('admin.document-types.destroy', $doc->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus dokumen ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>

            {{-- MODAL EDIT (Di-loop untuk setiap item) --}}
            <div id="modal-edit-{{ $doc->id }}" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 transform scale-90 opacity-0 transition-all duration-300 modal-content">
                    <div class="px-6 py-4 border-b flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Edit Dokumen</h3>
                        <button onclick="closeModal('modal-edit-{{ $doc->id }}')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-times"></i></button>
                    </div>
                    <form action="{{ route('admin.document-types.update', $doc->id) }}" method="POST" class="p-6">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Dokumen</label>
                            <input type="text" name="nama" value="{{ $doc->nama }}" class="w-full border-gray-300 rounded-lg focus:ring-gold-500 focus:border-gold-500" required>
                        </div>
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Sifat</label>
                                <select name="is_required" class="w-full border-gray-300 rounded-lg focus:ring-gold-500">
                                    <option value="1" {{ $doc->is_required ? 'selected' : '' }}>Wajib</option>
                                    <option value="0" {{ !$doc->is_required ? 'selected' : '' }}>Opsional</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                                <select name="is_active" class="w-full border-gray-300 rounded-lg focus:ring-gold-500">
                                    <option value="1" {{ $doc->is_active ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ !$doc->is_active ? 'selected' : '' }}>Sembunyikan</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end pt-4">
                            <button type="button" onclick="closeModal('modal-edit-{{ $doc->id }}')" class="mr-2 px-4 py-2 text-gray-500 hover:text-gray-700 font-bold">Batal</button>
                            <button type="submit" class="bg-gold-600 hover:bg-gold-700 text-white font-bold py-2 px-6 rounded-lg">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            @empty
            <tr>
                <td colspan="4" class="py-8 text-center text-gray-400 italic">Belum ada jenis dokumen. Silakan tambah baru.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MODAL CREATE (Satu saja) --}}
<div id="modal-create" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 transform scale-90 opacity-0 transition-all duration-300 modal-content">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900">Tambah Dokumen Baru</h3>
            <button onclick="closeModal('modal-create')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-times"></i></button>
        </div>
        <form action="{{ route('admin.document-types.store') }}" method="POST" class="p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Dokumen</label>
                <input type="text" name="nama" class="w-full border-gray-300 rounded-lg focus:ring-gold-500 focus:border-gold-500" placeholder="Contoh: Surat Vaksin" required>
            </div>
            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Sifat</label>
                    <select name="is_required" class="w-full border-gray-300 rounded-lg focus:ring-gold-500">
                        <option value="1">Wajib</option>
                        <option value="0">Opsional</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                    <select name="is_active" class="w-full border-gray-300 rounded-lg focus:ring-gold-500">
                        <option value="1">Aktif</option>
                        <option value="0">Sembunyikan</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end pt-4">
                <button type="button" onclick="closeModal('modal-create')" class="mr-2 px-4 py-2 text-gray-500 hover:text-gray-700 font-bold">Batal</button>
                <button type="submit" class="bg-gold-600 hover:bg-gold-700 text-white font-bold py-2 px-6 rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection