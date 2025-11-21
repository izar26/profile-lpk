<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'LPK Profile') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Lato', 'sans-serif'],
                        'serif': ['Playfair Display', 'serif'],
                    },
                    colors: {
                        // Menggunakan 'amber' untuk palet 'gold' yang profesional
                        gold: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24', // Aksen utama
                            500: '#f59e0b', // Aksen utama (lebih pekat)
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                            950: '#451a03',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Transisi halus untuk sidebar */
        .transition-all-300 {
            transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>

</head>

<body class="font-sans antialiased bg-gray-950 text-gray-300">

    <div x-data="{ sidebarOpen: true, userMenuOpen: false }">
        
        <nav class="fixed top-0 z-50 w-full bg-gray-900 border-b border-gray-700 shadow-lg">
            <div class="px-3 py-3 lg:px-5 lg:pl-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center justify-start rtl:justify-end">
                        
                        <button @click="sidebarOpen = !sidebarOpen" 
                                class="inline-flex items-center p-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-gold-400 focus:outline-none focus:ring-2 focus:ring-gray-600">
                            <span class="sr-only">Toggle sidebar</span>
                            <i class="fa-solid fa-bars w-6 h-6"></i>
                        </button>

                        <a href="{{ route('dashboard') }}" class="flex ms-2 md:me-24">
                            <span class="font-serif self-center text-xl font-bold sm:text-2xl whitespace-nowrap text-white">
                                LPK <span class="text-gold-400">PROFILE</span>
                            </span>
                        </a>
                    </div>
                    
                    <div class="relative flex items-center ms-3">
                        <button @click="userMenuOpen = !userMenuOpen" type="button" class="flex text-sm bg-gold-500 rounded-full focus:ring-4 focus:ring-gold-300" aria-expanded="false">
                            <span class="sr-only">Open user menu</span>
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gold-500 text-gray-900 font-bold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </span>
                        </button>
                        
                        <div x-show="userMenuOpen" @click.outside="userMenuOpen = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 z-50 mt-12 w-48 text-base list-none bg-gray-800 divide-y divide-gray-700 rounded shadow-lg"
                             style="display: none;">
                            
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm text-white" role="none">{{ Auth::user()->name }}</p>
                                <p class="text-sm font-medium text-gray-400 truncate" role="none">{{ Auth::user()->email }}</p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-gold-400" role="menuitem">Profile</a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a href="{{ route('logout') }}" 
                                           onclick="event.preventDefault(); this.closest('form').submit();" 
                                           class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-gold-400" 
                                           role="menuitem">
                                            Sign out
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <aside x-show="sidebarOpen" 
               x-transition:enter="transition-transform transition-all-300"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition-transform transition-all-300"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 bg-gray-900 border-r border-gray-700 sm:translate-x-0" 
               aria-label="Sidebar">
            
            <div class="h-full px-3 pb-4 overflow-y-auto bg-gray-900">
                <ul class="space-y-2 font-medium">
                    
                    @auth
                        @if(Auth::user()->isAdmin())
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center p-2 rounded-lg group
                                    {{ request()->routeIs('admin.dashboard') 
                                        ? 'bg-gold-500 text-gray-900' 
                                        : 'text-gray-300 hover:bg-gray-800 hover:text-gold-400' }}">
                                    
                                    <i class="fa-solid fa-house-user w-5 h-5 transition duration-75 
                                        {{ request()->routeIs('admin.dashboard') 
                                            ? 'text-gray-900' 
                                            : 'text-gray-500 group-hover:text-gold-400' }}"></i>
                                    <span class="ms-3">Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.index') }}" class="flex items-center p-2 rounded-lg group
                                    {{ request()->routeIs('admin.users.*') 
                                        ? 'bg-gold-500 text-gray-900' 
                                        : 'text-gray-300 hover:bg-gray-800 hover:text-gold-400' }}">
                                        
                                    <i class="fa-solid fa-users-cog w-5 h-5 transition duration-75 
                                        {{ request()->routeIs('admin.users.*') 
                                            ? 'text-gray-900' 
                                            : 'text-gray-500 group-hover:text-gold-400' }}"></i>
                                    <span class="ms-3">Kelola User</span>
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->isGuru())
                            <li>
                                <a href="{{ route('guru.dashboard') }}" class="flex items-center p-2 rounded-lg group
                                    {{ request()->routeIs('guru.dashboard') ? 'bg-gold-500 text-gray-900' : 'text-gray-300 hover:bg-gray-800 hover:text-gold-400' }}">
                                    <i class="fa-solid fa-chalkboard-user w-5 h-5 transition duration-75 {{ request()->routeIs('guru.dashboard') ? 'text-gray-900' : 'text-gray-500 group-hover:text-gold-400' }}"></i>
                                    <span class="ms-3">Dashboard Guru</span>
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->isSiswa())
                            <li>
                                <a href="{{ route('siswa.dashboard') }}" class="flex items-center p-2 rounded-lg group
                                    {{ request()->routeIs('siswa.dashboard') ? 'bg-gold-500 text-gray-900' : 'text-gray-300 hover:bg-gray-800 hover:text-gold-400' }}">
                                    <i class="fa-solid fa-graduation-cap w-5 h-5 transition duration-75 {{ request()->routeIs('siswa.dashboard') ? 'text-gray-900' : 'text-gray-500 group-hover:text-gold-400' }}"></i>
                                    <span class="ms-3">Dashboard Siswa</span>
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </aside>

        <div x-show="sidebarOpen" @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-30 bg-black/50 sm:hidden" style="display: none;">
        </div>

        <div class="p-4 md:p-8 transition-all-300" 
             :class="sidebarOpen ? 'sm:ml-64' : 'sm:ml-0'">
            <div class="mt-14">
                @if (isset($header))
                    <header class="mb-6">
                        <div class="max-w-7xl mx-auto">
                            {{ $header->attributes->merge(['class' => 'font-serif font-bold text-3xl text-white leading-tight']) }}
                        </div>
                    </header>
                @endif

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>

    </div> </body>
</html>