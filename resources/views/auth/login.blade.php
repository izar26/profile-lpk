<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ $profile->nama_lpk ?? config('app.name', 'LPK Profile') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Lato', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        gold: {
                            50: '#FCF9EE', 100: '#F7F1D6', 200: '#EEDFA8',
                            300: '#E4CC7A', 400: '#DBBA50', 500: '#D4AF37',
                            600: '#AA8C2C', 700: '#806921', 800: '#554616',
                            900: '#2B230B',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-gray-50">

    <div class="min-h-screen flex">
        
        <div class="hidden lg:flex w-1/2 relative bg-gray-900 items-center justify-center overflow-hidden">
            <img src="https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?q=80&w=2070&auto=format&fit=crop" 
                 alt="Background" 
                 class="absolute inset-0 w-full h-full object-cover opacity-40">
            
            <div class="absolute inset-0 bg-gradient-to-br from-gold-900/80 to-black/60"></div>

            <div class="relative z-10 text-center p-12 text-white max-w-lg">
                <div class="mb-6 flex justify-center">
                    @if($profile && $profile->logo)
                        <img src="{{ asset('storage/' . $profile->logo) }}" alt="Logo LPK" 
                             class="h-24 w-auto object-contain drop-shadow-2xl">
                    @else
                        <div class="w-20 h-20 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center border border-gold-400/50 shadow-xl">
                            <i class="fa-solid fa-building-columns text-4xl text-gold-400"></i>
                        </div>
                    @endif
                </div>

                <h1 class="font-serif text-4xl md:text-5xl font-bold mb-4 text-white tracking-wide leading-tight">
                    {{ $profile->nama_lpk ?? 'LPK PROFILE' }}
                </h1>

                <p class="text-lg text-gray-300 leading-relaxed font-light">
                    {{ $profile->tagline ?? 'Sistem Informasi Manajemen Pelatihan & Penempatan Tenaga Kerja ke Jepang.' }}
                </p>
            </div>

            <div class="absolute bottom-0 left-0 w-full h-2 bg-gradient-to-r from-gold-600 via-gold-400 to-gold-600"></div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white relative">
            
            <div class="w-full max-w-md space-y-8">
                
                <div class="text-center lg:text-left">
                    <h2 class="font-serif text-4xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
                    <p class="text-gray-500">Silakan masuk untuk mengakses akun Anda.</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                                   class="pl-10 block w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-500 focus:ring-gold-500 py-3 transition-all" 
                                   placeholder="nama@email.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password" 
                                   class="pl-10 block w-full border-gray-300 rounded-xl shadow-sm focus:border-gold-500 focus:ring-gold-500 py-3 transition-all" 
                                   placeholder="••••••••">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-gold-600 shadow-sm focus:ring-gold-500" name="remember">
                            <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gold-600 hover:bg-gold-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-500 transition-all transform hover:-translate-y-0.5">
                        MASUK SEKARANG
                    </button>
                </form>

                <div class="mt-10 text-center text-xs text-gray-400">
                    &copy; {{ date('Y') }} {{ $profile->nama_lpk ?? config('app.name') }}. All rights reserved.
                    <br>Sistem Informasi Manajemen LPK
                </div>
            </div>

            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-gold-600 via-gold-400 to-gold-600 lg:hidden"></div>
        </div>
    </div>
</body>
</html>