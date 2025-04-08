<aside
    class="h-screen bg-gradient-to-b from-[#030330] to-[#03044b] text-white p-3 flex flex-col transition-all duration-300 ease-in-out overflow-hidden"
    x-cloak x-data="sidebar()" :class="isOpen ? 'w-64' : 'w-20'" @mouseleave="handleMouseLeave()">
    <!-- Logo Section -->
    <div class="flex items-center px-2 mb-6 h-16">
        <img src="https://ucarecdn.com/60bcc816-2da9-4f6a-ab6e-b926d11341e1/LogoTutWuriHandayani.png" alt="Logo"
            class="w-10 h-auto min-w-[40px] flex-shrink-0" />
        <div class="overflow-hidden transition-opacity duration-300"
            :class="isOpen ? 'w-auto opacity-100 ml-5' : 'w-0 opacity-0'">
            <span class="text-white text-sm font-semibold">SDN CIJEDIL</span>
        </div>
    </div>

    <!-- Toggle Button -->
    <button @click="toggleMenu()"
        class="w-full flex items-center rounded-lg hover:bg-white/10 text-white transition-all duration-300 ease-in-out h-14">
        <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
            <span class="iconify text-2xl" data-icon="mdi:menu"></span>
        </div>
        <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
            :class="isOpen ? 'opacity-100' : 'opacity-0 w-0'">
            <span>Menu</span>
        </div>
    </button>

    <!-- Navigation Section -->
    <nav class="flex-1 mt-4 overflow-y-auto scrollbar-hide" x-ref="navScroll" @scroll="saveScrollPosition()">
        <ul class="space-y-1">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('dashboard') }}" @click="handleNavigation"
                    class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                    <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                        <span class="iconify text-2xl" data-icon="mdi:view-dashboard"></span>
                    </div>
                    <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                        :class="isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                        <span>Dashboard</span>
                    </div>
                </a>
            </li>

            <!-- Manajemen Siswa -->
            <li>
                <a href="{{ auth()->user()->hasRole('Admin') ? route('admin.siswa.kelas') : route('walikelas.students.index', ['classId' => auth()->user()->class_id]) }}" 
                   @click="handleNavigation"
                   class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                    <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                        <span class="iconify text-2xl" data-icon="mdi:account-group"></span>
                    </div>
                    <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                        :class="isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                        <span>Manajemen Siswa</span>
                    </div>
                </a>
            </li>
            {{-- <li class="nav-item">
                <a href="{{ auth()->user()->hasRole('Admin') ? route('admin.siswa.kelas') : route('walikelas.students.index', ['classId' => auth()->user()->class_id ?? 0]) }}" 
                   class="nav-link {{ request()->routeIs('admin.siswa.kelas') || request()->routeIs('walikelas.students.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users"></i>
                    <p>Manajemen Siswa</p>
                </a>
            </li> --}}

            <!-- Manajemen Kelas -->
            <li>
                <a href="{{ route('admin.kelas.index') }}" @click="handleNavigation"
                    class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                    <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                        <span class="iconify text-2xl" data-icon="mdi:google-classroom"></span>
                    </div>
                    <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                        :class="isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                        <span>Manajemen Kelas</span>
                    </div>
                </a>
            </li>

            <!-- Input Nilai -->
            <li>
                <a href="{{ route('grades.create') }}" @click="handleNavigation"
                    class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                    <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                        <span class="iconify text-2xl" data-icon="mdi:clipboard-text"></span>
                    </div>
                    <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                        :class="isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                        <span>Input Nilai</span>
                    </div>
                </a>
            </li>

            <!-- Rekap Nilai -->
            <li>
                <a href=" {{ route('grades.recap') }}" @click="handleNavigation"
                    class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                    <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                        <span class="iconify text-2xl" data-icon="mdi:file-chart"></span>
                    </div>
                    <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                        :class="isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                        <span>Rekap Nilai</span>
                    </div>
                </a>
            </li>

            <!-- Notifikasi -->
            <li>
                <a href="{{ route('notifications.index') }}" @click="handleNavigation"
                    class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                    <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                        <span class="iconify text-2xl" data-icon="mdi:cellphone-message"></span>
                    </div>
                    <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                        :class="isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                        <span>Notifikasi</span>
                    </div>
                </a>
            </li>

            <!-- Export Data -->
            <li>
                <a href="{{ route('grades.export') }}" @click="handleNavigation"
                    class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                    <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                        <span class="iconify text-2xl" data-icon="mdi:export"></span>
                    </div>
                    <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                        :class="isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                        <span>Export Data</span>
                    </div>
                </a>
            </li>

            <!-- Pengaturan Akun -->
            <li>
                <a href="{{ route('profile.edit') }}" @click="handleNavigation"
                    class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                    <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                        <span class="iconify text-2xl" data-icon="mdi:cog"></span>
                    </div>
                    <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                        :class="isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                        <span>Pengaturan Akun</span>
                    </div>
                </a>
            </li>


            <!-- Manajemen Pengguna -->
            <a href="{{ route('admin.users.index') }}" @click="handleNavigation"
                class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                    <span class="iconify text-2xl" data-icon="mdi:account-circle"></span>
                </div>
                <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                    :class="isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                    <span>Pengelolaan Akun</span>
                </div>
            </a>


            <!-- Manajemen Pengguna -->
            <li>
                <a href="#" @click="handleNavigation"
                    class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                    <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                        <span class="iconify text-2xl" data-icon="mdi:account-cog"></span>
                    </div>
                    <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                        :class="isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                        <span>Manajemen Pengguna</span>
                    </div>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Logout Button -->
    <div class="mt-4 border-t border-white/20 pt-4">
        <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            class="w-full flex items-center rounded-lg hover:bg-red-500/80 text-white transition-all duration-300 ease-in-out h-14">
            <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                <span class="iconify text-2xl" data-icon="mdi:logout"></span>
            </div>
            <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                :class="isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                <span>Logout</span>
            </div>
        </a>

        <!-- Hidden Logout Form -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</aside>

