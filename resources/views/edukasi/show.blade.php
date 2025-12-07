<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $edukasi->judul }} - {{ $profile->nama_lpk }}</title>

    <meta name="description" content="{{ Str::limit(strip_tags($edukasi->konten), 160) }}">
    <meta name="author" content="{{ $edukasi->author->name ?? 'Admin' }}">

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
    <style> 
        html { scroll-behavior: smooth; } 
        /* Styling khusus untuk konten artikel agar rapi */
        .article-content p { margin-bottom: 1.5rem; line-height: 1.8; }
        .article-content h2 { font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: bold; margin-top: 2rem; margin-bottom: 1rem; color: #111827; }
        .article-content h3 { font-family: 'Playfair Display', serif; font-size: 1.25rem; font-weight: bold; margin-top: 1.5rem; margin-bottom: 0.75rem; color: #374151; }
        .article-content ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.5rem; }
        .article-content ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1.5rem; }
        .article-content img { border-radius: 0.75rem; margin: 1.5rem 0; width: 100%; }
        .article-content blockquote { border-left: 4px solid #D4AF37; padding-left: 1rem; font-style: italic; color: #4B5563; margin: 1.5rem 0; }
    </style>
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
            <a href="{{ route('home') }}#edukasi" class="text-gray-600 hover:text-gold-600 font-medium flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> <span class="hidden sm:inline">Kembali ke Artikel</span>
            </a>
        </div>
    </nav>

    <header class="relative h-[40vh] min-h-[300px] flex items-center justify-center bg-gray-900 text-white overflow-hidden">
        @if($edukasi->gambar_fitur)
            <img src="{{ asset('storage/' . $edukasi->gambar_fitur) }}" class="absolute inset-0 w-full h-full object-cover opacity-30 blur-sm scale-105">
        @else
            <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 opacity-50"></div>
        @endif
        
        <div class="absolute inset-0 bg-gradient-to-t from-gray-50 via-transparent to-transparent"></div>

        <div class="relative z-10 container mx-auto px-6 text-center pt-10">
            <span class="inline-block px-4 py-1 rounded-full bg-gold-500/20 border border-gold-500 text-gold-400 text-xs font-bold tracking-widest uppercase mb-4 backdrop-blur-md">
                Artikel & Edukasi
            </span>
            <h1 class="font-serif text-3xl md:text-5xl font-bold mb-6 drop-shadow-lg leading-tight text-white max-w-4xl mx-auto">
                {{ $edukasi->judul }}
            </h1>
            
            <div class="flex items-center justify-center gap-6 text-sm text-gray-300">
                <span class="flex items-center gap-2"><i class="fa-regular fa-calendar text-gold-500"></i> {{ $edukasi->created_at->format('d M Y') }}</span>
                <span class="w-1 h-1 bg-gray-500 rounded-full"></span>
                <span class="flex items-center gap-2"><i class="fa-regular fa-user text-gold-500"></i> {{ $edukasi->author->name ?? 'Admin' }}</span>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-6 py-12 -mt-20 relative z-20">
        <div class="grid lg:grid-cols-3 gap-12">
            
            <div class="lg:col-span-2">
                <div class="bg-white p-8 md:p-12 rounded-2xl shadow-xl border border-gray-100">
                    @if($edukasi->gambar_fitur)
                    <div class="mb-10 rounded-xl overflow-hidden shadow-lg">
                        <img src="{{ asset('storage/' . $edukasi->gambar_fitur) }}" class="w-full h-auto object-cover">
                    </div>
                    @endif

                    <article class="article-content text-gray-700 text-lg leading-relaxed">
                        {!! $edukasi->konten !!}
                    </article>

                    <div class="mt-12 pt-8 border-t border-gray-100">
                        <p class="font-bold text-gray-900 mb-4 font-serif">Bagikan artikel ini:</p>
                        <div class="flex gap-3">
                            <a href="https://wa.me/?text={{ urlencode($edukasi->judul . ' - ' . url()->current()) }}" target="_blank" class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center hover:bg-green-600 transition shadow-md">
                                <i class="fa-brands fa-whatsapp"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition shadow-md">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($edukasi->judul) }}&url={{ urlencode(url()->current()) }}" target="_blank" class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center hover:bg-gray-800 transition shadow-md">
                                <i class="fa-brands fa-x-twitter"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="sticky top-28 space-y-8">
                    
                    <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-gold-500">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-400">
                                <i class="fa-solid fa-user text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $edukasi->author->name ?? 'Admin LPK' }}</h4>
                                <p class="text-xs text-gold-600 font-bold uppercase">Penulis</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Ingin berkonsultasi lebih lanjut mengenai materi ini atau program pelatihan kami?</p>
                        <a href="https://wa.me/{{ $profile->nomor_wa ?? '' }}?text=Halo%20Admin,%20saya%20membaca%20artikel%20{{ urlencode($edukasi->judul) }}%20dan%20ingin%20bertanya." 
                           target="_blank"
                           class="block w-full py-3 bg-gray-900 text-white text-center rounded-xl font-bold text-sm hover:bg-gold-600 transition">
                           <i class="fa-brands fa-whatsapp mr-2"></i> Chat Admin
                        </a>
                    </div>

                    @if(isset($artikelLain) && $artikelLain->count() > 0)
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200">
                        <h3 class="font-serif text-lg font-bold text-gray-900 mb-6 pb-2 border-b border-gray-200">Artikel Lainnya</h3>
                        <div class="space-y-6">
                            @foreach($artikelLain as $item)
                            <a href="{{ route('public.edukasi.show', $item->slug) }}" class="group flex gap-4 items-start">
                                <div class="w-20 h-20 rounded-lg overflow-hidden shrink-0">
                                    @if($item->gambar_fitur)
                                        <img src="{{ asset('storage/' . $item->gambar_fitur) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center"><i class="fa-regular fa-image text-gray-400"></i></div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm leading-tight group-hover:text-gold-600 transition mb-1 line-clamp-2">{{ $item->judul }}</h4>
                                    <span class="text-xs text-gray-500">{{ $item->created_at->format('d M Y') }}</span>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    <footer class="bg-black text-white pt-10 pb-10 border-t-4 border-gold-600 mt-20">
        <div class="container mx-auto px-6 text-center">
            <h3 class="font-serif text-xl font-bold text-gold-500 mb-2">{{ $profile->nama_lpk }}</h3>
            <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>