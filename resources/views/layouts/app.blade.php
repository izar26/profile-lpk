<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $lpkProfile->nama_lpk ?? config('app.name', 'LPK Profile') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Lato', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-serif { font-family: 'Playfair Display', serif; }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-800">

    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 shadow-soft">
        <div class="px-4 py-3 lg:px-6 flex justify-between items-center">

            <div class="flex items-center gap-3">
                <button data-drawer-target="logo-sidebar" 
                        data-drawer-toggle="logo-sidebar"
                        class="sm:hidden inline-flex items-center p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    @if($lpkProfile && $lpkProfile->logo)
                        <img src="{{ asset('storage/' . $lpkProfile->logo) }}" alt="Logo" class="h-9 w-auto object-contain">
                    @endif
                    
                    {{-- <span class="font-serif text-2xl font-bold text-gray-900 tracking-wide hidden sm:block">
                        @if($lpkProfile && $lpkProfile->nama_lpk)
                            {{ $lpkProfile->nama_lpk }}
                        @else
                            LPK <span class="text-gold-500">PROFILE</span>
                        @endif
                    </span> --}}
                </a>
            </div>

            <div class="flex items-center gap-4">

                <button class="flex items-center text-sm bg-gold-500 text-white rounded-full p-1.5 shadow-gold focus:ring-4 focus:ring-gold-200 transition"
                        data-dropdown-toggle="dropdown-user">
                    @if(Auth::user()->foto)
                        <img class="w-9 h-9 rounded-full object-cover" src="{{ asset('storage/' . Auth::user()->foto) }}" alt="{{ Auth::user()->name }}">
                    @else
                        <img class="w-9 h-9 rounded-full object-cover" src="{{ 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=D97706&color=FFF' }}" alt="{{ Auth::user()->name }}">
                    @endif
                </button>

                <div id="dropdown-user" class="hidden z-50 mt-4 bg-white rounded-xl shadow-lg border border-gray-100 w-56">
                    <div class="px-4 py-3">
                        <p class="text-gray-900 font-semibold">{{ Auth::user()->name }}</p>
                        <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                    <ul class="py-1">
                        <li>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gold-50 hover:text-gold-600 transition-all duration-200">Profile</a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gold-50 hover:text-gold-600 transition-all duration-200">Sign out</a>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 border-r border-gray-200 bg-white transition-transform -translate-x-full sm:translate-x-0 shadow-soft">
        <div class="h-full px-3 pb-6 overflow-y-auto">
            <ul class="space-y-2 font-medium">
                
                @auth
                
                {{-- ================= ADMIN MENU ================= --}}
                @if(Auth::user()->isAdmin())
                    
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gold-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-gold-50 hover:text-gold-600' }}">
                            <i class="fa-solid fa-house-user w-5 h-5 transition duration-75 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-500 group-hover:text-gold-600' }}"></i>
                            <span class="ms-3">Dashboard</span>
                        </a>
                    </li>

                    <div class="pt-4 pb-2">
                        <span class="px-2 text-xs font-bold text-gray-400 uppercase">Konten Website</span>
                    </div>

                    <li>
                        <a href="{{ route('admin.lpk-profile.edit') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('admin.lpk-profile.edit') ? 'bg-gold-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-gold-50 hover:text-gold-600' }}">
                            <i class="fa-solid fa-building-columns w-5 h-5 transition duration-75 {{ request()->routeIs('admin.lpk-profile.edit') ? 'text-white' : 'text-gray-500 group-hover:text-gold-600' }}"></i>
                            <span class="ms-3">Profil LPK</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.albums.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('admin.albums.*') ? 'bg-gold-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-gold-50 hover:text-gold-600' }}">
                            <i class="fa-solid fa-layer-group w-5 h-5 transition duration-75 {{ request()->routeIs('admin.albums.*') ? 'text-white' : 'text-gray-500 group-hover:text-gold-600' }}"></i>
                            <span class="ms-3">Galeri & Album</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.edukasi.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('admin.edukasi.*') ? 'bg-gold-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-gold-50 hover:text-gold-600' }}">
                            <i class="fa-solid fa-book w-5 h-5 transition duration-75 {{ request()->routeIs('admin.edukasi.*') ? 'text-white' : 'text-gray-500 group-hover:text-gold-600' }}"></i>
                            <span class="ms-3">Artikel Edukasi</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.keberangkatan.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('admin.keberangkatan.*') ? 'bg-gold-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-gold-50 hover:text-gold-600' }}">
                            <i class="fa-solid fa-plane-departure w-5 h-5 transition duration-75 {{ request()->routeIs('admin.keberangkatan.*') ? 'text-white' : 'text-gray-500 group-hover:text-gold-600' }}"></i>
                            <span class="ms-3">Info Keberangkatan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.cara-daftar.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('admin.cara-daftar.*') ? 'bg-gold-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-gold-50 hover:text-gold-600' }}">
                            <i class="fa-solid fa-list-ol w-5 h-5 transition duration-75 {{ request()->routeIs('admin.cara-daftar.*') ? 'text-white' : 'text-gray-500 group-hover:text-gold-600' }}"></i>
                            <span class="ms-3">Alur Pendaftaran</span>
                        </a>
                    </li>

                    <div class="pt-4 pb-2">
                        <span class="px-2 text-xs font-bold text-gray-400 uppercase">Akademik & SDM</span>
                    </div>

                    <li>
                        <a href="{{ route('admin.program-pelatihan.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('admin.program-pelatihan.*') ? 'bg-gold-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-gold-50 hover:text-gold-600' }}">
                            <i class="fa-solid fa-book-open-reader w-5 h-5 transition duration-75 {{ request()->routeIs('admin.program-pelatihan.*') ? 'text-white' : 'text-gray-500 group-hover:text-gold-600' }}"></i>
                            <span class="ms-3">Program Pelatihan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.students.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('admin.students.*') ? 'bg-gold-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-gold-50 hover:text-gold-600' }}">
                            <i class="fa-solid fa-users-rectangle w-5 h-5 transition duration-75 {{ request()->routeIs('admin.students.*') ? 'text-white' : 'text-gray-500 group-hover:text-gold-600' }}"></i>
                            <span class="ms-3">Data Siswa</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.employees.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('admin.employees.*') ? 'bg-gold-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-gold-50 hover:text-gold-600' }}">
                            <i class="fa-solid fa-id-card-clip w-5 h-5 transition duration-75 {{ request()->routeIs('admin.employees.*') ? 'text-white' : 'text-gray-500 group-hover:text-gold-600' }}"></i>
                            <span class="ms-3">Data Pegawai</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.alumni.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('admin.alumni.*') ? 'bg-gold-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-gold-50 hover:text-gold-600' }}">
                            <i class="fa-solid fa-user-graduate w-5 h-5 transition duration-75 {{ request()->routeIs('admin.alumni.*') ? 'text-white' : 'text-gray-500 group-hover:text-gold-600' }}"></i>
                            <span class="ms-3">Jejak Alumni</span>
                        </a>
                    </li>

                    <div class="pt-4 pb-2">
                        <span class="px-2 text-xs font-bold text-gray-400 uppercase">Pengaturan</span>
                    </div>

                    <li>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-gold-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-gold-50 hover:text-gold-600' }}">
                            <i class="fa-solid fa-users-cog w-5 h-5 transition duration-75 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-gray-500 group-hover:text-gold-600' }}"></i>
                            <span class="ms-3">Kelola User</span>
                        </a>
                    </li>
                @endif


                {{-- ================= PEGAWAI MENU ================= --}}
                @if(Auth::user()->isPegawai())
                    <li>
                        <a href="{{ route('pegawai.dashboard') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('pegawai.dashboard') ? 'bg-gold-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-gold-50 hover:text-gold-600' }}">
                            <i class="fa-solid fa-house-user w-5 h-5 transition duration-75 {{ request()->routeIs('pegawai.dashboard') ? 'text-white' : 'text-gray-500 group-hover:text-gold-600' }}"></i>
                            <span class="ms-3">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pegawai.biodata.edit') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('pegawai.biodata.*') ? 'bg-gold-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-gold-50 hover:text-gold-600' }}">
                            <i class="fa-solid fa-id-card w-5 h-5 transition duration-75 {{ request()->routeIs('pegawai.biodata.*') ? 'text-white' : 'text-gray-500 group-hover:text-gold-600' }}"></i>
                            <span class="ms-3">Biodata Saya</span>
                        </a>
                    </li>
                @endif


                {{-- ================= SISWA MENU ================= --}}
                @if(Auth::user()->isSiswa())
                    <li>
                        <a href="{{ route('siswa.dashboard') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('siswa.dashboard') ? 'bg-gold-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-gold-50 hover:text-gold-600' }}">
                            <i class="fa-solid fa-house-user w-5 h-5 transition duration-75 {{ request()->routeIs('siswa.dashboard') ? 'text-white' : 'text-gray-500 group-hover:text-gold-600' }}"></i>
                            <span class="ms-3">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('siswa.formulir.show') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('siswa.formulir.*') ? 'bg-gold-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-gold-50 hover:text-gold-600' }}">
                            <i class="fa-solid fa-file-pen w-5 h-5 transition duration-75 {{ request()->routeIs('siswa.formulir.*') ? 'text-white' : 'text-gray-500 group-hover:text-gold-600' }}"></i>
                            <span class="ms-3">Biodata & Dokumen</span>
                        </a>
                    </li>
                    @if(Auth::user()->student && Auth::user()->student->status == 'Alumni')
                        <li>
                            <a href="{{ route('siswa.testimoni.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('siswa.testimoni.*') ? 'bg-gold-500 text-white font-bold shadow-md' : 'text-gray-700 hover:bg-gold-50 hover:text-gold-600' }}">
                                <i class="fa-solid fa-comment-dots w-5 h-5 transition duration-75 {{ request()->routeIs('siswa.testimoni.*') ? 'text-white' : 'text-gray-500 group-hover:text-gold-600' }}"></i>
                                <span class="ms-3">Testimoni Saya</span>
                            </a>
                        </li>
                    @endif
                @endif

                @endauth

            </ul>
        </div>
    </aside>

    <div class="sm:ml-64 p-6">
        <div class="mt-16">
            
            {{-- Header Halaman --}}
            @hasSection('header')
                <header class="mb-8">
                    <h1 class="font-serif text-3xl font-bold text-gray-900">
                        @yield('header')
                    </h1>
                </header>
            @endif

            {{-- Konten Utama --}}
            <main class="bg-white shadow-soft rounded-2xl p-6 border border-gray-100 min-h-[80vh]">
                @yield('content')
            </main>

        </div>
    </div>

    @yield('scripts')

    <style>
        .loader {
            border: 4px solid #ddd;
            border-top: 4px solid #c9a033; /* Warna gold */
            border-radius: 50%;
            width: 38px;
            height: 38px;
            animation: spin 0.9s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>

    <script>
        /* ---------------------- OPEN MODAL (VERSI GLOBAL) ---------------------- */
        function openModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            const content = modal.querySelector('.modal-content');
        
            modal.classList.remove('hidden');
        
            if (content) {
                setTimeout(() => {
                    content.classList.remove('scale-90', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }, 10);
            }
        }
        
        /* ---------------------- CLOSE MODAL (VERSI GLOBAL) ---------------------- */
        function closeModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            const content = modal.querySelector('.modal-content');
        
            if (content) {
                content.classList.add('scale-90', 'opacity-0');
                setTimeout(() => modal.classList.add('hidden'), 200);
            } else {
                modal.classList.add('hidden');
            }
        }
    </script>
    
</body>
</html>