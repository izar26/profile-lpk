@extends('layouts.app')

@section('header', 'Data Siswa')

@section('content')

{{-- Alert Success --}}
@if (session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm">
        {{ session('success') }}
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
                   placeholder="Cari Nama / NIK...">
        </div>

        <select id="statusFilter" class="border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500 text-gray-700">
            <option value="Semua">Semua Status</option>
            <option value="Mendaftar">Mendaftar</option>
            <option value="Wawancara">Wawancara</option>
            <option value="Pelatihan">Pelatihan</option>
            <option value="Magang">Magang</option>
            <option value="Kerja">Kerja</option>
            <option value="Alumni">Alumni</option>
            <option value="Keluar">Keluar</option>
        </select>
    </div>

    <div class="flex flex-wrap gap-3 w-full xl:w-auto justify-end">
        
        <div class="flex rounded-md shadow-sm" role="group">
            <a href="{{ route('admin.students.export-excel') }}" class="px-4 py-2.5 text-sm font-semibold text-green-700 bg-green-100 border border-green-200 rounded-l-lg hover:bg-green-200 focus:z-10 focus:ring-2 focus:ring-green-300 flex items-center">
                <i class="fa-solid fa-file-excel mr-2"></i> Excel
            </a>
            <a href="{{ route('admin.students.export-pdf') }}" class="px-4 py-2.5 text-sm font-semibold text-red-700 bg-red-100 border border-l-0 border-red-200 rounded-r-lg hover:bg-red-200 focus:z-10 focus:ring-2 focus:ring-red-300 flex items-center">
                <i class="fa-solid fa-file-pdf mr-2"></i> PDF
            </a>
        </div>

        <button onclick="openModal('modalTambah')"
                class="px-5 py-2.5 bg-gold-500 text-white rounded-xl shadow-lg hover:bg-gold-600 transition-all font-semibold flex items-center">
            <i class="fa-solid fa-plus mr-2"></i> Siswa Baru
        </button>
    </div>
</div>

{{-- ================= TOOLBAR BULK ACTIONS (Muncul saat checkbox dipilih) ================= --}}
<div id="bulkActions" class="hidden bg-gold-50 border border-gold-200 p-3 rounded-xl mb-4 flex justify-between items-center animate-fade-in-down">
    <div class="flex items-center">
        <div class="bg-gold-100 text-gold-700 px-3 py-1 rounded-lg text-sm font-bold mr-3">
            <span id="selectedCount">0</span> Dipilih
        </div>
        <span class="text-sm text-gray-600">Aksi Massal:</span>
    </div>
    <div class="flex gap-2">
        <button onclick="bulkExport('excel')" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition shadow-sm">
            <i class="fa-solid fa-file-excel mr-1"></i> Export Excel
        </button>
        <button onclick="bulkExport('pdf')" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition shadow-sm">
            <i class="fa-solid fa-file-pdf mr-1"></i> Export PDF
        </button>
    </div>
</div>

{{-- ================= TABEL DATA (AJAX Container) ================= --}}
<div id="tableContainer" class="transition-opacity duration-200">
    @include('admin.students.partials.table')
</div>


{{-- ================================================================================= --}}
{{-- ========================== MODAL GENERATE AKUN ================================== --}}
{{-- ================================================================================= --}}
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
                <button type="button" onclick="closeModal('modalGenerateAkun')" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-md transition">
                    Ya, Buat Akun
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================================================= --}}
{{-- ============================= MODAL TAMBAH SISWA ================================ --}}
{{-- ================================================================================= --}}
<div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
    <div class="modal-content bg-white w-full max-w-2xl rounded-lg shadow-lg scale-90 opacity-0 transition-all duration-300 overflow-y-auto max-h-[90vh]">
        <h2 class="text-xl font-bold text-gray-900 p-6 border-b border-gray-200">Tambah Siswa Baru</h2>
        <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-4">
                <div class="bg-green-50 p-3 rounded-lg border border-green-100 mb-2 flex items-start">
                    <i class="fa-solid fa-circle-check text-green-600 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm text-green-800 font-bold">Akun Login Otomatis</p>
                        <p class="text-xs text-green-600 mt-1">
                            Sistem akan otomatis membuatkan User Login.<br>
                            Username: Email di bawah | Password: <strong>12345678</strong>
                        </p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="nama" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">NIK</label>
                        <input type="text" name="NIK" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300" placeholder="16 Digit Angka">
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
                        <label class="block text-sm mb-1 font-medium text-gray-700">Telepon / WA</label>
                        <input type="text" name="telepon" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300">
                    </div>
                </div>
                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300 focus:ring-gold-300"></textarea>
                </div>
                
                <div x-data="{ photoPreviewUrl: null, photoFilename: '', previewPhoto(event) { 
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
                    } }">
                    <label class="block text-sm mb-1 font-medium text-gray-700">Foto Siswa</label>
                    <div class="flex items-center gap-4">
                        <div class="shrink-0">
                            <img :src="photoPreviewUrl ?? 'https://ui-avatars.com/api/?background=random&name=Siswa'" 
                                 class="h-14 w-14 rounded-full object-cover border-2 border-gold-200">
                        </div>
                        <div class="w-full">
                            <label for="foto_tambah" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gold-50 text-gold-700 rounded-lg shadow-sm hover:bg-gold-100 transition-all duration-200 border border-gold-200 w-full justify-center">
                                <i class="fa-solid fa-upload mr-2"></i>
                                <span>Pilih Foto...</span>
                            </label>
                            <input @change="previewPhoto($event)" name="foto" type="file" id="foto_tambah" class="hidden"/>
                            <p x-text="photoFilename" class="mt-2 text-xs text-gray-600 italic"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end p-6 bg-gray-50 border-t border-gray-200 rounded-b-lg gap-2">
                <button type="button" onclick="closeModal('modalTambah')" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 font-medium text-gray-700">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-lg hover:bg-gold-600 font-semibold">Simpan & Buat Akun</button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================================================= --}}
{{-- =============================== MODAL EDIT SISWA ================================ --}}
{{-- ================================================================================= --}}
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
                        <input type="text" id="edit_nama" name="nama" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 font-medium text-gray-700">NIK</label>
                        <input type="text" id="edit_nik" name="NIK" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300">
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
                        <label class="block text-sm mb-1 font-medium text-gray-700">Telepon</label>
                        <input type="text" id="edit_telepon" name="telepon" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300">
                    </div>
                </div>
                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Alamat</label>
                    <textarea id="edit_alamat" name="alamat" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-300"></textarea>
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
    // ================= AJAX SEARCH & FILTER =================
    let timer; 
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const tableContainer = document.getElementById('tableContainer');

    searchInput.addEventListener('keyup', function() {
        clearTimeout(timer);
        timer = setTimeout(() => fetchStudents(), 500);
    });

    statusFilter.addEventListener('change', function() {
        fetchStudents();
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            let pageUrl = e.target.closest('.pagination a').href;
            fetchStudents(pageUrl);
        }
    });

    function fetchStudents(url = null) {
        let query = searchInput.value;
        let status = statusFilter.value;
        let fetchUrl = url ? url : `{{ route('admin.students.index') }}?search=${query}&status=${status}`;
        tableContainer.style.opacity = '0.5';
        fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                tableContainer.innerHTML = html;
                tableContainer.style.opacity = '1';
            });
    }


    // ================= CHECKBOX & EXPORT =================
    document.addEventListener('change', function(e) {
        if (e.target.id === 'selectAll') {
            document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = e.target.checked);
            updateBulkToolbar();
        }
        if (e.target.classList.contains('student-checkbox')) {
            updateBulkToolbar();
        }
    });

    function updateBulkToolbar() {
        const selected = document.querySelectorAll('.student-checkbox:checked');
        const toolbar = document.getElementById('bulkActions');
        const countSpan = document.getElementById('selectedCount');
        if (selected.length > 0) {
            toolbar.classList.remove('hidden');
            countSpan.textContent = selected.length;
        } else {
            toolbar.classList.add('hidden');
        }
    }

    function bulkExport(type) {
        const selected = document.querySelectorAll('.student-checkbox:checked');
        let ids = [];
        selected.forEach(cb => ids.push(cb.value));
        
        let url = '';
        if (type === 'excel') url = "{{ route('admin.students.export-excel') }}";
        if (type === 'pdf') url = "{{ route('admin.students.export-pdf') }}";

        window.location.href = url + '?ids=' + ids.join(',');
    }

    // ================= MODAL FUNCTIONS =================
    function loadEditStudent(id) {
        openModal('modalEdit');
        document.getElementById('edit-loading').classList.remove('hidden');
        document.getElementById('formEdit').classList.add('hidden');

        fetch(`/admin/students/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('edit_nama').value = data.nama;
                document.getElementById('edit_nik').value = data.NIK;
                document.getElementById('edit_program').value = data.program_pelatihan_id;
                document.getElementById('edit_status').value = data.status;
                document.getElementById('edit_email').value = data.email;
                document.getElementById('edit_telepon').value = data.telepon;
                document.getElementById('edit_alamat').value = data.alamat;

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
</script>
@endsection