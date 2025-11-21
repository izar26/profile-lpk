@extends('layouts.app')

@section('header', 'Program Pelatihan')

@section('content')

{{-- success message --}}
@if (session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif

{{-- error message (penting untuk validasi modal) --}}
@if ($errors->any() && !session('openModalEdit') && !session('openModalTambah'))
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
    <button onclick="openModal('modalTambah')"
            class="px-4 py-2 bg-gold-500 text-white rounded-xl shadow hover:bg-gold-600 transition-all">
        + Tambah Program
    </button>
</div>


<div class="bg-white shadow-md rounded-xl overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 table-fixed">
        <thead class="bg-gray-50">
            <tr>
                <th class="w-1/6 px-6 py-3 text-left text-xs text-gray-500 uppercase">Gambar</th>
                <th class="w-2/5 px-6 py-3 text-left text-xs text-gray-500 uppercase">Judul</th>
                <th class="w-1/6 px-6 py-3 text-left text-xs text-gray-500 uppercase">Status</th>
                <th class="w-1/5 px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($programs as $program)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        @if($program->gambar_fitur)
                            <button type="button" 
                                    onclick="lihatGambar('{{ asset('storage/' . $program->gambar_fitur) }}', '{{ $program->judul }}')">
                                <img src="{{ asset('storage/' . $program->gambar_fitur) }}" alt="{{ $program->judul }}" 
                                     class="h-10 w-16 object-cover rounded-md border border-gray-200 
                                            hover:scale-110 hover:shadow-md transition-all duration-200">
                            </button>
                        @else
                            <div class="h-10 w-16 bg-gray-100 rounded-md flex items-center justify-center text-gray-400">
                                <i class="fa-solid fa-image"></i>
                            </div>
                        @endif
                    </td>
                    
                    <td class="px-6 py-4 max-w-sm break-words">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $program->judul }}</p>
                        <p class="text-sm text-gray-600">{{ $program->deskripsi_singkat }}</p>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-xl
                            @if($program->status=='Buka Pendaftaran') bg-green-100 text-green-800 @endif
                            @if($program->status=='Berjalan') bg-blue-100 text-blue-800 @endif
                            @if($program->status=='Selesai') bg-gray-100 text-gray-800 @endif
                            @if($program->status=='Akan Datang') bg-yellow-100 text-yellow-800 @endif">
                            {{ $program->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm space-x-3 whitespace-nowrap">
                        <button onclick="loadEditProgram({{ $program->id }})"
                                class="text-indigo-600 hover:text-indigo-900 transition">
                            Edit
                        </button>
                        <button type="button" 
                                onclick="siapkanHapusProgram('{{ route('admin.program-pelatihan.destroy', $program) }}', '{{ $program->judul }}')"
                                class="text-red-600 hover:text-red-800 transition">
                            Hapus
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        Belum ada program pelatihan yang ditambahkan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $programs->links() }}
</div>



{{-- ================================================================================= --}}
{{-- ============================= MODAL TAMBAH PROGRAM ============================== --}}
{{-- ================================================================================= --}}

