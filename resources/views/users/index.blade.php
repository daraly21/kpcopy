{{-- resources/views/users/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 flex items-center">
            <span class="iconify text-indigo-600 text-2xl mr-2" data-icon="mdi:account-multiple"></span>
            Kelola Pengguna
        </h2>
    </x-slot>

    <div class="py-6" x-data="userManager()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-700 flex items-center">
                    <span class="iconify text-indigo-600 text-xl mr-2" data-icon="mdi:account-group"></span>
                    Daftar Pengguna
                </h3>
                <button @click="openAddModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-md shadow-sm transition-all duration-200 flex items-center border border-indigo-700">
                    <span class="iconify text-xl mr-2" data-icon="mdi:plus"></span>
                    Tambah Pengguna
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

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm">
                    <div class="flex items-center">
                        <span class="iconify h-5 w-5 mr-3 text-red-600" data-icon="mdi:alert-circle"></span>
                        <div>
                            <ul class="list-disc ml-5">
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
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                    No</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kelas</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($users as $index => $user)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-56 break-words">
                                        {{ $user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $user->roles->first()->name == 'Admin' ? 'Dapodik' : $user->roles->first()->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $user->class->name ?? '-' }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex justify-center space-x-2">
                                            <button
                                                @click="openEditModal({{ json_encode([
                                                    'id' => $user->id,
                                                    'name' => $user->name,
                                                    'email' => $user->email,
                                                    'role' => $user->roles->first()->id ?? '',
                                                    'class_id' => $user->class_id ?? '',
                                                    'subject_id' => $user->subject_id ?? '',
                                                    'profile_picture' => $user->profile_picture ?? '',
                                                ]) }})"
                                                class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                <span class="iconify text-xl" data-icon="mdi:pencil"></span>
                                            </button>
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')"
                                                    class="text-red-600 hover:text-red-900" title="Hapus">
                                                    <span class="iconify text-xl" data-icon="mdi:trash-can"></span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Tidak ada data pengguna yang tersedia
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
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4" @click.away="openModal = false">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4 flex items-center"
                        x-text="isEdit ? 'Edit Pengguna' : 'Tambah Pengguna'">
                        <span class="iconify text-indigo-600 text-2xl mr-2"
                            :data-icon="isEdit ? 'mdi:pencil' : 'mdi:plus'"></span>
                    </h3>

                    <form :action="formAction" method="POST" @submit="validateAndSubmit">
                        @csrf
                        <input type="hidden" name="_method" x-bind:value="isEdit ? 'PUT' : 'POST'">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nama -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="name"
                                    class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                    x-model="form.name" required>
                                <div x-show="errors.name" class="text-red-500 text-xs mt-1" x-text="errors.name"></div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span
                                        class="text-red-500">*</span></label>
                                <input type="email" name="email"
                                    class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                    x-model="form.email" required>
                                <div x-show="errors.email" class="text-red-500 text-xs mt-1" x-text="errors.email">
                                </div>
                            </div>

                            <!-- Password -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"
                                    x-text="isEdit ? 'Password (opsional)' : 'Password *'"></label>
                                <input type="password" name="password"
                                    class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                    x-model="form.password" :required="!isEdit">
                                <div x-show="errors.password" class="text-red-500 text-xs mt-1"
                                    x-text="errors.password"></div>
                            </div>

                            <!-- Konfirmasi Password -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"
                                    x-text="isEdit ? 'Konfirmasi Password (opsional)' : 'Konfirmasi Password *'"></label>
                                <input type="password" name="password_confirmation"
                                    class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                    x-model="form.password_confirmation" :required="!isEdit">
                                <div x-show="errors.password_confirmation" class="text-red-500 text-xs mt-1"
                                    x-text="errors.password_confirmation"></div>
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role <span
                                        class="text-red-500">*</span></label>
                                <select name="role"
                                    class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                    x-model="form.role" required>
                                    <option value="">-- Pilih Role --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">
                                            {{ $role->name == 'Admin' ? 'Dapodik' : $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div x-show="errors.role" class="text-red-500 text-xs mt-1" x-text="errors.role">
                                </div>
                            </div>

                            <!-- Kelas (hanya role 2 / Wali Kelas) -->
                            <div x-show="form.role == 2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                                <select name="class_id"
                                    class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                    x-model="form.class_id">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <div x-show="errors.class_id" class="text-red-500 text-xs mt-1"
                                    x-text="errors.class_id"></div>
                            </div>

                            <!-- Mapel (hanya role 3 / Guru Mapel) -->
                            <div x-show="form.role == 3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                                <select name="subject_id"
                                    class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                    x-model="form.subject_id">
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                                <div x-show="errors.subject_id" class="text-red-500 text-xs mt-1"
                                    x-text="errors.subject_id"></div>
                            </div>

                            <!-- Foto Profil (URL) -->
                            {{-- <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil (URL)</label>
                                <input type="text" name="profile_picture" 
                                       class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                       placeholder="https://ucarecdn.com/..."
                                       x-model="form.profile_picture">
                                <div x-show="errors.profile_picture" class="text-red-500 text-xs mt-1" x-text="errors.profile_picture"></div>
                            </div> --}}
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 mt-6">
                            <button type="button" @click="openModal = false"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-all duration-200 font-medium">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 border border-indigo-700 rounded-md text-white hover:bg-indigo-700 transition-all duration-200 font-medium flex items-center">
                                <span class="iconify text-xl mr-2" data-icon="mdi:content-save"></span>
                                <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function userManager() {
            return {
                openModal: false,
                isEdit: false,
                form: {
                    id: null,
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    role: '',
                    class_id: '',
                    subject_id: '',
                    profile_picture: ''
                },
                errors: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    role: '',
                    class_id: '',
                    subject_id: '',
                    profile_picture: ''
                },
                formAction: '{{ route('admin.users.store') }}',

                openAddModal() {
                    this.isEdit = false;
                    this.form = {
                        id: null,
                        name: '',
                        email: '',
                        password: '',
                        password_confirmation: '',
                        role: '',
                        class_id: '',
                        subject_id: '',
                        profile_picture: ''
                    };
                    this.errors = {
                        name: '',
                        email: '',
                        password: '',
                        password_confirmation: '',
                        role: '',
                        class_id: '',
                        subject_id: '',
                        profile_picture: ''
                    };
                    this.formAction = '{{ route('admin.users.store') }}';
                    this.openModal = true;
                },

                openEditModal(user) {
                    this.isEdit = true;
                    this.form = {
                        id: user.id,
                        name: user.name || '',
                        email: user.email || '',
                        password: '',
                        password_confirmation: '',
                        role: user.role || '',
                        class_id: user.class_id || '',
                        subject_id: user.subject_id || '',
                        profile_picture: user.profile_picture || ''
                    };
                    this.errors = {
                        name: '',
                        email: '',
                        password: '',
                        password_confirmation: '',
                        role: '',
                        class_id: '',
                        subject_id: '',
                        profile_picture: ''
                    };
                    this.formAction = '/admin/users/' + user.id;
                    this.openModal = true;
                },

                validateAndSubmit(e) {
                    let valid = true;
                    this.errors = {
                        name: '',
                        email: '',
                        password: '',
                        password_confirmation: '',
                        role: '',
                        class_id: '',
                        subject_id: '',
                        profile_picture: ''
                    };

                    // Validasi Nama
                    if (!this.form.name || this.form.name.trim() === '') {
                        this.errors.name = 'Nama tidak boleh kosong';
                        valid = false;
                    }

                    // Validasi Email
                    if (!this.form.email || this.form.email.trim() === '') {
                        this.errors.email = 'Email tidak boleh kosong';
                        valid = false;
                    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)) {
                        this.errors.email = 'Format email tidak valid';
                        valid = false;
                    }

                    // Validasi Password (hanya untuk tambah user baru)
                    if (!this.isEdit) {
                        if (!this.form.password || this.form.password.trim() === '') {
                            this.errors.password = 'Password tidak boleh kosong';
                            valid = false;
                        } else if (this.form.password.length < 8) {
                            this.errors.password = 'Password minimal 8 karakter';
                            valid = false;
                        }

                        if (!this.form.password_confirmation || this.form.password_confirmation.trim() === '') {
                            this.errors.password_confirmation = 'Konfirmasi password tidak boleh kosong';
                            valid = false;
                        } else if (this.form.password !== this.form.password_confirmation) {
                            this.errors.password_confirmation = 'Konfirmasi password tidak sesuai';
                            valid = false;
                        }
                    } else if (this.form.password && this.form.password !== this.form.password_confirmation) {
                        this.errors.password_confirmation = 'Konfirmasi password tidak sesuai';
                        valid = false;
                    }

                    // Validasi Role
                    if (!this.form.role || this.form.role === '') {
                        this.errors.role = 'Role harus dipilih';
                        valid = false;
                    }

                    // Validasi Kelas untuk Wali Kelas
                    if (parseInt(this.form.role) === 2 && (!this.form.class_id || this.form.class_id === '')) {
                        this.errors.class_id = 'Wali Kelas harus memilih kelas';
                        valid = false;
                    }

                    // Validasi Mata Pelajaran untuk Guru Mapel
                    if (parseInt(this.form.role) === 3 && (!this.form.subject_id || this.form.subject_id === '')) {
                        this.errors.subject_id = 'Guru Mapel harus memilih mata pelajaran';
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
