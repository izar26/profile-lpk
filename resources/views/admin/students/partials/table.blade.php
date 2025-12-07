<div class="bg-white shadow-md rounded-xl overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 table-fixed">
        <thead class="bg-gray-50">
            <tr>
                <th class="w-10 px-6 py-3">
                    {{-- <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-gold-600 focus:ring-gold-500"> --}}
                </th>
                <th class="w-1/4 px-6 py-3 text-left text-xs text-gray-500 uppercase">Siswa</th>
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
                                <button onclick="siapkanGenerateAkun('{{ route('admin.students.generate-account', $student) }}', '{{ $student->nama_lengkap }}')" 
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

                    {{-- KOLOM AKSI --}}
                    <td class="px-6 py-4 text-right text-sm font-medium space-x-2 whitespace-nowrap">
                        @if($student->status == 'Menunggu Verifikasi')
                            {{-- Tombol Verifikasi (Prioritas) --}}
                            <a href="{{ route('admin.students.verify', $student->id) }}" class="inline-flex items-center px-3 py-1.5 bg-gold-500 text-white text-xs font-bold rounded-lg hover:bg-gold-600 shadow-sm transition transform hover:-translate-y-0.5">
                                <i class="fa-solid fa-clipboard-check mr-1.5"></i> VERIFIKASI
                            </a>
                            {{-- Tombol PDF Tetap Muncul (Untuk Cek Data Fisik) --}}
                            <a href="{{ route('admin.students.export-biodata', $student->id) }}" target="_blank" class="text-red-600 hover:text-red-800" title="Cetak Biodata PDF">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
                        @else
                            <a href="{{ route('admin.students.export-id-card', ['ids' => $student->id]) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 mr-1" title="Cetak ID Card">
    <i class="fa-solid fa-id-card"></i>
</a>

                            {{-- Tombol PDF --}}
                            <a href="{{ route('admin.students.export-biodata', $student->id) }}" target="_blank" class="text-red-600 hover:text-red-800 mr-1" title="Cetak Biodata PDF">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>

                            <a href="{{ route('admin.students.export-agreement', $student->id) }}" target="_blank" class="text-green-600 hover:text-green-800 mr-1" title="Cetak Surat Perjanjian">
    <i class="fa-solid fa-file-contract"></i>
</a>

                            {{-- Tombol Detail --}}
                            <a href="{{ route('admin.students.show', $student) }}" class="text-blue-600 hover:text-blue-900 font-bold" title="Lihat Data Lengkap">
                                Detail
                            </a>
                            
                            {{-- Tombol Edit --}}
                            <button onclick="loadEditStudent({{ $student->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                            
                            {{-- Tombol Hapus --}}
                            <button onclick="siapkanHapusStudent('{{ route('admin.students.destroy', $student) }}', '{{ $student->nama_lengkap }}')" class="text-red-600 hover:text-red-900">Hapus</button>
                        @endif
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