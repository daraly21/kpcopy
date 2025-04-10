<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        /* Menyembunyikan scrollbar horizontal */
        #scroll-container {
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        #scroll-container::-webkit-scrollbar {
            display: none;
        }

        .no-select {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .cursor-grab {
            cursor: grab;
        }

        .cursor-grabbing {
            cursor: grabbing !important;
        }

        /* Media Queries untuk Responsivitas */
        @media (max-width: 768px) {
            .max-w-6xl {
                max-width: 100%;
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .h-screen {
                height: 70vh;
            }

            nav .flex {
                flex-direction: column;
                padding: 1rem;
            }

            nav .space-x-4 {
                margin-top: 1rem;
                margin-right: 0;
                gap: 1rem;
            }

            .swiper-slide img {
                object-fit: cover;
                height: 70vh;
            }

            .text-4xl {
                font-size: 1.5rem;
            }

            .text-xl {
                font-size: 1rem;
            }

            .text-lg {
                font-size: 0.9rem;
            }

            .py-20 {
                padding-top: 3rem;
                padding-bottom: 3rem;
            }

            .grid-cols-2 {
                grid-template-columns: 1fr;
            }

            .md\:w-1\/2 {
                width: 100%;
            }

            .flex-col {
                gap: 2rem;
            }

            .w-64 {
                width: 100%;
                max-width: 16rem;
            }

            .h-96 {
                height: 16rem;
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    @if (Route::has('login'))
        <nav class="bg-white shadow-lg fixed w-full top-0 z-50 bg-opacity-85">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex justify-between items-center py-4">
                    <div class="text-xl font-semibold text-slate-900 ml-3">
                        Emang ini laravel?
                    </div>
                    <div class="flex space-x-4 mr-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-slate-900 hover:text-red-800">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-slate-900 hover:text-red-800">
                                Log in
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-slate-900 hover:text-blue-600">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    @endif

    <!-- Hero Section dengan Slideshow -->
    <section class="relative h-screen w-full overflow-hidden pt-18">
        <div class="swiper absolute inset-0">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="https://lib.sdmupat.sch.id/wp-content/uploads/2023/06/Pertukaran-budaya-antara-SD-muhammadiyah-4-Kota-Malang-dari-Indonesia-dan-Trafalgar-Primary-School-di-Australia.jpg" alt="Siswa Sekolah" class="w-full h-full object-cover">
                </div>
                <div class="swiper-slide">
                    <img src="https://sulselprov.go.id/upload/post/1666600351.jpg" alt="Kelas Belajar" class="w-full h-full object-cover">
                </div>
                <div class="swiper-slide">
                    <img src="https://lib.sdmupat.sch.id/wp-content/uploads/2023/06/Miss-Zakki-membuka-kegiatan-cultural-exchange-antara-SD-Muhammadiyah-4-dan-Trafalgar-Primary-School-Australia-1536x1024.jpg" alt="Guru dan Siswa" class="w-full h-full object-cover">
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
        <div class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center z-10">
            <div class="text-center text-white px-4">
                <h1 class="text-4xl md:text-5xl font-bold mt-10">Selamat Datang di Nama Sekolah</h1>
                <p class="text-lg md:text-xl mt-2">Sekolah Unggulan dengan Berbagai Prestasi Gemilang</p>
                <a href="#tentang" class="bg-white text-black px-6 py-3 rounded-full font-semibold hover:bg-slate-200 inline-block mt-4">Pelajari Lebih Lanjut</a>
            </div>
        </div>
    </section>

    <!-- About School -->
    <section id="tentang" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto pl-12">
            <div class="flex flex-col md:flex-row items-center gap-12 ">
                <div class="md:w-1/2">
                    <h2 class="text-3xl font-bold mb-6 text-gray-800 relative">
                        <span class="inline-block pb-2 relative">
                            Tentang Sekolah
                            <span class="absolute bottom-0 left-0 w-1/3 h-1 bg-blue-600"></span>
                        </span>
                    </h2>
                    <p class="text-gray-700 text-lg mb-6 leading-relaxed">
                        Nama Sekolah adalah sekolah yang berkomitmen untuk memberikan pendidikan terbaik kepada siswa-siswinya. Dengan fasilitas modern dan tenaga pengajar yang berkualitas, kami siap mencetak generasi unggul.
                    </p>
                    <p class="text-gray-700 text-lg leading-relaxed">
                        Kami memiliki kurikulum yang komprehensif dan program ekstrakurikuler yang beragam untuk mengembangkan potensi siswa secara maksimal, baik dalam bidang akademik maupun non-akademik.
                    </p>
                </div>
                <div class="md:w-1/2 grid grid-cols-2 gap-4 pr-12 pt-12">
                    <div class="rounded-lg overflow-hidden shadow-md">
                        <img src="https://lib.sdmupat.sch.id/wp-content/uploads/2023/06/Pertukaran-budaya-antara-SD-muhammadiyah-4-Kota-Malang-dari-Indonesia-dan-Trafalgar-Primary-School-di-Australia.jpg" alt="Aktivitas Sekolah" class="w-full h-48 object-cover">
                    </div>
                    <div class="rounded-lg overflow-hidden shadow-md">
                        <img src="https://sulselprov.go.id/upload/post/1666600351.jpg" alt="Fasilitas Sekolah" class="w-full h-48 object-cover">
                    </div>
                    <div class="rounded-lg overflow-hidden shadow-md col-span-2">
                        <img src="https://lib.sdmupat.sch.id/wp-content/uploads/2023/06/Miss-Zakki-membuka-kegiatan-cultural-exchange-antara-SD-Muhammadiyah-4-dan-Trafalgar-Primary-School-Australia-1536x1024.jpg" alt="Kegiatan Sekolah" class="w-full h-48 object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Peta Lokasi -->
    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-12">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="md:w-1/2 w-full h-96 rounded-lg overflow-hidden shadow-2xl transform hover:scale-105 transition-transform duration-300">
                    <iframe 
                        class="w-full h-full"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3162.104042381849!2d126.977732315647!3d37.56653527980046!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x357ca2c74aeddea1%3A0x8b21a8c6b98b3b7d!2sSeoul%2C%20Korea%20Selatan!5e0!3m2!1sid!2sid!4v1698765432100!5m2!1sid!2sid"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <div class="md:w-1/2">
                    <h2 class="text-3xl font-bold mb-6 text-gray-800 relative">
                        <span class="inline-block pb-2 relative">
                            Lokasi Sekolah
                            <span class="absolute bottom-0 left-0 w-1/3 h-1 bg-blue-600"></span>
                        </span>
                    </h2>
                    <p class="text-gray-700 text-lg mb-6 leading-relaxed">
                        Sekolah kami berlokasi di area yang strategis dan mudah diakses dari berbagai wilayah. Dengan lingkungan yang nyaman dan fasilitas lengkap, kami memastikan siswa dapat belajar dengan optimal.
                    </p>
                    <p class="text-gray-700 text-lg mb-6 leading-relaxed">
                        Alamat lengkap: Jl. Pendidikan No. 123, Kota Malang, Jawa Timur. Silakan kunjungi kami atau hubungi untuk informasi lebih lanjut.
                    </p>
                    <div class="flex justify-end mt-8">
                        <a 
                            href="https://www.google.com/maps?q=Jl.+Pendidikan+No.+123,+Kota+Malang,+Jawa+Timur" 
                            target="_blank" 
                            class="inline-flex items-center px-6 py-3 bg-black text-white rounded-lg hover:bg-slate-900 transition-all duration-300 shadow-lg">
                            <span class="iconify mr-2" data-icon="mdi:map-marker" data-width="24" data-height="24"></span>
                            Arahkan Saya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Anggota Sekolah -->
    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-2 text-gray-800">Anggota Sekolah</h2>
            <p class="text-gray-600 text-center max-w-2xl mx-auto mb-12">Kenali tim pengajar profesional kami yang berdedikasi tinggi.</p>
            <div class="relative overflow-hidden">
                <div id="scroll-container" class="flex space-x-6 pb-6 overflow-x-auto scroll-smooth cursor-grab no-select">
                    <div class="flex-shrink-0 w-64 bg-gray-50 p-6 rounded-lg shadow-lg text-center transition-all hover:shadow-xl hover:transform hover:-translate-y-2">
                        <img src="https://th.bing.com/th/id/OIP.bhmCGGf0Va5dPb63I3TE9AHaHa?w=186&h=186&c=7&r=0&o=5&dpr=1.7&pid=1.7" alt="Kepala Sekolah" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                        <h3 class="text-xl font-semibold mb-2 text-gray-800">John Doe</h3>
                        <p class="text-gray-700">Kepala Sekolah</p>
                    </div>
                    <div class="flex-shrink-0 w-64 bg-gray-50 p-6 rounded-lg shadow-lg text-center transition-all hover:shadow-xl hover:transform hover:-translate-y-2">
                        <img src="https://source.unsplash.com/200x200/?teacher,man" alt="Guru 1" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                        <h3 class="text-xl font-semibold mb-2 text-gray-800">Jane Smith</h3>
                        <p class="text-gray-700">Guru Matematika</p>
                    </div>
                    <!-- Tambahkan kartu guru lainnya dengan cara yang sama -->
                </div>
            </div>
        </div>
    </section>

    <!-- Kontak -->
    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-10">
            <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">Hubungi Kami</h2>
            <p class="text-center max-w-2xl mx-auto mb-12 text-gray-600">
                Jika Anda memiliki pertanyaan atau ingin mengetahui lebih lanjut tentang sekolah kami, silakan hubungi kami melalui informasi di bawah ini.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 px-10">
                <div class="bg-gray-50 rounded-lg p-8 text-center hover:shadow-lg transition-all duration-300 border border-gray-200">
                    <div class="flex justify-center mb-4">
                        <span class="iconify text-4xl text-black" data-icon="mdi:email-outline"></span>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Email</h3>
                    <p class="text-lg text-gray-600">info@namasekolah.com</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-8 text-center hover:shadow-lg transition-all duration-300 border border-gray-200">
                    <div class="flex justify-center mb-4">
                        <span class="iconify text-4xl text-black" data-icon="mdi:phone-outline"></span>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Telepon</h3>
                    <p class="text-lg text-gray-600">(021) 1234-5678</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-8 text-center hover:shadow-lg transition-all duration-300 border border-gray-200">
                    <div class="flex justify-center mb-4">
                        <span class="iconify text-4xl text-black" data-icon="mdi:map-marker-outline"></span>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Alamat</h3>
                    <p class="text-lg text-gray-600">Jl. Pendidikan No. 123, Kota Malang, Jawa Timur</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <script>
        const swiper = new Swiper('.swiper', {
            loop: true,
            autoplay: {
                delay: 3000,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });

        const scrollContainer = document.getElementById('scroll-container');
        let isDown = false;
        let startX;
        let scrollLeft;

        scrollContainer.addEventListener('mousedown', (e) => {
            isDown = true;
            scrollContainer.classList.add('cursor-grabbing');
            startX = e.pageX - scrollContainer.offsetLeft;
            scrollLeft = scrollContainer.scrollLeft;
        });

        scrollContainer.addEventListener('mouseup', () => {
            isDown = false;
            scrollContainer.classList.remove('cursor-grabbing');
        });

        scrollContainer.addEventListener('mouseleave', () => {
            isDown = false;
            scrollContainer.classList.remove('cursor-grabbing');
        });

        scrollContainer.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - scrollContainer.offsetLeft;
            const walk = (x - startX) * 2;
            scrollContainer.scrollLeft = scrollLeft - walk;
        });

        scrollContainer.addEventListener('touchstart', (e) => {
            isDown = true;
            startX = e.touches[0].pageX - scrollContainer.offsetLeft;
            scrollLeft = scrollContainer.scrollLeft;
        });

        scrollContainer.addEventListener('touchend', () => {
            isDown = false;
        });

        scrollContainer.addEventListener('touchmove', (e) => {
            if (!isDown) return;
            const x = e.touches[0].pageX - scrollContainer.offsetLeft;
            const walk = (x - startX) * 2;
            scrollContainer.scrollLeft = scrollLeft - walk;
        });
    </script>
</body>
</html>