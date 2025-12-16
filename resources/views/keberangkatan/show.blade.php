<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $keberangkatan->judul }} - {{ $profile->nama_lpk }}</title>

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
<body class="font-sans text-gray-700 antialiased bg-gray-50 flex flex-col min-h-screen">

    <nav class="bg-white shadow-md py-3 sticky top-0 z-50">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                @if($profile && $profile->logo)
                    <img src="{{ asset('storage/' . $profile->logo) }}" class="h-10 w-auto object-contain">
                @else
                    <span class="font-serif text-xl font-bold text-gray-900">LPK PROFILE</span>
                @endif
            </a>
            <a href="{{ route('home') }}#keberangkatan" class="text-gray-600 hover:text-gold-600 font-medium flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> <span class="hidden sm:inline">Kembali</span>
            </a>
        </div>
    </nav>

    <header class="relative h-[40vh] min-h-[350px] flex items-end pb-12 bg-gray-900 text-white overflow-hidden">
        @if($keberangkatan->foto)
            <img src="{{ asset('storage/' . $keberangkatan->foto) }}" class="absolute inset-0 w-full h-full object-cover opacity-50">
        @else
            <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-gray-700 to-gray-900 opacity-50"></div>
        @endif
        
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>

        <div class="relative z-10 container mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded bg-gold-500 text-white text-xs font-bold tracking-widest uppercase mb-3 shadow-lg">
                        <i class="fa-solid fa-plane-departure"></i> Info Keberangkatan
                    </span>
                    <h1 class="font-serif text-3xl md:text-5xl font-bold mb-2 drop-shadow-lg leading-tight">
                        {{ $keberangkatan->judul }}
                    </h1>
                    <div class="flex items-center text-gray-300 text-sm gap-4">
                        <span><i class="fa-solid fa-calendar-day text-gold-500 mr-1"></i> {{ $keberangkatan->tanggal_berangkat->format('d F Y') }}</span>
                        <span><i class="fa-solid fa-location-dot text-gold-500 mr-1"></i> {{ $keberangkatan->tujuan }}</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-6 py-12 flex-grow">
        <div class="grid lg:grid-cols-3 gap-10">
            
            <div class="lg:col-span-2">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="font-serif text-2xl font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">
                        Detail Kegiatan
                    </h2>
                    
                    @if(!$keberangkatan->deskripsi)
                        <div class="text-gray-400 italic">Belum ada deskripsi detail untuk keberangkatan ini.</div>
                    @else
                        <div class="prose prose-lg max-w-none text-gray-600 leading-relaxed whitespace-pre-line">
                            {!! nl2br(e($keberangkatan->deskripsi)) !!}
                        </div>
                    @endif
                </div>

                @if($keberangkatan->foto)
                <div class="mt-8">
                    <h3 class="font-serif text-xl font-bold text-gray-900 mb-4">Dokumentasi</h3>
                    <div class="rounded-2xl overflow-hidden shadow-lg border border-gray-200">
                        <img src="{{ asset('storage/' . $keberangkatan->foto) }}" class="w-full h-auto object-cover hover:scale-105 transition duration-500">
                    </div>
                </div>
                @endif
            </div>

            <div class="lg:col-span-1 space-y-8">
                
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 relative">
                    <div class="bg-gray-900 p-4 flex justify-between items-center text-white">
                        <span class="font-bold tracking-wide uppercase text-sm">Boarding Info</span>
                        <i class="fa-solid fa-plane text-gold-500 text-xl"></i>
                    </div>
                    <div class="p-6 space-y-6 relative z-10">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-gold-50 flex items-center justify-center text-gold-600 flex-shrink-0">
                                <i class="fa-solid fa-map-location-dot text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Tujuan Negara/Kota</p>
                                <p class="text-lg font-bold text-gray-900">{{ $keberangkatan->tujuan }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-gold-50 flex items-center justify-center text-gold-600 flex-shrink-0">
                                <i class="fa-solid fa-calendar-check text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Tanggal Berangkat</p>
                                <p class="text-lg font-bold text-gray-900">{{ $keberangkatan->tanggal_berangkat->format('d M Y') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-gold-50 flex items-center justify-center text-gold-600 flex-shrink-0">
                                <i class="fa-solid fa-users text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Total Peserta</p>
                                <p class="text-lg font-bold text-gray-900">{{ $keberangkatan->jumlah_peserta }} Orang</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t-2 border-dashed border-gray-200 mx-4 mb-4"></div>
                    
                    <div class="px-6 pb-6">
                        <a href="https://wa.me/{{ $profile->nomor_wa ?? '' }}?text=Halo,%20saya%20ingin%20bertanya%20tentang%20keberangkatan:%20{{ urlencode($keberangkatan->judul) }}" 
                           target="_blank"
                           class="block w-full py-3 bg-gold-500 hover:bg-gold-600 text-white text-center font-bold rounded-xl transition shadow-md">
                            <i class="fa-brands fa-whatsapp mr-2"></i> Tanya Admin
                        </a>
                    </div>
                </div>

                @if($lainnya->count() > 0)
                <div>
                    <h3 class="font-serif text-lg font-bold text-gray-900 mb-4 pl-2 border-l-4 border-gold-500">
                        Keberangkatan Lainnya
                    </h3>
                    <div class="space-y-4">
                        @foreach($lainnya as $item)
                        <a href="{{ route('public.keberangkatan.show', $item->id) }}" class="flex bg-white p-3 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition group">
                            <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0 bg-gray-200">
                                @if($item->foto)
                                    <img src="{{ asset('storage/'.$item->foto) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fa-solid fa-image"></i></div>
                                @endif
                            </div>
                            <div class="ml-4 flex flex-col justify-center">
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-gold-600 transition line-clamp-2 leading-snug">
                                    {{ $item->judul }}
                                </h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fa-regular fa-calendar mr-1"></i> {{ $item->tanggal_berangkat->format('d M Y') }}
                                </p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    <footer class="bg-black text-white pt-8 pb-8 border-t-4 border-gold-600 mt-auto">
        <div class="container mx-auto px-6 text-center">
            <h3 class="font-serif text-xl font-bold text-gold-500 mb-2">{{ $profile->nama_lpk }}</h3>
            <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>