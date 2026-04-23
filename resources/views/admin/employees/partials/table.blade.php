<div class="bg-white shadow-md rounded-xl overflow-x-auto min-h-[400px] pb-20">
    <table class="min-w-full divide-y divide-gray-200 table-fixed">
        <thead class="bg-gray-50">
            <tr>
                {{-- CHECKBOX SELECT ALL --}}
                <th class="w-10 px-6 py-3">
                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-gold-600 focus:ring-gold-500">
                </th>

                <th class="w-1/4 px-6 py-3 text-left text-xs text-gray-500 uppercase font-bold tracking-wider">Pegawai</th>
                <th class="w-1/6 px-6 py-3 text-left text-xs text-gray-500 uppercase font-bold tracking-wider">Akun Login</th>
                <th class="w-1/6 px-6 py-3 text-left text-xs text-gray-500 uppercase font-bold tracking-wider">Jabatan</th>
                <th class="w-1/5 px-6 py-3 text-left text-xs text-gray-500 uppercase font-bold tracking-wider">Kontak</th>
                <th class="w-1/6 px-6 py-3 text-left text-xs text-gray-500 uppercase font-bold tracking-wider">Status</th>
                <th class="w-1/6 px-6 py-3 text-right text-xs text-gray-500 uppercase font-bold tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($employees as $emp)
                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">

                    {{-- CHECKBOX ROW --}}
                    <td class="px-6 py-4">
                        <input type="checkbox" name="selected_ids[]" value="{{ $emp->id }}" class="employee-checkbox rounded border-gray-300 text-gold-600 focus:ring-gold-500">
                    </td>

                    {{-- KOLOM PEGAWAI (FOTO + NAMA + NIP) --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($emp->foto)
                                    <img class="h-10 w-10 rounded-full object-cover border border-gray-200 shadow-sm" src="{{ asset('storage/'.$emp->foto) }}" alt="{{ $emp->nama }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gold-100 flex items-center justify-center text-gold-600 font-bold border border-gold-200">
                                        {{ substr($emp->nama, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 max-w-[150px]">
                                <div class="text-sm font-bold text-gray-900 truncate" title="{{ $emp->nama }}">{{ $emp->nama }}</div>
                                <div class="text-xs text-gray-500 truncate font-mono">NIP: {{ $emp->nip ?? '-' }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- KOLOM AKUN LOGIN --}}
                    <td class="px-6 py-4">
                        @if($emp->user)
                            <div class="flex items-center max-w-[150px]">
                                <span class="h-2.5 w-2.5 bg-green-500 rounded-full mr-2 flex-shrink-0 animate-pulse" title="Akun Aktif"></span>
                                <div class="truncate">
                                    <div class="text-xs text-gray-500">Aktif</div>
                                    <div class="text-xs font-medium text-gray-900 truncate" title="{{ $emp->user->email }}">
                                        {{ $emp->user->email }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col items-start gap-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    Belum Ada Akun
                                </span>
                                <button onclick="siapkanGenerateAkun('{{ route('admin.employees.generate-account', $emp) }}', '{{ addslashes($emp->nama) }}')"
                                        class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline cursor-pointer flex items-center">
                                    <i class="fa-solid fa-key mr-1"></i> Buat Akun
                                </button>
                            </div>
                        @endif
                    </td>

                    {{-- KOLOM JABATAN --}}
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                            {{ $emp->jabatan }}
                        </span>
                    </td>

                    {{-- KOLOM KONTAK --}}
                    <td class="px-6 py-4 max-w-[150px]">
                        <div class="text-sm text-gray-900 truncate" title="{{ $emp->email }}">{{ $emp->email ?? '-' }}</div>
                        <div class="text-xs text-gray-500 truncate">
                            <i class="fa-brands fa-whatsapp mr-1 text-green-500"></i> {{ $emp->telepon ?? '-' }}
                        </div>
                    </td>

                    {{-- KOLOM STATUS --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($emp->status_kepegawaian == 'Tetap') bg-blue-100 text-blue-800 border border-blue-200
                            @elseif($emp->status_kepegawaian == 'Kontrak') bg-green-100 text-green-800 border border-green-200
                            @elseif($emp->status_kepegawaian == 'Magang') bg-gray-100 text-gray-800 border border-gray-200
                            @elseif($emp->status_kepegawaian == 'Part-time') bg-purple-100 text-purple-800 border border-purple-200
                            @else bg-yellow-100 text-yellow-800 border border-yellow-200 @endif">
                            {{ $emp->status_kepegawaian }}
                        </span>
                    </td>

                    {{-- KOLOM AKSI (FIXED DROPDOWN) --}}
                    <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                        <div x-data="{
                                open: false,
                                top: 0,
                                left: 0,
                                toggle() {
                                    this.open = !this.open;
                                    if (this.open) {
                                        const rect = $refs.btn.getBoundingClientRect();
                                        this.top = rect.bottom;
                                        this.left = rect.right - 192;
                                    }
                                }
                             }"
                             @scroll.window="open = false"
                             class="relative">

                            <button x-ref="btn" @click="toggle()" @click.away="open = false"
                                class="inline-flex items-center px-3 py-1.5 bg-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-300 transition text-gray-700">
                                Aksi
                                <i class="fa-solid fa-caret-down ml-1"></i>
                            </button>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="fixed z-50 w-48 bg-white shadow-xl rounded-xl border border-gray-100 overflow-hidden text-left"
                                 :style="`top: ${top}px; left: ${left}px`"
                                 style="display: none;">

                                <ul class="py-1 text-sm">
                                    <li>
                                        <a href="{{ route('admin.employees.export-id-card', ['ids' => $emp->id]) }}"
                                           target="_blank"
                                           class="flex items-center px-4 py-2 hover:bg-gray-100 text-gray-700 transition">
                                            <i class="fa-solid fa-id-card mr-2 w-5 text-center text-indigo-600"></i>
                                            Cetak ID Card
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.employees.export-biodata', $emp) }}"
                                           target="_blank"
                                           class="flex items-center px-4 py-2 hover:bg-red-50 text-red-600 transition">
                                            <i class="fa-solid fa-file-pdf mr-2 w-5 text-center"></i>
                                            Biodata PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.employees.show', $emp) }}"
                                           class="flex items-center px-4 py-2 hover:bg-blue-50 text-blue-600 font-semibold transition">
                                            <i class="fa-solid fa-circle-info mr-2 w-5 text-center"></i>
                                            Detail
                                        </a>
                                    </li>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <li>
                                        <a href="{{ route('admin.employees.edit', $emp->id) }}"
                                            class="w-full text-left flex items-center px-4 py-2 hover:bg-gray-100 text-gray-700 transition">
                                            <i class="fa-solid fa-pen-to-square mr-2 w-5 text-center text-slate-600"></i>
                                            Edit
                                        </a>
                                    </li>
                                    <li>
                                        <button onclick="siapkanHapus('{{ route('admin.employees.destroy', $emp) }}', '{{ addslashes($emp->nama) }}')" @click="open = false"
                                            class="w-full text-left flex items-center px-4 py-2 hover:bg-red-50 text-red-600 transition">
                                            <i class="fa-solid fa-trash mr-2 w-5 text-center"></i>
                                            Hapus
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fa-solid fa-users-slash text-4xl text-gray-300 mb-3"></i>
                            <p class="font-medium">Tidak ada data pegawai ditemukan.</p>
                            <p class="text-xs mt-1">Coba sesuaikan kata kunci pencarian atau filter jabatan.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $employees->links() }}
</div>

<script>
    document.getElementById('selectAll').addEventListener('change', function(e) {
        const checkboxes = document.querySelectorAll('.employee-checkbox');
        checkboxes.forEach(cb => cb.checked = e.target.checked);
    });
</script>
