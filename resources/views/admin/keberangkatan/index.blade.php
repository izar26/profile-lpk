@extends('layouts.app')

@section('header', 'Info Keberangkatan')

@section('content')

@if (session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any() && !session('openModalEdit') && !session('openModalTambah'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm">
        <ul class="list-disc list-inside">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
    </div>
@endif

<div class="flex justify-end mb-4">
    <button onclick="openModal('modalTambah')" class="px-5 py-2.5 bg-gold-500 text-white rounded-xl shadow-lg hover:bg-gold-600 transition-all font-semibold">
        + Tambah Info
    </button>
</div>

<div class="bg-white shadow-md rounded-xl overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Foto</th>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Judul & Tujuan</th>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Tanggal & Peserta</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($keberangkatans as $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        @if($item->foto)
                            <button onclick="lihatGambar('{{ asset('storage/' . $item->foto) }}', '{{ $item->judul }}')">
                                <img src="{{ asset('storage/' . $item->foto) }}" class="h-16 w-24 object-cover rounded-md border hover:scale-105 transition">
                            </button>
                        @else
                            <div class="h-16 w-24 bg-gray-100 rounded-md flex items-center justify-center text-gray-400"><i class="fa-solid fa-image"></i></div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-900">{{ $item->judul }}</p>
                        <p class="text-sm text-gold-600"><i class="fa-solid fa-location-dot mr-1"></i> {{ $item->tujuan }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-900"><i class="fa-regular fa-calendar mr-1"></i> {{ $item->tanggal_berangkat->format('d M Y') }}</p>
                        <p class="text-sm text-gray-600 mt-1"><i class="fa-solid fa-users mr-1"></i> {{ $item->jumlah_peserta }} Orang</p>
                    </td>
                    <td class="px-6 py-4 text-right text-sm space-x-2">
                        <button onclick="loadEdit({{ $item->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                        <button onclick="siapkanHapus('{{ route('admin.keberangkatan.destroy', $item) }}', '{{ $item->judul }}')" class="text-red-600 hover:text-red-800">Hapus</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada data keberangkatan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $keberangkatans->links() }}</div>

{{-- MODAL TAMBAH --}}
<div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
    <div class="modal-content bg-white w-full max-w-lg rounded-lg shadow-lg scale-90 opacity-0 transition-all duration-300 overflow-y-auto max-h-[90vh]">
        <h2 class="text-xl font-bold text-gray-900 p-6 border-b border-gray-200">Tambah Info Keberangkatan</h2>
        <form action="{{ route('admin.keberangkatan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Judul Kegiatan</label>
                    <input type="text" name="judul" required placeholder="Contoh: Pelepasan Angkatan 10" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                </div>
                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Tujuan Keberangkatan</label>
                    <input type="text" name="tujuan" required placeholder="Contoh: Tokyo, Jepang" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Tanggal</label>
                        <input type="date" name="tanggal_berangkat" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Jumlah Peserta</label>
                        <input type="number" name="jumlah_peserta" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                    </div>
                </div>
                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Deskripsi (Opsional)</label>
                    <textarea name="deskripsi" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300"></textarea>
                </div>
                
                <div x-data="{ photoPreviewUrl: null, previewPhoto(event) { 
                        const file = event.target.files[0];
                        if(file){ const reader = new FileReader(); reader.onload = (e) => { this.photoPreviewUrl = e.target.result; }; reader.readAsDataURL(file); }
                    } }">
                    <label class="block text-sm mb-1 font-medium text-gray-700">Foto Kegiatan (Wajib)</label>
                    <label class="cursor-pointer flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg hover:bg-gray-50">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="!photoPreviewUrl">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Upload Foto</p>
                        </div>
                        <img x-show="photoPreviewUrl" :src="photoPreviewUrl" class="h-full object-contain">
                        <input @change="previewPhoto($event)" name="foto" type="file" class="hidden" />
                    </label>
                </div>
            </div>
            <div class="flex justify-end p-6 bg-gray-50 border-t border-gray-200 rounded-b-lg gap-2">
                <button type="button" onclick="closeModal('modalTambah')" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-lg hover:bg-gold-600">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
    <div class="modal-content bg-white w-full max-w-lg rounded-lg shadow-lg scale-90 opacity-0 transition-all duration-300">
        <h2 class="text-xl font-bold text-gray-900 p-6 border-b border-gray-200">Edit Info</h2>
        <div id="edit-loading" class="hidden text-center py-10"><div class="loader mx-auto"></div></div>
        <form id="formEdit" method="POST" enctype="multipart/form-data" class="hidden">
            @csrf @method('PUT')
            <div class="p-6 space-y-4 overflow-y-auto max-h-[70vh]">
                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Judul Kegiatan</label>
                    <input type="text" id="edit_judul" name="judul" required class="w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Tujuan</label>
                    <input type="text" id="edit_tujuan" name="tujuan" required class="w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Tanggal</label>
                        <input type="date" id="edit_tanggal" name="tanggal_berangkat" required class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Jumlah Peserta</label>
                        <input type="number" id="edit_jumlah" name="jumlah_peserta" required class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Deskripsi</label>
                    <textarea id="edit_deskripsi" name="deskripsi" rows="2" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
                
                <div x-data="{ photoPreviewUrl: null, previewPhoto(event) { 
                        const file = event.target.files[0];
                        if(file){ const reader = new FileReader(); reader.onload = (e) => { this.photoPreviewUrl = e.target.result; document.getElementById('edit_preview_img').classList.add('hidden'); }; reader.readAsDataURL(file); }
                    } }">
                    <label class="block text-sm mb-1 font-medium text-gray-700">Ganti Foto</label>
                    <img id="edit_preview_img" src="" class="h-32 w-full object-cover rounded-lg mb-2 hidden">
                    <div x-show="photoPreviewUrl" class="mb-2"><img :src="photoPreviewUrl" class="h-32 w-full object-cover rounded-lg"></div>
                    <label class="cursor-pointer px-3 py-1.5 bg-gray-100 border border-gray-300 rounded text-sm hover:bg-gray-200">
                        Upload Baru <input @change="previewPhoto($event)" name="foto" type="file" class="hidden"/>
                    </label>
                </div>
            </div>
            <div class="flex justify-end p-6 bg-gray-50 border-t border-gray-200 rounded-b-lg gap-2">
                <button type="button" onclick="closeModal('modalEdit')" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-lg">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL HAPUS & LIGHTBOX (Sama seperti sebelumnya) --}}
