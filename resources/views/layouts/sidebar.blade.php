<aside
    class="h-screen bg-gradient-to-b from-[#030330] to-[#03044b] text-white p-3 flex flex-col transition-all duration-300 ease-in-out overflow-hidden"
    x-cloak x-data="sidebar()" :class="isOpen ? 'w-64' : 'w-20'" @mouseleave="handleMouseLeave()">
    <!-- Logo Section -->
    <div class="flex items-center px-2 mb-6 h-16">
        <img src="https://ucarecdn.com/140db37d-4117-4b98-bc00-20e8d0147903/WhatsApp_Image_20250430_at_105328_AM__1_removebgpreview.png"
            alt="Logo" class="w-10 h-auto min-w-[40px] flex-shrink-0" />
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
            <!-- Dashboard (Tampil untuk semua peran) -->
            <li>
                <a href="{{ route('dashboard') }}" @click="handleNavigation"
                    class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                    <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                        <span class="iconify text-2xl" data-icon="mdi:view-dashboard"></span>
                    </div>
                    <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                        :class="$store.sidebar.isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                        <span>Dashboard</span>
                    </div>
                </a>
            </li>

            <!-- Manajemen Siswa (Admin) -->
            @if (auth()->user()->hasRole('Admin'))
                <li>
                    <a href="{{ route('admin.siswa.kelas') }}" @click="handleNavigation"
                        class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                        <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                            <span class="iconify text-2xl" data-icon="mdi:account-school"></span>
                        </div>
                        <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                            :class="$store.sidebar.isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                            <span>Kelola Siswa</span>
                        </div>
                    </a>
                </li>
            @endif

            <!-- Manajemen Siswa (Wali Kelas) -->
            {{-- @if (auth()->user()->hasRole('Wali Kelas'))
            <li>
                <a href="{{ route('walikelas.students.index', ['classId' => auth()->user()->class_id]) }}" @click="handleNavigation"
                    class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                    <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                        <span class="iconify text-2xl" data-icon="mdi:account-group"></span>
                    </div>
                    <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                        :class="$store.sidebar.isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                        <span>Kelola Siswa</span>
                    </div>
                </a>
            </li>
            @endif --}}


            <!-- Kelola Mata Pelajaran (Admin) -->
            @if (auth()->user()->hasRole('Admin'))
                <li>
                    <a href="{{ route('admin.mapel.index') }}" @click="handleNavigation"
                        class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                        <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                            <span class="iconify text-2xl" data-icon="mdi:book-open-page-variant"></span>
                        </div>
                        <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                            :class="$store.sidebar.isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                            <span>Kelola Mata Pelajaran</span>
                        </div>
                    </a>
                </li>
            @endif

            <!-- Kelola Kelas (Admin) -->
            @if (auth()->user()->hasRole('Admin'))
                <li>
                    <a href="{{ route('admin.kelas.index') }}" @click="handleNavigation"
                        class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                        <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                            <span class="iconify text-2xl" data-icon="mdi:google-classroom"></span>
                        </div>
                        <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                            :class="$store.sidebar.isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                            <span>Kelola Kelas</span>
                        </div>
                    </a>
                </li>
            @endif

            @if (auth()->user()->hasRole('Admin'))
                <li>
                    <a href="{{ route('admin.academic-years.index') }}" @click="handleNavigation"
                        class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                        <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                            <span class="iconify text-2xl" data-icon="mdi:calendar-range"></span>
                        </div>
                        <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                            :class="$store.sidebar.isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                            <span>Kelola Tahun Ajaran</span>
                        </div>
                    </a>
                </li>
            @endif

            @if (auth()->user()->hasRole('Admin'))
                <li>
                    <a href="{{ route('admin.teachers.index') }}" @click="handleNavigation"
                        class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                        <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                            <span class="iconify text-2xl" data-icon="mdi:account-tie"></span>
                        </div>
                        <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                            :class="$store.sidebar.isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                            <span>Kelola Guru</span>
                        </div>
                    </a>
                </li>
            @endif

            <!-- Kelola Nilai (Wali Kelas & Guru Mata Pelajaran) -->
            @can('kelola nilai')
                <li>
                    @if (auth()->user()->hasRole('Guru Mata Pelajaran') && auth()->user()->subject_id)
                        <a href="{{ route('teacher.grades.index', auth()->user()->subject_id) }}" @click="handleNavigation"
                            class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                            <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                                <span class="iconify text-2xl" data-icon="mdi:file-document-edit"></span>
                            </div>
                            <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                                :class="$store.sidebar.isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                                <span>Kelola Nilai</span>
                            </div>
                        </a>
                    @else
                        <a href="{{ route('grades.list') }}" @click="handleNavigation"
                            class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                            <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                                <span class="iconify text-2xl" data-icon="mdi:file-document-edit"></span>
                            </div>
                            <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                                :class="$store.sidebar.isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                                <span>Kelola Nilai</span>
                            </div>
                        </a>
                    @endif
                </li>
            @endcan

            <!-- Notifikasi (Wali Kelas & Guru Mata Pelajaran) -->
            @can('kirim notifikasi orang tua')
                <li>
                    <a href="{{ route('notifications.index') }}" @click="handleNavigation"
                        class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                        <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                            <span class="iconify text-2xl" data-icon="mdi:cellphone-message"></span>
                        </div>
                        <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                            :class="$store.sidebar.isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                            <span>Notifikasi</span>
                        </div>
                    </a>
                </li>
            @endcan

            <!-- Kelola Nilai (Wali Kelas) -->
            @if (auth()->user()->hasRole('Wali Kelas'))
                <li>
                    <a href="{{ route('grades.export') }}" @click="handleNavigation"
                        class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                        <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                            <span class="iconify text-2xl" data-icon="mdi:file-export"></span>
                        </div>
                        <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                            :class="$store.sidebar.isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                            <span>Rekap Nilai</span>
                        </div>
                    </a>
                </li>
            @endif

            <!-- rapor Siswa (Wali Kelas) -->
            @if (auth()->user()->hasRole('Wali Kelas'))
                <li>
                    <a href="{{ route('rapor.index') }}" @click="handleNavigation"
                        class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                        <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                            <span class="iconify text-2xl" data-icon="mdi:file-certificate"></span>
                        </div>
                        <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                            :class="$store.sidebar.isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                            <span>Rapor Siswa</span>
                        </div>
                    </a>
                </li>
            @endif

            <!-- Kelola Pengguna (Admin) -->
            @if (auth()->user()->hasRole('Admin'))
                <li>
                    <a href="{{ route('admin.users.index') }}" @click="handleNavigation"
                        class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                        <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                            <span class="iconify text-2xl" data-icon="mdi:account-cog"></span>
                        </div>
                        <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                            :class="$store.sidebar.isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                            <span>Kelola Pengguna</span>
                        </div>
                    </a>
                </li>
            @endif

            <!-- Pengaturan Akun (Semua Role) -->
            <li>
                <a href="{{ route('profile.edit') }}" @click="handleNavigation"
                    class="w-full flex items-center rounded-lg hover:bg-white/10 transition-all duration-300 ease-in-out h-14">
                    <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                        <span class="iconify text-2xl" data-icon="mdi:cog"></span>
                    </div>
                    <div class="overflow-hidden whitespace-nowrap transition-opacity duration-300"
                        :class="$store.sidebar.isOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                        <span>Pengaturan Akun</span>
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
            autoCollapseEnabled: true,

            init() {
                this.isOpen = localStorage.getItem('sidebarState') === 'open';
                document.querySelector('aside').classList.remove('opacity-0');
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
                localStorage.setItem('sidebarScrollPosition', this.$refs.navScroll.scrollTop);
            },

            toggleMenu() {
                this.isOpen = !this.isOpen;
                this.saveState();
                this.clearCollapseTimer();
            },

            handleNavigation(event) {
                this.saveState();
                this.saveScrollPosition();
                this.clearCollapseTimer();
            },

            handleMouseLeave() {
                if (this.autoCollapseEnabled && this.isOpen) {
                    this.clearCollapseTimer();
                    this.collapseTimer = setTimeout(() => {
                        this.isOpen = false;
                        this.saveState();
                    }, 1700);
                }
            },

            clearCollapseTimer() {
                if (this.collapseTimer) {
                    clearTimeout(this.collapseTimer);
                    this.collapseTimer = null;
                }
            }
        }));
    });
</script>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    [x-cloak] {
        display: none !important;
    }
</style>
