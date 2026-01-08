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
        <div class="bg-slate-800 p-8 text-center relative overflow-hidden">
            {{-- Hiasan Background --}}
            <div class="absolute top-0 left-0 w-full h-full opacity-10">
                <i class="fa-solid fa-fingerprint text-9xl absolute -top-4 -right-4 text-white"></i>
            </div>

            @if($profile && $profile->logo)
                <img src="{{ asset('storage/' . $profile->logo) }}" class="h-20 w-20 mx-auto rounded-full bg-white p-1 mb-3 object-contain shadow-lg relative z-10">
            @else
                <div class="h-20 w-20 mx-auto rounded-full bg-white flex items-center justify-center mb-3 shadow-lg relative z-10">
                    <i class="fa-solid fa-briefcase text-3xl text-slate-800"></i>
                </div>
            @endif
            <h1 class="text-white font-bold text-lg uppercase relative z-10 tracking-wide">Portal Verifikasi</h1>
            <p class="text-slate-300 text-xs relative z-10">{{ $profile->nama_lpk ?? 'Sistem Informasi Kepegawaian' }}</p>
        </div>

        <div class="p-8">
            <div class="text-center mb-8">
                <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold mb-2">Anda sedang memverifikasi:</p>
                <h2 class="text-2xl font-bold text-gray-800">{{ $employee->nama }}</h2>
                <div class="mt-2 inline-block bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-mono">
                    NIP: {{ $employee->nip ?? 'N/A' }}
                </div>
            </div>

            @if(session('error'))
                <div class="bg-red-50 text-red-600 text-sm p-4 rounded-xl mb-6 text-center border border-red-100 flex items-center justify-center gap-2 animate-pulse">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                </div>
            @endif

            {{-- Pastikan nama route ini sudah ada di web.php Anda --}}
            <form action="{{ route('pegawai.verification.check') }}" method="POST">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">

                <div class="mb-6">
                    <label class="block text-gray-700 text-xs font-bold uppercase mb-2 text-center">
                        Masukkan Kode Keamanan
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-key text-gray-400"></i>
                        </div>
                        <input type="tel" name="verifikasi_key"
                               class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-slate-500 focus:ring-0 text-center font-mono text-xl tracking-[0.2em] text-slate-800 placeholder-gray-300 transition-colors"
                               placeholder="DDMMYYYY" required autocomplete="off">
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2 text-center">
                        <i class="fa-solid fa-circle-info mr-1"></i> Hint: Gunakan Tanggal Lahir Pegawai (HariBulanTahun)
                    </p>
                </div>

                <button type="submit" class="w-full bg-slate-800 text-white font-bold py-3.5 px-4 rounded-xl hover:bg-slate-700 transition transform hover:-translate-y-0.5 shadow-lg flex items-center justify-center gap-2">
                    <i class="fa-solid fa-shield-halved"></i> Verifikasi Data
                </button>
            </form>
        </div>

        <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
            <a href="{{ url('/') }}" class="text-xs text-gray-500 hover:text-slate-800 transition">Kembali ke Beranda</a>
        </div>
    </div>

</body>
</html>
