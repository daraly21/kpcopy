<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
            <span class="iconify text-indigo-600 text-2xl mr-2" data-icon="mdi:google-classroom"></span>
            {{ __('Manajemen Kelas') }}
        </h2>
    </x-slot>

    <div class="py-6 container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded-lg border border-gray-200 p-6">
            <h2 class="text-xl font-bold mb-6 flex items-center">
                <span class="iconify text-indigo-600 text-2xl mr-2" data-icon="mdi:information"></span>
                Detail Kelas: {{ $kelas->name }}
            </h2>

            <div class="mb-6">
                <p class="text-sm">
                    <strong class="text-gray-700 font-semibold">Wali Kelas:</strong> 
                    <span class="text-gray-900 max-w-48 break-words">{{ $kelas->waliKelas?->name ?? '-' }}</span>
                </p>
            </div>

            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <span class="iconify text-indigo-600 text-xl mr-2" data-icon="mdi:account-group"></span>
                Daftar Siswa
            </h3>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12 border border-gray-200">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-56 border border-gray-200">Nama</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32 border border-gray-200">NIS</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28 border border-gray-200">Jenis Kelamin</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40 border border-gray-200">Tempat Lahir</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32 border border-gray-200">Tanggal Lahir</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48 border border-gray-200">Orang Tua</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40 border border-gray-200">No. HP Orang Tua</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($kelas->students as $index => $siswa)
                            <tr class="hover:bg-gray-50 transition-colors duration-150 border-b border-gray-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-56 break-words">{{ $siswa->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-32 break-words">{{ $siswa->nis }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-28">
                                    @if($siswa->gender == 'L')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Laki-laki</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-pink-100 text-pink-800">Perempuan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-40 break-words">{{ $siswa->birth_place ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-32 break-words">
                                    {{ $siswa->birth_date ? date('d-m-Y', strtotime($siswa->birth_date)) : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-48 break-words">{{ $siswa->parent_name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-40 break-words">
                                    {{ $siswa->parent_phone ? '0'.substr($siswa->parent_phone, 2) : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 border-b border-gray-200">
                                    Belum ada siswa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.kelas.index') }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg border border-gray-700 hover:bg-gray-700 hover:shadow-md transition-all duration-200 flex items-center">
                    <span class="iconify text-xl mr-2" data-icon="mdi:arrow-left"></span>
                    Kembali ke Daftar Kelas
                </a>
            </div>
        </div>
    </div>
</x-app-layout>