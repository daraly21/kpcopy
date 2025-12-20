<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Kelola Tahun Ajaran</h2>
    </x-slot>

    <div class="py-6" x-data="academicYearManager()">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-700 flex items-center">
                    <span class="iconify text-indigo-600 text-xl mr-2" data-icon="mdi:calendar-range"></span>
                    Daftar Tahun Ajaran
                </h3>
                <button @click="openModal = true"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-md shadow-sm transition-all duration-200 flex items-center border border-indigo-700">
                    <span class="iconify text-xl mr-2" data-icon="mdi:plus"></span>
                    Tambah Tahun Ajaran
                </button>
            </div>

            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm">
                    <div class="flex items-center">
                        <span class="iconify h-5 w-5 mr-3 text-green-600" data-icon="mdi:check-circle"></span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm">
                    <div class="flex items-center">
                        <span class="iconify h-5 w-5 mr-3 text-red-600" data-icon="mdi:alert-circle"></span>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm">
                    <div class="flex items-start">
                        <span class="iconify h-5 w-5 mr-3 text-red-600 mt-0.5" data-icon="mdi:alert-circle"></span>
                        <div>
                            <p class="font-medium mb-2">Terjadi kesalahan:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                    No
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tahun Ajaran
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($academicYears as $i => $year)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $i + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $year->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @if ($year->is_active)
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <span class="iconify mr-1" data-icon="mdi:check-circle"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        @if (!$year->is_active)
                                            <div class="flex items-center justify-center gap-3">
                                                <form action="{{ route('admin.academic-years.setActive', $year->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        onclick="return confirm('Aktifkan tahun ajaran {{ $year->name }}?')"
                                                        class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                                        title="Aktifkan">
                                                        <span class="iconify text-xl" data-icon="mdi:power"></span>
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.academic-years.destroy', $year->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus tahun ajaran {{ $year->name }}? Data tidak dapat dikembalikan.')"
                                                        class="text-red-600 hover:text-red-900 transition-colors"
                                                        title="Hapus">
                                                        <span class="iconify text-xl"
                                                            data-icon="mdi:trash-can-outline"></span>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-gray-400 flex items-center justify-center">
                                                <span class="iconify text-xl" data-icon="mdi:check"></span>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center">
                                        <span class="iconify text-4xl text-gray-400 mb-2"
                                            data-icon="mdi:calendar-blank"></span>
                                        <p class="text-sm text-gray-500 mt-2">
                                            Belum ada tahun ajaran yang terdaftar
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


        </div>

        <!-- Modal Tambah -->
        <div x-show="openModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
            x-cloak>
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4" @click.away="openModal = false">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <span class="iconify text-indigo-600 text-2xl mr-2" data-icon="mdi:plus"></span>
                        Tambah Tahun Ajaran
                    </h3>

                    <form action="{{ route('admin.academic-years.store') }}" method="POST">
                        @csrf

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun Ajaran <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" placeholder="2024/2025" pattern="\d{4}/\d{4}"
                                title="Format harus YYYY/YYYY (contoh: 2024/2025)"
                                class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                required>
                            <p class="text-xs text-gray-500 mt-1">Format: YYYY/YYYY (contoh: 2024/2025)</p>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                            <button type="button" @click="openModal = false"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-all duration-200 font-medium">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 border border-indigo-700 rounded-md text-white hover:bg-indigo-700 transition-all duration-200 font-medium flex items-center">
                                <span class="iconify text-xl mr-2" data-icon="mdi:content-save"></span>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function academicYearManager() {
            return {
                openModal: false
            }
        }
    </script>
</x-app-layout>
