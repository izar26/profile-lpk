<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pegawai - {{ $profile->nama_lpk ?? 'LPK' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
        
        {{-- Header --}}
        <div class="bg-slate-800 p-6 text-center">
            @if($profile && $profile->logo)
                <img src="{{ asset('storage/' . $profile->logo) }}" class="h-20 w-20 mx-auto rounded-full bg-white p-1 mb-3 object-contain">
            @else
                <div class="h-20 w-20 mx-auto rounded-full bg-white flex items-center justify-center mb-3">
                    <i class="fa-solid fa-briefcase text-3xl text-slate-800"></i>
                </div>
            @endif
            <h1 class="text-white font-bold text-lg uppercase">Verifikasi Pegawai</h1>
            <p class="text-slate-300 text-xs">{{ $profile->nama_lpk ?? 'Sistem Kepegawaian' }}</p>
        </div>

        <div class="p-8">
            <div class="text-center mb-6">
                <p class="text-gray-500 text-sm">Validasi Data Pegawai:</p>
                <h2 class="text-xl font-bold text-gray-800 mt-1">{{ $employee->nama }}</h2>
                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">NIP: {{ $employee->nip ?? 'N/A' }}</span>
            </div>

            @if(session('error'))
                <div class="bg-red-50 text-red-600 text-sm p-3 rounded-lg mb-4 text-center border border-red-100">
                    <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('pegawai.verification.check') }}" method="POST">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                
                <div class="mb-5">
                    <label class="block text-gray-700 text-xs font-bold uppercase mb-2 text-center">
                        Kode Akses (Tanggal Lahir)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-calendar text-gray-400"></i>
                        </div>
                        <input type="tel" name="verifikasi_key" 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-500 text-center font-mono text-lg tracking-widest" 
                               placeholder="DDMMYYYY" required>
                    </div>
                    <p class="text-xs text-gray-400 mt-2 text-center">Format: HariBulanTahun (Cth: 25121990)</p>
                </div>

                <button type="submit" class="w-full bg-slate-800 text-white font-bold py-3 px-4 rounded-xl hover:bg-slate-700 transition shadow-lg">
                    <i class="fa-solid fa-shield-halved mr-2"></i> Verifikasi Data
                </button>
            </form>
        </div>
    </div>
</body>
</html>