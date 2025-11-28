<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Rekap Nilai Siswa</h2>
    </x-slot>
    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6 card">
                        <div class="px-4 py-3 text-lg font-semibold text-gray-700 bg-gray-100 rounded-t-lg">Filter Daftar Nilai</div>

                        <div class="p-4">
                            @if ($class)
                                <div class="mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800">Kelas: {{ $class->name }}</h3>
                                </div>

                                <!-- Filter Form -->
                                <form method="GET" action="{{ route('grades.export') }}" class="mb-6">
                                    <div class="flex flex-wrap items-end gap-4 mb-4">
                                        <div class="flex-1 min-w-[200px]">
                                            <label for="subject_id" class="block mb-2 text-sm font-medium text-gray-700">Mata Pelajaran</label>
                                            <select class="w-full p-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                                    id="subject_id" name="subject_id" required>
                                                <option value="">Pilih Mata Pelajaran</option>
                                                @foreach ($subjects as $subject)
                                                    <option value="{{ $subject->id }}"
                                                        {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                                        {{ $subject->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex-1 min-w-[200px]">
                                            <label for="semester" class="block mb-2 text-sm font-medium text-gray-700">Semester</label>
                                            <select class="w-full p-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                                    id="semester" name="semester" required>
                                                <option value="">Pilih Semester</option>
                                                <option value="Odd" {{ request('semester') == 'Odd' ? 'selected' : '' }}>Ganjil</option>
                                                <option value="Even" {{ request('semester') == 'Even' ? 'selected' : '' }}>Genap</option>
                                            </select>
                                        </div>
                                        <div class="flex-none">
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="mr-1 fas fa-filter"></i> Filter
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <!-- Show table only if filter has been applied -->
                                @if ($selectedSubject && $selectedSemester)
                                    <!-- Table of students and their grades -->
                                    <div class="mb-8">
                                        <h4 class="mb-4 text-lg font-semibold text-gray-800">Nilai: {{ $selectedSubject->name }} - Semester {{ $selectedSemester == 'Odd' ? '1 (Ganjil)' : '2 (Genap)' }}</h4>

                                        <div class="overflow-x-auto border border-gray-200 rounded-lg hide-scrollbar">
                                            <table class="min-w-full border border-gray-300">
                                                <!-- Table header -->
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th rowspan="3" class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-left text-gray-500 uppercase align-middle">No</th>
                                                        <th rowspan="3" class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-left text-gray-500 uppercase align-middle">NIS</th>
                                                        <th rowspan="3" class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-left text-gray-500 uppercase align-middle">Nama Siswa</th>
                                                        <th colspan="12" class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">FORMATIF</th>
                                                        <th colspan="2" rowspan="2" class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">SUMATIF</th>
                                                        <th rowspan="3" class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-center text-gray-500 uppercase align-middle">Nilai Akhir</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="6" class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">TERTULIS (A)</th>
                                                        <th colspan="6" class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">NON TERTULIS (B)</th>
                                                    </tr>
                                                    <tr>
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <th class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">{{ $i }}</th>
                                                        @endfor
                                                        <th class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">RT2</th>
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <th class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">{{ $i }}</th>
                                                        @endfor
                                                        <th class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">RT2</th>
                                                        <th class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">UTS</th>
                                                        <th class="p-2 border border-gray-300 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">UAS</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @forelse($studentsData as $index => $student)
                                                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                                                            <td class="p-2 border border-gray-300 text-sm text-gray-900 whitespace-nowrap">
                                                                {{ $index + 1 }}
                                                            </td>
                                                            <td class="p-2 border border-gray-300 text-sm text-gray-900 whitespace-nowrap">
                                                                {{ $student['student_number'] }}
                                                            </td>
                                                            <td class="p-2 border border-gray-300 text-sm text-gray-900 whitespace-nowrap">
                                                                {{ $student['name'] }}
                                                            </td>
                                                            <!-- Written scores (Tertulis) -->
                                                            @for ($i = 0; $i < 5; $i++)
                                                                <td class="p-2 border border-gray-300 text-sm text-center text-gray-900 whitespace-nowrap">
                                                                    {{ $student['written'][$i] }}
                                                                </td>
                                                            @endfor
                                                            <td class="p-2 border border-gray-300 text-sm text-center text-gray-900 whitespace-nowrap">
                                                                {{ $student['average_written'] ?? '-' }}
                                                            </td>
                                                            <!-- Observation scores (Pengamatan) -->
                                                            @for ($i = 0; $i < 5; $i++)
                                                                <td class="p-2 border border-gray-300 text-sm text-center text-gray-900 whitespace-nowrap">
                                                                    {{ $student['observation'][$i] }}
                                                                </td>
                                                            @endfor
                                                            <td class="p-2 border border-gray-300 text-sm text-center text-gray-900 whitespace-nowrap">
                                                                {{ $student['average_observation'] ?? '-' }}
                                                            </td>
                                                            <!-- Sumatif scores -->
                                                            <td class="p-2 border border-gray-300 text-sm text-center text-gray-900 whitespace-nowrap">
                                                                {{ $student['midterm_score'] ?? '-' }}
                                                            </td>
                                                            <td class="p-2 border border-gray-300 text-sm text-center text-gray-900 whitespace-nowrap">
                                                                {{ $student['final_exam_score'] ?? '-' }}
                                                            </td>
                                                            <!-- Final scores -->
                                                            <td class="p-2 border border-gray-300 text-sm text-center text-gray-900 whitespace-nowrap">
                                                                {{ $student['final_score'] ?? '-' }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="18" class="p-4 text-sm text-center text-gray-500 border border-gray-300">
                                                                Tidak ada data nilai untuk filter yang dipilih
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Export form -->
                                    <form method="POST" action="{{ route('grades.generate-export') }}">
                                        @csrf
                                        <input type="hidden" name="class_id" value="{{ $class->id }}">
                                        <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
                                        <input type="hidden" name="semester" value="{{ $selectedSemester }}">

                                        <div class="flex gap-2 justify-end">
                                            <button type="submit" name="export_type" value="pdf"
                                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                <i class="mr-1 fas fa-file-pdf"></i> Ekspor PDF
                                            </button>
                                            <button type="submit" name="export_type" value="excel"
                                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                <i class="mr-1 fas fa-file-excel"></i> Ekspor Excel
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="px-4 py-3 text-sm text-blue-700 bg-blue-100 rounded-md">
                                        Silakan pilih mata pelajaran dan semester untuk melihat data nilai.
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