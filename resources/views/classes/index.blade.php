<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 flex items-center">
            <span class="iconify text-indigo-600 text-2xl mr-2" data-icon="mdi:google-classroom"></span>
            Kelola Kelas
        </h2>
    </x-slot>

    <div class="py-6" x-data="classManager()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-700 flex items-center">
                    <span class="iconify text-indigo-600 text-xl mr-2" data-icon="mdi:account-group"></span>
                    Daftar Kelas ({{ $selectedYear->name }})
                </h3>

                <div class="flex items-center gap-4">
                    <form method="GET" action="{{ route('admin.kelas.index') }}" class="flex items-center gap-2">
                        <select name="academic_year_id"
                            class="border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150 bg-white"
                            onchange="this.form.submit()">
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}"
                                    {{ $selectedYear->id == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <button @click="openAddModal()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-md shadow-sm transition-all duration-200 flex items-center border border-indigo-700">
                        <span class="iconify text-xl mr-2" data-icon="mdi:plus"></span>
                        Tambah Kelas
                    </button>
                </div>
            </div>

            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm">
                    <div class="flex items-center">
                        <span class="iconify h-5 w-5 mr-3 text-green-600" data-icon="mdi:check-circle"></span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                    No</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-56">
                                    Nama Kelas</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Jumlah Siswa</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Wali Kelas</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($classes as $index => $kelas)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-56 break-words">
                                        {{ $kelas->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-32 break-words">
                                        {{ $kelas->student_classes_count }} Siswa</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 break-words">
                                        {{ $kelas->waliKelas?->name ?? '-' }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex justify-center space-x-2">

                                            <button
                                                @click="openEditModal({ id: {{ $kelas->id }}, name: '{{ addslashes($kelas->name) }}' })"
                                                class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                <span class="iconify text-xl" data-icon="mdi:pencil"></span>
                                            </button>
                                            <form action="{{ route('admin.kelas.destroy', $kelas->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus kelas ini?')"
                                                    class="text-red-600 hover:text-red-900" title="Hapus">
                                                    <span class="iconify text-xl" data-icon="mdi:trash-can"></span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Tidak ada data kelas yang tersedia
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Tambah/Edit -->
        <div x-show="openModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
            x-cloak>
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4" @click.away="openModal = false">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4 flex items-center"
                        x-text="isEdit ? 'Edit Kelas' : 'Tambah Kelas'">
                        <span class="iconify text-indigo-600 text-2xl mr-2"
                            :data-icon="isEdit ? 'mdi:pencil' : 'mdi:plus'"></span>
                    </h3>

                    <form :action="formAction" method="POST" @submit="validateAndSubmit">
                        @csrf
                        <input type="hidden" name="_method" x-bind:value="isEdit ? 'PUT' : 'POST'">

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="name"
                                    class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                    x-model="form.name" required>
                                <div x-show="errors.name" class="text-red-500 text-xs mt-1" x-text="errors.name"></div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                            <button type="button" @click="openModal = false"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-all duration-200 font-medium">Batal</button>
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
        function classManager() {
            return {
                openModal: false,
                isEdit: false,
                form: {
                    name: ''
                },
                errors: {
                    name: ''
                },
                formAction: '{{ route('admin.kelas.store') }}',

                openAddModal() {
                    this.isEdit = false;
                    this.form = {
                        name: ''
                    };
                    this.errors = {
                        name: ''
                    };
                    this.formAction = '{{ route('admin.kelas.store') }}';
                    this.openModal = true;
                },

                openEditModal(kelas) {
                    this.isEdit = true;
                    this.form = {
                        name: kelas.name
                    };
                    this.errors = {
                        name: ''
                    };
                    this.formAction = '/admin/kelas/' + kelas.id;
                    this.openModal = true;
                },

                validateAndSubmit(e) {
                    let valid = true;
                    this.errors = {
                        name: ''
                    };

                    if (!this.form.name || this.form.name.trim() === '') {
                        this.errors.name = 'Nama kelas tidak boleh kosong';
                        valid = false;
                    }

                    if (!valid) {
                        e.preventDefault();
                    }
                }
            }
        }
    </script>
</x-app-layout>
