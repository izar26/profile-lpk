@php
    // Ambil data profile
    $profile = $profile ?? \App\Models\LpkProfile::first();
    
    // Logika Background: Gunakan gambar dari DB jika ada, jika tidak gunakan default Unsplash (Wanita memegang buku/folder)
    $bgImage = $profile && $profile->gambar_auth 
                ? asset('storage/' . $profile->gambar_auth) 
                : 'https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?q=80&w=2070&auto=format&fit=crop';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun - {{ $profile->nama_lpk ?? config('app.name', 'LPK Profile') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Lato', 'sans-serif'], serif: ['Playfair Display', 'serif'] },
                    colors: { gold: { 50: '#FCF9EE', 100: '#F7F1D6', 200: '#EEDFA8', 300: '#E4CC7A', 400: '#DBBA50', 500: '#D4AF37', 600: '#AA8C2C', 700: '#806921', 800: '#554616', 900: '#2B230B' } }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-gray-50">

    <div class="min-h-screen flex">
        
        <!-- Bagian Kiri: Gambar Background & Info -->
        <div class="hidden lg:flex w-1/2 relative bg-gray-900 items-center justify-center overflow-hidden">
            {{-- Background Image Dinamis --}}
            <img src="{{ $bgImage }}" 
                 alt="Background" 
                 class="absolute inset-0 w-full h-full object-cover opacity-50 transition-transform duration-[20s] hover:scale-110 ease-linear">
            
            <div class="absolute inset-0 bg-gradient-to-br from-gold-900/90 to-black/70"></div>

            <div class="relative z-10 text-center p-12 text-white max-w-lg animate-fade-in-up">
                <div class="mb-6 flex justify-center">
                    @if($profile && $profile->logo)
                        <img src="{{ asset('storage/' . $profile->logo) }}" alt="Logo LPK" 
                             class="h-24 w-auto object-contain drop-shadow-2xl hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-20 h-20 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center border border-gold-400/50 shadow-xl">
                            <i class="fa-solid fa-user-plus text-4xl text-gold-400"></i>
                        </div>
                    @endif
                </div>
                
                <h1 class="font-serif text-5xl font-bold mb-4 text-white tracking-wide drop-shadow-lg">
                    Bergabunglah <br> <span class="text-gold-400">Bersama Kami</span>
                </h1>
                
                <p class="text-lg text-gray-200 leading-relaxed font-light drop-shadow-md">
                    Langkah awal menuju karir internasional Anda dimulai di sini. Buat akun untuk memulai proses pendaftaran di {{ $profile->nama_lpk ?? 'LPK Kami' }}.
                </p>
            </div>
            
            <div class="absolute bottom-0 left-0 w-full h-2 bg-gradient-to-r from-gold-600 via-gold-400 to-gold-600"></div>
        </div>

        <!-- Bagian Kanan: Form Register -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white relative overflow-y-auto">
            <div class="w-full max-w-md space-y-6 py-10">
                
                <div class="text-center lg:text-left mb-8">
                    <h2 class="font-serif text-4xl font-bold text-gray-900 mb-2">Buat Akun Baru</h2>
                    <p class="text-gray-500">Isi data diri singkat untuk akses dashboard siswa.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none group-focus-within:text-gold-600 transition-colors">
                                <i class="fa-solid fa-user text-gray-400"></i>
                            </div>
                            <input type="text" name="name" value="{{ old('name') }}" required autofocus 
                                   class="pl-10 block w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-500 focus:ring-gold-500 py-3 transition-all" 
                                   placeholder="Nama Sesuai KTP">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none group-focus-within:text-gold-600 transition-colors">
                                <i class="fa-solid fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required 
                                   class="pl-10 block w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-500 focus:ring-gold-500 py-3 transition-all" 
                                   placeholder="nama@email.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- WhatsApp -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none group-focus-within:text-gold-600 transition-colors">
                                <i class="fa-brands fa-whatsapp text-gray-400 text-lg"></i>
                            </div>
                            <input type="text" name="telepon" value="{{ old('telepon') }}" required 
                                   class="pl-10 block w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-500 focus:ring-gold-500 py-3 transition-all" 
                                   placeholder="08123456789">
                        </div>
                        <x-input-error :messages="$errors->get('telepon')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none group-focus-within:text-gold-600 transition-colors">
                                <i class="fa-solid fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" name="password" required 
                                   class="pl-10 block w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-500 focus:ring-gold-500 py-3 transition-all" 
                                   placeholder="Minimal 8 karakter">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none group-focus-within:text-gold-600 transition-colors">
                                <i class="fa-solid fa-check-double text-gray-400"></i>
                            </div>
                            <input type="password" name="password_confirmation" required 
                                   class="pl-10 block w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-500 focus:ring-gold-500 py-3 transition-all" 
                                   placeholder="Ulangi password">
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Tombol Gradient -->
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-gold-600 to-gold-500 hover:from-gold-700 hover:to-gold-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-500 transition-all transform hover:-translate-y-0.5 mt-4">
                        DAFTAR SEKARANG
                    </button>
                </form>

                <div class="mt-8 text-center text-sm text-gray-600">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="font-bold text-gold-600 hover:text-gold-800 hover:underline transition-colors">
                        Masuk di sini
                    </a>
                </div>

                <div class="mt-8 text-center text-xs text-gray-400 border-t border-gray-100 pt-6">
                    &copy; {{ date('Y') }} {{ $profile->nama_lpk ?? config('app.name') }}. All rights reserved.
                </div>
            </div>
            
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-gold-600 via-gold-400 to-gold-600 lg:hidden"></div>
        </div>
    </div>
</body>
</html>