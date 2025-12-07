@extends('layouts.app')

@section('header', 'Data Pegawai')

@section('content')

{{-- Alert Success --}}
@if (session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm flex items-center">
        <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
@endif

{{-- ================= TOOLBAR UTAMA ================= --}}
<div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-6 gap-4">
    
    <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
        {{-- Input Pencarian --}}
        <div class="relative w-full sm:w-64">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
            </div>
            <input type="text" id="searchInput" 
                   class="pl-10 w-full border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500" 
                   placeholder="Cari Nama / NIP...">
        </div>

        {{-- Filter Jabatan --}}
        <select id="jabatanFilter" class="border-gray-300 rounded-lg focus:border-gold-500 focus:ring-gold-500 text-gray-700">
            <option value="Semua">Semua Jabatan</option>
            @foreach($listJabatan as $jabatan)
                <option value="{{ $jabatan }}">{{ $jabatan }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex flex-wrap gap-3 w-full xl:w-auto justify-end">
        
        {{-- [BARU] MENU CETAK DROPDOWN --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.away="open = false" class="px-4 py-2.5 bg-gray-800 text-white rounded-lg hover:bg-gray-700 font-medium shadow-md flex items-center transition">
                <i class="fa-solid fa-print mr-2"></i> Menu Cetak <i class="fa-solid fa-chevron-down ml-2 text-xs"></i>
            </button>
            
            <div x-show="open" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl z-50 border border-gray-100 overflow-hidden" style="display: none;">
                <div class="p-2 space-y-1">
                    {{-- Opsi 1: Cetak ID Card Pilihan --}}
                    <button onclick="cetakKartuPilihan()" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gold-50 text-gray-700 text-sm font-medium transition flex items-center group">
                        <span class="w-8 h-8 rounded-full bg-gold-100 text-gold-600 flex items-center justify-center mr-3 group-hover:bg-gold-500 group-hover:text-white transition">
                            <i class="fa-solid fa-id-card"></i>
                        </span>
                        <div>
                            <div class="font-bold">ID Card (Terpilih)</div>
                            <div class="text-xs text-gray-400">Yg dicentang saja</div>
                        </div>
                    </button>

                    {{-- Opsi 2: Cetak ID Card Semua --}}
                    <button onclick="cetakKartuSemua()" class="w-full text-left px-3 py-2 rounded-lg hover:bg-blue-50 text-gray-700 text-sm font-medium transition flex items-center group">
                        <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 group-hover:bg-blue-600 group-hover:text-white transition">
                            <i class="fa-solid fa-users"></i>
                        </span>
                        <div>
                            <div class="font-bold">ID Card (Semua)</div>
                            <div class="text-xs text-gray-400">Sesuai Filter</div>
                        </div>
                    </button>

                    <div class="border-t border-gray-100 my-1"></div>

                    {{-- Laporan Excel & PDF --}}
                    <a href="{{ route('admin.employees.export-excel') }}" class="w-full text-left px-3 py-2 rounded-lg hover:bg-green-50 text-gray-700 text-sm font-medium transition flex items-center">
                        <i class="fa-solid fa-file-excel mr-3 text-green-600"></i> Laporan Excel
                    </a>
                    <a href="{{ route('admin.employees.export-pdf') }}" class="w-full text-left px-3 py-2 rounded-lg hover:bg-red-50 text-gray-700 text-sm font-medium transition flex items-center">
                        <i class="fa-solid fa-file-pdf mr-3 text-red-600"></i> Laporan PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Tombol Tambah --}}
        <button onclick="openModal('modalTambah')"
                class="px-5 py-2.5 bg-gold-500 text-white rounded-xl shadow-lg hover:bg-gold-600 transition-all font-semibold flex items-center">
            <i class="fa-solid fa-plus mr-2"></i> Pegawai Baru
        </button>
    </div>
</div>

{{-- ================= TABEL DATA (AJAX Container) ================= --}}
<div id="tableContainer" class="transition-opacity duration-200">
    @include('admin.employees.partials.table')
</div>

{{-- Include Modal Tambah, Edit, Hapus, Generate Akun disini (Sama seperti sebelumnya/Siswa, sesuaikan ID modalnya) --}}
{{-- ... (Gunakan kode modal yang sudah ada, pastikan ID modal sesuai script dibawah) ... --}}

@endsection

@section('scripts')
<script>
    // === 1. AJAX SEARCH & FILTER ===
    let timer;
    const searchInput = document.getElementById('searchInput');
    const jabatanFilter = document.getElementById('jabatanFilter');
    const tableContainer = document.getElementById('tableContainer');

    // Trigger saat mengetik
    searchInput.addEventListener('keyup', function() {
        clearTimeout(timer);
        timer = setTimeout(() => fetchEmployees(), 500);
    });

    // Trigger saat ganti jabatan
    jabatanFilter.addEventListener('change', function() { fetchEmployees(); });

    // Trigger saat klik pagination
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            fetchEmployees(e.target.closest('.pagination a').href);
        }
    });

    function fetchEmployees(url = null) {
        let query = searchInput.value;
        let jabatan = jabatanFilter.value;
        let fetchUrl = url ? url : `{{ route('admin.employees.index') }}?search=${query}&jabatan=${jabatan}`;
        
        tableContainer.style.opacity = '0.5';
        
        fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                tableContainer.innerHTML = html;
                tableContainer.style.opacity = '1';
            });
    }

    // === 2. FUNGSI CETAK KARTU ===

    // Cetak Berdasarkan Checkbox
    function cetakKartuPilihan() {
        // Pastikan di partials/table.blade.php Anda sudah menambahkan checkbox dengan class 'employee-checkbox'
        const selected = document.querySelectorAll('.employee-checkbox:checked');
        
        if (selected.length === 0) {
            alert('Pilih minimal satu pegawai pada tabel untuk mencetak kartu.');
            return;
        }

        let ids = [];
        selected.forEach((cb) => ids.push(cb.value));

        const url = `{{ route('admin.employees.export-id-card') }}?ids=${ids.join(',')}`;
        window.open(url, '_blank');
    }

    // Cetak Semua (Sesuai Filter)
    function cetakKartuSemua() {
        const query = searchInput.value;
        const jabatan = jabatanFilter.value;

        let url = `{{ route('admin.employees.export-id-card') }}?mode=all`;
        if (query) url += `&search=${query}`;
        if (jabatan && jabatan !== 'Semua') url += `&jabatan=${jabatan}`;

        if(confirm('Yakin ingin mencetak ID Card untuk semua data yang tampil?')) {
            window.open(url, '_blank');
        }
    }

    // === 3. MODAL FUNCTIONS (Paste fungsi loadEdit, delete, generateAccount disini) ===
    // (Sama seperti kode sebelumnya, sesuaikan nama field ID-nya)
</script>
@endsection