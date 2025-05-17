<x-app-layout title="Rekap Nilai">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Nilai') }}
                @if (isset($className))
                    <span class="text-blue-600">{{ $className }}</span>
                @endif
            </h2>
        </div>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                {{-- Filter Section --}}
                <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Filter Data Nilai</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                     
                        {{-- Pilihan Mata Pelajaran & Jenis Tugas jika kelas sudah dipilih --}}
                        @if ($class_id)
                            <div>
                                <form action="{{ route('grades.list') }}" method="GET" id="filterForm"
                                    class="space-y-2">
                                    <input type="hidden" name="class_id" value="{{ $class_id }}">
                                    <label for="subject_id" class="block text-sm font-medium text-gray-700">Mata
                                        Pelajaran:</label>
                                    <select id="subject_id" name="subject_id"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                        onchange="document.getElementById('filterForm').submit()">
                                        <option value="">Semua Mata Pelajaran</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>

                            <div>
                                <form action="{{ route('grades.list') }}" method="GET" id="taskForm"
                                    class="space-y-2">
                                    <input type="hidden" name="class_id" value="{{ $class_id }}">
                                    @if ($selectedSubject)
                                        <input type="hidden" name="subject_id" value="{{ $selectedSubject }}">
                                    @endif
                                    <label for="task_name" class="block text-sm font-medium text-gray-700">Jenis
                                        Tugas:</label>
                                    <select id="task_name" name="task_name"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                        onchange="document.getElementById('taskForm').submit()">
                                        <option value="">Semua Jenis Tugas</option>
                                        @foreach ($task_types as $task)
                                            <option value="{{ $task }}"
                                                {{ request('task_name') == $task ? 'selected' : '' }}>
                                                {{ $task }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tampilkan dashboard statistik nilai --}}
                @if ($class_id && $stats)
                    <div class="mb-8">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 shadow-sm">
                                <h3 class="text-sm font-medium text-blue-700">Jumlah Data</h3>
                                <p class="text-2xl font-bold text-blue-800">{{ $stats['count'] }}</p>
                            </div>

                            <div class="bg-green-50 p-4 rounded-lg border border-green-100 shadow-sm">
                                <h3 class="text-sm font-medium text-green-700">Rata-rata</h3>
                                <p class="text-2xl font-bold text-green-800">{{ $stats['average'] }}</p>
                            </div>

                            <div class="bg-purple-50 p-4 rounded-lg border border-purple-100 shadow-sm">
                                <h3 class="text-sm font-medium text-purple-700">Nilai Tertinggi</h3>
                                <p class="text-2xl font-bold text-purple-800">{{ $stats['highest'] }}</p>
                            </div>

                            <div class="bg-red-50 p-4 rounded-lg border border-red-100 shadow-sm">
                                <h3 class="text-sm font-medium text-red-700">Nilai Terendah</h3>
                                <p class="text-2xl font-bold text-red-800">{{ $stats['lowest'] }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Tabel Rekap Nilai --}}
                @if ($class_id && count($grades) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Siswa</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mata Pelajaran</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jenis Tugas</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nilai</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Keterangan</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($grades as $index => $grade)
                                    <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}"
                                        id="grade-row-{{ $grade->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $grade->student_name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $grade->subject_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $grade->task_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="score-display px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                                {{ $grade->score >= 90
                                                    ? 'bg-green-100 text-green-800'
                                                    : ($grade->score >= 75
                                                        ? 'bg-blue-100 text-blue-800'
                                                        : ($grade->score >= 60
                                                            ? 'bg-yellow-100 text-yellow-800'
                                                            : 'bg-red-100 text-red-800')) }}">
                                                {{ $grade->score }}
                                            </span>
                                            <input type="number"
                                                class="score-edit hidden w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                value="{{ $grade->score }}" min="0" max="100"
                                                data-grade-id="{{ $grade->id }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($grade->score >= 90)
                                                Sangat Baik
                                            @elseif($grade->score >= 75)
                                                Baik
                                            @elseif($grade->score >= 60)
                                                Cukup
                                            @else
                                                Perlu Perbaikan
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2 items-center">
                                                <!-- Edit Button -->
                                                <button
                                                    class="edit-btn p-1 text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 rounded-md transition"
                                                    data-grade-id="{{ $grade->id }}" title="Edit Nilai">
                                                    <span class="iconify w-4 h-4" data-icon="mdi:pencil"></span>
                                                </button>

                                                <!-- Save Button (hidden by default) -->
                                                <button
                                                    class="save-btn hidden p-1 text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 rounded-md transition"
                                                    data-grade-id="{{ $grade->id }}" title="Simpan Perubahan">
                                                    <span class="iconify w-4 h-4" data-icon="mdi:check"></span>
                                                </button>

                                                <!-- Cancel Button (hidden by default) -->
                                                <button
                                                    class="cancel-btn hidden p-1 text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 rounded-md transition"
                                                    data-grade-id="{{ $grade->id }}" title="Batal Edit">
                                                    <span class="iconify w-4 h-4" data-icon="mdi:close"></span>
                                                </button>

                                                <!-- Delete Button -->
                                                <form class="delete-form inline"
                                                    action="{{ route('grade_tasks.destroy', $grade->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="delete-btn p-1 text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 rounded-md transition"
                                                        onclick="confirmDelete(this)" title="Hapus Nilai">
                                                        <span class="iconify w-4 h-4"
                                                            data-icon="mdi:trash-can-outline"></span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($class_id)
                    <div class="py-8 text-center">
                        <div class="bg-yellow-50 p-4 rounded-md border border-yellow-100">
                            <p class="text-yellow-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Belum ada data nilai yang sesuai dengan filter yang dipilih.
                            </p>
                        </div>
                    </div>
                @elseif(auth()->user()->role_id == 1)
                    <div class="py-8 text-center">
                        <div class="bg-blue-50 p-4 rounded-md border border-blue-100">
                            <p class="text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Silakan pilih kelas terlebih dahulu untuk melihat rekap nilai.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- JavaScript for Grade Management -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle edit button click
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const gradeId = this.getAttribute('data-grade-id');
                    toggleEditMode(gradeId, true);
                });
            });

            // Handle cancel button click
            document.querySelectorAll('.cancel-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const gradeId = this.getAttribute('data-grade-id');
                    toggleEditMode(gradeId, false);
                });
            });

            // Handle save button click
            document.querySelectorAll('.save-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const gradeId = this.getAttribute('data-grade-id');
                    saveGrade(gradeId);
                });
            });

            // Handle enter key in edit field
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.classList.contains('score-edit')) {
                    const gradeId = e.target.getAttribute('data-grade-id');
                    saveGrade(gradeId);
                }
            });
        });

        function toggleEditMode(gradeId, isEdit) {
            const row = document.querySelector(`#grade-row-${gradeId}`);
            if (!row) return;

            // Toggle visibility
            const elements = {
                display: row.querySelector('.score-display'),
                input: row.querySelector('.score-edit'),
                editBtn: row.querySelector('.edit-btn'),
                saveBtn: row.querySelector('.save-btn'),
                cancelBtn: row.querySelector('.cancel-btn'),
                deleteBtn: row.querySelector('.delete-form .delete-btn') // Fixed selector
            };

            // Toggle the hidden class
            elements.display.classList.toggle('hidden', isEdit);
            elements.input.classList.toggle('hidden', !isEdit);
            elements.editBtn.classList.toggle('hidden', isEdit);
            elements.saveBtn.classList.toggle('hidden', !isEdit);
            elements.cancelBtn.classList.toggle('hidden', !isEdit);
            elements.deleteBtn.classList.toggle('hidden', isEdit);

            // Focus on input if in edit mode
            if (isEdit) {
                elements.input.focus();
                elements.input.select();
            }
        }

        function saveGrade(gradeId) {
            const input = document.querySelector(`#grade-row-${gradeId} .score-edit`);
            const newScore = input.value;
            const gradeDisplay = document.querySelector(`#grade-row-${gradeId} .score-display`);

            // Validate input
            if (newScore < 0 || newScore > 100 || isNaN(newScore)) {
                showAlert('error', 'Nilai harus antara 0-100');
                return;
            }

            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Make sure we're using the right endpoint - updated to use grade_tasks if needed
            const endpoint = `/grade-tasks/${gradeId}`; // Adjust this if your route is different

            // Send update request
            fetch(endpoint, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        score: newScore
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update UI
                        gradeDisplay.textContent = newScore;

                        // Update color class based on new value
                        gradeDisplay.className =
                            'score-display px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full ';
                        if (newScore >= 90) {
                            gradeDisplay.classList.add('bg-green-100', 'text-green-800');
                        } else if (newScore >= 75) {
                            gradeDisplay.classList.add('bg-blue-100', 'text-blue-800');
                        } else if (newScore >= 60) {
                            gradeDisplay.classList.add('bg-yellow-100', 'text-yellow-800');
                        } else {
                            gradeDisplay.classList.add('bg-red-100', 'text-red-800');
                        }

                        toggleEditMode(gradeId, false);

                        // Show notification
                        showAlert('success', 'Nilai berhasil diperbarui');
                    } else {
                        showAlert('error', data.message || 'Gagal memperbarui nilai');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Terjadi kesalahan saat memperbarui nilai: ' + error.message);
                });
        }

        function confirmDelete(button) {
            if (confirm('Apakah Anda yakin ingin menghapus nilai ini?')) {
                button.closest('form').submit();
            }
        }

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className =
                `mb-4 p-4 rounded-lg border-l-4 ${type === 'error' ? 'bg-red-50 border-red-500 text-red-700' : 'bg-green-50 border-green-500 text-green-700'}`;
            alertDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;

            const container = document.querySelector('.max-w-7xl.mx-auto');
            container.insertBefore(alertDiv, container.firstChild);

            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    </script>

</x-app-layout>
