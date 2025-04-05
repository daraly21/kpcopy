<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen User') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-1">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                <button type="button" @click="$dispatch('close')">
                                    <span class="iconify text-green-500 h-6 w-6" data-icon="mdi:close"></span>
                                </button>
                            </span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Daftar User</h3>
                        <a href="{{ route('admin.users.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center">
                            <span class="iconify mr-2" data-icon="mdi:plus"></span>
                            Tambah User
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="usersTable" class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        No</th>
                                    <th
                                        class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Foto Profil</th>
                                    <th
                                        class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Nama</th>
                                    <th
                                        class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Role</th>
                                    <th
                                        class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Kelas</th>
                                    <th
                                        class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $index => $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                                            {{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200 text-center">
                                            @if ($user->profile_picture)
                                                <img src="{{ $user->profile_picture }}" alt="Profile"
                                                    class="rounded-full inline-block h-12 w-12 object-cover">
                                            @else
                                                <div
                                                    class="rounded-full inline-block h-12 w-12 bg-gray-200 flex items-center justify-center">
                                                    <span class="iconify text-gray-400 text-xl"
                                                        data-icon="mdi:account"></span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                                            {{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                                            {{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                                            @foreach ($user->roles as $role)
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                                            {{ $user->class ? $user->class->name : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200 text-center">

                                            <!-- Edit -->
                                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                                class="text-yellow-600 hover:text-yellow-900 mr-2">
                                                <span class="iconify text-lg" data-icon="mdi:pencil"></span>
                                            </a>

                                            <!-- Delete -->
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Yakin ingin menghapus user {{ $user->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <span class="iconify text-lg" data-icon="mdi:trash"></span>
                                                </button>
                                            </form>

                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('usersTable', () => ({
                    init() {
                        $('#usersTable').DataTable({
                            responsive: true
                        });
                    },
                    showUserDetails(userId) {
                        fetch(`/admin/users/${userId}`)
                            .then(res => res.json())
                            .then(data => {
                                const user = data.user;
                                document.getElementById('showUserName').textContent = user.name;
                                document.getElementById('showUserEmail').textContent = user.email;
                                document.getElementById('showUserRole').textContent = user.roles[0]
                                    .name;
                                document.getElementById('showUserClass').textContent = user.class ? user
                                    .class.name : '-';
                                document.getElementById('showUserImage').src = user.profile_picture ?
                                    `/storage/${user.profile_picture}` :
                                    '/img/default-profile.png';
                                $dispatch('open-modal', 'show-user-modal');
                            }).catch(err => {
                                console.error(err);
                                alert('Gagal mendapatkan data user');
                            });
                    },
                    editUser(userId) {
                        fetch(`/admin/users/${userId}`)
                            .then(res => res.json())
                            .then(data => {
                                const user = data.user;
                                document.getElementById('editUserId').value = userId;
                                document.getElementById('editName').value = user.name;
                                document.getElementById('editEmail').value = user.email;
                                document.getElementById('editRole').value = user.roles[0].id;
                                if (user.roles[0].id == 2) {
                                    document.querySelector('.edit-class-section').style.display =
                                        'block';
                                    document.getElementById('editClassId').value = user.class ? user
                                        .class.id : '';
                                } else {
                                    document.querySelector('.edit-class-section').style.display =
                                        'none';
                                }
                                document.getElementById('editUserForm').action =
                                    `/admin/users/${userId}`;
                                $dispatch('open-modal', 'edit-user-modal');
                            }).catch(err => {
                                console.error(err);
                                alert('Gagal mendapatkan data user untuk edit');
                            });
                    },
                    handleRoleChange(event, type) {
                        const value = event.target.value;
                        const selector = type === 'create' ? '.class-section' : '.edit-class-section';
                        document.querySelector(selector).style.display = value == 2 ? 'block' : 'none';
                    }
                }));
            });
        </script>
    @endpush
</x-app-layout>
