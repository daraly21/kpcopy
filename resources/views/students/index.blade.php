<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Kelola Siswa</h2>
    </x-slot>

    <h3 class="text-lg font-medium text-gray-700 flex items-center">
                    <span class="iconify text-indigo-600 text-xl mr-2" data-icon="student"></span>
                    Pilih Daftar Siswa per Kelas
                </h3>
    <div class="py-6 container mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($classes as $class)
                <a href="{{ route('admin.siswa.list', $class->id) }}" 
                   class="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:bg-blue-50 hover:shadow-lg transition-all duration-200 ease-in-out flex items-center justify-center">
                    <div class="text-center">
                        <span class="iconify text-3xl text-indigo-600 mb-2 inline-block" data-icon="mdi:google-classroom"></span>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $class->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Lihat Daftar Siswa</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-app-layout>