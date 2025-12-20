<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 flex items-center">
            <span class="iconify text-indigo-600 text-2xl mr-2" data-icon="mdi:google-classroom"></span>
            Siswa {{ $class->name }} - {{ $selectedYear->name }}
        </h2>
    </x-slot>

    <div class="py-6" x-data="studentManager()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Info Tahun Ajaran & Tombol Kembali -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="iconify text-indigo-600 text-xl" data-icon="mdi:calendar-range"></span>
                        <span class="text-sm font-medium text-gray-700">Tahun Ajaran:</span>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                            {{ $selectedYear->name }}
                        </span>
                    </div>

                    <a href="{{ route('admin.siswa.kelas', ['academic_year_id' => $selectedYear->id]) }}"
                        class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center">
                        <span class="iconify text-lg mr-1" data-icon="mdi:arrow-left"></span>
                        Kembali ke Daftar Kelas
                    </a>
                </div>
            </div>

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-700 flex items-center">
                    <span class="iconify text-indigo-600 text-xl mr-2" data-icon="mdi:account-group"></span>
                    Daftar Siswa ({{ $students->count() }} siswa)
                </h3>
                <div class="flex gap-2">
                    {{-- <a href="{{ route('admin.siswa.promotion', ['class' => $class->id, 'academic_year_id' => $selectedYear->id]) }}"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-md shadow-sm transition-all duration-200 flex items-center border border-green-700">
                        <span class="iconify text-xl mr-2" data-icon="mdi:arrow-up-bold-box-outline"></span>
                        Naik Kelas
                    </a> --}}
                    <button @click="openAddModal()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-md shadow-sm transition-all duration-200 flex items-center border border-indigo-700">
                        <span class="iconify text-xl mr-2" data-icon="mdi:plus"></span>
                        Tambah Siswa
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

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm"
                    x-data="{ show: true }" x-show="show">
                    <div class="flex items-start justify-between">
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
                        <button @click="show = false" class="text-red-600 hover:text-red-800 ml-4">
                            <span class="iconify text-xl" data-icon="mdi:close"></span>
                        </button>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        @if (old('_method') === 'PUT')
                            const studentData = {
                                id: {{ old('id', 0) }},
                                name: "{{ old('name') }}",
                                nis: "{{ old('nis') }}",
                                gender: "{{ old('gender') }}",
                                parent_name: "{{ old('parent_name') }}",
                                birth_place: "{{ old('birth_place') }}",
                                birth_date: "{{ old('birth_date') }}",
                                parent_phone: "{{ old('parent_phone_display', old('parent_phone')) }}"
                            };
                            setTimeout(() => {
                                Alpine.$data(document.querySelector('[x-data]')).openEditModal(studentData);
                            }, 100);
                        @else
                            setTimeout(() => {
                                const component = Alpine.$data(document.querySelector('[x-data]'));
                                component.openAddModal();
                                component.form = {
                                    name: "{{ old('name') }}",
                                    nis: "{{ old('nis') }}",
                                    gender: "{{ old('gender') }}",
                                    parent_name: "{{ old('parent_name') }}",
                                    birth_place: "{{ old('birth_place') }}",
                                    birth_date: "{{ old('birth_date') }}",
                                    parent_phone: "{{ old('parent_phone_display', old('parent_phone')) }}"
                                };
                            }, 100);
                        @endif
                    });
                </script>
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
                                    Nama</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    NIS</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                                    Jenis Kelamin</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                    Tempat Lahir</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Tanggal Lahir</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                    Orang Tua</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                    No. HP Orang Tua</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($students as $i => $student)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $i + 1 }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-56 break-words">
                                        {{ $student->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-32 break-words">
                                        {{ $student->nis }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div class="whitespace-nowrap">
                                            @if ($student->gender == 'L')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Laki-laki
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                                    Perempuan
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-40 break-words">
                                        {{ $student->birth_place ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-32 break-words">
                                        {{ $student->birth_date ? date('d-m-Y', strtotime($student->birth_date)) : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-48 break-words">
                                        {{ $student->parent_name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-40 break-words">
                                        {{ $student->parent_phone ? '0' . substr($student->parent_phone, 2) : '-' }}
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex justify-center space-x-2">
                                            <button @click="openEditModal({{ json_encode($student) }})"
                                                class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                <span class="iconify text-xl" data-icon="mdi:pencil"></span>
                                            </button>

                                            <form action="{{ route('admin.siswa.destroy', $student->id) }}"
                                                method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini?')"
                                                    class="text-red-600 hover:text-red-900" title="Hapus">
                                                    <span class="iconify text-xl" data-icon="mdi:trash-can"></span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-8 text-center">
                                        <span class="iconify text-4xl text-gray-400 mb-2"
                                            data-icon="mdi:account-off"></span>
                                        <p class="text-sm text-gray-500 mt-2">
                                            Belum ada siswa di kelas ini untuk tahun ajaran {{ $selectedYear->name }}
                                        </p>
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
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4" @click.away="openModal = false">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4 flex items-center"
                        x-text="isEdit ? 'Edit Siswa' : 'Tambah Siswa'">
                        <span class="iconify text-indigo-600 text-2xl mr-2"
                            :data-icon="isEdit ? 'mdi:pencil' : 'mdi:plus'"></span>
                    </h3>

                    <form :action="formAction" method="POST" @submit="validateAndSubmit">
                        @csrf
                        <input type="hidden" name="_method" x-bind:value="isEdit ? 'PUT' : 'POST'">
                        <input type="hidden" name="class_id" value="{{ $class->id }}">
                        <input type="hidden" name="academic_year_id" value="{{ $selectedYear->id }}">
                        <input type="hidden" name="id" x-model="form.id">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Column 1 -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="name" pattern="[a-zA-Z\s]+"
                                        title="Hanya huruf dan spasi yang diperbolehkan" placeholder="Nama Siswa"
                                        class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                        x-model="form.name" required>
                                    <div x-show="errors.name" class="text-red-500 text-xs mt-1" x-text="errors.name">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">NIS <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" name="nis" pattern="\d*"
                                        title="Hanya angka yang diperbolehkan"
                                        class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                        x-model="form.nis" required>
                                    <div x-show="errors.nis" class="text-red-500 text-xs mt-1" x-text="errors.nis">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span
                                            class="text-red-500">*</span></label>
                                    <select name="gender"
                                        class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150 bg-white"
                                        x-model="form.gender" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                    <div x-show="errors.gender" class="text-red-500 text-xs mt-1"
                                        x-text="errors.gender"></div>
                                </div>
                            </div>

                            <!-- Column 2 -->
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="birth_place"
                                            class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                            x-model="form.birth_place" required>
                                        <div x-show="errors.birth_place" class="text-red-500 text-xs mt-1"
                                            x-text="errors.birth_place"></div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span
                                                class="text-red-500">*</span></label>
                                        <input type="date" name="birth_date"
                                            class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                            x-model="form.birth_date" required>
                                        <div x-show="errors.birth_date" class="text-red-500 text-xs mt-1"
                                            x-text="errors.birth_date"></div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Orang Tua <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="parent_name" pattern="[a-zA-Z\s]+"
                                        title="Hanya huruf dan spasi yang diperbolehkan"
                                        class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                        x-model="form.parent_name" required>
                                    <div x-show="errors.parent_name" class="text-red-500 text-xs mt-1"
                                        x-text="errors.parent_name"></div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor HP Orang Tua
                                        <span class="text-red-500">*</span></label>
                                    <input type="number" name="parent_phone" pattern="\d*"
                                        title="Hanya angka yang diperbolehkan"
                                        class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                        x-model="form.parent_phone" placeholder="083113355381"
                                        @input="formatPhoneNumber" required>
                                    <div x-show="errors.parent_phone" class="text-red-500 text-xs mt-1"
                                        x-text="errors.parent_phone"></div>
                                    <span class="text-xs text-gray-500 mt-1 block">Format: Tulis nomor tanpa awalan +62
                                        (contoh: 083113355381)</span>
                                </div>
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
        function studentManager() {
            return {
                openModal: false,
                isEdit: false,
                form: {
                    id: '',
                    name: '',
                    nis: '',
                    gender: '',
                    parent_name: '',
                    birth_place: '',
                    birth_date: '',
                    parent_phone: ''
                },
                errors: {
                    name: '',
                    nis: '',
                    gender: '',
                    parent_name: '',
                    birth_place: '',
                    birth_date: '',
                    parent_phone: ''
                },
                formAction: '{{ route('admin.siswa.store') }}',

                openAddModal() {
                    this.isEdit = false;
                    this.form = {
                        id: '',
                        name: '',
                        nis: '',
                        gender: '',
                        parent_name: '',
                        birth_place: '',
                        birth_date: '',
                        parent_phone: ''
                    };
                    this.errors = {
                        name: '',
                        nis: '',
                        gender: '',
                        parent_name: '',
                        birth_place: '',
                        birth_date: '',
                        parent_phone: ''
                    };
                    this.formAction = '{{ route('admin.siswa.store') }}';
                    this.openModal = true;
                },

                openEditModal(student) {
                    this.isEdit = true;
                    this.form = {
                        ...student
                    };

                    if (this.form.parent_phone && this.form.parent_phone.startsWith('62')) {
                        this.form.parent_phone = '0' + this.form.parent_phone.substring(2);
                    }

                    this.errors = {
                        name: '',
                        nis: '',
                        gender: '',
                        parent_name: '',
                        birth_place: '',
                        birth_date: '',
                        parent_phone: ''
                    };
                    this.formAction = '{{ route('admin.siswa.update', ':id') }}'.replace(':id', student.id);
                    this.openModal = true;
                },



                formatPhoneNumber() {
                    let phone = this.form.parent_phone.replace(/\D/g, '');
                    if (phone.startsWith('62')) {
                        phone = '0' + phone.substring(2);
                    }
                    this.form.parent_phone = phone;
                },

                formatPhoneNumberForStorage() {
                    let phoneNumber = this.form.parent_phone.replace(/\D/g, '');

                    if (phoneNumber.startsWith('0')) {
                        phoneNumber = '62' + phoneNumber.substring(1);
                    } else if (!phoneNumber.startsWith('62') && phoneNumber.length > 0) {
                        phoneNumber = '62' + phoneNumber;
                    }

                    return phoneNumber;
                },

                validateAndSubmit(e) {
                    let valid = true;
                    this.errors = {
                        name: '',
                        nis: '',
                        gender: '',
                        parent_name: '',
                        birth_place: '',
                        birth_date: '',
                        parent_phone: ''
                    };

                    if (!this.form.name || this.form.name.trim() === '') {
                        this.errors.name = 'Nama tidak boleh kosong';
                        valid = false;
                    } else if (!/^[a-zA-Z\s]+$/.test(this.form.name)) {
                        this.errors.name = 'Nama hanya boleh berisi huruf dan spasi';
                        valid = false;
                    }

                    if (!this.form.nis || this.form.nis.trim() === '') {
                        this.errors.nis = 'NIS tidak boleh kosong';
                        valid = false;
                    } else if (!/^\d+$/.test(this.form.nis)) {
                        this.errors.nis = 'NIS hanya boleh berisi angka';
                        valid = false;
                    } else if (this.form.nis.length > 10) {
                        this.errors.nis = 'NIS maksimal 10 digit';
                        valid = false;
                    }

                    if (!this.form.gender) {
                        this.errors.gender = 'Jenis kelamin harus dipilih';
                        valid = false;
                    }

                    if (!this.form.parent_name || this.form.parent_name.trim() === '') {
                        this.errors.parent_name = 'Nama orang tua tidak boleh kosong';
                        valid = false;
                    } else if (!/^[a-zA-Z\s]+$/.test(this.form.parent_name)) {
                        this.errors.parent_name = 'Nama orang tua hanya boleh berisi huruf dan spasi';
                        valid = false;
                    }

                    if (!this.form.birth_place || this.form.birth_place.trim() === '') {
                        this.errors.birth_place = 'Tempat lahir tidak boleh kosong';
                        valid = false;
                    }

                    if (!this.form.birth_date) {
                        this.errors.birth_date = 'Tanggal lahir tidak boleh kosong';
                        valid = false;
                    }

                    if (!this.form.parent_phone || this.form.parent_phone.trim() === '') {
                        this.errors.parent_phone = 'Nomor HP tidak boleh kosong';
                        valid = false;
                    } else {
                        const cleanedPhone = this.form.parent_phone.replace(/\D/g, '');
                        if (!/^\d+$/.test(cleanedPhone)) {
                            this.errors.parent_phone = 'Nomor HP hanya boleh berisi angka';
                            valid = false;
                        } else if (cleanedPhone.length < 10 || cleanedPhone.length > 13) {
                            this.errors.parent_phone = 'Nomor HP harus antara 10-13 digit';
                            valid = false;
                        }
                    }

                    if (valid) {
                        const formattedPhone = this.formatPhoneNumberForStorage();

                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'parent_phone';
                        hiddenInput.value = formattedPhone;

                        e.target.appendChild(hiddenInput);

                        const visibleInput = e.target.querySelector('input[name="parent_phone"]');
                        if (visibleInput) {
                            visibleInput.name = 'parent_phone_display';
                        }
                    } else {
                        e.preventDefault();
                    }
                }
            }
        }
    </script>
</x-app-layout>