<!-- Alpine JS Component Script -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('sidebar', () => ({
            isOpen: localStorage.getItem('sidebarState') === 'open',
            collapseTimer: null,
            autoCollapseEnabled: true, // Flag untuk mengaktifkan/menonaktifkan fitur auto-collapse

            init() {
                // Menerapkan state sidebar dari localStorage saat komponen diinisialisasi
                this.isOpen = localStorage.getItem('sidebarState') === 'open';

                // Mencegah flash dengan menambahkan kelas x-cloak dari Alpine
                document.querySelector('aside').classList.remove('opacity-0');

                // Mengembalikan posisi scroll sidebar saat halaman dimuat
                this.$nextTick(() => {
                    const savedScrollPosition = localStorage.getItem(
                        'sidebarScrollPosition');
                    if (savedScrollPosition) {
                        this.$refs.navScroll.scrollTop = parseInt(savedScrollPosition);
                    }
                });
            },

            saveState() {
                localStorage.setItem('sidebarState', this.isOpen ? 'open' : 'closed');
            },

            saveScrollPosition() {
                // Menyimpan posisi scroll saat pengguna scroll
                localStorage.setItem('sidebarScrollPosition', this.$refs.navScroll.scrollTop);
            },

            toggleMenu() {
                this.isOpen = !this.isOpen;
                this.saveState();
                // Reset timer ketika sidebar ditoggle secara manual
                this.clearCollapseTimer();
            },

            handleNavigation(event) {
                // Simpan status sidebar sebelum navigasi
                this.saveState();
                // Simpan posisi scroll sebelum navigasi
                this.saveScrollPosition();
                // Membersihkan timer
                this.clearCollapseTimer();
            },

            handleMouseLeave() {
                // Saat mouse meninggalkan sidebar, kita set timer untuk menutup sidebar
                // Hanya menjalankan timer jika sidebar sedang terbuka
                if (this.autoCollapseEnabled && this.isOpen) {
                    this.clearCollapseTimer();
                    this.collapseTimer = setTimeout(() => {
                        this.isOpen = false;
                        this.saveState();
                    }, 1700); // 3 detik
                }
            },

            clearCollapseTimer() {
                // Membersihkan timer yang sedang berjalan
                if (this.collapseTimer) {
                    clearTimeout(this.collapseTimer);
                    this.collapseTimer = null;
                }
            }
        }));
    });
</script>

<style>
    /* Custom scrollbar hiding styles */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    /* For IE, Edge and Firefox */
    .scrollbar-hide {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }

    /* Alpine JS cloak untuk mencegah flash selama loading */
    [x-cloak] {
        display: none !important;
    }
</style>
