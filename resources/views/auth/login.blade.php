<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Web Pengelolaan Nilai') }} - Login</title>

    <style>
        .clip-trapezoid {
            clip-path: polygon(10% 0%, 100% 0%, 100% 100%, 0% 100%);
            pointer-events: auto;
            z-index: 10;
        }
    </style>
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="relative min-h-screen w-full">
        <!-- Gambar Latar Belakang (Sisi Kiri) -->
        <div class="relative flex-1 bg-cover bg-center min-h-screen"
             style="background-image: url('https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=1170&auto=format&fit=crop');">
            <div class="absolute inset-0 bg-black opacity-80"></div>

            <!-- Navbar -->
            <div class="absolute top-6 left-6 flex items-center z-10 px-3">
                <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEium4wXO9XSBY9kNJgBmFxlOEgKk4tOaOrnWOM3LL3NG4PAEMnb9vbunblwPifY72AM_AhW6iJJ9EAxSPDc20S8Xp0-csqDlGVk3e-YXTwdbE5EUmrkyXDKWU2OIg_5EG_Dg27xjuyfn_EP/s1600/Logo+Tut+Wuri+Handayani.png" alt="Logo SDN CIJEDIL" class="w-10 h-10">
                <span class="text-white font-semibold ml-2 text-lg">SDN CIJEDIL</span>
            </div>

            <!-- Konten -->
            <div class="relative z-10 px-12 pt-24 text-slate-200">
                <h1 class="text-4xl font-bold mb-4">WEBSITE PENGELOLAAN<br>NILAI SISWA</h1>
                <p class="text-sm mb-10">Lorem ipsum dolor sit amet.</p>
            </div>
            <div class="relative z-10 px-12 pt-52 text-white">
                <p>
                    Lorem Ipsum Dolor Sit Amet<br>
                    consectetur adipiscing elit. Vivamus lacinia odio<br>
                    vitae vestibulum vestibulum.
                </p>
            </div>
        </div>

        <!-- Form Login Mengambang -->
        <div class="absolute top-0 right-0 bottom-0 h-full w-full md:w-2/4 pl-20 text-white px-8 py-12 flex flex-col 
        justify-center items-center clip-trapezoid shadow-2xl backdrop-blur-md bg-opacity-90 bg-gradient-to-b from-[#030330] to-[#03044b]">
            <h2 class="text-3xl font-bold mb-6">Masuk Akun</h2>
            {{-- <p class="text-blue-100 mb-6">Selamat datang kembali! Silakan masukkan data Anda.</p> --}}

            <form method="POST" action="{{ route('login') }}" class="w-full max-w-sm pl-6 pr-6">
                @csrf

                <!-- Alamat Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm text-blue-100 mb-2">Email</label>
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        autocomplete="username"
                        class="w-full px-4 py-2 rounded bg-white text-black border border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" 
                    />
                    @error('email')
                        <p class="text-red-300 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kata Sandi -->
                <div class="mb-6">
                    <label for="password" class="block text-sm text-blue-100 mb-2">Kata Sandi</label>
                    <input 
                        id="password" 
                        type="password"
                        name="password"
                        required 
                        autocomplete="current-password"
                        class="w-full px-4 py-2 rounded bg-white text-black border border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    />
                    @error('password')
                        <p class="text-red-300 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ingat Saya -->
                <div class="flex justify-between mb-8">
                    <div class="flex items-center">
                        <input 
                            id="remember_me" 
                            type="checkbox" 
                            class="mr-2 text-blue-200 focus:ring-blue-200 rounded" 
                            name="remember"
                        >
                        <label for="remember_me" class="text-sm text-blue-100">
                            Ingat saya
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <a 
                            href="{{ route('password.request') }}" 
                            class="text-sm text-blue-200 hover:text-white"
                        >
                            Lupa kata sandi?
                        </a>
                    @endif
                </div>

                <!-- Tombol Masuk -->
                <button 
                    type="submit" 
                    class="w-full py-2 px-2 bg-white text-black rounded font-semibold hover:bg-blue-100 transition"
                >
                    Masuk
                </button>
            </form>

            <!-- Tautan Daftar -->
            <div class="text-center mt-6">
                <span class="text-blue-200 text-sm">Belum punya akun?</span>
                @if (Route::has('register'))
                    <a 
                        href="{{ route('register') }}" 
                        class="text-white ml-1 hover:underline"
                    >
                        Daftar
                    </a>
                @endif
            </div>
        </div>
    </div>
</body>
</html>