<div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
    <div class="modal-content bg-white w-full max-w-md rounded-lg shadow-lg scale-90 opacity-0 transition-all duration-300">

        <h2 class="text-xl font-bold text-gray-900 p-6 border-b border-gray-200">Tambah Program Baru</h2>

        <form action="{{ route('admin.program-pelatihan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 overflow-y-auto" style="max-height: 70vh;">
                <div class="space-y-4">
                    
                    <div>
                        <label for="judul_tambah" class="block text-sm mb-1 font-medium text-gray-700">Judul Program</label>
                        <input type="text" id="judul_tambah" name="judul" value="{{ old('judul') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                        @error('judul') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="deskripsi_singkat_tambah" class="block text-sm mb-1 font-medium text-gray-700">Deskripsi Singkat</label>
                        <input type="text" id="deskripsi_singkat_tambah" name="deskripsi_singkat" value="{{ old('deskripsi_singkat') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300" placeholder="Maks. 255 karakter">
                        @error('deskripsi_singkat') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="deskripsi_lengkap_tambah" class="block text-sm mb-1 font-medium text-gray-700">Deskripsi Lengkap (Opsional)</label>
                        <textarea id="deskripsi_lengkap_tambah" name="deskripsi_lengkap" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">{{ old('deskripsi_lengkap') }}</textarea>
                        @error('deskripsi_lengkap') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="status_tambah" class="block text-sm mb-1 font-medium text-gray-700">Status</label>
                        <select id="status_tambah" name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                            <option value="Akan Datang" {{ old('status') == 'Akan Datang' ? 'selected' : '' }}>Akan Datang</option>
                            <option value="Buka Pendaftaran" {{ old('status') == 'Buka Pendaftaran' ? 'selected' : '' }}>Buka Pendaftaran</option>
                            <option value="Berjalan" {{ old('status') == 'Berjalan' ? 'selected' : '' }}>Berjalan</option>
                            <option value="Selesai" {{ old('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                        @error('status') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div x-data="{ photoPreviewUrl: null, photoFilename: '{{ old('gambar_fitur') ? old('gambar_fitur')->getClientOriginalName() : '' }}', 
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
                        <label class="block text-sm mb-1 font-medium text-gray-700">Gambar Fitur (Opsional)</label>
                        <label for="gambar_fitur_tambah" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gold-50 text-gold-700 rounded-lg shadow-sm hover:bg-gold-100 transition-all duration-200">
                            <i class="fa-solid fa-upload mr-2 text-gold-600"></i>
                            Pilih Gambar...
                        </label>
                        <input @change="previewPhoto($event)" name="gambar_fitur" type="file" id="gambar_fitur_tambah" class="hidden"/>
                        <span x-text="photoFilename" class="ml-3 text-sm text-gray-600 block"></span>
                        
                        <div x-show="photoPreviewUrl" class="mt-4">
                            <img :src="photoPreviewUrl" class="w-auto rounded-lg object-cover border-2 border-gold-200" style="max-height: 180px;">
                        </div>
                        @error('gambar_fitur') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
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

{{-- ================================================================================= --}}
{{-- =============================== MODAL EDIT PROGRAM ============================== --}}
{{-- ================================================================================= --}}

<div id="modalEdit" class="modal hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50 p-4">
    <div class="modal-content bg-white w-full max-w-md rounded-2xl shadow-2xl scale-90 opacity-0 transition-all duration-300">

        <h2 class="text-xl font-bold text-gray-900 p-6 border-b border-gray-200">Edit Program</h2>

        <div id="edit-loading" class="hidden text-center py-20">
            <div class="loader mx-auto mb-3"></div>
            <p class="text-gray-600">Mengambil data...</p>
        </div>

        <form id="formEdit" method="POST" enctype="multipart/form-data" class="hidden">
            @csrf
            @method('PUT')
            
            <div class="p-6 overflow-y-auto" style="max-height: 70vh;">
                <div class="space-y-4">
                    
                    <div>
                        <label for="edit_judul" class="block text-sm mb-1 font-medium text-gray-700">Judul Program</label>
                        <input type="text" id="edit_judul" name="judul" required class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300">
                        @error('judul') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="edit_deskripsi_singkat" class="block text-sm mb-1 font-medium text-gray-700">Deskripsi Singkat</label>
                        <input type="text" id="edit_deskripsi_singkat" name="deskripsi_singkat" required class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300">
                        @error('deskripsi_singkat') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="edit_deskripsi_lengkap" class="block text-sm mb-1 font-medium text-gray-700">Deskripsi Lengkap (Opsional)</label>
                        <textarea id="edit_deskripsi_lengkap" name="deskripsi_lengkap" rows="4" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300"></textarea>
                        @error('deskripsi_lengkap') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="edit_status" class="block text-sm mb-1 font-medium text-gray-700">Status</label>
                        <select id="edit_status" name="status" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-300 focus:ring-gold-300">
                            <option value="Akan Datang">Akan Datang</option>
                            <option value="Buka Pendaftaran">Buka Pendaftaran</option>
                            <option value="Berjalan">Berjalan</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                        @error('status') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2" x-data="{ photoPreviewUrl: null, photoFilename: '', 
                                      previewPhoto(event, previewElementId) { 
                                        const file = event.target.files[0];
                                        if (file) {
                                            this.photoFilename = file.name;
                                            const reader = new FileReader();
                                            reader.onload = (e) => {
                                                this.photoPreviewUrl = e.target.result;
                                                const img = document.getElementById(previewElementId);
                                                img.src = e.target.result;
                                                img.classList.remove('hidden');
                                            };
                                            reader.readAsDataURL(file);
                                        } else {
                                            this.photoFilename = '';
                                        }
                                      } 
                                    }">
                        <label class="block text-sm mb-1 font-medium text-gray-700">Gambar Fitur (Opsional)</label>
                        
                        <img id="edit_preview_image" src="" alt="Preview" class="w-auto rounded-lg object-cover border-2 border-gold-200 mb-2 hidden" style="max-height: 180px;">
                        
                        <label for="gambar_fitur_edit" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gold-50 text-gold-700 rounded-lg shadow-sm hover:bg-gold-100 transition-all duration-200">
                            <i class="fa-solid fa-upload mr-2 text-gold-600"></i>
                            Ganti Gambar...
                        </label>
                        <input @change="previewPhoto($event, 'edit_preview_image')" name="gambar_fitur" type="file" id="gambar_fitur_edit" class="hidden"/>
                        <span x-text="photoFilename" class="ml-3 text-sm text-gray-600 block"></span>
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengganti gambar.</p>
                        @error('gambar_fitur') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end p-6 bg-gray-50 border-t border-gray-200 rounded-b-lg space-x-2">
                <button type="button" onclick="closeModal('modalEdit')" class="px-4 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition text-gray-700 font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-xl hover:bg-gold-600 transition font-semibold">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Hapus --}}
<div id="modalHapusProgram" class="modal hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50">
    <div class="modal-content bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 scale-90 opacity-0 transition-all duration-300">
        <div class="flex items-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-xl font-bold mb-1 text-gray-900">Konfirmasi Hapus</h2>
                <p class="text-sm text-gray-600">Anda yakin ingin menghapus program ini?</p>
            </div>
        </div>
        <div class="mt-4 bg-gray-50 p-3 rounded-lg">
            <p class="text-sm text-gray-700">Program: <strong id="itemHapusNamaProgram" class="font-semibold">...</strong></p>
        </div>
        <form id="formHapusProgram" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex justify-end mt-6 space-x-2">
                <button type="button" onclick="closeModal('modalHapusProgram')" class="px-4 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition font-semibold text-gray-700">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition font-semibold">Ya, Hapus Program</button>
            </div>
        </form>
    </div>
</div>

<div id="modalLihatGambar" class="modal hidden fixed inset-0 bg-black/80 flex justify-center items-center z-50 p-4" onclick="closeModal('modalLihatGambar')">
    <button type="button" onclick="closeModal('modalLihatGambar')" class="absolute top-4 right-4 text-white/70 hover:text-white transition-all z-[60]">
        <i class="fa-solid fa-times text-4xl"></i>
    </button>
    <div class="modal-content scale-90 opacity-0 transition-all duration-300 relative" onclick="event.stopPropagation()">
        <img id="lihatGambarSrc" src="" alt="Preview Gambar" 
             class="w-auto h-auto object-contain rounded-lg shadow-lg" 
             style="max-width: 90vw; max-height: 85vh;">
        <div class="mt-3 text-center">
            <h3 id="lihatGambarJudul" class="text-lg font-semibold text-white">...</h3>
        </div>
    </div>
</div>

@endsection

{{-- ================================================================================= --}}
{{-- ================================== SCRIPT ======================================= --}}
{{-- ================================================================================= --}}
@section('scripts')
<script>
    // Fungsi preview gambar disatukan di sini
    function previewPhoto(event, previewElementId = null) {
        const file = event.target.files[0];
        const alpineScope = event.target.closest('[x-data]');
        
        if (file) {
            alpineScope._x_dataStack[0].photoFilename = file.name;
            const reader = new FileReader();
            reader.onload = (e) => {
                const previewUrl = e.target.result;
                alpineScope._x_dataStack[0].photoPreviewUrl = previewUrl; // Update scope Alpine
                
                if(previewElementId) {
                    // Modal Edit: Tampilkan preview di img yang ada
                    const img = document.getElementById(previewElementId);
                    img.src = previewUrl;
                    img.classList.remove('hidden');
                }
            };
            reader.readAsDataURL(file);
        } else {
            alpineScope._x_dataStack[0].photoFilename = '';
            alpineScope._x_dataStack[0].photoPreviewUrl = null;
            if(previewElementId) {
                 document.getElementById(previewElementId).classList.add('hidden');
            }
        }
    }

    // Fungsi untuk memuat data ke modal Edit
    function loadEditProgram(id) {
        openModal('modalEdit');
        document.getElementById('edit-loading').classList.remove('hidden');
        document.getElementById('formEdit').classList.add('hidden');

        fetch(`/admin/program-pelatihan/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                // Isi form
                document.getElementById('edit_judul').value = data.judul;
                document.getElementById('edit_deskripsi_singkat').value = data.deskripsi_singkat;
                document.getElementById('edit_deskripsi_lengkap').value = data.deskripsi_lengkap;
                document.getElementById('edit_status').value = data.status;

                // Tampilkan gambar yg ada
                const imgPreview = document.getElementById('edit_preview_image');
                const alpineScope = imgPreview.closest('[x-data]');

                if(data.gambar_fitur) {
                    const imageUrl = `/storage/${data.gambar_fitur}`;
                    imgPreview.src = imageUrl;
                    imgPreview.classList.remove('hidden');
                    if(alpineScope) alpineScope._x_dataStack[0].photoPreviewUrl = imageUrl;
                } else {
                    imgPreview.classList.add('hidden');
                    if(alpineScope) alpineScope._x_dataStack[0].photoPreviewUrl = null;
                }

                // Reset filename saat load data baru
                if (alpineScope) alpineScope._x_dataStack[0].photoFilename = '';

                // Set action form
                document.getElementById('formEdit').action = `/admin/program-pelatihan/${id}`;

                // Tampilkan form
                setTimeout(() => {
                    document.getElementById('edit-loading').classList.add('hidden');
                    document.getElementById('formEdit').classList.remove('hidden');
                }, 400);
            });
    }

    // Fungsi untuk menyiapkan modal Hapus
    function siapkanHapusProgram(url, nama) {
        document.getElementById('formHapusProgram').action = url;
        document.getElementById('itemHapusNamaProgram').textContent = nama;
        openModal('modalHapusProgram');
    }

    // Fungsi untuk modal Lihat Gambar
    function lihatGambar(url, judul) {
        document.getElementById('lihatGambarSrc').src = url;
        document.getElementById('lihatGambarJudul').textContent = judul;
        openModal('modalLihatGambar');
    }

    // --- LOGIKA MENAMPILKAN MODAL SAAT ADA ERROR VALIDASI ---
    document.addEventListener('DOMContentLoaded', function() {
        @if ($errors->any())
            @if (session('openModalTambah'))
                openModal('modalTambah');
            @elseif (session('openModalEdit'))
                const programId = {{ session('editProgramId') ?? 'null' }};
                if (programId) {
                    loadEditProgram(programId); // Memuat data dan menampilkan modal
                    // Setelah form muncul, isi ulang old input
                    setTimeout(() => {
                        document.getElementById('edit_judul').value = "{{ old('judul') }}";
                        document.getElementById('edit_deskripsi_singkat').value = "{{ old('deskripsi_singkat') }}";
                        document.getElementById('edit_deskripsi_lengkap').value = "{{ old('deskripsi_lengkap') }}";
                        document.getElementById('edit_status').value = "{{ old('status') }}";
                    }, 500); // Tunda sedikit agar data fetch selesai
                }
            @endif
        @endif
    });
</script>
@endsection