<div id="modalHapus" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="modal-content bg-white w-full max-w-md rounded-lg p-6 scale-90 opacity-0 transition-all">
        <h2 class="text-xl font-bold mb-2">Hapus Data?</h2>
        <p class="text-gray-600 mb-4">Data <strong id="hapusNama"></strong> akan dihapus.</p>
        <form id="formHapus" method="POST">
            @csrf @method('DELETE')
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalHapus')" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg">Hapus</button>
            </div>
        </form>
    </div>
</div>

<div id="modalLihatGambar" class="fixed inset-0 bg-black/80 flex justify-center items-center hidden z-50 p-4" onclick="closeModal('modalLihatGambar')">
    <div class="modal-content scale-90 opacity-0 transition-all relative" onclick="event.stopPropagation()">
        <img id="lihatGambarSrc" src="" class="max-w-[90vw] max-h-[85vh] rounded-lg shadow-lg">
        <div class="mt-3 text-center"><h3 id="lihatGambarJudul" class="text-lg font-semibold text-white">...</h3></div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function loadEdit(id) {
        openModal('modalEdit');
        document.getElementById('edit-loading').classList.remove('hidden');
        document.getElementById('formEdit').classList.add('hidden');

        fetch(`/admin/keberangkatan/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('edit_judul').value = data.judul;
                document.getElementById('edit_tujuan').value = data.tujuan;
                document.getElementById('edit_tanggal').value = data.tanggal_berangkat; // Pastikan format YYYY-MM-DD
                document.getElementById('edit_jumlah').value = data.jumlah_peserta;
                document.getElementById('edit_deskripsi').value = data.deskripsi;

                const img = document.getElementById('edit_preview_img');
                if(data.foto) {
                    img.src = `/storage/${data.foto}`;
                    img.classList.remove('hidden');
                } else {
                    img.classList.add('hidden');
                }

                document.getElementById('formEdit').action = `/admin/keberangkatan/${id}`;
                
                setTimeout(() => {
                    document.getElementById('edit-loading').classList.add('hidden');
                    document.getElementById('formEdit').classList.remove('hidden');
                }, 400);
            });
    }

    function siapkanHapus(url, nama) {
        document.getElementById('formHapus').action = url;
        document.getElementById('hapusNama').textContent = nama;
        openModal('modalHapus');
    }

    function lihatGambar(url, judul) {
        document.getElementById('lihatGambarSrc').src = url;
        document.getElementById('lihatGambarJudul').textContent = judul;
        openModal('modalLihatGambar');
    }
</script>
@endsection