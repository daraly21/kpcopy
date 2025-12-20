<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Kelola Siswa</h2>
    </x-slot>

    <div class="py-6 container mx-auto px-4">
        <!-- Filter Tahun Ajaran & Tombol Kelola -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3 flex-1">
                    <span class="iconify text-indigo-600 text-xl" data-icon="mdi:calendar-range"></span>
                    <label class="text-sm font-medium text-gray-700">Tahun Ajaran:</label>

                    <form method="GET" action="{{ route('admin.siswa.kelas') }}" class="flex items-center gap-3">
                        <select name="academic_year_id"
                            class="border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150 bg-white"
                            onchange="this.form.submit()">
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}"
                                    {{ $selectedYear->id == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                {{-- 
                <a href="{{ route('admin.academic-years.index') }}"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow-sm transition-all duration-200 flex items-center border border-indigo-700 text-sm">
                    <span class="iconify text-lg mr-2" data-icon="mdi:cog"></span>
                    Kelola Tahun Ajaran
                </a>

                <form action="{{ route('admin.siswa.promote-all') }}" method="POST"
                    onsubmit="return confirm('Apakah Anda yakin ingin menaikkan SEMUA siswa ke tahun ajaran berikutnya? Siswa Kelas 6 akan diluluskan.')">
                    @csrf
                    <input type="hidden" name="from_year_id" value="{{ $selectedYear->id }}">
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow-sm transition-all duration-200 flex items-center border border-green-700 text-sm">
                        <span class="iconify text-lg mr-2" data-icon="mdi:arrow-up-bold-box-outline"></span>
                        Naikkan Semua Siswa
                    </button>
                </form> --}}


            </div>
        </div>

        <h3 class="text-lg font-medium text-gray-700 flex items-center mb-4">
            <span class="iconify text-indigo-600 text-xl mr-2" data-icon="mdi:account-group"></span>
            Pilih Daftar Siswa per Kelas - {{ $selectedYear->name }}
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($classes as $class)
                <a href="{{ route('admin.siswa.list', ['class' => $class->id, 'academic_year_id' => $selectedYear->id]) }}"
                    class="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:bg-blue-50 hover:shadow-lg transition-all duration-200 ease-in-out">
                    <div class="text-center">
                        <span class="iconify text-3xl text-indigo-600 mb-2 inline-block"
                            data-icon="mdi:google-classroom"></span>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $class->name }}</h3>
                        <div class="mt-2">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $class->student_classes_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                <span class="iconify mr-1" data-icon="mdi:account-multiple"></span>
                                {{ $class->student_classes_count }} Siswa
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Klik untuk lihat detail</p>
                    </div>
                </a>
            @endforeach
        </div>

        @if ($classes->isEmpty())
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-6 rounded-md">
                <div class="flex">
                    <span class="iconify text-yellow-400 h-5 w-5 mr-3" data-icon="mdi:alert"></span>
                    <p class="text-sm text-yellow-700">
                        Belum ada kelas yang tersedia. Silakan tambah kelas terlebih dahulu di menu <a
                            href="{{ route('admin.kelas.index') }}" class="font-medium underline">Kelola Kelas</a>.
                    </p>
                </div>
            </div>
        @endif

        @if ($classes->isNotEmpty() && $classes->sum('student_classes_count') == 0)
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mt-6 rounded-md">
                <div class="flex">
                    <span class="iconify text-blue-400 h-5 w-5 mr-3" data-icon="mdi:information"></span>
                    <p class="text-sm text-blue-700">
                        Belum ada siswa yang terdaftar untuk tahun ajaran <strong>{{ $selectedYear->name }}</strong>.
                        Silakan pilih kelas dan tambahkan siswa.
                    </p>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
