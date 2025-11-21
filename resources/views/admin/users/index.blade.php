@extends('layouts.app')

@section('header', 'Manajemen User')

@section('content')

{{-- Success Message --}}
@if (session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Error Message --}}
@if (session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm">
        {{ session('error') }}
    </div>
@endif
@if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif


{{-- Header / Toolbar --}}
<div class="flex justify-end mb-4">
    <div class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-sm border border-blue-200 shadow-sm flex items-center">
        <i class="fa-solid fa-circle-info mr-2 text-lg"></i>
        <div>
            Untuk menambah user <strong>Siswa</strong> atau <strong>Pegawai</strong>, silakan input melalui menu datanya masing-masing.
            <br>Akun login akan dibuat secara otomatis.
        </div>
    </div>
</div>


{{-- Table --}}
<div class="bg-white shadow-md rounded-xl overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Nama</th>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Role</th>
                <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Status</th> 
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $user->name }}</td>
                    
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                    
                    <td class="px-6 py-4">
                        {{-- [UPDATE] Badge Role: Guru diganti Pegawai --}}
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full
                            @if($user->role=='admin') bg-red-100 text-red-800 @endif
                            @if($user->role=='pegawai') bg-yellow-100 text-yellow-800 @endif
                            @if($user->role=='siswa') bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    
                    <td class="px-6 py-4 text-sm text-gray-700">
                        @if(in_array($user->id, $onlineUserIds))
                            <span class="flex items-center text-green-600 font-semibold">
                                <span class="h-2.5 w-2.5 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                Online
                            </span>
                        @else
                            <span class="flex items-center text-gray-500">
                                <span class="h-2.5 w-2.5 bg-gray-400 rounded-full mr-2"></span>
                                Offline
                            </span>
                        @endif
                    </td>
                    
                    <td class="px-6 py-4 text-right text-sm space-x-3">
                        <button onclick="loadEdit({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900 transition font-medium">
                            Edit
                        </button>
                        
                        <button type="button" onclick="siapkanHapusUser('{{ route('admin.users.destroy', $user) }}', '{{ $user->name }}')" class="text-red-600 hover:text-red-800 transition font-medium">
                            Hapus
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>


{{-- ================================================================================= --}}
{{-- =============================== MODAL EDIT USER ================================= --}}
{{-- ================================================================================= --}}

<div id="modalEdit" class="modal hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50 p-4">
    <div class="modal-content bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 scale-90 opacity-0 transition-all duration-300">

        <h2 class="text-xl font-bold mb-4 text-gray-900">Edit User</h2>

        <div id="edit-loading" class="hidden text-center py-6">
            <div class="loader mx-auto mb-3"></div>
            <p class="text-gray-600">Mengambil data...</p>
        </div>

        <form id="formEdit" method="POST" class="hidden">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Nama</label>
                    <input type="text" id="edit_name" name="name" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gold-500 focus:ring-gold-500">
                </div>

                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Email</label>
                    <input type="email" id="edit_email" name="email" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gold-500 focus:ring-gold-500">
                </div>

                <div>
                    <label class="block text-sm mb-1 font-medium text-gray-700">Role</label>
                    <select id="edit_role" name="role" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-gold-500 focus:ring-gold-500">
                        <option value="admin">Admin</option>
                        <option value="pegawai">Pegawai</option>
                        <option value="siswa">Siswa</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end mt-6 space-x-2">
                <button type="button" onclick="closeModal('modalEdit')" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-medium text-gray-700">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-lg hover:bg-gold-600 transition font-semibold">
                    Update
                </button>
            </div>
        </form>

    </div>
</div>


{{-- ================================================================================= --}}
{{-- =========================== MODAL HAPUS USER ==================================== --}}
{{-- ================================================================================= --}}

<div id="modalHapusUser" class="modal hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50 p-4">
    <div class="modal-content bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 scale-90 opacity-0 transition-all duration-300">
        
        <div class="flex items-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-xl font-bold mb-1 text-gray-900">Konfirmasi Hapus</h2>
                <p class="text-sm text-gray-600">Anda yakin ingin menghapus user ini?</p>
            </div>
        </div>
        
        <div class="mt-4 bg-gray-50 p-3 rounded-lg border border-gray-200">
            <p class="text-sm text-gray-700">User: <strong id="itemHapusNamaUser" class="font-semibold">...</strong></p>
        </div>

        <form id="formHapusUser" method="POST" action="">
            @csrf
            @method('DELETE')
            
            <div class="flex justify-end mt-6 space-x-2">
                <button type="button" onclick="closeModal('modalHapusUser')" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-medium text-gray-700">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                    Ya, Hapus User
                </button>
            </div>
        </form>
    </div>
</div>

@endsection


@section('scripts')
<script>
function loadEdit(id) {
    openModal('modalEdit');
    document.getElementById('edit-loading').classList.remove('hidden');
    document.getElementById('formEdit').classList.add('hidden');

    fetch(`/admin/users/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_role').value = data.role;

            document.getElementById('formEdit').action = `/admin/users/${id}`;

            setTimeout(() => {
                document.getElementById('edit-loading').classList.add('hidden');
                document.getElementById('formEdit').classList.remove('hidden');
            }, 400);
        });
}

function siapkanHapusUser(url, nama) {
    document.getElementById('formHapusUser').action = url;
    document.getElementById('itemHapusNamaUser').textContent = nama;
    openModal('modalHapusUser');
}
</script>
@endsection