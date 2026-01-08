<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Terverifikasi - {{ $employee->nama }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .verified-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .animate-pop {
            animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }
        @keyframes popIn {
            0% { opacity: 0; transform: scale(0.8); }
            100% { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4 flex items-center justify-center">

    <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden animate-pop">

        {{-- Status Bar: VERIFIED --}}
        <div class="verified-badge text-white p-4 text-center shadow-md relative z-10">
            <div class="flex items-center justify-center gap-2 text-lg font-bold uppercase tracking-widest">
                <i class="fa-solid fa-circle-check text-yellow-300 text-xl"></i> Pegawai Resmi
            </div>
            <p class="text-xs text-green-100 mt-1">
                Data Valid & Terdaftar di {{ $profile->nama_lpk ?? 'Database Pusat' }}
            </p>
        </div>

        <div class="relative">
            {{-- Background Header --}}
            <div class="h-36 bg-slate-800"></div>

            {{-- Foto Profil Besar --}}
            <div class="absolute -bottom-16 left-1/2 transform -translate-x-1/2">
                <div class="h-32 w-32 rounded-full border-4 border-white bg-white shadow-lg overflow-hidden">
                    @if($employee->foto)
                        <img src="{{ asset('storage/' . $employee->foto) }}" class="h-full w-full object-cover">
                    @else
                        <div class="h-full w-full bg-gray-200 flex items-center justify-center text-gray-400 text-4xl">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="pt-20 pb-6 px-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900">{{ $employee->nama }}</h1>
            <p class="text-gray-500 font-medium">{{ $employee->jabatan }}</p>

            <div class="mt-3 flex justify-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                    NIP: {{ $employee->nip ?? '-' }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                    {{ $employee->status_kepegawaian == 'Tetap' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                    {{ strtoupper($employee->status_kepegawaian) }}
                </span>
            </div>
        </div>

        {{-- Detail Informasi --}}
        <div class="bg-gray-50 px-6 py-8 border-t border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- TTL --}}
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1">Tempat, Tgl Lahir</p>
                    <p class="text-gray-800 font-medium">
                        {{ $employee->tempat_lahir ?? '-' }},
                        {{ $employee->tanggal_lahir ? \Carbon\Carbon::parse($employee->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                    </p>
                </div>

                {{-- Gender --}}
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1">Jenis Kelamin</p>
                    <p class="text-gray-800 font-medium">
                        {{ $employee->jenis_kelamin == 'L' ? 'Laki-laki' : ($employee->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                    </p>
                </div>

                {{-- Pendidikan (Mengambil dari Relasi) --}}
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1">Pendidikan Terakhir</p>
                    <p class="text-gray-800 font-medium">
                        @if($employee->educations->isNotEmpty())
                            {{ $employee->educations->sortByDesc('tahun_lulus')->first()->jenjang }}
                            <span class="text-xs text-gray-500 block truncate">{{ $employee->educations->sortByDesc('tahun_lulus')->first()->nama_sekolah }}</span>
                        @else
                            -
                        @endif
                    </p>
                </div>

                {{-- Email --}}
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1">Email</p>
                    <p class="text-gray-800 font-medium break-all">{{ $employee->email }}</p>
                </div>
            </div>

            {{-- Alamat --}}
            <div class="mt-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1">Alamat Domisili</p>
                <p class="text-gray-800">
                    {{ $employee->alamat_domisili ?? $employee->alamat_ktp ?? '-' }}
                </p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="p-6 bg-white flex flex-col gap-3 text-center">
            @if($profile && $profile->nomor_wa)
                <a href="https://wa.me/{{ $profile->nomor_wa }}" class="block w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold shadow-md transition flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp text-xl"></i> Hubungi Kantor
                </a>
            @endif

            <div class="mt-2">
                <p class="text-[10px] text-gray-400">
                    Verifikasi ID: <span class="font-mono">{{ substr(md5($employee->id . $employee->created_at), 0, 16) }}</span>
                </p>
                <p class="text-[10px] text-gray-300">
                    {{ now()->year }} &copy; {{ $profile->nama_lpk ?? 'Sistem Kepegawaian' }}
                </p>
            </div>
        </div>

    </div>

</body>
</html>
