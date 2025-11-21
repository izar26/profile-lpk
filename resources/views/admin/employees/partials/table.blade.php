<div class="bg-white shadow-md rounded-xl overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 table-fixed">
        <thead class="bg-gray-50">
            <tr>
                <th class="w-10 px-6 py-3"><input type="checkbox" id="selectAll" class="rounded border-gray-300 text-gold-600"></th>
                <th class="w-1/4 px-6 py-3 text-left text-xs text-gray-500 uppercase">Pegawai</th>
                <th class="w-1/6 px-6 py-3 text-left text-xs text-gray-500 uppercase">Akun Login</th>
                <th class="w-1/6 px-6 py-3 text-left text-xs text-gray-500 uppercase">Jabatan</th>
                <th class="w-1/5 px-6 py-3 text-left text-xs text-gray-500 uppercase">Kontak</th>
                <th class="w-1/6 px-6 py-3 text-left text-xs text-gray-500 uppercase">Data</th>
                <th class="w-1/6 px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($employees as $emp)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4"><input type="checkbox" name="selected_ids[]" value="{{ $emp->id }}" class="employee-checkbox rounded border-gray-300"></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($emp->foto) <img class="h-10 w-10 rounded-full object-cover border border-gray-200" src="{{ asset('storage/'.$emp->foto) }}">
                                @else <div class="h-10 w-10 rounded-full bg-gold-100 flex items-center justify-center text-gold-600 font-bold">{{ substr($emp->nama, 0, 1) }}</div> @endif
                            </div>
                            <div class="ml-4 max-w-[150px]">
                                <div class="text-sm font-medium text-gray-900 truncate" title="{{ $emp->nama }}">{{ $emp->nama }}</div>
                                <div class="text-xs text-gray-500 truncate">NIP: {{ $emp->nip ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($emp->user)
                            <div class="flex items-center max-w-[150px]">
                                <span class="h-2.5 w-2.5 bg-green-500 rounded-full mr-2 flex-shrink-0"></span>
                                <div class="truncate">
                                    <div class="text-xs text-gray-500">Aktif</div>
                                    <div class="text-xs font-medium text-gray-900 truncate">{{ $emp->user->email }}</div>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col items-start gap-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Belum Ada Akun</span>
                                <button onclick="siapkanGenerateAkun('{{ route('admin.employees.generate-account', $emp) }}', '{{ $emp->nama }}')" class="text-xs font-bold text-blue-600 hover:underline"><i class="fa-solid fa-key mr-1"></i> Buat Akun</button>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $emp->jabatan }}</span></td>
                    <td class="px-6 py-4 max-w-[150px]">
                        <div class="text-sm text-gray-900 truncate">{{ $emp->email ?? '-' }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ $emp->telepon ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2 max-w-[60px]">
                                <div class="bg-gold-500 h-2.5 rounded-full" style="width: {{ $emp->data_completion['percentage'] }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-600">{{ $emp->data_completion['percentage'] }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium space-x-2 whitespace-nowrap">
                        <a href="{{ route('admin.employees.show', $emp) }}" class="text-blue-600 hover:text-blue-900" title="Detail">Detail</a>
                        <a href="{{ route('admin.employees.export-biodata', $emp) }}" target="_blank" class="text-gray-500 hover:text-red-600"><i class="fa-solid fa-file-pdf"></i></a>
                        <button onclick="loadEdit({{ $emp->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                        <button onclick="siapkanHapus('{{ route('admin.employees.destroy', $emp) }}', '{{ $emp->nama }}')" class="text-red-600 hover:text-red-900">Hapus</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada data pegawai.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4" id="pagination-links">{{ $employees->links() }}</div>