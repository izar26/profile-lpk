<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profile->nama_lpk ?? config('app.name') }} - Lembaga Pelatihan Kerja</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:wght@400;600;700;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

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
    <style>
        /* [PERBAIKAN 1] CSS Native Smooth Scroll */
        html {
            scroll-behavior: smooth; 
            scroll-padding-top: 80px; /* Jarak agar tidak ketutup navbar */
        }
        
        /* Pattern Background */
        .hero-pattern {
            background-color: #ffffff;
            background-image: radial-gradient(#D4AF37 0.5px, transparent 0.5px), radial-gradient(#D4AF37 0.5px, #ffffff 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            opacity: 0.1;
        }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <style>
    .swiper-pagination-bullet-active {
        background-color: #d97706 !important; /* Sesuaikan dengan kode warna gold-600 kamu */
    }
</style>
</head>
<body class="font-sans text-gray-700 antialiased bg-white" x-data="landingPage()">

    <nav x-data="{ scrolled: false, mobileMenu: false }" 
     @scroll.window="scrolled = (window.pageYOffset > 20)"
     :class="scrolled ? 'bg-white shadow-md py-2' : 'bg-white/90 backdrop-blur-sm py-4'"
     class="fixed w-full z-50 transition-all duration-300 border-b border-gold-100">
    <div class="container mx-auto px-6 flex justify-between items-center">
        
        <a href="#" class="flex items-center gap-3 group">
            @if($profile && $profile->logo)
                <img src="{{ asset('storage/' . $profile->logo) }}" class="h-10 w-auto object-contain group-hover:scale-105 transition">
            @endif
        </a>

        <div class="hidden md:flex items-center gap-8 font-medium text-sm">
            <a href="#beranda" class="hover:text-gold-600 transition">Beranda</a>
            <a href="#tentang" class="hover:text-gold-600 transition">Tentang Kami</a>
            <a href="#program" class="hover:text-gold-600 transition">Program</a>
            <a href="#edukasi" class="hover:text-gold-600 transition">Artikel</a>
            <a href="#galeri" class="hover:text-gold-600 transition">Galeri</a>
            <a href="#alur" class="hover:text-gold-600 transition">Cara Daftar</a>
            
            {{-- [PERBAIKAN] Menu Pengajar hanya muncul jika ada data --}}
            @if(isset($employees) && $employees->count() > 0)
                <a href="#instruktur" class="hover:text-gold-600 transition">Pengajar</a>
            @endif

            <a href="#testimoni" class="hover:text-gold-600 transition">Testimoni</a>
            
            {{-- [PERBAIKAN] Menu Keberangkatan hanya muncul jika ada data --}}
            @if(isset($keberangkatans) && $keberangkatans->count() > 0)
                <a href="#keberangkatan" class="hover:text-gold-600 transition">Keberangkatan</a>
            @endif
            
            @auth
                <a href="{{ url('/dashboard') }}" class="px-5 py-2 bg-gold-500 text-white rounded-full shadow-lg hover:bg-gold-600 transition transform hover:-translate-y-0.5">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="px-5 py-2 border border-gold-500 text-gold-600 rounded-full hover:bg-gold-500 hover:text-white transition">Masuk</a>
            @endauth
        </div>

        <button @click="mobileMenu = !mobileMenu" class="md:hidden text-gray-700 text-2xl focus:outline-none"><i class="fa-solid fa-bars"></i></button>
    </div>

    <div x-show="mobileMenu" class="md:hidden bg-white border-t border-gray-100 absolute w-full shadow-lg">
        <div class="flex flex-col p-4 gap-4 text-center font-medium">
            <a href="#beranda" @click="mobileMenu = false">Beranda</a>
            <a href="#tentang" @click="mobileMenu = false">Tentang Kami</a>
            <a href="#program" @click="mobileMenu = false">Program</a>
            <a href="#galeri" @click="mobileMenu = false">Galeri</a>
            
            {{-- [PERBAIKAN MOBILE] Tambahkan menu Pengajar di Mobile juga --}}
            @if(isset($employees) && $employees->count() > 0)
                <a href="#instruktur" @click="mobileMenu = false">Pengajar</a>
            @endif

            {{-- [PERBAIKAN MOBILE] Tambahkan menu Keberangkatan di Mobile --}}
            @if(isset($keberangkatans) && $keberangkatans->count() > 0)
                <a href="#keberangkatan" @click="mobileMenu = false">Keberangkatan</a>
            @endif

            @auth
                <a href="{{ url('/dashboard') }}" class="px-5 py-2 bg-gold-500 text-white rounded-full font-bold">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="px-5 py-2 bg-gold-500 text-white rounded-full font-bold">Masuk / Daftar</a>
            @endauth
        </div>
    </div>
</nav>

  <section id="beranda" class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute top-0 right-0 w-2/3 h-full bg-gold-50 skew-x-12 transform translate-x-32 z-0"></div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                
                <div data-aos="fade-right" data-aos-duration="1000">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gold-200 shadow-sm mb-6">
                        <span class="flex h-3 w-3 relative"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gold-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-gold-500"></span></span>
                        <span class="text-xs font-bold text-gold-700 tracking-widest uppercase">Pendaftaran Dibuka</span>
                    </div>
                    
                    <h1 class="font-serif text-5xl lg:text-7xl font-black text-gray-900 mb-6 leading-tight">
                        Wujudkan Mimpi <br> 
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-500 via-yellow-500 to-gold-700">Bekerja di Jepang</span>
                    </h1>
                    
                    <p class="text-lg text-gray-600 mb-10 max-w-lg leading-relaxed border-l-4 border-gold-500 pl-6">
                        {{ $profile->tagline ?? 'Lembaga pelatihan resmi dengan kurikulum standar Jepang dan jaminan penempatan kerja.' }}
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#program" class="group px-8 py-4 bg-gray-900 text-white rounded-xl font-bold shadow-xl hover:bg-gray-800 transition transform hover:-translate-y-1 flex items-center justify-center">
                            Lihat Program <i class="fa-solid fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="https://wa.me/{{ $profile->nomor_wa ?? '' }}?text=Halo%20Admin,%20saya%20tertarik%20mendaftar." target="_blank" class="px-8 py-4 bg-white border-2 border-gray-200 text-gray-700 rounded-xl font-bold hover:border-gold-500 hover:text-gold-600 transition flex items-center justify-center gap-2">
                            <i class="fa-brands fa-whatsapp text-xl"></i> Daftar via WA
                        </a>
                    </div>
                </div>

                <div class="relative hidden lg:block" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="absolute top-0 right-0 w-full h-full bg-gold-200 rounded-full filter blur-3xl opacity-20"></div>
                    
                    <div class="relative z-10 bg-white p-4 rounded-3xl shadow-2xl rotate-2 hover:rotate-0 transition duration-500">
                         @if($profile->gambar_hero)
                            <img src="{{ asset('storage/' . $profile->gambar_hero) }}" alt="Hero Image" class="rounded-2xl w-full h-[500px] object-cover">
                         @else
                            <img src="https://images.unsplash.com/photo-1528164344705-47542687000d?q=80&w=1000&auto=format&fit=crop" alt="Japan" class="rounded-2xl w-full h-[500px] object-cover">
                         @endif
                         
                         <div class="absolute -bottom-10 -left-10 bg-white p-6 rounded-2xl shadow-xl border border-gray-100 max-w-xs flex items-center gap-4 animate-bounce" style="animation-duration: 3s;">
                            <div class="w-12 h-12 bg-gold-100 rounded-full flex items-center justify-center text-gold-600">
                                <i class="fa-solid fa-certificate text-2xl"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-lg">Izin Resmi</p>
                                <p class="text-xs text-gray-500">{{ $profile->nomor_sk ?? 'Terdaftar di Kemenaker' }}</p>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </section> 

    <section id="tentang" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="relative" data-aos="fade-right">
                    @if($profile->gambar_tentang)
                        <img src="{{ asset('storage/' . $profile->gambar_tentang) }}" class="rounded-2xl shadow-2xl w-full object-cover h-[400px] bg-gray-50">
                    @elseif($profile->logo)
                        <img src="{{ asset('storage/' . $profile->logo) }}" class="rounded-2xl shadow-2xl w-full object-cover h-[400px] bg-gray-50">
                    @else
                        <div class="w-full h-[400px] bg-gray-100 rounded-2xl flex items-center justify-center text-gray-300"><i class="fa-solid fa-building text-6xl"></i></div>
                    @endif
                </div>
                <div data-aos="fade-left">
                    <h4 class="text-gold-600 font-bold tracking-widest uppercase text-sm mb-2">Tentang LPK</h4>
                    <h2 class="font-serif text-4xl font-bold text-gray-900 mb-6">{{ $profile->nama_lpk }}</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">{{ $profile->deskripsi_singkat }}</p>
                    
                    @if($profile->nama_pimpinan)
                    <div class="mb-6 p-4 bg-gold-50 rounded-lg border-l-4 border-gold-500">
                        <p class="text-sm text-gray-500 uppercase tracking-wide font-bold">Pimpinan LPK</p>
                        <p class="text-lg font-serif font-bold text-gray-900">{{ $profile->nama_pimpinan }}</p>
                    </div>
                    @endif

                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-gold-100 flex items-center justify-center text-gold-600 shrink-0"><i class="fa-solid fa-eye"></i></div>
                            <div><h5 class="font-bold text-gray-900">Visi Kami</h5><p class="text-sm text-gray-600 mt-1">{{ Str::limit($profile->visi, 150) }}</p></div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-gold-100 flex items-center justify-center text-gold-600 shrink-0"><i class="fa-solid fa-bullseye"></i></div>
                            <div><h5 class="font-bold text-gray-900">Misi Kami</h5><p class="text-sm text-gray-600 mt-1 whitespace-pre-line">{{ Str::limit($profile->misi, 150) }}</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="program" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="font-serif text-4xl font-bold text-gray-900 mb-4">Program Pelatihan</h2>
                <p class="text-gray-600 max-w-xl mx-auto">Pilih program yang sesuai dengan minat dan bakat Anda.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @forelse($programs as $index => $program)
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-shadow duration-300 group" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                    <div class="relative h-48 overflow-hidden">
                        @if($program->gambar_fitur)
                            <img src="{{ asset('storage/' . $program->gambar_fitur) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400"><i class="fa-solid fa-image text-3xl"></i></div>
                        @endif
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-gold-600 shadow-sm">{{ $program->status }}</div>
                    </div>
                    <div class="p-6">
                        <a href="{{ route('public.program.show', $program->id) }}" class="hover:underline">
    <h3 class="font-serif text-xl font-bold text-gray-900 mb-2">{{ $program->judul }}</h3>
</a>

<a href="{{ route('public.program.show', $program->id) }}" class="inline-flex items-center text-gold-500 font-bold uppercase text-xs tracking-widest hover:text-gold-700 transition">
    Lihat Detail Program <i class="fa-solid fa-arrow-right-long ml-2"></i>
</a>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $program->deskripsi_singkat }}</p>
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center text-gray-500 py-10">Belum ada program yang dibuka.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="edukasi" class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-gold-600 font-bold tracking-[0.2em] text-sm uppercase">Wawasan</span>
            <h2 class="font-serif text-4xl font-bold text-gray-900 mt-2">Artikel & Berita Terbaru</h2>
            <p class="text-gray-600 max-w-xl mx-auto mt-4">Informasi terkini seputar dunia kerja dan pelatihan.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @forelse($artikels as $index => $artikel)
            <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 group border border-gray-100" 
                 data-aos="fade-up" 
                 data-aos-delay="{{ $index * 100 }}">
                
                <div class="relative h-56 overflow-hidden">
                    @if($artikel->gambar_fitur)
                        <img src="{{ asset('storage/' . $artikel->gambar_fitur) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                    @else
                        <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400">
                            <i class="fa-regular fa-newspaper text-4xl"></i>
                        </div>
                    @endif
                    
                    <div class="absolute top-4 left-4 bg-gold-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        {{ $artikel->created_at->format('d M Y') }}
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex items-center gap-2 mb-3 text-xs text-gray-500 font-bold uppercase tracking-wide">
                        <i class="fa-solid fa-user text-gold-500"></i> {{ $artikel->author->name ?? 'Admin' }}
                    </div>

                    <a href="{{ route('public.edukasi.show', $artikel->slug) }}" class="hover:underline decoration-gold-500 decoration-2">
                        <h3 class="font-serif text-xl font-bold text-gray-900 mb-3 leading-tight line-clamp-2 hover:text-gold-600 transition">
                            {{ $artikel->judul }}
                        </h3>
                    </a>

                    <p class="text-gray-600 text-sm mb-4 line-clamp-3 leading-relaxed">
                        {{ Str::limit(strip_tags($artikel->konten), 120) }}
                    </p>

                    <a href="{{ route('public.edukasi.show', $artikel->slug) }}" class="inline-flex items-center text-gold-600 font-bold text-sm hover:text-gold-800 transition group-hover:translate-x-2 duration-300">
                        Baca Selengkapnya <i class="fa-solid fa-arrow-right-long ml-2"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                <p class="text-gray-500">Belum ada artikel yang dipublikasikan.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

    <section id="galeri" class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-gold-600 font-bold tracking-[0.2em] text-sm uppercase">Dokumentasi</span>
            <h2 class="font-serif text-4xl font-bold text-gray-900 mt-2">Galeri Kegiatan</h2>
        </div>
        
        @if($albums->isEmpty())
            <div class="text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                <i class="fa-regular fa-images text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">Belum ada galeri kegiatan saat ini.</p>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-8 mb-12">
                @foreach($albums as $key => $album)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden cursor-pointer group hover:shadow-2xl transition-all duration-300 {{ $key >= 4 ? 'hidden album-item-hidden' : '' }}"
                         @click="openAlbum({{ $album->id }})"
                         data-aos="fade-up">
                        
                        @php $count = $album->galeris->count(); @endphp
                        
                        <div class="h-72 w-full relative overflow-hidden bg-gray-100 grid gap-1
                            @if($count == 1) grid-cols-1
                            @elseif($count == 2) grid-cols-2
                            @elseif($count == 3) grid-cols-2 grid-rows-2
                            @else grid-cols-2 grid-rows-2
                            @endif">

                            @if($count == 0)
                                <div class="col-span-full flex items-center justify-center text-gray-400"><i class="fa-solid fa-images text-4xl"></i></div>
                            @else
                                @foreach($album->galeris->take(4) as $index => $media)
                                    <div class="relative overflow-hidden w-full h-full {{ ($count == 3 && $index == 0) ? 'row-span-2' : '' }}">
                                        
                                        @if($media->tipe == 'foto')
                                            <img src="{{ asset('storage/' . $media->path_file) }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                                        @else
                                            <div class="relative w-full h-full group-hover:scale-110 transition duration-700">
                                                <img src="{{ $media->thumbnail_url }}" class="w-full h-full object-cover opacity-90">
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <div class="w-10 h-10 bg-red-600 text-white rounded-full flex items-center justify-center shadow-lg">
                                                        <i class="fa-solid fa-play ml-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($index == 3 && $album->galeris_count > 4)
                                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center text-white font-bold text-xl">
                                                +{{ $album->galeris_count - 4 }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-80 transition-opacity duration-300"></div>
                            <div class="absolute bottom-0 left-0 p-6">
                                <h3 class="text-white font-bold text-2xl mb-1 font-serif">{{ $album->nama_album }}</h3>
                                <p class="text-gold-300 text-sm font-bold uppercase tracking-wider flex items-center gap-2">
                                    <i class="fa-solid fa-camera"></i> {{ $album->galeris_count }} Media
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($albums->count() > 4)
                <div class="text-center" id="btnWrapper">
                    <button onclick="loadMoreGaleri()" class="px-8 py-3 bg-white border-2 border-gold-600 text-gold-600 font-bold rounded-full hover:bg-gold-600 hover:text-white transition duration-300 uppercase tracking-wider text-sm shadow-md hover:shadow-lg">
                        Lihat Album Lainnya <i class="fa-solid fa-arrow-down ml-2 animate-bounce"></i>
                    </button>
                </div>
            @endif
        @endif
    </div>
</section>

    <section id="alur" class="py-20 bg-gray-50 overflow-hidden">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-down">
                <h2 class="font-serif text-4xl font-bold text-gray-900 mb-4">Alur Pendaftaran</h2>
                <p class="text-gray-600">Langkah mudah bergabung bersama kami.</p>
            </div>
            <div class="relative">
                <div class="hidden md:block absolute left-1/2 transform -translate-x-1/2 top-0 h-full w-1 bg-gray-200 rounded-full"></div>
                <div class="space-y-12 md:space-y-24">
                    @foreach($alurDaftar as $index => $step)
                    <div class="relative flex flex-col md:flex-row items-center" data-aos="fade-up">
                        <div class="w-full md:w-1/2 pl-12 md:pl-0 md:pr-16 {{ $index % 2 == 0 ? 'md:text-right order-2 md:order-1' : 'order-2 md:order-3 md:pl-16' }}">
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-gold-300 transition duration-300 relative group">
                                <h3 class="font-serif text-xl font-bold text-gray-900 mb-2 group-hover:text-gold-600">{{ $step->judul }}</h3>
                                <p class="text-gray-600 text-sm">{{ $step->deskripsi }}</p>
                            </div>
                        </div>
                        <div class="absolute left-4 md:left-1/2 transform -translate-x-1/2 flex items-center justify-center order-1 md:order-2 z-10">
                            <div class="w-12 h-12 bg-gold-500 rounded-full border-4 border-white shadow-xl flex items-center justify-center text-white font-bold text-xl">
                                @if($step->gambar) <img src="{{ asset('storage/'.$step->gambar) }}" class="w-6 h-6 object-contain"> @else {{ $step->urutan }} @endif
                            </div>
                        </div>
                        <div class="w-full md:w-1/2 {{ $index % 2 == 0 ? 'order-3' : 'order-1' }}"></div>
                    </div>
                    @endforeach
                </div>
                <div class="text-center mt-12">
                    <a href="{{ route('register') }}" class="inline-flex items-center px-10 py-4 bg-gold-600 text-white rounded-full font-bold text-lg shadow-xl hover:bg-gold-700 transition transform hover:-translate-y-1">
                        Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>

    @if($employees->count() > 0)
    <section id="instruktur" class="py-20 bg-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="font-serif text-4xl font-bold text-gray-900 mb-16" data-aos="fade-up">Tim Pengajar</h2>
            <div class="grid md:grid-cols-4 gap-8">
                @foreach($employees as $emp)
                <div class="group" data-aos="fade-up">
                    <div class="relative w-40 h-40 mx-auto mb-6 rounded-full overflow-hidden border-4 border-gold-100 group-hover:border-gold-500 transition duration-300">
                        @if($emp->foto)
                            <img src="{{ asset('storage/' . $emp->foto) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gold-600 font-bold text-4xl">{{ substr($emp->nama, 0, 1) }}</div>
                        @endif
                    </div>
                    <h4 class="font-bold text-xl text-gray-900">{{ $emp->nama }}</h4>
                    <p class="text-gold-600 text-sm uppercase tracking-wide mt-1">{{ $emp->jabatan }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

<section id="testimoni" class="py-24 bg-gold-50">
    <div class="container mx-auto px-6">
        
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="font-serif text-4xl font-bold text-gray-900 mb-4">Kata Alumni</h2>
            <p class="text-gray-600">Apa kata mereka tentang pengalaman belajar di sini</p>
        </div>

        <div class="swiper myTestimonialSwiper mb-8 px-4" data-aos="fade-up" data-aos-delay="100">
            <div class="swiper-wrapper pb-12"> 
                @foreach($alumnis as $alumni)
                
                {{-- LOGIKA PHP UNTUK GAMBAR & NAMA --}}
                @php
                    // Ambil nama dari relasi student (karena kolom 'nama' di tabel alumni sudah dihapus)
                    $nama = $alumni->student->nama_lengkap ?? 'Alumni';

                    // Prioritas Foto: 
                    // 1. Foto Testimoni (Upload saat isi testimoni)
                    // 2. Foto Profil Siswa (Dari database siswa)
                    // 3. Default Avatar
                    $fotoUrl = $alumni->foto ? asset('storage/'.$alumni->foto) : 
                              ($alumni->student->foto ? asset('storage/'.$alumni->student->foto) : 
                              'https://ui-avatars.com/api/?name='.urlencode($nama).'&background=f3f4f6&color=9ca3af');
                @endphp

                <div class="swiper-slide h-auto"> 
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 relative h-full flex flex-col justify-between transition hover:shadow-md">
                        <div class="absolute top-6 right-8 text-6xl text-gold-100 font-serif leading-none">"</div>
                        
                        <p class="text-gray-600 italic mb-6 relative z-10">
                            "{{ Str::limit($alumni->testimoni, 150) }}"
                        </p>
                        
                        <div class="flex items-center gap-4 mt-auto">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200 shrink-0 border border-gray-100">
                                {{-- Tampilkan Foto hasil logika di atas --}}
                                <img src="{{ $fotoUrl }}" alt="{{ $nama }}" class="w-full h-full object-cover">
                            </div>
                            <div>
                                {{-- Tampilkan Nama dari relasi --}}
                                <h4 class="font-bold text-gray-900 text-sm">{{ $nama }}</h4>
                                <p class="text-xs text-gold-600 font-bold uppercase tracking-wide">{{ $alumni->kerja_dimana }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="swiper-pagination"></div>
        </div>

    </div>
</section>

@if($keberangkatans->count() > 0)
<section id="keberangkatan" class="py-24 bg-white border-t border-gray-100">
    <div class="container mx-auto px-6">
        
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="font-serif text-4xl font-bold text-gray-900 mb-4">Info Keberangkatan</h2>
            <p class="text-gray-600">Jadwal keberangkatan peserta pelatihan terbaru</p>
        </div>

        <div class="flex gap-6 overflow-x-auto pb-8 snap-x no-scrollbar justify-start md:justify-center px-4" data-aos="fade-up">
            @foreach($keberangkatans as $info)
            <div class="snap-center shrink-0 w-80 bg-white rounded-xl overflow-hidden shadow-lg border border-gray-200 hover:shadow-xl transition duration-300">
                <div class="h-48 overflow-hidden relative group">
                    @if($info->foto) 
                        <img src="{{ asset('storage/'.$info->foto) }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110"> 
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <i class="fa-regular fa-image text-4xl text-gray-400"></i>
                        </div>
                    @endif
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-gold-600 shadow-sm">
                        {{ $info->tanggal_berangkat->format('d M Y') }}
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-center text-xs text-gray-500 font-medium mb-2 gap-2">
                        <span><i class="fa-solid fa-users text-gold-500"></i> {{ $info->jumlah_peserta }} Peserta</span>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 line-clamp-2">{{ $info->judul }}</h3>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>
@endif

    <footer class="bg-black text-white pt-24 pb-10 border-t-4 border-gold-600">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-12 mb-16">
                <div class="md:col-span-1">
                    <h3 class="font-serif text-2xl font-bold text-white mb-6 flex items-center gap-3">
                        @if($profile->logo) <img src="{{ asset('storage/'.$profile->logo) }}" class="h-8 w-auto"> @endif
                    </h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">{{ Str::limit($profile->deskripsi_singkat, 150) }}</p>
                    <p class="text-xs text-gray-500 mt-4 border-t border-gray-800 pt-4">
    Izin: {{ $profile->nomor_sk ?? '-' }} <br>
    Pimpinan: {{ $profile->nama_pimpinan ?? '-' }}
</p>
                    <div class="flex gap-4">
                        @if($profile->facebook_url) <a href="{{ $profile->facebook_url }}" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-gold-500 hover:text-white transition"><i class="fa-brands fa-facebook-f"></i></a> @endif
                        @if($profile->instagram_url) <a href="{{ $profile->instagram_url }}" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-gold-500 hover:text-white transition"><i class="fa-brands fa-instagram"></i></a> @endif
                        @if($profile->tiktok_url) <a href="{{ $profile->tiktok_url }}" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-gold-500 hover:text-white transition"><i class="fa-brands fa-tiktok"></i></a> @endif
                        @if($profile->youtube_url) <a href="{{ $profile->youtube_url }}" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-gold-500 hover:text-white transition"><i class="fa-brands fa-youtube"></i></a> @endif
                    </div>
                </div>

                <div>
    <h4 class="font-bold text-lg mb-6 text-gold-500">Navigasi</h4>
    <ul class="space-y-3 text-sm text-gray-400">
        <li><a href="#beranda" class="hover:text-white transition">Beranda</a></li>
        <li><a href="#tentang" class="hover:text-white transition">Tentang Kami</a></li>
        <li><a href="#program" class="hover:text-white transition">Program</a></li>
        <li><a href="#edukasi" class="hover:text-white transition">Artikel</a></li>
        <li><a href="#galeri" class="hover:text-white transition">Galeri</a></li>
        <li><a href="#alur" class="hover:text-white transition">Cara Daftar</a></li>
        <li><a href="#instruktur" class="hover:text-white transition">Pengajar</a></li>
        <li><a href="#testimoni" class="hover:text-white transition">Testimoni</a></li>
        <li><a href="#keberangkatan" class="hover:text-white transition">Keberangkatan</a></li>
    </ul>
</div>


                <div>
                    <h4 class="font-bold text-lg mb-6 text-gold-500">Kontak</h4>
                    <ul class="space-y-4 text-sm text-gray-400">
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-location-dot mt-1 text-gold-600"></i>
                            <span>{{ $profile->alamat }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-phone text-gold-600"></i>
                            <span>{{ $profile->telepon_lpk }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-brands fa-whatsapp text-gold-600"></i>
                            <span>{{ $profile->nomor_wa }}</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-6 text-gold-500">Lokasi</h4>
                    @if($profile->google_map_embed)
                        <div class="rounded-lg overflow-hidden h-40 grayscale hover:grayscale-0 transition duration-500 border border-gray-700">
                            {!! $profile->google_map_embed !!}
                        </div>
                    @else
                        <div class="h-40 bg-gray-800 rounded-lg flex items-center justify-center text-gray-600 text-xs">Peta belum diatur</div>
                    @endif
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} <strong class="text-white">{{ $profile->nama_lpk }}</strong>. All Rights Reserved.
            </div>
        </div>
    </footer>


    <div x-show="activeAlbum" 
         class="fixed inset-0 z-[100] flex items-center justify-center px-4 py-6 sm:px-6"
         style="display: none;">
        
        <div class="fixed inset-0 bg-black/90 backdrop-blur-sm transition-opacity" 
             @click="activeAlbum = null"
             x-show="activeAlbum"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        </div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col"
             x-show="activeAlbum"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 font-serif" x-text="activeAlbum?.nama_album"></h3>
                    <p class="text-sm text-gray-500 mt-1" x-text="activeAlbum?.deskripsi_album"></p>
                </div>
                <button @click="activeAlbum = null" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto bg-gray-50 flex-1">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <template x-for="(media, index) in activeAlbum?.galeris" :key="media.id">
                        <div class="relative group aspect-square rounded-xl overflow-hidden bg-gray-200 cursor-pointer" 
                             @click="viewImage(index)">
                            
                            <template x-if="media.tipe === 'foto'">
                                <img :src="'/storage/' + media.path_file" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                            </template>
                            
                            <template x-if="media.tipe === 'video'">
                                <div class="w-full h-full relative">
                                    <img :src="media.thumbnail_url" class="w-full h-full object-cover opacity-90 group-hover:scale-110 transition duration-500">
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-10 h-10 bg-white/80 rounded-full flex items-center justify-center text-red-600 shadow-md group-hover:bg-red-600 group-hover:text-white transition">
                                            <i class="fa-solid fa-play ml-0.5"></i>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div x-show="zoomOpen" 
         class="fixed inset-0 z-[110] bg-black/95 flex items-center justify-center p-4" 
         style="display: none;"
         @keydown.window.arrow-right.prevent="nextZoom()" 
         @keydown.window.arrow-left.prevent="prevZoom()"
         @keydown.window.escape.prevent="closeZoom()">
        
        <button @click="closeZoom()" class="absolute top-6 right-6 text-white/70 hover:text-white text-4xl z-50"><i class="fa-solid fa-times"></i></button>

        <button @click.stop="prevZoom()" class="absolute left-4 text-white/50 hover:text-white text-5xl p-4 z-50 focus:outline-none">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <div class="relative w-full max-w-5xl max-h-[85vh] flex items-center justify-center" @click.stop>
            
            <template x-if="currentMedia?.tipe === 'foto'">
                <img :src="'/storage/' + currentMedia?.path_file" class="max-w-full max-h-[85vh] rounded shadow-2xl object-contain">
            </template>

            <template x-if="currentMedia?.tipe === 'video'">
                <div class="w-full aspect-video bg-black rounded-xl overflow-hidden shadow-2xl">
                    <iframe :src="currentMedia?.embed_url" class="w-full h-full" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                </div>
            </template>

        </div>

        <button @click.stop="nextZoom()" class="absolute right-4 text-white/50 hover:text-white text-5xl p-4 z-50 focus:outline-none">
            <i class="fa-solid fa-chevron-right"></i>
        </button>

        <div class="absolute bottom-6 text-center w-full pointer-events-none">
            <p x-text="currentMedia?.judul" class="text-white font-semibold text-lg mb-1 drop-shadow-md"></p>
            <p class="text-white/60 text-sm tracking-widest">
                <span x-text="zoomIndex + 1"></span> / <span x-text="activeAlbum?.galeris.length"></span>
            </p>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ once: true, duration: 800, offset: 50 });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    window.scrollTo({ top: targetElement.offsetTop - 80, behavior: 'smooth' });
                }
            });
        });

        function landingPage() {
            return {
                activeAlbum: null,
                zoomOpen: false,
                zoomIndex: 0,
                albumsData: @json($albums),

                // Computed property untuk media saat ini
                get currentMedia() {
                    if(this.activeAlbum && this.activeAlbum.galeris[this.zoomIndex]) {
                        return this.activeAlbum.galeris[this.zoomIndex];
                    }
                    return null;
                },

                openAlbum(id) {
                    this.activeAlbum = this.albumsData.find(a => a.id === id);
                },
                
                viewImage(index) {
                    this.zoomIndex = index;
                    this.zoomOpen = true;
                },

                closeZoom() {
                    this.zoomOpen = false;
                    // Reset index opsional, tapi biar UX bagus biarkan saja
                },

                nextZoom() {
                    if(!this.activeAlbum) return;
                    this.zoomIndex = (this.zoomIndex + 1) % this.activeAlbum.galeris.length;
                },

                prevZoom() {
                    if(!this.activeAlbum) return;
                    this.zoomIndex = (this.zoomIndex - 1 + this.activeAlbum.galeris.length) % this.activeAlbum.galeris.length;
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function () {
        var swiper = new Swiper(".myTestimonialSwiper", {
            slidesPerView: 1,      // Tampilan Mobile: 1 kartu
            spaceBetween: 30,      // Jarak antar kartu
            loop: true,            // Bisa digeser terus menerus (infinite)
            autoplay: {
                delay: 3000,       // Geser otomatis setiap 3 detik
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 1,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 2, // Tablet: 2 kartu
                    spaceBetween: 30,
                },
                1024: {
                    slidesPerView: 3, // Desktop: 3 kartu
                    spaceBetween: 30,
                },
            },
        });
    });

    function loadMoreGaleri() {
        // 1. Ambil semua elemen yang disembunyikan
        const hiddenItems = document.querySelectorAll('.album-item-hidden');
        
        // 2. Tampilkan mereka dengan menghapus class 'hidden'
        hiddenItems.forEach(item => {
            item.classList.remove('hidden');
            // Opsional: Tambahkan animasi fade-in kecil agar halus
            item.classList.add('animate-fade-in-up'); 
        });

        // 3. Sembunyikan tombolnya setelah semua tampil
        const btnWrapper = document.getElementById('btnWrapper');
        if(btnWrapper) {
            btnWrapper.style.display = 'none';
        }

        // 4. Jika menggunakan AOS (Animate On Scroll), refresh agar posisi baru terdeteksi
        if (typeof AOS !== 'undefined') {
            setTimeout(() => {
                AOS.refresh();
            }, 100); // delay sedikit
        }
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</body>
</html>