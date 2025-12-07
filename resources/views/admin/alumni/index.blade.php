@extends('layouts.app')

@section('header', 'Jejak Alumni & Testimoni')

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

{{-- Header Button --}}
<div class="flex justify-end mb-4">
    <button onclick="openModal('modalTambah')"
            class="px-5 py-2.5 bg-gold-500 text-white rounded-xl shadow-lg hover:bg-gold-600 transition-all font-semibold flex items-center shrink-0">
        <i class="fa-solid fa-plus mr-2"></i> Tambah Testimoni
    </button>
</div>

{{-- Table --}}
<div class="bg-white shadow-md rounded-xl overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Foto</th>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Nama & Angkatan</th>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Bekerja Di</th>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Testimoni</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($alumnis as $alumni)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        @php 
                            // Prioritas foto: Foto di tabel Alumni -> Foto di tabel Student -> Placeholder
                            $fotoUrl = $alumni->foto ? asset('storage/' . $alumni->foto) : 
                                      ($alumni->student->foto ? asset('storage/' . $alumni->student->foto) : null);
                        @endphp

                        @if($fotoUrl)
                            <button onclick="lihatGambar('{{ $fotoUrl }}', '{{ $alumni->student->nama_lengkap }}')">
                                <img src="{{ $fotoUrl }}" class="h-12 w-12 rounded-full object-cover border-2 border-gold-200 hover:scale-110 transition">
                            </button>
                        @else
                            <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 border-2 border-gray-200">
                                <i class="fa-solid fa-user"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-900">{{ $alumni->student->nama_lengkap ?? 'Data Terhapus' }}</p>
                        <p class="text-xs text-gray-500">Angkatan: {{ $alumni->angkatan ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fa-solid fa-map-pin mr-1"></i> {{ $alumni->kerja_dimana }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($alumni->is_published)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fa-solid fa-check-circle mr-1"></i> Public
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fa-solid fa-eye-slash mr-1"></i> Draft
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 max-w-xs">
                        <p class="text-sm text-gray-600 italic truncate">"{{ $alumni->testimoni }}"</p>
                    </td>
                    <td class="px-6 py-4 text-right text-sm space-x-2 whitespace-nowrap">
                        <button onclick="loadEditAlumni({{ $alumni->id }})" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</button>
                        <button onclick="siapkanHapusAlumni('{{ route('admin.alumni.destroy', $alumni) }}', '{{ $alumni->student->nama_lengkap }}')" class="text-red-600 hover:text-red-800 font-medium">Hapus</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada data testimoni alumni.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $alumnis->links() }}</div>


{{-- MODAL TAMBAH --}}
<div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
    <div class="modal-content bg-white w-full max-w-lg rounded-lg shadow-lg scale-90 opacity-0 transition-all duration-300">
        <h2 class="text-xl font-bold text-gray-900 p-6 border-b border-gray-200">Tambah Testimoni Alumni</h2>
        <form action="{{ route('admin.alumni.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-4 overflow-y-auto max-h-[70vh]">
                
                {{-- Select Student --}}
                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Pilih Siswa (Status: Alumni)</label>
                    <select name="student_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300 bg-white">
                        <option value="" disabled selected>-- Pilih Alumni --</option>
                        @foreach($availableStudents as $student)
                            <option value="{{ $student->id }}">{{ $student->nama_lengkap }} ({{ $student->nomor_ktp ?? 'No ID' }})</option>
                        @endforeach
                    </select>
                    @if($availableStudents->isEmpty())
                        <p class="text-xs text-red-500 mt-1">*Tidak ada siswa dengan status 'Alumni' yang belum memiliki testimoni.</p>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Bekerja di</label>
                        <input type="text" name="kerja_dimana" required placeholder="Contoh: Toyota, Osaka" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Angkatan</label>
                        <input type="text" name="angkatan" placeholder="Contoh: 2023" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Testimoni</label>
                    <textarea name="testimoni" rows="3" required placeholder="Kesan pesan..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300"></textarea>
                </div>
                
                {{-- Status Publish --}}
                <div class="flex items-center">
                    <input id="create_is_published" name="is_published" type="checkbox" value="1" class="h-4 w-4 text-gold-600 focus:ring-gold-500 border-gray-300 rounded">
                    <label for="create_is_published" class="ml-2 block text-sm text-gray-900">
                        Tampilkan di Website Publik?
                    </label>
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
                    <label class="block text-sm mb-1 font-medium text-gray-700">Foto Baru (Opsional)</label>
                    <p class="text-xs text-gray-500 mb-2">Jika kosong, akan menggunakan foto dari data siswa.</p>
                    <div class="flex items-center gap-4">
                        <div class="shrink-0">
                            <img :src="photoPreviewUrl ?? 'https://ui-avatars.com/api/?background=random&name=Alumni'" 
                                 class="h-16 w-16 rounded-full object-cover border-2 border-gold-200">
                        </div>
                        <div class="w-full">
                            <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-gold-50 text-gold-700 rounded-lg shadow-sm hover:bg-gold-100 transition border border-gold-200 w-full justify-center">
                                <i class="fa-solid fa-camera mr-2"></i> Upload Foto Kerja
                                <input @change="previewPhoto($event)" name="foto" type="file" class="hidden"/>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end p-6 bg-gray-50 border-t border-gray-200 rounded-b-lg gap-2">
                <button type="button" onclick="closeModal('modalTambah')" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 font-medium text-gray-700">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-lg hover:bg-gold-600 font-semibold">Simpan</button>
            </div>
        </form>
    </div>
