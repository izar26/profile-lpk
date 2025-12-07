<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $program->judul }} - {{ $profile->nama_lpk }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Lato', 'sans-serif'], serif: ['Playfair Display', 'serif'] },
                    colors: { gold: { 50: '#FCF9EE', 500: '#D4AF37', 600: '#AA8C2C', 700: '#806921' } }
                }
            }
        }
    </script>
    <style> html { scroll-behavior: smooth; } </style>
</head>
<body class="font-sans text-gray-700 antialiased bg-gray-50">

    <nav class="bg-white shadow-md py-3 sticky top-0 z-50">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                @if($profile && $profile->logo)
                    <img src="{{ asset('storage/' . $profile->logo) }}" class="h-10 w-auto object-contain">
                @endif
                {{-- <span class="font-serif text-xl font-bold text-gray-900 hidden sm:block">
                    {{ $profile->nama_lpk ?? 'LPK PROFILE' }}
                </span> --}}
            </a>
            <a href="{{ route('home') }}" class="text-gray-600 hover:text-gold-600 font-medium flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </nav>

    <header class="relative h-[50vh] min-h-[400px] flex items-center justify-center bg-gray-900 text-white overflow-hidden">
        @if($program->gambar_fitur)
            <img src="{{ asset('storage/' . $program->gambar_fitur) }}" class="absolute inset-0 w-full h-full object-cover opacity-40">
        @else
            <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 opacity-50"></div>
        @endif
        
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent"></div>

        <div class="relative z-10 container mx-auto px-6 text-center">
            <span class="inline-block px-4 py-1 rounded-full bg-gold-500 text-white text-xs font-bold tracking-widest uppercase mb-4 shadow-lg">
                {{ $program->status }}
            </span>
            <h1 class="font-serif text-4xl md:text-6xl font-bold mb-4 drop-shadow-lg leading-tight">
                {{ $program->judul }}
            </h1>
            <div class="w-20 h-1 bg-gold-500 mx-auto rounded-full"></div>
        </div>
    </header>

    <div class="container mx-auto px-6 py-16">
        <div class="grid lg:grid-cols-3 gap-12">
            
            <div class="lg:col-span-2">
                <div class="bg-white p-8 md:p-10 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="font-serif text-2xl font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Tentang Program</h2>
                    
                    <div class="prose prose-lg max-w-none text-gray-600 leading-relaxed whitespace-pre-line">
                        {!! nl2br(e($program->deskripsi_lengkap ?? $program->deskripsi_singkat)) !!}
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="sticky top-28 space-y-6">
                    
                    <div class="bg-white p-8 rounded-2xl shadow-lg border-t-4 border-gold-500 text-center">
                        <h3 class="font-serif text-xl font-bold text-gray-900 mb-2">Tertarik Bergabung?</h3>
                        <p class="text-sm text-gray-500 mb-6">Segera amankan kuota Anda dan konsultasikan persyaratan pendaftaran sekarang.</p>
                        
                        <a href="https://wa.me/{{ $profile->nomor_wa ?? '' }}?text=Halo%20Admin,%20saya%20tertarik%20dengan%20program%20{{ urlencode($program->judul) }}." 
                           target="_blank"
                           class="block w-full py-4 bg-gold-600 text-white rounded-xl font-bold shadow-lg hover:bg-gold-700 hover:shadow-xl transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                           <i class="fa-brands fa-whatsapp text-xl"></i> Daftar via WhatsApp
                        </a>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                        <h4 class="font-bold text-gray-900 mb-4 text-sm uppercase tracking-wide">Ringkasan</h4>
                        <ul class="space-y-4 text-sm text-gray-600">
                            <li class="flex justify-between">
                                <span><i class="fa-solid fa-circle-check text-gold-500 mr-2"></i> Status</span>
                                <span class="font-bold text-gray-800">{{ $program->status }}</span>
                            </li>
                            <li class="flex justify-between">
                                <span><i class="fa-regular fa-calendar text-gold-500 mr-2"></i> Diposting</span>
                                <span>{{ $program->created_at->format('d M Y') }}</span>
                            </li>
                            <li class="flex justify-between">
                                <span><i class="fa-solid fa-user-shield text-gold-500 mr-2"></i> Penyelenggara</span>
                                <span>{{ $profile->nama_lpk }}</span>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <footer class="bg-black text-white pt-10 pb-10 border-t-4 border-gold-600 mt-12">
        <div class="container mx-auto px-6 text-center">
            <h3 class="font-serif text-xl font-bold text-gold-500 mb-2">{{ $profile->nama_lpk }}</h3>
            <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>