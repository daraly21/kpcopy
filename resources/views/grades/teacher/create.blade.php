<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 flex items-center">
            <span class="iconify text-indigo-600 text-2xl mr-2" data-icon="mdi:clipboard-plus"></span>
            Input Nilai - {{ $subject->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
{{-- Tambahkan ini tepat setelah <div class="max-w-7xl ..."> dan sebelum Breadcrumb --}}
@if (session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded flex items-center">
        <span class="iconify mr-2 text-xl" data-icon="mdi:check-circle-outline"></span>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded flex items-center">
        <span class="iconify mr-2 text-xl" data-icon="mdi:alert-circle-outline"></span>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if (session('warning'))
    <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded flex items-center">
        <span class="iconify mr-2 text-xl" data-icon="mdi:information-outline"></span>
        <span>{{ session('warning') }}</span>
    </div>
@endif
            {{-- Alert Messages --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
                    <div class="flex items-start">
                        <span class="iconify text-red-600 text-xl mr-2 mt-0.5" data-icon="mdi:alert-circle"></span>
                        <div>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="list-disc list-inside mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Breadcrumb Navigation --}}
            <div class="flex items-center space-x-2 text-sm text-gray-600 mb-6">
                <a href="{{ route('teacher.grades.select-class') }}" class="hover:text-indigo-600">Pilih Kelas</a>
                <span class="iconify" data-icon="mdi:chevron-right"></span>
                <a href="{{ route('teacher.grades.index', ['subjectId' => $subject->id]) }}?class_id={{ $classId }}&class_name={{ urlencode($className) }}"
                    class="hover:text-indigo-600">{{ $className }}</a>
                <span class="iconify" data-icon="mdi:chevron-right"></span>
                <span class="text-gray-900 font-medium">Input Nilai Baru</span>
            </div>


            {{-- Form Input Nilai --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="iconify text-indigo-600 text-xl mr-2" data-icon="mdi:clipboard-edit"></span>
                        Input Nilai Siswa
                    </h3>
                    <p class="text-gray-600 text-sm mt-1">Isi nilai untuk siswa yang ingin dinilai, kosongkan jika tidak
                        ada nilai</p>
                </div>

                <form action="{{ route('teacher.grades.store-batch') }}" method="POST" class="p-6" id="gradeForm">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $classId }}">

                    {{-- Task Information dengan Template --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                        {{-- Template Tugas --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Template Tugas</label>
                            <select id="task_template"
                                class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Pilih Template</option>
                                <option value="harian1">Nilai Harian 1</option>
                                <option value="harian2">Nilai Harian 2</option>
                                <option value="harian3">Nilai Harian 3</option>
                                <option value="uts">UTS</option>
                                <option value="uas">UAS</option>
                                <option value="custom">Custom (Manual)</option>
                            </select>
                        </div>

                        {{-- Nama Tugas --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tugas/Ujian <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="task_name" id="task_name" required
                                value="{{ old('task_name') }}"
                                class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('task_name') border-red-500 @enderror"
                                placeholder="Contoh: Ulangan Harian 1">
                            @error('task_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                       {{-- Tipe Penilaian --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Tipe Penilaian <span class="text-red-500">*</span>
    </label>
    
    <!-- Select untuk tampilan saja -->
    <select id="assignment_type" required
        class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('assignment_type') border-red-500 @enderror">
        <option value="">- Pilih Tipe -</option>
        <option value="written">Tugas Tertulis</option>
        <option value="observation">Observasi</option>
        <option value="sumatif">Sumatif</option>
    </select>

    <!-- Yang benar-benar dikirim ke server -->
    <input type="hidden" name="assignment_type" id="hidden_assignment_type" value="{{ old('assignment_type') }}">

    @error('assignment_type')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

                        {{-- Semester --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Semester <span
                                    class="text-red-500">*</span></label>
                            <select name="semester" required
                                class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('semester') border-red-500 @enderror">
                                <option value="">- Pilih Semester -</option>
                                <option value="Odd" {{ old('semester') == 'Odd' ? 'selected' : '' }}>Ganjil</option>
                                <option value="Even" {{ old('semester') == 'Even' ? 'selected' : '' }}>Genap</option>
                            </select>
                            @error('semester')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tabel Siswa --}}
                    @if ($students->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                            No</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            NIS</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Siswa</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                            Nilai (0-100)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($students as $student)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $loop->iteration }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $student->nis ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $student->name }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <input type="number" name="scores[{{ $student->id }}]" min="0"
                                                    max="100" value="{{ old('scores.' . $student->id) }}"
                                                    class="score-input w-20 border border-gray-300 rounded-md py-1 px-2 text-center focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('scores.' . $student->id) border-red-500 @enderror"
                                                    placeholder="0-100"
                                                    data-next-row="{{ $loop->iteration < $students->count() ? $loop->iteration + 1 : '' }}">
                                                @error('scores.' . $student->id)
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex justify-between items-center pt-6 mt-6 border-t border-gray-200">
                            <a href="{{ route('teacher.grades.index', ['subjectId' => $subject->id]) }}?class_id={{ $classId }}&class_name={{ urlencode($className) }}"
                                class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-all duration-200 flex items-center">
                                Kembali
                            </a>

                            <div class="flex gap-3">
                                <button type="button" onclick="clearAllScores()"
                                    class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-all duration-200 flex items-center">
                                    Bersihkan Semua
                                </button>

                                <button type="submit" id="submitBtn"
                                    class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-all duration-200 flex items-center">
                                    Simpan Nilai
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- sama seperti sebelumnya -->
                    @endif
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const templateSelect = document.getElementById('task_template');
            const taskNameInput = document.getElementById('task_name');
            const typeSelect = document.getElementById('assignment_type');

            // Template mapping
            const templates = {
                'harian1': {
                    name: 'Nilai Harian 1',
                    type: 'written'
                },
                'harian2': {
                    name: 'Nilai Harian 2',
                    type: 'written'
                },
                'harian3': {
                    name: 'Nilai Harian 3',
                    type: 'written'
                },
                'uts': {
                    name: 'UTS',
                    type: 'sumatif'
                },
                'uas': {
                    name: 'UAS',
                    type: 'sumatif'
                },
                'custom': {
                    name: '',
                    type: null
                }
            };

           templateSelect.addEventListener('change', function () {
    const val = this.value;
    const typeSelect = document.getElementById('assignment_type');
    const hiddenTypeInput = document.getElementById('hidden_assignment_type'); // kita tambah nanti

    if (val && val !== 'custom') {
        taskNameInput.value = templates[val].name;
        taskNameInput.readOnly = true;

        if (val === 'uts' || val === 'uas') {
            // Paksa sumatif + visually disabled
            typeSelect.value = 'sumatif';
            typeSelect.disabled = true;
            typeSelect.classList.add('bg-gray-100', 'cursor-not-allowed', 'opacity-70');

            // Pastikan nilai tetap terkirim
            hiddenTypeInput.value = 'sumatif';
        } else {
            typeSelect.value = templates[val].type;
            typeSelect.disabled = false;
            typeSelect.classList.remove('bg-gray-100', 'cursor-not-allowed', 'opacity-70');
            hiddenTypeInput.value = templates[val].type;
        }
    } else {
        taskNameInput.value = '';
        taskNameInput.readOnly = false;
        taskNameInput.focus();

        typeSelect.disabled = false;
        typeSelect.classList.remove('bg-gray-100', 'cursor-not-allowed', 'opacity-70');
        typeSelect.value = '';
        hiddenTypeInput.value = '';
    }
});

            // Enter → pindah ke baris bawah
            document.querySelectorAll('.score-input').forEach((input, index) => {
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const nextRow = this.closest('tr').nextElementSibling;
                        if (nextRow) {
                            const nextInput = nextRow.querySelector('.score-input');
                            if (nextInput) {
                                nextInput.focus();
                                nextInput.select();
                            }
                        }
                    }
                });

                // Hanya angka bulat (0-100), otomatis format saat blur
                input.addEventListener('blur', function() {
                    if (this.value !== '') {
                        let val = parseInt(this.value);
                        if (isNaN(val) || val < 0) val = 0;
                        if (val > 100) val = 100;
                        this.value = val; // tanpa desimal
                    }
                });

                // Cegah input desimal
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            });

            // Bersihkan semua nilai
            window.clearAllScores = function() {
                if (confirm('Hapus semua nilai yang sudah diisi?')) {
                    document.querySelectorAll('.score-input').forEach(i => i.value = '');
                }
            };

            // Submit dengan loading
            document.getElementById('gradeForm').addEventListener('submit', function() {
                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.innerHTML = 'Menyimpan...';
            });
        });

        // Sinkronkan hidden input saat halaman pertama kali load (jika ada old input)
document.addEventListener('DOMContentLoaded', function () {
    const visibleSelect = document.getElementById('assignment_type');
    const hiddenInput = document.getElementById('hidden_assignment_type');

    // Jika ada old value (dari error validasi)
    if (hiddenInput.value) {
        visibleSelect.value = hiddenInput.value;
        if (visibleSelect.value === 'sumatif') {
            visibleSelect.disabled = true;
            visibleSelect.classList.add('bg-gray-100', 'cursor-not-allowed', 'opacity-70');
        }
    }

    // Pastikan saat user pilih manual (bukan dari template), hidden ikut berubah
    visibleSelect.addEventListener('change', function () {
        if (!visibleSelect.disabled) {
            hiddenInput.value = this.value;
        }
    });
});
    </script>

    <style>
        /* Loading animation */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Score input styling */
        .score-input {
            -webkit-appearance: none;
            -moz-appearance: textfield;
            appearance: textfield;
        }

        .score-input::-webkit-inner-spin-button,
        .score-input::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Hover effects */
        .score-input:hover {
            border-color: #6366f1;
        }

        /* Focus styles */
        .score-input:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Error state */
        .border-red-500:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
    </style>
</x-app-layout>