</div>


{{-- MODAL EDIT --}}
<div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
    <div class="modal-content bg-white w-full max-w-lg rounded-lg shadow-lg scale-90 opacity-0 transition-all duration-300">
        <h2 class="text-xl font-bold text-gray-900 p-6 border-b border-gray-200">Edit Testimoni</h2>
        <div id="edit-loading" class="hidden text-center py-10"><div class="loader mx-auto"></div></div>
        
        <form id="formEdit" method="POST" enctype="multipart/form-data" class="hidden">
            @csrf @method('PUT')
            <div class="p-6 space-y-4 overflow-y-auto max-h-[70vh]">
                
                {{-- Nama Readonly --}}
                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Nama Alumni</label>
                    <input type="text" id="edit_nama_display" readonly class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-gray-500 cursor-not-allowed">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Bekerja di</label>
                        <input type="text" id="edit_kerja" name="kerja_dimana" required class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Angkatan</label>
                        <input type="text" id="edit_angkatan" name="angkatan" class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Testimoni</label>
                    <textarea id="edit_testimoni" name="testimoni" rows="3" required class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>

                 {{-- Status Publish Edit --}}
                 <div class="flex items-center bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                    <input id="edit_is_published" name="is_published" type="checkbox" value="1" class="h-4 w-4 text-gold-600 focus:ring-gold-500 border-gray-300 rounded">
                    <label for="edit_is_published" class="ml-2 block text-sm font-medium text-gray-900">
                        Layak ditampilkan di publik
                    </label>
                </div>
                
                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Ganti Foto</label>
                    <div class="flex items-center gap-4">
                        <img id="edit_foto_preview" src="" class="h-16 w-16 rounded-full object-cover border-2 border-gray-200 hidden">
                        <div id="edit_foto_placeholder" class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-2xl">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition border border-gray-300">
                            <i class="fa-solid fa-camera mr-2"></i> Upload Baru
                            <input name="foto" type="file" class="hidden" onchange="previewEditFoto(this)"/>
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex justify-end p-6 bg-gray-50 border-t border-gray-200 rounded-b-lg gap-2">
                <button type="button" onclick="closeModal('modalEdit')" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 font-medium text-gray-700">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-lg hover:bg-gold-600 font-semibold">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL HAPUS & LIHAT GAMBAR --}}
<div id="modalHapus" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="modal-content bg-white w-full max-w-md rounded-lg shadow-lg p-6 scale-90 opacity-0 transition-all duration-300">
        <h2 class="text-xl font-bold text-gray-900 mb-2">Hapus Testimoni?</h2>
        <p class="text-gray-600 mb-4">Testimoni dari <strong id="hapusNama"></strong> akan dihapus.</p>
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
    function loadEditAlumni(id) {
        openModal('modalEdit');
        document.getElementById('edit-loading').classList.remove('hidden');
        document.getElementById('formEdit').classList.add('hidden');

        fetch(`/admin/alumni/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                // Isi field
                document.getElementById('edit_nama_display').value = data.student.nama_lengkap;
                document.getElementById('edit_angkatan').value = data.angkatan;
                document.getElementById('edit_kerja').value = data.kerja_dimana;
                document.getElementById('edit_testimoni').value = data.testimoni;
                document.getElementById('edit_is_published').checked = data.is_published == 1;
                
                // Handle Foto Preview
                const imgPreview = document.getElementById('edit_foto_preview');
                const imgPlaceholder = document.getElementById('edit_foto_placeholder');
                
                // Jika ada foto di tabel Alumni
                if(data.foto) {
                    imgPreview.src = `/storage/${data.foto}`;
                    imgPreview.classList.remove('hidden');
                    imgPlaceholder.classList.add('hidden');
                } 
                // Jika tidak ada, coba ambil dari Student (opsional, perlu logic tambahan di backend JSON respon)
                else {
                    imgPreview.src = '';
                    imgPreview.classList.add('hidden');
                    imgPlaceholder.classList.remove('hidden');
                }

                document.getElementById('formEdit').action = `/admin/alumni/${id}`;
                setTimeout(() => {
                    document.getElementById('edit-loading').classList.add('hidden');
                    document.getElementById('formEdit').classList.remove('hidden');
                }, 300);
            });
    }

    // Helper untuk preview di modal edit
    function previewEditFoto(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('edit_foto_preview').src = e.target.result;
                document.getElementById('edit_foto_preview').classList.remove('hidden');
                document.getElementById('edit_foto_placeholder').classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function siapkanHapusAlumni(url, nama) {
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