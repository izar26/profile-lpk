<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Terverifikasi - {{ $employee->nama }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Menggunakan Gradient Hijau (Verified) seperti Siswa */
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
<body class="bg-slate-50 min-h-screen py-8 px-4">

    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden animate-pop">
        
        {{-- Status Bar: VERIFIED --}}
        <div class="verified-badge text-white p-4 text-center shadow-md relative z-10">
            <div class="flex items-center justify-center gap-2 text-lg font-bold uppercase tracking-widest">
                <i class="fa-solid fa-circle-check text-yellow-300"></i> Pegawai Resmi
            </div>
            <p class="text-xs text-green-100 mt-1">Dicetak dari Database Resmi {{ $profile->nama_lpk ?? 'LPK' }}</p>
        </div>

        <div class="relative">
            {{-- Background Header (Gelap Professional) --}}
            <div class="h-32 bg-slate-800"></div>
            
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

        <div class="pt-20 pb-8 px-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900 capitalize">{{ $employee->nama }}</h1>
            <p class="text-gray-500 font-medium">{{ $employee->jabatan }}</p>
            
            {{-- Status Badge --}}
            <div class="mt-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold 
                    {{ $employee->status_kepegawaian == 'Tetap' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                    STATUS: {{ strtoupper($employee->status_kepegawaian) }}
                </span>
            </div>
        </div>

        {{-- Detail Informasi (Grid Layout) --}}
        <div class="bg-gray-50 px-6 py-6 border-t border-gray-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                {{-- NIP --}}
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1">NIP (Nomor Induk)</p>
                    <p class="text-gray-800 font-mono font-bold">{{ $employee->nip ?? '-' }}</p>
                </div>

                {{-- TTL --}}
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1">Tempat, Tgl Lahir</p>
                    <p class="text-gray-800">
                        {{ $employee->tempat_lahir ?? '-' }}, 
                        {{ $employee->tanggal_lahir ? \Carbon\Carbon::parse($employee->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                    </p>
                </div>

                {{-- Gender & Agama --}}
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1">Jenis Kelamin</p>
                    <p class="text-gray-800">
                        {{ $employee->jenis_kelamin == 'L' ? 'Laki-laki' : ($employee->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                    </p>
                </div>

                {{-- Pendidikan --}}
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1">Pendidikan Terakhir</p>
                    <p class="text-gray-800">{{ $employee->pendidikan_terakhir ?? '-' }}</p>
                </div>

                {{-- Email --}}
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm sm:col-span-2">
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1">Email Kontak</p>
                    <p class="text-gray-800">{{ $employee->email }}</p>
                </div>
            </div>

            {{-- Alamat --}}
            <div class="mt-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1">Alamat Lengkap</p>
                <p class="text-gray-800">
                    {{ $employee->alamat ?? '-' }}<br>
                    @if($employee->kota || $employee->provinsi)
                        <span class="text-sm text-gray-500">{{ $employee->kota }} - {{ $employee->provinsi }} {{ $employee->kode_pos }}</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="p-6 bg-white flex flex-col gap-3">
            <a href="https://wa.me/{{ $profile->nomor_wa ?? '' }}" class="block w-full py-3 bg-green-500 hover:bg-green-600 text-white text-center rounded-xl font-bold shadow-md transition flex items-center justify-center gap-2">
                <i class="fa-brands fa-whatsapp text-xl"></i> Hubungi Kantor LPK
            </a>
            
            <div class="text-center mt-4">
                <p class="text-xs text-gray-400">
                    Data ini ditampilkan secara digital dan sah sebagai bukti kepegawaian di {{ $profile->nama_lpk ?? 'LPK' }}.
                </p>
                <p class="text-[10px] text-gray-300 mt-1">
                    Verifikasi ID: {{ md5($employee->id . $employee->created_at) }}
                </p>
            </div>
        </div>

    </div>

</body>
</html>