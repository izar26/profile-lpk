@extends('layouts.app')

@section('header', 'Manajemen Album Galeri')

@section('content')

{{-- success message --}}
@if (session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif

{{-- error message (penting untuk validasi modal) --}}
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


{{-- header --}}
<div class="flex justify-end mb-4">
    <button onclick="openModal('modalTambahAlbum')"
            class="px-4 py-2 bg-gold-500 text-white rounded-xl shadow hover:bg-gold-600 transition-all">
        + Tambah Album Baru
    </button>
</div>


{{-- table --}}
<div class="bg-white shadow-md rounded-xl overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Nama Album</th>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Deskripsi</th>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Total Media</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
    @forelse ($albums as $album)
        <tr class="hover:bg-gray-50 transition">
            <td class="px-6 py-4">
                <p class="text-sm font-semibold text-gray-900">{{ $album->nama_album }}</p>
            </td>
            <td class="px-6 py-4">
                <p class="text-sm text-gray-600 truncate max-w-sm">{{ $album->deskripsi_album ?? '-' }}</p>
            </td>
            <td class="px-6 py-4">
                <span class="text-sm text-gray-700">{{ $album->galeris_count }} item</span> 
            </td>
            <td class="px-6 py-4 text-right text-sm space-x-3 whitespace-nowrap">

                <a href="{{ route('admin.albums.media.index', $album) }}" 
                   class="text-green-600 hover:text-green-900 transition font-semibold">
                    Kelola Media
                </a>

                <button onclick="loadEditAlbum({{ $album->id }})"
                        class="text-indigo-600 hover:text-indigo-900 transition">
                    Edit
                </button>
                <button type="button" 
                        onclick="siapkanHapusAlbum('{{ route('admin.albums.destroy', $album) }}', '{{ $album->nama_album }}')"
                        class="text-red-600 hover:text-red-800 transition">
                    Hapus
                </button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                Belum ada album yang dibuat.
            </td>
        </tr>
    @endforelse
</tbody>
    </table>
</div>

<div class="mt-4">
    {{ $albums->links() }}
</div>



{{-- ================================================================================= --}}
{{-- ============================= MODAL TAMBAH ALBUM ================================ --}}
{{-- ================================================================================= --}}
<div id="modalTambahAlbum" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
    <div class="modal-content bg-white w-full max-w-md rounded-lg shadow-lg scale-90 opacity-0 transition-all duration-300">

        <h2 class="text-xl font-bold text-gray-900 p-6 border-b border-gray-200">Tambah Album Baru</h2>

        <form action="{{ route('admin.albums.store') }}" method="POST">
            @csrf
            <div class="p-6 overflow-y-auto" style="max-height: 70vh;">
                <div class="space-y-4">
                    <div>
                        <label for="nama_album" class="block text-sm mb-1 font-medium text-gray-700">Nama Album</label>
                        <input type="text" id="nama_album" name="nama_album" value="{{ old('nama_album') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                        @error('nama_album') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="deskripsi_album" class="block text-sm mb-1 font-medium text-gray-700">Deskripsi (Opsional)</label>
                        <textarea id="deskripsi_album" name="deskripsi_album" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">{{ old('deskripsi_album') }}</textarea>
                        @error('deskripsi_album') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end p-6 bg-gray-50 border-t border-gray-200 rounded-b-lg space-x-2">
                <button type="button" onclick="closeModal('modalTambahAlbum')" class="px-4 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition text-gray-700 font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-xl hover:bg-gold-600 transition font-semibold">Simpan Album</button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================================================= --}}
{{-- =============================== MODAL EDIT ALBUM ================================ --}}
{{-- ================================================================================= --}}
<div id="modalEditAlbum" class="modal hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50 p-4">
    <div class="modal-content bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 scale-90 opacity-0 transition-all duration-300">

        <h2 class="text-xl font-bold text-gray-900 mb-4">Edit Album</h2>

        <div id="editLoadingAlbum" class="hidden text-center py-20">
            <div class="loader mx-auto mb-3"></div>
            <p class="text-gray-600">Mengambil data...</p>
        </div>

        <form id="formEditAlbum" method="POST" enctype="multipart/form-data" class="hidden">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label for="edit_nama_album" class="block text-sm mb-1 font-medium text-gray-700">Nama Album</label>
                    <input type="text" id="edit_nama_album" name="nama_album" required class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300">
                    @error('nama_album') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="edit_deskripsi_album" class="block text-sm mb-1 font-medium text-gray-700">Deskripsi (Opsional)</label>
                    <textarea id="edit_deskripsi_album" name="deskripsi_album" rows="3" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300"></textarea>
                    @error('deskripsi_album') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-6 space-x-2">
                <button type="button" onclick="closeModal('modalEditAlbum')" class="px-4 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition text-gray-700 font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-xl hover:bg-gold-600 transition font-semibold">Update Album</button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================================================= --}}
{{-- =========================== MODAL HAPUS ALBUM =================================== --}}
{{-- ================================================================================= --}}
<div id="modalHapusAlbum" class="modal hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50">
    <div class="modal-content bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 scale-90 opacity-0 transition-all duration-300">
        <div class="flex items-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-xl font-bold mb-1 text-gray-900">Konfirmasi Hapus</h2>
                <p class="text-sm text-gray-600">Anda yakin? Semua foto/video di dalam album ini akan ikut terhapus.</p>
            </div>
        </div>
        <div class="mt-4 bg-gray-50 p-3 rounded-lg">
            <p class="text-sm text-gray-700">Album: <strong id="itemHapusNamaAlbum" class="font-semibold">...</strong></p>
        </div>
        <form id="formHapusAlbum" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex justify-end mt-6 space-x-2">
                <button type="button" onclick="closeModal('modalHapusAlbum')" class="px-4 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition font-semibold text-gray-700">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition font-semibold">Ya, Hapus Album</button>
            </div>
        </form>
    </div>
