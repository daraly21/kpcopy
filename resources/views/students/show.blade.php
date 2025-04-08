<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight">
            Detail Siswa - {{ $student->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="font-semibold text-gray-700">Nami Lengkap</dt>
                    <dd class="text-gray-900">{{ $student->name }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">NIS</dt>
                    <dd class="text-gray-900">{{ $student->nis ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Jenis Kelamin</dt>
                    <dd class="text-gray-900">
                        {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Nama Orang Tua</dt>
                    <dd class="text-gray-900">{{ $student->parent_name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Tempat Lahir</dt>
                    <dd class="text-gray-900">{{ $student->birth_place ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Tanggal Lahir</dt>
                    <dd class="text-gray-900">
                        {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') : '-' }}
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">No HP Orang Tua</dt>
                    <dd class="text-gray-900">{{ $student->parent_phone }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-700">Kelas</dt>
                    <dd class="text-gray-900">Kelas {{ $student->class->name }}</dd>
                </div>
            </dl>

            <div class="mt-6 text-right">
                <a href="{{ route('admin.siswa.list', $student->class_id) }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded shadow hover:bg-gray-700">
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
