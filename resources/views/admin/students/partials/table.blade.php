<div class="bg-white shadow-md rounded-xl overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 table-fixed">
        <thead class="bg-gray-50">
            <tr>
                <th class="w-10 px-6 py-3">
                    {{-- <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-gold-600 focus:ring-gold-500"> --}}
                </th>
                <th class="w-1/4 px-6 py-3 text-left text-xs text-gray-500 uppercase">Siswa</th>
                <th class="w-1/6 px-6 py-3 text-left text-xs text-gray-500 uppercase">No. Peserta</th>
                <th class="w-1/5 px-6 py-3 text-left text-xs text-gray-500 uppercase">Akun Login</th>
                <th class="w-1/6 px-6 py-3 text-left text-xs text-gray-500 uppercase">Program</th>
                <th class="w-1/5 px-6 py-3 text-left text-xs text-gray-500 uppercase">Kontak</th>
                <th class="w-1/6 px-6 py-3 text-left text-xs text-gray-500 uppercase">Status</th>
                <th class="w-1/6 px-6 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($students as $student)
                {{-- Highlight baris jika status Menunggu Verifikasi --}}
                <tr class="{{ $student->status == 'Menunggu Verifikasi' ? 'bg-yellow-50 hover:bg-yellow-100' : 'hover:bg-gray-50' }} transition">
                    <td class="px-6 py-4">
                        <input type="checkbox" name="selected_ids[]" value="{{ $student->id }}" class="student-checkbox rounded border-gray-300 text-gold-600 focus:ring-gold-500">
                    </td>

                    {{-- KOLOM SISWA --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($student->foto)
                                    <img class="h-10 w-10 rounded-full object-cover border border-gray-200" src="{{ asset('storage/' . $student->foto) }}" alt="">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gold-100 flex items-center justify-center text-gold-600 font-bold">
                                        {{ substr($student->nama_lengkap, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 max-w-[150px]">
                                <div class="text-sm font-medium text-gray-900 truncate" title="{{ $student->nama_lengkap }}">
                                    {{ $student->nama_lengkap }}
                                </div>
                                <div class="text-xs text-gray-500 truncate">
                                    ID: {{ $student->nomor_ktp ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- KOLOM NO PESERTA --}}
                    <td class="px-6 py-4">
                        <div class="text-sm font-mono text-gray-700 bg-gray-50 px-2 py-1 rounded inline-block whitespace-nowrap">
                             {{ $student->participant_number ?? '-' }}
                        </div>
                    </td>

                    {{-- KOLOM AKUN LOGIN --}}
                    <td class="px-6 py-4">
                        @if($student->user)
                            <div class="flex items-center max-w-[150px]">
                                <span class="h-2.5 w-2.5 bg-green-500 rounded-full mr-2 flex-shrink-0" title="Akun Aktif"></span>
                                <div class="truncate">
                                    <div class="text-xs text-gray-500">Aktif</div>
                                    <div class="text-xs font-medium text-gray-900 truncate" title="{{ $student->user->email }}">
                                        {{ $student->user->email }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col items-start gap-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    Belum Ada Akun
                                </span>
                                <button onclick="siapkanGenerateAkun('{{ route('admin.students.generate-account', $student) }}', '{{ addslashes($student->nama_lengkap) }}')"
                                        class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline cursor-pointer">
                                    <i class="fa-solid fa-key mr-1"></i> Buat Akun
                                </button>
                            </div>
                        @endif
                    </td>

                    {{-- KOLOM PROGRAM --}}
                    <td class="px-6 py-4 max-w-[150px]">
                        <div class="text-sm text-gray-700 font-medium truncate" title="{{ $student->program->judul ?? 'Belum Memilih' }}">
                            {{ $student->program->judul ?? 'Belum Memilih' }}
                        </div>
                    </td>

                    {{-- KOLOM KONTAK --}}
                    <td class="px-6 py-4 max-w-[150px]">
                        <div class="text-sm text-gray-900 truncate" title="{{ $student->email ?? '-' }}">{{ $student->email ?? '-' }}</div>
                        <div class="text-xs text-gray-500 truncate">
                            {{ $student->no_hp_peserta ?? '-' }}
                        </div>
                    </td>

                    {{-- KOLOM STATUS --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($student->status == 'Mendaftar') bg-gray-100 text-gray-800
                            @elseif($student->status == 'Menunggu Verifikasi') bg-yellow-200 text-yellow-800 animate-pulse
                            @elseif($student->status == 'Perlu Revisi') bg-orange-100 text-orange-800
                            @elseif($student->status == 'Wawancara') bg-purple-100 text-purple-800
                            @elseif($student->status == 'Pelatihan') bg-blue-100 text-blue-800
                            @elseif($student->status == 'Magang') bg-indigo-100 text-indigo-800
                            @elseif($student->status == 'Kerja') bg-green-100 text-green-800
                            @elseif($student->status == 'Alumni') bg-gold-100 text-gold-800 border border-gold-200
                            @else bg-red-100 text-red-800 @endif">
                            {{ $student->status }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">

    {{-- START: DROPDOWN FIXED POSITION (SOLUSI 1) --}}
    <div x-data="{
            open: false,
            top: 0,
            left: 0,
            toggle() {
                this.open = !this.open;
                if (this.open) {
                    const rect = $refs.btn.getBoundingClientRect();
                    // Posisi Top: Tepat di bawah tombol
                    this.top = rect.bottom;
                    // Posisi Left: Rata kanan dengan tombol (dikurangi lebar dropdown 192px/w-48)
                    this.left = rect.right - 192;
                }
            }
         }"
         @scroll.window="open = false"
         class="relative">

        {{-- TOMBOL TRIGGER --}}
        <button x-ref="btn" @click="toggle()" @click.away="open = false"
            class="inline-flex items-center px-3 py-1.5 bg-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-300 transition text-gray-700">
            Aksi
            <i class="fa-solid fa-caret-down ml-1"></i>
        </button>

        {{-- ISI DROPDOWN (FIXED) --}}
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

                {{-- OPSI 1: MENUNGGU VERIFIKASI --}}
                @if($student->status == 'Menunggu Verifikasi')

                    <li>
                        <a href="{{ route('admin.students.verify', $student->id) }}"
                           class="flex items-center px-4 py-2 hover:bg-green-50 text-green-600 font-semibold transition">
                            <i class="fa-solid fa-clipboard-check mr-2 w-5 text-center"></i>
                            Verifikasi
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.students.export-biodata', $student->id) }}" target="_blank"
                           class="flex items-center px-4 py-2 hover:bg-red-50 text-red-600 transition">
                            <i class="fa-solid fa-file-pdf mr-2 w-5 text-center"></i>
                            Biodata PDF
                        </a>
                    </li>

                {{-- OPSI 2: DATA SUDAH VERIFIED / NORMAL --}}
                @else

                    <li>
                        <a href="{{ route('admin.students.export-id-card', ['ids' => $student->id]) }}" target="_blank"
                           class="flex items-center px-4 py-2 hover:bg-gray-100 text-gray-700 transition">
                            <i class="fa-solid fa-id-card mr-2 w-5 text-center text-gray-500"></i>
                            Cetak ID Card
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.students.export-biodata', $student->id) }}" target="_blank"
                           class="flex items-center px-4 py-2 hover:bg-red-50 text-red-600 transition">
                            <i class="fa-solid fa-file-pdf mr-2 w-5 text-center"></i>
                            Biodata PDF
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.students.export-agreement', $student->id) }}" target="_blank"
                           class="flex items-center px-4 py-2 hover:bg-gray-100 text-gray-700 transition">
                            <i class="fa-solid fa-file-contract mr-2 w-5 text-center text-gray-500"></i>
                            Surat Perjanjian
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.students.show', $student) }}"
                           class="flex items-center px-4 py-2 hover:bg-blue-50 text-blue-600 font-semibold transition">
                            <i class="fa-solid fa-circle-info mr-2 w-5 text-center"></i>
                            Detail
                        </a>
                    </li>

                    <div class="border-t border-gray-100 my-1"></div>

                    <li>
                        {{-- Tambahkan open = false agar menu menutup saat diklik --}}
                        <button onclick="loadEditStudent({{ $student->id }});" @click="open = false"
                           class="w-full text-left flex items-center px-4 py-2 hover:bg-gray-100 text-gray-700 transition">
                            <i class="fa-solid fa-pen mr-2 w-5 text-center text-indigo-600"></i>
                            Edit
                        </button>
                    </li>

                    <li>
                        {{-- Tambahkan addslashes & open = false --}}
                        <button onclick="siapkanHapusStudent('{{ route('admin.students.destroy', $student) }}', '{{ addslashes($student->nama_lengkap) }}');" @click="open = false"
                           class="w-full text-left flex items-center px-4 py-2 hover:bg-red-50 text-red-600 transition">
                            <i class="fa-solid fa-trash mr-2 w-5 text-center"></i>
                            Hapus
                        </button>
                    </li>

                @endif

            </ul>
        </div>
    </div>
    {{-- END: DROPDOWN FIXED POSITION --}}

</td>


                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-2"></i>
                            <p>Belum ada data siswa ditemukan.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $students->withQueryString()->links() }}
</div>