</div>

@endsection

{{-- ================================================================================= --}}
{{-- ================================== SCRIPT ======================================= --}}
{{-- ================================================================================= --}}
@section('scripts')
<script>
    // Fungsi untuk memuat data ke modal Edit
    function loadEditAlbum(id) {
        openModal('modalEditAlbum');
        document.getElementById('editLoadingAlbum').classList.remove('hidden');
        document.getElementById('formEditAlbum').classList.add('hidden');

        fetch(`/admin/albums/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('edit_nama_album').value = data.nama_album;
                document.getElementById('edit_deskripsi_album').value = data.deskripsi_album;
                document.getElementById('formEditAlbum').action = `/admin/albums/${id}`;

                setTimeout(() => {
                    document.getElementById('editLoadingAlbum').classList.add('hidden');
                    document.getElementById('formEditAlbum').classList.remove('hidden');
                }, 400);
            });
    }

    // Fungsi untuk menyiapkan modal Hapus
    function siapkanHapusAlbum(url, nama) {
        document.getElementById('formHapusAlbum').action = url;
        document.getElementById('itemHapusNamaAlbum').textContent = nama;
        openModal('modalHapusAlbum');
    }

    // --- LOGIKA MENAMPILKAN MODAL SAAT ADA ERROR VALIDASI ---
    document.addEventListener('DOMContentLoaded', function() {
        @if ($errors->any())
            @if (old('nama_album')) // Cek jika ada old input (tanda gagal validasi)
                // Cek apakah ada ID di form, jika ada berarti ini modal Edit
                if (document.getElementById('formEditAlbum').action.includes('/')) {
                     openModal('modalEditAlbum');
                     document.getElementById('editLoadingAlbum').classList.add('hidden');
                     document.getElementById('formEditAlbum').classList.remove('hidden');
                     // Isi ulang old input
                     document.getElementById('edit_nama_album').value = "{{ old('nama_album') }}";
                     document.getElementById('edit_deskripsi_album').value = "{{ old('deskripsi_album') }}";
                } else {
                     openModal('modalTambahAlbum');
                     // Isi ulang old input
                     document.getElementById('nama_album').value = "{{ old('nama_album') }}";
                     document.getElementById('deskripsi_album').value = "{{ old('deskripsi_album') }}";
                }
            @endif
        @endif
    });
</script>
@endsection