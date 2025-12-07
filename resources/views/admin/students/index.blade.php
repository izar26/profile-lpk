@extends('layouts.app')

@section('header', 'Data Siswa')

@section('content')

{{-- Alert Success --}}
@if (session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm flex items-center">
        <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
@endif

{{-- Alert Error --}}
@if ($errors->any() && !session('openModalEdit') && !session('openModalTambah'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

{{-- ================= TOOLBAR UTAMA ================= --}}
<div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-6 gap-4">
    
    <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
        <div class="relative w-full sm:w-64">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
            </div>
            <input type="text" id="searchInput" 
                   class="pl-10 w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500" 
                   placeholder="Cari Nama / No KTP...">
        </div>

        <select id="statusFilter" class="border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500 text-gray-700">
            <option value="Semua">Semua Status</option>
            <option value="Mendaftar">Mendaftar</option>
            <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
            <option value="Perlu Revisi">Perlu Revisi</option>
            <option value="Ditolak">Ditolak</option>
            <option value="Wawancara">Wawancara</option>
            <option value="Pelatihan">Pelatihan</option>
            <option value="Magang">Magang</option>
            <option value="Kerja">Kerja</option>
            <option value="Alumni">Alumni</option>
            <option value="Keluar">Keluar</option>
        </select>
    </div>

    <div class="flex flex-wrap gap-3 w-full xl:w-auto justify-end">
        
        {{-- GRUP TOMBOL CETAK --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.away="open = false" class="px-4 py-2.5 bg-gray-800 text-white rounded-lg hover:bg-gray-700 font-medium shadow-md flex items-center transition">
                <i class="fa-solid fa-print mr-2"></i> Menu Cetak <i class="fa-solid fa-chevron-down ml-2 text-xs"></i>
            </button>
            
            {{-- Dropdown Menu --}}
            <div x-show="open" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl z-50 border border-gray-100 overflow-hidden" style="display: none;">
                <div class="p-2 space-y-1">
                    {{-- Opsi 1: Cetak Kartu Pilihan (Checkbox) --}}
                    <button onclick="cetakKartuPilihan()" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gold-50 text-gray-700 text-sm font-medium transition flex items-center group">
                        <span class="w-8 h-8 rounded-full bg-gold-100 text-gold-600 flex items-center justify-center mr-3 group-hover:bg-gold-500 group-hover:text-white transition">
                            <i class="fa-solid fa-check-double"></i>
                        </span>
                        <div>
                            <div class="font-bold">Kartu (Terpilih)</div>
                            <div class="text-xs text-gray-400">Yg dicentang saja</div>
                        </div>
                    </button>

                    {{-- Opsi 2: Cetak Kartu Semua/Filter --}}
                    <button onclick="cetakKartuSemua()" class="w-full text-left px-3 py-2 rounded-lg hover:bg-blue-50 text-gray-700 text-sm font-medium transition flex items-center group">
                        <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 group-hover:bg-blue-600 group-hover:text-white transition">
                            <i class="fa-solid fa-users"></i>
                        </span>
                        <div>
                            <div class="font-bold">Kartu (Semua)</div>
                            <div class="text-xs text-gray-400">Sesuai Filter</div>
                        </div>
                    </button>

                    <div class="border-t border-gray-100 my-1"></div>

                    {{-- Laporan Excel & PDF Biasa --}}
                    <a href="{{ route('admin.students.export-excel') }}" class="w-full text-left px-3 py-2 rounded-lg hover:bg-green-50 text-gray-700 text-sm font-medium transition flex items-center">
                        <i class="fa-solid fa-file-excel mr-3 text-green-600"></i> Laporan Excel
                    </a>
                    <a href="{{ route('admin.students.export-pdf') }}" class="w-full text-left px-3 py-2 rounded-lg hover:bg-red-50 text-gray-700 text-sm font-medium transition flex items-center">
                        <i class="fa-solid fa-file-pdf mr-3 text-red-600"></i> Laporan PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Tombol Tambah Siswa (Tetap) --}}
        <button onclick="openModal('modalTambah')"
                class="px-5 py-2.5 bg-gold-500 text-white rounded-xl shadow-lg hover:bg-gold-600 transition-all font-semibold flex items-center">
            <i class="fa-solid fa-plus mr-2"></i> Siswa Baru
        </button>
    </div>
</div>

{{-- ================= TABEL DATA (AJAX Container) ================= --}}
<div id="tableContainer" class="transition-opacity duration-200">
    @include('admin.students.partials.table')
</div>


{{-- ================= MODAL GENERATE AKUN ================= --}}
<div id="modalGenerateAkun" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
    <div class="modal-content bg-white w-full max-w-md rounded-xl shadow-2xl p-6 scale-90 opacity-0 transition-all duration-300">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-user-shield text-blue-600 text-2xl"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Buat Akun Login?</h2>
            <p class="text-sm text-gray-500 mt-2">
                Anda akan membuat akun login untuk siswa <strong id="generateNamaSiswa" class="text-gray-800">...</strong>.
            </p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg mb-6 text-sm border border-gray-100">
            <p class="flex justify-between mb-1">
                <span class="text-gray-500">Password Default:</span>
                <span class="font-mono font-bold text-gray-800">12345678</span>
            </p>
            <p class="flex justify-between">
                <span class="text-gray-500">Role:</span>
                <span class="font-bold text-blue-600">Siswa</span>
            </p>
        </div>
        <form id="formGenerateAkun" method="POST" action="">
            @csrf
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('modalGenerateAkun')" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-md transition">Ya, Buat Akun</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL TAMBAH SISWA ================= --}}
<div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
    <div class="modal-content bg-white w-full max-w-2xl rounded-lg shadow-lg scale-90 opacity-0 transition-all duration-300 overflow-y-auto max-h-[90vh]">
        <h2 class="text-xl font-bold text-gray-900 p-6 border-b border-gray-200">Tambah Siswa Baru</h2>
        
        <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-4">
                
                {{-- Info Akun Otomatis --}}
                <div class="bg-green-50 p-3 rounded-lg border border-green-100 mb-2 flex items-start">
                    <i class="fa-solid fa-circle-check text-green-600 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm text-green-800 font-bold">Akun Login Otomatis</p>
                        <p class="text-xs text-green-600 mt-1">Username: Email | Password: <strong>12345678</strong></p>
                    </div>
                </div>
                
                {{-- Grid Input Data --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Nama Lengkap</label>
                        {{-- UPDATE: name="nama_lengkap" --}}
                        <input type="text" name="nama_lengkap" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Nomor KTP</label>
                        <input type="text" name="nomor_ktp" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300" placeholder="16 Digit Angka">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Program Pelatihan</label>
                        <select name="program_pelatihan_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                            <option value="">-- Pilih Program --</option>
                            @foreach($programs as $prog)
                                <option value="{{ $prog->id }}">{{ $prog->judul }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                            <option value="Mendaftar">Mendaftar</option>
                            <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                            <option value="Perlu Revisi">Perlu Revisi</option>
                            <option value="Ditolak">Ditolak</option>
                            <option value="Wawancara">Wawancara</option>
                            <option value="Pelatihan">Pelatihan</option>
                            <option value="Magang">Magang</option>
                            <option value="Kerja">Kerja</option>
                            <option value="Alumni">Alumni</option>
                            <option value="Keluar">Keluar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Email (Wajib Login)</label>
                        <input type="email" name="email" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">No. HP / WA</label>
                        {{-- UPDATE: name="no_hp_peserta" --}}
                        <input type="text" name="no_hp_peserta" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Alamat Domisili</label>
                    {{-- UPDATE: name="alamat_domisili" --}}
                    <textarea name="alamat_domisili" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300"></textarea>
                </div>
                
                {{-- CUSTOM UPLOAD FOTO DENGAN PREVIEW (AlpineJS) --}}
                <div x-data="{ 
                    photoPreviewUrl: null, 
                    photoFilename: null, 
                    previewPhoto(event) { 
                        const file = event.target.files[0];
                        if (file) {
                            this.photoFilename = file.name;
                            const reader = new FileReader();
                            reader.onload = (e) => { this.photoPreviewUrl = e.target.result; };
                            reader.readAsDataURL(file);
                        } else {
                            this.photoFilename = null;
                            this.photoPreviewUrl = null;
                        }
                    } 
                }">
                    <label class="block text-sm mb-2 font-medium text-gray-700">Foto Siswa</label>
                    <div class="flex items-center gap-5">
                        <div class="shrink-0 relative group">
                            <div class="h-16 w-16 rounded-full overflow-hidden border-2 border-gray-200 bg-gray-100 flex items-center justify-center">
                                <template x-if="photoPreviewUrl">
                                    <img :src="photoPreviewUrl" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!photoPreviewUrl">
                                    <i class="fa-solid fa-user text-gray-400 text-2xl"></i>
                                </template>
                            </div>
                        </div>

                        <div class="w-full">
                            <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 shadow-sm transition-all duration-200">
                                <i class="fa-solid fa-camera mr-2 text-gray-500"></i>
                                <span class="text-sm font-medium">Pilih Foto...</span>
                                <input name="foto" type="file" accept="image/*" class="hidden" @change="previewPhoto($event)">
                            </label>
                            <p x-show="photoFilename" x-text="photoFilename" class="mt-2 text-xs text-green-600 font-medium"></p>
                            <p x-show="!photoFilename" class="mt-2 text-xs text-gray-400">Format: JPG, PNG (Max 2MB)</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex justify-end p-6 bg-gray-50 border-t border-gray-200 rounded-b-lg gap-2">
                <button type="button" onclick="closeModal('modalTambah')" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 font-medium text-gray-700 transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-lg hover:bg-gold-600 font-semibold shadow-md transition transform hover:-translate-y-0.5">Simpan & Buat Akun</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL EDIT SISWA ================= --}}
<div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
    <div class="modal-content bg-white w-full max-w-2xl rounded-lg shadow-lg scale-90 opacity-0 transition-all duration-300">
        <h2 class="text-xl font-bold text-gray-900 p-6 border-b border-gray-200">Edit Data Siswa</h2>
        <div id="edit-loading" class="hidden text-center py-10"><div class="loader mx-auto"></div></div>
        <form id="formEdit" method="POST" enctype="multipart/form-data" class="hidden">
            @csrf @method('PUT')
            <div class="p-6 space-y-4 overflow-y-auto max-h-[70vh]">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Nama Lengkap</label>
                        {{-- UPDATE: id="edit_nama_lengkap", name="nama_lengkap" --}}
                        <input type="text" id="edit_nama_lengkap" name="nama_lengkap" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Nomor KTP</label>
                        <input type="text" id="edit_nomor_ktp" name="nomor_ktp" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Program</label>
                        <select id="edit_program" name="program_pelatihan_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300">
                            <option value="">-- Pilih Program --</option>
                            @foreach($programs as $prog)
                                <option value="{{ $prog->id }}">{{ $prog->judul }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Status</label>
                        <select id="edit_status" name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300">
                            <option value="Mendaftar">Mendaftar</option>
                            <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                            <option value="Perlu Revisi">Perlu Revisi</option>
                            <option value="Ditolak">Ditolak</option>
                            <option value="Wawancara">Wawancara</option>
                            <option value="Pelatihan">Pelatihan</option>
                            <option value="Magang">Magang</option>
                            <option value="Kerja">Kerja</option>
                            <option value="Alumni">Alumni</option>
                            <option value="Keluar">Keluar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Email (Update Akun Login)</label>
                        <input type="email" id="edit_email" name="email" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">No. HP / WA</label>
                        {{-- UPDATE: id="edit_no_hp_peserta", name="no_hp_peserta" --}}
                        <input type="text" id="edit_no_hp_peserta" name="no_hp_peserta" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300">
                    </div>
                </div>
                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Alamat Domisili</label>
                    {{-- UPDATE: id="edit_alamat_domisili", name="alamat_domisili" --}}
                    <textarea id="edit_alamat_domisili" name="alamat_domisili" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Ganti Foto</label>
                    <div class="flex items-center gap-4">
                        <img id="edit_foto_preview" src="" class="h-14 w-14 rounded-full object-cover border-2 border-gray-200 hidden">
                        <div id="edit_foto_placeholder" class="h-14 w-14 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-xl"><i class="fa-solid fa-user"></i></div>
                        <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 border border-gray-300 transition">
                            <i class="fa-solid fa-camera mr-2"></i> Ganti Foto
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

{{-- MODAL HAPUS --}}
<div id="modalHapus" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="modal-content bg-white w-full max-w-md rounded-lg shadow-lg p-6 scale-90 opacity-0 transition-all duration-300">
        <h2 class="text-xl font-bold text-gray-900 mb-2">Hapus Data?</h2>
        <p class="text-gray-600 mb-4">Yakin ingin menghapus data siswa <strong id="hapusNama"></strong>? Akun login terkait juga akan dihapus permanen.</p>
        <form id="formHapus" method="POST">
            @csrf @method('DELETE')
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalHapus')" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg">Hapus</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
   document.addEventListener('DOMContentLoaded', function () {
        // Ambil elemen
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const tableContainer = document.getElementById('tableContainer');

        // Variable untuk Debounce (Jeda waktu ketik)
        let debounceTimer;

        // 1. Event Listener untuk SEARCH (KeyUp)
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchStudents();
            }, 500); // Tunggu 500ms setelah user selesai mengetik
        });

        // 2. Event Listener untuk FILTER STATUS (Change)
        statusFilter.addEventListener('change', function () {
            fetchStudents();
        });

        // 3. Event Listener untuk PAGINATION (Klik Link)
        document.addEventListener('click', function(e) {
            const paginationLink = e.target.closest('#tableContainer .pagination a');
            if (paginationLink) {
                e.preventDefault();
                const url = paginationLink.getAttribute('href');
                fetchStudents(url);
            }
        });

        // === FUNGSI UTAMA AJAX FETCH ===
        function fetchStudents(url = null) {
            let fetchUrl = url || "{{ route('admin.students.index') }}";
            
            const searchValue = searchInput.value;
            const statusValue = statusFilter.value;

            const urlObj = new URL(fetchUrl, window.location.origin);
            
            if (searchValue) {
                urlObj.searchParams.set('search', searchValue);
            }
            if (statusValue && statusValue !== 'Semua') {
                urlObj.searchParams.set('status', statusValue);
            }

            tableContainer.style.opacity = '0.5';

            fetch(urlObj.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                tableContainer.innerHTML = html;
                tableContainer.style.opacity = '1';
            })
            .catch(error => {
                console.error('Error:', error);
                tableContainer.style.opacity = '1';
            });
        }
    });

    // ================= MODAL FUNCTIONS =================
    function loadEditStudent(id) {
        openModal('modalEdit');
        document.getElementById('edit-loading').classList.remove('hidden');
        document.getElementById('formEdit').classList.add('hidden');

        fetch(`/admin/students/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                // UPDATE FIELD MAPPING SESUAI DATABASE BARU
                // Pastikan key object data.nama_field sesuai dengan response controller
                document.getElementById('edit_nama_lengkap').value = data.nama_lengkap; // Update ke nama_lengkap
                document.getElementById('edit_nomor_ktp').value = data.nomor_ktp;
                document.getElementById('edit_program').value = data.program_pelatihan_id;
                document.getElementById('edit_status').value = data.status;
                document.getElementById('edit_email').value = data.email;
                document.getElementById('edit_no_hp_peserta').value = data.no_hp_peserta;
                document.getElementById('edit_alamat_domisili').value = data.alamat_domisili;

                const imgPreview = document.getElementById('edit_foto_preview');
                const imgPlaceholder = document.getElementById('edit_foto_placeholder');
                if (data.foto) {
                    imgPreview.src = `/storage/${data.foto}`;
                    imgPreview.classList.remove('hidden');
                    imgPlaceholder.classList.add('hidden');
                } else {
                    imgPreview.src = '';
                    imgPreview.classList.add('hidden');
                    imgPlaceholder.classList.remove('hidden');
                }

                document.getElementById('formEdit').action = `/admin/students/${id}`;
                setTimeout(() => {
                    document.getElementById('edit-loading').classList.add('hidden');
                    document.getElementById('formEdit').classList.remove('hidden');
                }, 400);
            });
    }

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

    function siapkanHapusStudent(url, nama) {
        document.getElementById('formHapus').action = url;
        document.getElementById('hapusNama').textContent = nama;
        openModal('modalHapus');
    }

    function siapkanGenerateAkun(url, nama) {
        document.getElementById('formGenerateAkun').action = url;
        document.getElementById('generateNamaSiswa').textContent = nama;
        openModal('modalGenerateAkun');
    }

    // --- FUNGSI CETAK KARTU ---
    
    // 1. Cetak Berdasarkan Checkbox (Pilihan)
    function cetakKartuPilihan() {
        // Ambil semua checkbox yang dicentang
        const selected = document.querySelectorAll('.student-checkbox:checked');
        
        if (selected.length === 0) {
            alert('Pilih minimal satu siswa pada tabel untuk mencetak kartu.');
            return;
        }

        // Ambil ID dari checkbox value
        let ids = [];
        selected.forEach((checkbox) => {
            ids.push(checkbox.value);
        });

        // Redirect ke route export dengan parameter ids
        const url = `{{ route('admin.students.export-id-card') }}?ids=${ids.join(',')}`;
        window.open(url, '_blank');
    }

    // 2. Cetak Semua (Berdasarkan Filter saat ini)
    function cetakKartuSemua() {
        // Ambil nilai filter saat ini
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;

        // Build URL
        let url = `{{ route('admin.students.export-id-card') }}?mode=all`;
        if (search) url += `&search=${search}`;
        if (status && status !== 'Semua') url += `&status=${status}`;

        // Konfirmasi jika data banyak
        if(confirm('Apakah Anda yakin ingin mencetak ID Card untuk semua data yang tampil saat ini?')) {
            window.open(url, '_blank');
        }
    }
</script>
@endsection