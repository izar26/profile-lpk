@extends('layouts.app')

@section('header')
    Pengaturan Rincian Biaya
@endsection

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500">Atur komponen rincian biaya yang akan tampil pada cetakan PDF Perjanjian Peserta Pelatihan.</p>
        <button onclick="openModal('addModal')" class="bg-gold-500 text-white px-4 py-2 rounded-lg text-sm font-bold shadow hover:bg-gold-600 transition">
            <i class="fa-solid fa-plus mr-1"></i> Tambah Komponen
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200" role="alert">
            <span class="font-medium">Berhasil!</span> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 w-16 text-center">No</th>
                        <th scope="col" class="px-6 py-4">Nama Item / Komponen</th>
                        <th scope="col" class="px-6 py-4 text-right">Nominal (Rp)</th>
                        <th scope="col" class="px-6 py-4 w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php $total = 0; @endphp
                    @forelse($components as $i => $item)
                        @php $total += $item->amount; @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-center font-medium">{{ $i + 1 }}</td>
                            <td class="px-6 py-4 text-gray-900 font-bold">{{ $item->name }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center flex justify-center gap-2">
                                <button onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->amount }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button onclick="openDeleteModal({{ $item->id }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-file-invoice-dollar text-4xl mb-3 block"></i>
                                Belum ada komponen biaya yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-right font-bold text-gray-900 uppercase">Total Biaya</td>
                        <td class="px-6 py-4 text-right font-bold text-red-600 text-lg">Rp {{ number_format($total, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

{{-- MODAL TAMBAH --}}
<div id="addModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50 flex">
    <div class="relative p-4 w-full max-w-md max-h-full modal-content scale-90 opacity-0 transition-all duration-300">
        <div class="relative bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-lg font-bold text-gray-900">Tambah Komponen Biaya</h3>
                <button type="button" onclick="closeModal('addModal')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.fee-components.store') }}" method="POST" class="p-4 md:p-5">
                @csrf
                <div class="grid gap-4 mb-4 grid-cols-1">
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama Item Pembiayaan <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gold-500 focus:border-gold-500 block w-full p-2.5" placeholder="Cth: Biaya Pendaftaran" required>
                    </div>
                    <div>
                        <label for="amount" class="block mb-2 text-sm font-medium text-gray-900">Nominal (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" id="amount" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gold-500 focus:border-gold-500 block w-full p-2.5" placeholder="Cth: 100000" min="0" required>
                    </div>
                </div>
                <button type="submit" class="w-full text-white bg-gold-500 hover:bg-gold-600 focus:ring-4 focus:outline-none focus:ring-gold-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition">Simpan Data</button>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="editModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50 flex">
    <div class="relative p-4 w-full max-w-md max-h-full modal-content scale-90 opacity-0 transition-all duration-300">
        <div class="relative bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-lg font-bold text-gray-900">Edit Komponen Biaya</h3>
                <button type="button" onclick="closeModal('editModal')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <form id="editForm" method="POST" class="p-4 md:p-5">
                @csrf
                @method('PUT')
                <div class="grid gap-4 mb-4 grid-cols-1">
                    <div>
                        <label for="edit_name" class="block mb-2 text-sm font-medium text-gray-900">Nama Item Pembiayaan <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gold-500 focus:border-gold-500 block w-full p-2.5" required>
                    </div>
                    <div>
                        <label for="edit_amount" class="block mb-2 text-sm font-medium text-gray-900">Nominal (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" id="edit_amount" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gold-500 focus:border-gold-500 block w-full p-2.5" min="0" required>
                    </div>
                </div>
                <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition">Perbarui Data</button>
            </form>
        </div>
    </div>
</div>

{{-- MODAL HAPUS --}}
<div id="deleteModal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50 flex">
    <div class="relative p-4 w-full max-w-md max-h-full modal-content scale-90 opacity-0 transition-all duration-300">
        <div class="relative bg-white rounded-xl shadow">
            <button type="button" onclick="closeModal('deleteModal')" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                <i class="fa-solid fa-times"></i>
            </button>
            <div class="p-4 md:p-5 text-center">
                <i class="fa-solid fa-circle-exclamation text-5xl text-red-500 mb-4"></i>
                <h3 class="mb-5 text-lg font-normal text-gray-500">Apakah Anda yakin ingin menghapus komponen biaya ini?</h3>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                        Ya, Hapus
                    </button>
                </form>
                <button onclick="closeModal('deleteModal')" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100">Batalkan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openEditModal(id, name, amount) {
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_amount').value = amount;
        
        let url = '{{ route("admin.fee-components.update", ":id") }}';
        url = url.replace(':id', id);
        document.getElementById('editForm').action = url;
        
        openModal('editModal');
    }

    function openDeleteModal(id) {
        let url = '{{ route("admin.fee-components.destroy", ":id") }}';
        url = url.replace(':id', id);
        document.getElementById('deleteForm').action = url;
        
        openModal('deleteModal');
    }
</script>
@endsection
