<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
            <span class="iconify text-indigo-600 text-2xl mr-2" data-icon="mdi:account-group"></span>
            {{ __('Manajemen User') }}
        </h2>
    </x-slot>

    <div class="py-6 container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200">
            <div class="p-6">
                <!-- Success Message -->
                @if (session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 relative shadow-sm" role="alert">
                        <div class="flex items-center">
                            <span class="iconify text-green-600 h-5 w-5 mr-2" data-icon="mdi:check-circle"></span>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                        <button type="button" @click="$dispatch('close')" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <span class="iconify text-green-600 h-5 w-5" data-icon="mdi:close"></span>
                        </button>
                    </div>
                @endif

                <!-- Header dan Tombol Tambah -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="iconify text-indigo-600 text-xl mr-2" data-icon="mdi:account-multiple"></span>
                        Daftar User
                    </h3>
                    <a href="{{ route('admin.users.create') }}"
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center border border-indigo-700 transition-all duration-200 hover:shadow-md">
                        <span class="iconify text-xl mr-2" data-icon="mdi:plus"></span>
                        Tambah User
                    </a>
                </div>

                <!-- Tabel -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider w-12 border border-gray-200">No</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider w-20 border border-gray-200">Foto Profil</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider w-56 border border-gray-200">Nama</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider w-64 border border-gray-200">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider w-36 border border-gray-200">Role</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider w-40 border border-gray-200">Kelas</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider w-24 border border-gray-200">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($users as $index => $user)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border border-gray-200">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center border border-gray-200">
                                        @if ($user->profile_picture)
                                            <img src="{{ $user->profile_picture }}" alt="Profile"
                                                 class="rounded-full inline-block h-12 w-12 object-cover border-2 border-indigo-200 hover:border-indigo-400 transition-colors duration-150">
                                        @else
                                            <div class="rounded-full h-12 w-12 bg-gray-100 flex items-center justify-center border-2 border-gray-200">
                                                <span class="iconify text-gray-400 text-2xl" data-icon="mdi:account"></span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-56 break-words border border-gray-200">{{ $user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-64 break-words border border-gray-200">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-sm max-w-36 border border-gray-200">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($user->roles as $role)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800 border border-indigo-200 hover:bg-indigo-200 transition-colors duration-150 whitespace-nowrap">{{ $role->name }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-40 break-words border border-gray-200">{{ $user->class ? $user->class->name : '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center border border-gray-200">
                                        <div class="flex justify-center space-x-3">
                                            <!-- Edit -->
                                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                               class="text-indigo-600 hover:text-indigo-900 flex items-center">
                                                <span class="iconify text-xl" data-icon="mdi:pencil"></span>
                                            </a>
                                            <!-- Delete -->
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus user {{ $user->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 flex items-center">
                                                    <span class="iconify text-xl" data-icon="mdi:trash-can"></span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 border border-gray-200">
                                        Tidak ada data user yang tersedia
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>