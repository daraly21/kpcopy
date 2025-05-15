<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 flex items-center">
            <span class="iconify text-indigo-600 text-2xl mr-2" data-icon="mdi:account-group"></span>
            Manajemen User
        </h2>
    </x-slot>

    <div class="py-6" x-data="userManager()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-700 flex items-center">
                    <span class="iconify text-indigo-600 text-xl mr-2" data-icon="mdi:account-multiple"></span>
                    Daftar User
                </h3>
                <button type="button" @click="openAddModal()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-md shadow-sm transition-all duration-200 flex items-center border border-indigo-700">
                    <span class="iconify text-xl mr-2" data-icon="mdi:plus"></span>
                    Tambah User
                </button>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm">
                    <div class="flex items-center">
                        <span class="iconify h-5 w-5 mr-3 text-green-600" data-icon="mdi:check-circle"></span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full  divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Foto Profil</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-56">Nama</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-36">Role</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Kelas</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($users as $index => $user)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($user->profile_picture)
                                            <img src="{{ $user->profile_picture }}" alt="Profile"
                                                 class="rounded-full inline-block h-12 w-12 object-cover border-2 border-indigo-200 hover:border-indigo-400 transition-colors duration-150">
                                        @else
                                            <div class="rounded-full h-12 w-12 bg-gray-100 flex items-center justify-center border-2 border-gray-200">
                                                <span class="iconify text-gray-400 text-2xl" data-icon="mdi:account"></span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-56 break-words">{{ $user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-64 break-words">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-sm max-w-36">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($user->roles as $role)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">{{ $role->name }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-40 break-words">{{ $user->class ? $user->class->name : '-' }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex justify-center space-x-2">
                                            <button @click="openEditModal({{ json_encode($user) }})"
                                                    class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                <span class="iconify text-xl" data-icon="mdi:pencil"></span>
                                            </button>
                                            <button @click="confirmDelete({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                    class="text-red-600 hover:text-red-900" title="Hapus">
                                                <span class="iconify text-xl" data-icon="mdi:trash-can"></span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Tidak ada data user yang tersedia
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Tambah/Edit User -->
        <div x-show="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50" x-cloak>
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4" @click.away="closeModal()">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4 flex items-center" x-text="isEdit ? 'Edit User' : 'Tambah User'">
                        <span class="iconify text-indigo-600 text-2xl mr-2" :data-icon="isEdit ? 'mdi:account-edit' : 'mdi:account-plus'"></span>
                    </h3>

                    <form :action="formAction" method="POST" @submit="validateAndSubmit">
                        @csrf
                        <input type="hidden" name="_method" x-bind:value="isEdit ? 'PUT' : 'POST'">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Kolom Kiri -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="form.name" required
                                           pattern="[a-zA-Z\s]+" title="Hanya huruf dan spasi yang diperbolehkan"
                                           class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150">
                                    <div x-show="errors.name" class="text-red-500 text-xs mt-1" x-text="errors.name"></div>
                                    @error('name')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" x-model="form.email" required
                                           class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150">
                                    <div x-show="errors.email" class="text-red-500 text-xs mt-1" x-text="errors.email"></div>
                                    @error('email')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div x-show="!isEdit">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="password" x-model="form.password" x-bind:required="!isEdit"
                                           class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150">
                                    <div x-show="errors.password" class="text-red-500 text-xs mt-1" x-text="errors.password"></div>
                                    @error('password')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div x-show="!isEdit">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="password_confirmation" x-model="form.password_confirmation" x-bind:required="!isEdit"
                                           class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150">
                                    <div x-show="errors.password_confirmation" class="text-red-500 text-xs mt-1" x-text="errors.password_confirmation"></div>
                                    @error('password_confirmation')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div x-show="isEdit">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                    <input type="password" name="password" x-model="form.password"
                                           class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150">
                                    <div x-show="errors.password" class="text-red-500 text-xs mt-1" x-text="errors.password"></div>
                                    @error('password')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                    <span class="text-xs text-gray-500 mt-1 block">Kosongkan jika tidak ingin mengubah password.</span>
                                </div>

                                <div x-show="isEdit">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" x-model="form.password_confirmation"
                                           class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150">
                                    <div x-show="errors.password_confirmation" class="text-red-500 text-xs mt-1" x-text="errors.password_confirmation"></div>
                                    @error('password_confirmation')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                                    <select name="role" x-model="form.role" required
                                            class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150 bg-white">
                                        <option value="" disabled>Pilih Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <div x-show="errors.role" class="text-red-500 text-xs mt-1" x-text="errors.role"></div>
                                    @error('role')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div x-show="form.role == '2'">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-500">*</span></label>
                                    <select name="class_id" x-model="form.class_id" x-bind:required="form.role == '2'"
                                            class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150 bg-white">
                                        <option value="">Pilih Kelas</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    <div x-show="errors.class_id" class="text-red-500 text-xs mt-1" x-text="errors.class_id"></div>
                                    @error('class_id')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                                    <input type="text" name="profile_picture" x-model="form.profile_picture"
                                           placeholder="Masukkan URL dari ucarecdn"
                                           class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150">
                                    <div x-show="errors.profile_picture" class="text-red-500 text-xs mt-1" x-text="errors.profile_picture"></div>
                                    @error('profile_picture')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                    <span class="text-xs text-gray-500 mt-1 block">Masukkan URL ucarecdn foto profil (opsional)</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                            <button type="button" @click="closeModal()"
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

        <!-- Modal Konfirmasi Hapus -->
        <div x-show="isDeleteModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50" x-cloak>
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full" @click.away="isDeleteModalOpen = false">
                <div class="p-6 text-center">
                    <h3 class="text-xl font-bold mb-4 flex items-center justify-center">
                        <span class="iconify text-indigo-600 text-2xl mr-2" data-icon="mdi:alert"></span>
                        Konfirmasi Hapus
                    </h3>
                    <p class="mb-6 text-gray-700">Apakah Anda yakin ingin menghapus user <span class="font-semibold" x-text="userToDelete.name"></span>?</p>

                    <div class="flex justify-center gap-3">
                        <button type="button" @click="isDeleteModalOpen = false"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-all duration-200 font-medium">Batal</button>
                        <form :action="deleteRoute" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-4 py-2 bg-red-600 border border-red-700 rounded-md text-white hover:bg-red-700 transition-all duration-200 font-medium flex items-center">
                                <span class="iconify text-xl mr-2" data-icon="mdi:trash-can"></span>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function userManager() {
        return {
            isModalOpen: false,
            isEdit: false,
            isDeleteModalOpen: false,
            form: {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                role: '',
                class_id: '',
                profile_picture: ''
            },
            errors: {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                role: '',
                class_id: '',
                profile_picture: ''
            },
            userToDelete: {
                id: null,
                name: ''
            },
            formAction: '{{ route('admin.users.store') }}',
            deleteRoute: '',

            openAddModal() {
                this.isEdit = false;
                this.form = {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    role: '',
                    class_id: '',
                    profile_picture: ''
                };
                this.errors = {
                    미: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    role: '',
                    class_id: '',
                    profile_picture: ''
                };
                this.formAction = '{{ route('admin.users.store') }}';
                this.isModalOpen = true;
            },

            openEditModal(user) {
                this.isEdit = true;
                this.form = {
                    name: user.name,
                    email: user.email,
                    password: '',
                    password_confirmation: '',
                    role: user.roles[0]?.id || '',
                    class_id: user.class_id || '',
                    profile_picture: user.profile_picture || ''
                };
                this.errors = {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    role: '',
                    class_id: '',
                    profile_picture: ''
                };
                this.formAction = '{{ route('admin.users.update', ':id') }}'.replace(':id', user.id);
                this.isModalOpen = true;
            },

            confirmDelete(userId, userName) {
                this.userToDelete = {
                    id: userId,
                    name: userName
                };
                this.deleteRoute = '{{ route('admin.users.destroy', ':id') }}'.replace(':id', userId);
                this.isDeleteModalOpen = true;
            },

            closeModal() {
                this.isModalOpen = false;
                this.isEdit = false;
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
                    profile_picture: ''
                };

                // Validasi nama
                if (!this.form.name || this.form.name.trim() === '') {
                    this.errors.name = 'Nama tidak boleh kosong';
                    valid = false;
                } else if (!/^[a-zA-Z\s]+$/.test(this.form.name)) {
                    this.errors.name = 'Nama hanya boleh berisi huruf dan spasi';
                    valid = false;
                }

                // Validasi email
                if (!this.form.email || this.form.email.trim() === '') {
                    this.errors.email = 'Email tidak boleh kosong';
                    valid = false;
                } else if (!/\S+@\S+\.\S+/.test(this.form.email)) {
                    this.errors.email = 'Email tidak valid';
                    valid = false;
                }

                // Validasi password (hanya untuk tambah atau jika diisi saat edit)
                if (!this.isEdit && (!this.form.password || this.form.password.length < 8)) {
                    this.errors.password = 'Password minimal 8 karakter';
                    valid = false;
                } else if (this.isEdit && this.form.password && this.form.password.length < 8) {
                    this.errors.password = 'Password minimal 8 karakter';
                    valid = false;
                }

                // Validasi konfirmasi password
                if (!this.isEdit && (!this.form.password_confirmation || this.form.password !== this.form.password_confirmation)) {
                    this.errors.password_confirmation = 'Konfirmasi password tidak cocok';
                    valid = false;
                } else if (this.isEdit && this.form.password && this.form.password !== this.form.password_confirmation) {
                    this.errors.password_confirmation = 'Konfirmasi password tidak cocok';
                    valid = false;
                }

                // Validasi role
                if (!this.form.role) {
                    this.errors.role = 'Role tidak boleh kosong';
                    valid = false;
                }

                // Validasi class_id untuk role wali kelas
                if (this.form.role == '2' && !this.form.class_id) {
                    this.errors.class_id = 'Kelas harus dipilih untuk wali kelas';
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                }
            }
        };
    }
    </script>
</x-app-layout>