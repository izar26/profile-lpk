<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Terverifikasi - {{ $student->nama_lengkap }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .verified-badge { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .animate-pop { animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
        @keyframes popIn { 0% { opacity: 0; transform: scale(0.8); } 100% { opacity: 1; transform: scale(1); } }
        .section-title { @apply text-sm font-bold text-slate-700 uppercase tracking-wider border-b border-slate-200 pb-2 mb-3 mt-2; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4">

    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden animate-pop">
        
        {{-- ================= HEADER STATUS ================= --}}
        <div class="verified-badge text-white p-4 text-center shadow-md relative z-10">
            <div class="flex items-center justify-center gap-2 text-lg font-bold uppercase tracking-widest">
                <i class="fa-solid fa-circle-check text-yellow-300"></i> Siswa Terverifikasi
            </div>
            <p class="text-xs text-green-100 mt-1">Data Valid dari Database Resmi {{ $profile->nama_lpk ?? 'LPK' }}</p>
        </div>

        {{-- ================= PROFIL UTAMA ================= --}}
        <div class="relative bg-slate-800">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
            
            <div class="flex flex-col items-center pt-10 pb-8 px-6 text-center relative z-10">
                {{-- Foto Profil --}}
                <div class="h-32 w-32 rounded-full border-4 border-white bg-white shadow-lg overflow-hidden mb-4">
                    @if($student->foto)
                        <img src="{{ asset('storage/' . $student->foto) }}" class="h-full w-full object-cover">
                    @else
                        <div class="h-full w-full bg-slate-200 flex items-center justify-center text-slate-400 text-4xl">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    @endif
                </div>

                {{-- Nama & Program --}}
                <h1 class="text-2xl md:text-3xl font-bold text-white capitalize">{{ $student->nama_lengkap }}</h1>
                <p class="text-slate-300 font-medium mt-1 text-lg">{{ $student->program->judul ?? 'Peserta Pelatihan' }}</p>
                
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-500 text-white shadow-sm uppercase tracking-wide">
                        STATUS: {{ $student->status }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-700 text-slate-200 shadow-sm font-mono">
                        ID: {{ $student->nomor_ktp }}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-8 bg-white">

            {{-- ================= 1. DATA PRIBADI ================= --}}
            <div>
                <h3 class="section-title"><i class="fa-solid fa-user mr-2 text-slate-400"></i> Data Pribadi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="block text-xs text-slate-400 uppercase">Tempat, Tanggal Lahir</span>
                        <span class="font-medium text-slate-800">{{ $student->tempat_lahir }}, {{ $student->tanggal_lahir ? $student->tanggal_lahir->translatedFormat('d F Y') : '-' }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="block text-xs text-slate-400 uppercase">Jenis Kelamin</span>
                        <span class="font-medium text-slate-800">{{ $student->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="block text-xs text-slate-400 uppercase">Agama</span>
                        <span class="font-medium text-slate-800">{{ $student->agama ?? '-' }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="block text-xs text-slate-400 uppercase">Status Pernikahan</span>
                        <span class="font-medium text-slate-800">{{ $student->status_pernikahan ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- ================= 2. FISIK & KONTAK ================= --}}
            <div>
                <h3 class="section-title"><i class="fa-solid fa-address-card mr-2 text-slate-400"></i> Fisik & Kontak</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="block text-xs text-slate-400 uppercase">Tinggi / Berat</span>
                        <span class="font-medium text-slate-800">{{ $student->tinggi_badan ?? '-' }} cm / {{ $student->berat_badan ?? '-' }} kg</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="block text-xs text-slate-400 uppercase">Golongan Darah</span>
                        <span class="font-medium text-slate-800">{{ $student->golongan_darah ?? '-' }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="block text-xs text-slate-400 uppercase">Email</span>
                        <span class="font-medium text-slate-800 break-words">{{ $student->email }}</span>
                    </div>
                    <div class="md:col-span-3 bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="block text-xs text-slate-400 uppercase">Alamat Domisili</span>
                        <span class="font-medium text-slate-800">{{ $student->alamat_domisili ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- ================= 3. RIWAYAT PENDIDIKAN (Looping) ================= --}}
            @if($student->educations->isNotEmpty())
            <div>
                <h3 class="section-title"><i class="fa-solid fa-graduation-cap mr-2 text-slate-400"></i> Riwayat Pendidikan</h3>
                <div class="space-y-3">
                    @foreach($student->educations as $edu)
                    <div class="flex items-start bg-slate-50 p-3 rounded-lg border-l-4 border-blue-500 shadow-sm">
                        <div class="flex-1">
                            <h4 class="font-bold text-slate-800 text-sm">{{ $edu->nama_institusi }}</h4>
                            <p class="text-xs text-slate-600">{{ $edu->jurusan }} ({{ $edu->tingkat }})</p>
                        </div>
                        <div class="text-right">
                            <span class="block text-xs font-bold text-blue-600 bg-blue-100 px-2 py-1 rounded">Lulus: {{ $edu->tahun_lulus }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ================= 4. PENGALAMAN KERJA (Looping) ================= --}}
            @if($student->experiences->where('tipe', 'Pekerjaan')->isNotEmpty())
            <div>
                <h3 class="section-title"><i class="fa-solid fa-briefcase mr-2 text-slate-400"></i> Pengalaman Kerja</h3>
                <div class="space-y-3">
                    @foreach($student->experiences->where('tipe', 'Pekerjaan') as $exp)
                    <div class="flex items-start bg-slate-50 p-3 rounded-lg border-l-4 border-orange-500 shadow-sm">
                        <div class="flex-1">
                            <h4 class="font-bold text-slate-800 text-sm">{{ $exp->posisi }}</h4>
                            <p class="text-xs text-slate-600">{{ $exp->nama_instansi }}</p>
                        </div>
                        <div class="text-right">
                            <span class="block text-[10px] text-slate-500">
                                {{ $exp->tanggal_mulai ? $exp->tanggal_mulai->format('M Y') : '?' }} - 
                                {{ $exp->tanggal_selesai ? $exp->tanggal_selesai->format('M Y') : 'Sekarang' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ================= 5. DATA KELUARGA (Looping) ================= --}}
            @if($student->families->isNotEmpty())
            <div>
                <h3 class="section-title"><i class="fa-solid fa-users mr-2 text-slate-400"></i> Data Keluarga</h3>
                <div class="overflow-x-auto border border-slate-200 rounded-lg">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-100 text-slate-600">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-bold uppercase">Nama</th>
                                <th class="px-3 py-2 text-left text-xs font-bold uppercase">Hubungan</th>
                                <th class="px-3 py-2 text-left text-xs font-bold uppercase">Pekerjaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($student->families as $fam)
                            <tr>
                                <td class="px-3 py-2 text-slate-800 font-medium">{{ $fam->nama }}</td>
                                <td class="px-3 py-2 text-slate-600">{{ $fam->hubungan }}</td>
                                <td class="px-3 py-2 text-slate-600">{{ $fam->pekerjaan ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>

        {{-- ================= FOOTER ================= --}}
        <div class="bg-slate-900 p-6 flex flex-col gap-4 text-center">
            
            <a href="https://wa.me/{{ $profile->nomor_wa ?? '' }}" class="block w-full py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold shadow-lg transition flex items-center justify-center gap-2 animate-bounce">
                <i class="fa-brands fa-whatsapp text-2xl"></i> Hubungi Admin LPK
            </a>

            <div class="text-slate-400 text-xs mt-2">
                <p>Dokumen ini valid dan digenerate secara otomatis oleh sistem.</p>
                <p class="font-mono mt-1 text-slate-600">Secure Hash: {{ md5($student->id . $student->created_at . 'Hachimitsu') }}</p>
                <p class="mt-4">&copy; {{ date('Y') }} {{ $profile->nama_lpk ?? 'LPK System' }}</p>
            </div>
        </div>

    </div>

</body>
</html>