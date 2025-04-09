<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Website Pengelolaan Nilai') }}</title>
    <link rel="icon" type="image/png"
        href="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEium4wXO9XSBY9kNJgBmFxlOEgKk4tOaOrnWOM3LL3NG4PAEMnb9vbunblwPifY72AM_AhW6iJJ9EAxSPDc20S8Xp0-csqDlGVk3e-YXTwdbE5EUmrkyXDKWU2OIg_5EG_Dg27xjuyfn_EP/s1600/Logo+Tut+Wuri+Handayani.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
@vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (performance.navigation.type === 1) {
                const sidebarState = localStorage.getItem('sidebar_state');
                if (sidebarState === 'closed') {
                    document.querySelector('aside').classList.remove('w-64');
                    document.querySelector('aside').classList.add('w-20');
                }
            }
        });
    </script>
</head>

<body class="font-sans antialiased">
    <div class="flex min-h-screen bg-gray-100">
        <!-- Sidebar dengan posisi fixed -->
        <div class="sticky top-0 h-screen overflow-y-auto">
            @include('layouts.sidebar')
        </div>

        <!-- Content dengan overflow untuk scrolling -->
        <div class="flex-1 flex flex-col overflow-auto">
            @isset($header)
                <header class="bg-white shadow sticky top-0 z-10">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center justify-between">
                            <div>
                                {{ $header }}
                            </div>
                            <div class="flex items-center">
                                <div class="relative" x-data="{ open: false }">
                                    <div class="flex items-center cursor-pointer" @click="open = !open">
                                        <div class="flex items-center">
                                            <div class="mr-2">
                                                <!-- Gunakan URL dari profile_picture -->
                                                <img src="{{ Auth::user()->profile_picture ?? asset('images/default-avatar.png') }}"
                                                    alt="{{ Auth::user()->name }}"
                                                    class="rounded-full h-8 w-8 object-cover">
                                            </div>
                                            <div>
                                                <div class="text-xs font-medium text-gray-900">{{ Auth::user()->name }}
                                                </div>
                                                <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                                            </div>
                                        </div>
                                        <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                    <div x-show="open" @click.away="open = false"
                                        class="absolute right-0 mt-1 w-40 bg-white rounded-md shadow-lg py-2 z-50">
                                        <a href=" {{ route('profile.edit') }}"
                                            class="block px-3 py-1 text-xs text-gray-700 hover:bg-gray-100">
                                            Pengaturan Akun
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="block w-full text-left px-3 py-1 text-xs text-gray-700 hover:bg-gray-100">
                                                Keluar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="p-6 flex-1">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>
