<!-- resources/views/rapor/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Rapor Siswa</h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6 card">
                        <div class="px-4 py-3 text-lg font-semibold text-gray-700 bg-gray-100 rounded-t-lg">Filter Rapor</div>

                        <div class="p-4">
                            @if ($class)
                                <div class="mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800">Kelas: {{ $class->name }}</h3>
                                </div>

                                <!-- Filter Form -->
                                <form method="GET" action="{{ route('rapor.index') }}" class="mb-6">
                                    <div class="flex flex-wrap items-end gap-4 mb-4">
                                        <div class="flex-1 min-w-[200px]">
                                            <label for="semester" class="block mb-2 text-sm font-medium text-gray-700">Semester</label>
                                            <select class="w-full p-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                                    id="semester" name="semester" required>
                                                <option value="">Pilih Semester</option>
                                                <option value="Odd" {{ request('semester') == 'Odd' ? 'selected' : '' }}>1 (GASAL)</option>
                                                <option value="Even" {{ request('semester') == 'Even' ? 'selected' : '' }}>2 (GENAP)</option>
                                            </select>
                                        </div>
                                        <div class="flex-none">
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="mr-1 fas fa-filter"></i> Filter
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <!-- Tampilkan tabel rapor jika filter diterapkan -->
                                @if ($selectedSemester)
                                    <div class="mb-8">
                                        <h4 class="mb-4 text-lg font-semibold text-gray-800">Rapor Semester {{ $selectedSemester == 'Odd' ? '1 (GASAL)' : '2 (GENAP)' }}</h4>

                                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                            <table class="min-w-full border border-gray-300">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">No</th>
                                                        <th class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">NIS</th>
                                                        <th class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nama Siswa</th>
                                                        @foreach ($subjects as $subject)
                                                            <th class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">{{ $subject->name }}</th>
                                                        @endforeach
                                                        <th class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Jumlah</th>
                                                        <th class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Rata-rata</th>
                                                        <th class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Rank</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @forelse($studentsData as $index => $student)
                                                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                                                            <td class="p-2 border border-gray-300 text-sm text-gray-900 whitespace-nowrap">
                                                                {{ $index + 1 }}
                                                            </td>
                                                            <td class="p-2 border border-gray-300 text-sm text-gray-900 whitespace-nowrap">{{ $student['student_number'] }}</td>
                                                            <td class="p-2 border border-gray-300 text-sm text-gray-900 whitespace-nowrap">{{ $student['name'] }}</td>
                                                            @foreach ($subjects as $subject)
                                                                <td class="p-2 border border-gray-300 text-sm text-center text-gray-900 whitespace-nowrap">
                                                                    {{ $student['scores'][$subject->id] > 0 ? $student['scores'][$subject->id] : '-' }}
                                                                </td>
                                                            @endforeach
                                                            <td class="p-2 border border-gray-300 text-sm text-center text-gray-900 whitespace-nowrap">{{ $student['total'] > 0 ? $student['total'] : '-' }}</td>
                                                            <td class="p-2 border border-gray-300 text-sm text-center text-gray-900 whitespace-nowrap">{{ $student['average'] > 0 ? number_format($student['average'], 2) : '-' }}</td>
                                                            <td class="p-2 border border-gray-300 text-sm text-center text-gray-900 whitespace-nowrap">{{ $student['rank'] }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="{{ 3 + count($subjects) + 3 }}" class="p-4 text-sm text-center text-gray-500 border border-gray-300">
                                                                Tidak ada data rapor untuk semester yang dipilih
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <div class="px-4 py-3 text-sm text-blue-700 bg-blue-100 rounded-md">
                                        Silakan pilih semester untuk melihat data rapor.
                                    </div>
                                @endif
                            @else
                                <div class="p-4 text-red-700 bg-red-100 border-l-4 border-red-500">
                                    <h4 class="font-bold">Data Kelas Tidak Ditemukan</h4>
                                    <p class="text-sm">Sistem tidak dapat menemukan data kelas yang Anda ampu. Mohon hubungi administrator untuk memastikan bahwa akun Anda telah dikaitkan dengan kelas yang benar.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>