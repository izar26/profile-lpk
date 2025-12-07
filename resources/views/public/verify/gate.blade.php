<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Data Siswa - {{ $profile->nama_lpk ?? 'LPK' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f3f4f6; }
        .bg-pattern {
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>
<body class="bg-pattern min-h-screen flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
        
        {{-- Header dengan Warna Branding --}}
        <div class="bg-slate-900 p-6 text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-slate-800 to-slate-900 z-0"></div>
            
            <div class="relative z-10">
                @if($profile && $profile->logo)
                    <img src="{{ asset('storage/' . $profile->logo) }}" class="h-20 w-20 mx-auto rounded-full bg-white p-1 shadow-lg object-contain mb-4">
                @else
                    <div class="h-20 w-20 mx-auto rounded-full bg-white flex items-center justify-center mb-4">
                        <i class="fa-solid fa-graduation-cap text-3xl text-slate-800"></i>
                    </div>
                @endif
                
                <h1 class="text-white font-bold text-lg uppercase tracking-wider">Verifikasi Data Siswa</h1>
                <p class="text-slate-300 text-xs mt-1">{{ $profile->nama_lpk ?? 'LPK SYSTEM' }}</p>
            </div>
        </div>

        <div class="p-8">
            <div class="text-center mb-6">
                <p class="text-gray-500 text-sm">Anda sedang memverifikasi data:</p>
                <h2 class="text-xl font-bold text-gray-800 mt-1">{{ $student->nama_lengkap }}</h2>
                <div class="inline-block bg-blue-50 text-blue-700 text-xs px-3 py-1 rounded-full mt-2 font-medium">
                    ID: {{ $student->nomor_ktp }}
                </div>
            </div>

            @if(session('error'))
                <div class="bg-red-50 text-red-600 text-sm p-3 rounded-lg mb-4 text-center border border-red-100 animate-pulse">
                    <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('student.verify.check') }}" method="POST">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                
                <div class="mb-5">
                    <label class="block text-gray-700 text-xs font-bold uppercase mb-2 tracking-wide text-center">
                        Masukkan Kode Akses
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input type="tel" name="verifikasi_key" 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent text-center font-mono text-lg tracking-widest" 
                               placeholder="DDMMYYYY"
                               required>
                    </div>
                    <p class="text-xs text-gray-400 mt-2 text-center">
                        *Gunakan Tanggal Lahir Siswa (HariBulanTahun)<br>Contoh: 17 Agustus 1945 -> <b>17081945</b>
                    </p>
                </div>

                <button type="submit" class="w-full bg-slate-900 text-white font-bold py-3 px-4 rounded-xl hover:bg-slate-800 transition transform hover:-translate-y-0.5 shadow-lg">
                    <i class="fa-solid fa-shield-halved mr-2"></i> Buka Data Lengkap
                </button>
            </form>
        </div>

        <div class="bg-gray-50 px-6 py-4 text-center border-t border-gray-100">
            <p class="text-xs text-gray-400">
                &copy; {{ date('Y') }} {{ $profile->nama_lpk ?? 'LPK' }}. Secure Verification System.
            </p>
        </div>
    </div>

</body>
</html>