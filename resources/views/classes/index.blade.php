<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Kelas') }}
        </h2>
    </x-slot>

    <div x-data="{
        showModal: false,
        showEditModal: false,
        editId: null,
        editName: '',
    }" class="container mx-auto p-4">
        
        <!-- Tombol Tambah -->
        <button @click="showModal = true" 
            class="bg-green-600 text-white px-4 py-2 rounded-lg mb-4 hover:bg-green-700 flex items-center border border-green-700 transition-all duration-200">
            <span class="iconify text-2xl mr-2" data-icon="mdi:plus"></span>
            Tambah Kelas
        </button>

        <!-- Tabel -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200 w-12">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Nama Kelas</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200 w-32">Jumlah Siswa</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Wali Kelas</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200 w-48">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($classes as $index => $kelas)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border border-gray-200">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border border-gray-200">{{ $kelas->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border border-gray-200">{{ $kelas->students_count }} Siswa</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border border-gray-200">{{ $kelas->waliKelas?->name ?? '-' }}</td>
                                <td class="px-2 py-4 whitespace-nowrap text-sm font-medium border border-gray-200">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('admin.kelas.show', $kelas->id) }}"
                                           class="text-indigo-600 hover:text-indigo-900" title="Lihat">
                                            <span class="iconify text-xl" data-icon="mdi:eye"></span>
                                        </a>
                                        <button 
                                            @click="
                                                showEditModal = true;
                                                editId = {{ $kelas->id }};
                                                editName = '{{ addslashes($kelas->name) }}';
                                            "
                                            class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                            <span class="iconify text-xl" data-icon="mdi:pencil"></span>
                                        </button>
                                        <form action="{{ route('admin.kelas.destroy', $kelas->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Yakin hapus kelas ini?')"
                                                    class="text-red-600 hover:text-red-900" title="Hapus">
                                                <span class="iconify text-xl" data-icon="mdi:delete"></span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Tambah -->
        <div x-show="showModal" x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div @click.away="showModal = false" 
                 class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md border border-gray-200">
                <h2 class="text-lg font-semibold mb-4 flex items-center">
                    <span class="iconify text-indigo-600 text-2xl mr-2" data-icon="mdi:plus"></span>
                    Tambah Kelas
                </h2>
                <form action="{{ route('admin.kelas.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas</label>
                        <input type="text" name="name" required
                               class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="showModal = false" 
                                class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 border border-gray-300 transition-all duration-200">Batal</button>
                        <button type="submit" 
                                class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 border border-indigo-700 transition-all duration-200 flex items-center">
                            <span class="iconify text-xl mr-2" data-icon="mdi:content-save"></span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit -->
        <div x-show="showEditModal" x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div @click.away="showEditModal = false" 
                 class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md border border-gray-200">
                <h2 class="text-lg font-semibold mb-4 flex items-center">
                    <span class="iconify text-indigo-600 text-2xl mr-2" data-icon="mdi:pencil"></span>
                    Edit Kelas
                </h2>
                <form :action="'/admin/kelas/' + editId" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas</label>
                        <input type="text" name="name" x-model="editName" required
                               class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="showEditModal = false" 
                                class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 border border-gray-300 transition-all duration-200">Batal</button>
                        <button type="submit" 
                                class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 border border-indigo-700 transition-all duration-200 flex items-center">
                            <span class="iconify text-xl mr-2" data-icon="mdi:content-save"></span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>