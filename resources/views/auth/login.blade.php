<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistem Pengelolaan Nilai SDN Cijedil') }} - Login</title>
    <link rel="icon" type="image/png"
        href="https://ucarecdn.com/140db37d-4117-4b98-bc00-20e8d0147903/WhatsApp_Image_20250430_at_105328_AM__1_removebgpreview.png">
    <style>
        .clip-trapezoid {
            clip-path: polygon(10% 0%, 100% 0%, 100% 100%, 0% 100%);
            pointer-events: auto;
            z-index: 10;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <!-- Desktop View (md and up) -->
    <div class="hidden md:block relative min-h-screen w-full">
        <!-- Gambar Latar Belakang (Sisi Kiri) -->
        <div class="relative flex-1 bg-cover bg-right min-h-screen"
            style="background-image: url('https://ucarecdn.com/78962b32-6083-4274-9af4-9c0883d043cf/WhatsAppImage20250901at110026_0346c0c1.jpg');">
            <div class="absolute inset-0 bg-black opacity-80"></div>

            <!-- Navbar -->
            <div class="absolute top-6 left-6 flex items-center z-10 px-3">
                <img src="https://ucarecdn.com/140db37d-4117-4b98-bc00-20e8d0147903/WhatsApp_Image_20250430_at_105328_AM__1_removebgpreview.png"
                    alt="Logo SDN CIJEDIL" class="w-10 h-10">
                <span class="text-white font-semibold ml-2 text-lg">SDN Cijedil</span>
            </div>

            <!-- Konten -->
            <div class="relative z-10 px-12 pt-24 text-slate-200">
                <h1 class="text-4xl font-bold mb-4">Sistem Pengelolaan Nilai<br>SDN Cijedil</h1>
            </div>
            <div class="absolute bottom-12 z-10 px-12 text-white w-full">
                <p class="text-sm">
                    SDN Cijedil<br>
                    Solusi digital untuk pengelolaan nilai siswa yang efisien dan terpercaya, <br>
                    mendukung peningkatan mutu pendidikan.
                </p>
            </div>
        </div>

        <!-- Form Login Mengambang -->
        <div
            class="absolute top-0 right-0 bottom-0 h-full w-full md:w-2/4 pl-20 text-white px-8 py-12 flex flex-col 
        justify-center items-center clip-trapezoid shadow-2xl backdrop-blur-md bg-opacity-90 bg-gradient-to-b from-[#030330] to-[#03044b]">
            <h2 class="text-3xl font-bold mb-6">Login</h2>

            <form method="POST" action="{{ route('login') }}" class="w-full max-w-sm pl-6 pr-6">
                @csrf

                <!-- Alamat Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm text-blue-100 mb-2">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="username"
                        class="w-full px-4 py-2 rounded bg-white text-black border border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                    @error('email')
                        <p class="text-red-300 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kata Sandi -->
                <div class="mb-6">
                    <label for="password" class="block text-sm text-blue-100 mb-2">Kata Sandi</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full px-4 py-2 rounded bg-white text-black border border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                    @error('password')
                        <p class="text-red-300 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lupa kata sandi -->
                <div class="flex justify-end mb-8">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-200 hover:text-white">
                            Lupa kata sandi?
                        </a>
                    @endif
                </div>

                <!-- Tombol Masuk -->
                <button type="submit"
                    class="w-full py-2 px-2 bg-white text-black rounded font-semibold hover:bg-blue-100 transition">
                    Masuk
                </button>
            </form>
        </div>
    </div>

    <!-- Mobile View (below md) -->
    <div class="md:hidden min-h-screen flex flex-col">
        <!-- Header with Background Image & Logo -->
        <div class="relative h-[45vh] bg-cover bg-center overflow-hidden"
            style="background-image: url('https://ucarecdn.com/78962b32-6083-4274-9af4-9c0883d043cf/WhatsAppImage20250901at110026_0346c0c1.jpg');">
            <!-- Gradient Overlay - Black with adjustable transparency -->
            <div class="absolute inset-0 bg-black/60"></div>

            <!-- Decorative Elements -->
            <div class="absolute top-4 right-4 w-24 h-24 border-4 border-white/20 rounded-full"></div>
            <div class="absolute bottom-8 left-4 w-16 h-16 border-4 border-white/20 rounded-full"></div>
            <div class="absolute top-1/2 right-8 w-3 h-3 bg-white/40 rounded-full"></div>
            <div class="absolute top-1/4 left-8 w-2 h-2 bg-white/40 rounded-full"></div>

            <!-- Content -->
            <div class="relative z-10 h-full flex flex-col items-center justify-center px-6 text-white">
                <img src="https://ucarecdn.com/140db37d-4117-4b98-bc00-20e8d0147903/WhatsApp_Image_20250430_at_105328_AM__1_removebgpreview.png"
                    alt="Logo SDN CIJEDIL" class="w-24 h-24 mb-4 drop-shadow-lg">
                <h1 class="text-4xl font-bold mb-2 text-center drop-shadow-lg">SDN CIJEDIL</h1>
                <p class="text-white/90 text-center text-sm">Sistem Pengelolaan Nilai</p>
            </div>

            <!-- Wave Shape -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                    <path
                        d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z"
                        fill="#030330" />
                </svg>
            </div>
        </div>

        <!-- Login Form Section (Dark Blue) -->
        <div class="flex-1 bg-gradient-to-b from-[#030330] to-[#03044b] px-8 pt-8 pb-12">
            <form method="POST" action="{{ route('login') }}" class="space-y-5 max-w-md mx-auto">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email-mobile" class="block text-sm font-medium text-blue-100 mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input id="email-mobile" type="email" name="email" value="{{ old('email') }}" required
                            autofocus autocomplete="username"
                            class="w-full pl-12 pr-4 py-3.5 rounded-2xl bg-white border border-gray-200 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                            placeholder="Email" />
                    </div>
                    @error('email')
                        <p class="text-red-300 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password-mobile" class="block text-sm font-medium text-blue-100 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password-mobile" type="password" name="password" required
                            autocomplete="current-password"
                            class="w-full pl-12 pr-4 py-3.5 rounded-2xl bg-white border border-gray-200 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                            placeholder="Password" />
                    </div>
                    @error('password')
                        <p class="text-red-300 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Forgot Password -->
                <div class="flex justify-end">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm text-blue-200 hover:text-white font-medium transition">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3.5 px-4 bg-white hover:bg-gray-50 text-gray-900 rounded-2xl font-bold shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-95 transition duration-200">
                    Login
                </button>

            </form>

            <!-- Footer -->
            <p class="text-center text-xs text-blue-300/60 mt-8">
                © {{ date('Y') }} SDN Cijedil
            </p>
        </div>
    </div>
</body>

</html>
