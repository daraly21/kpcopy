<x-app-layout title="Input Nilai">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Input Nilai') }}
            </h2>
        </div>
    </x-slot>

    <!-- Add CSRF Meta Tag for AJAX Requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Notification Section -->
                <div id="alertContainer" class="mb-6"></div>
                
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded flex items-center">
                        <span class="iconify mr-2" data-icon="mdi:check-circle-outline"></span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded flex items-center">
                        <span class="iconify mr-2" data-icon="mdi:alert-circle-outline"></span>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Filter Section -->
                <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Form Input Nilai Siswa</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Subject Selection -->
                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700">Mata Pelajaran </label>
                            <select id="subject_id" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                required>
                                <option value="">Pilih Mata Pelajaran</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Semester Selection -->
                        <div>
                            <label for="semester" class="block text-sm font-medium text-gray-700">Semester </label>
                            <select id="semester" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                required>
                                <option value="odd" selected>Ganjil</option>
                                <option value="even">Genap</option>
                            </select>
                        </div>
                        
                        <!-- Task Type Selection -->
                        <div>
                            <label for="task_type" class="block text-sm font-medium text-gray-700">Template Tugas </label>
                            <select id="task_type" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                onchange="updateTaskName()">
                                <option value="">Jenis Tugas</option>
                                <option value="nilai_harian_1">Nilai Harian 1</option>
                                <option value="nilai_harian_2">Nilai Harian 2</option>
                                <option value="nilai_harian_3">Nilai Harian 3</option>
                                <option value="uts">UTS</option>
                                <option value="uas">UAS</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <!-- Task Name -->
                        <div>
                            <label for="task_name" class="block text-sm font-medium text-gray-700">Nama Tugas </label>
                            <input type="text" id="task_name" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                required>
                        </div>
                        
                        <!-- Assignment Type -->
                        <div>
                            <label for="assignment_type" class="block text-sm font-medium text-gray-700">Tipe Tugas </label>
                            <select id="assignment_type" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                required>
                                <option value="">Pilih Tipe Tugas</option>
                                <option value="written">Tertulis</option>
                                <option value="observation">Non Tertulis</option>
                                <option value="sumatif">Sumatif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Students Table and Form -->
                @if(count($students) > 0)
                    <form action="{{ route('grades.store_batch') }}" method="POST" id="gradeForm">
                        @csrf
                        <input type="hidden" name="subject_id" id="form_subject_id">
                        <input type="hidden" name="task_name" id="form_task_name">
                        <input type="hidden" name="grade_data" id="grade_data">
                        <input type="hidden" name="assignment_type" id="form_assignment_type">
                        <input type="hidden" name="semester" id="form_semester">

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">No</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="studentTableBody">
                                    @foreach($students as $index => $student)
                                        <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}" data-student-id="{{ $student->id }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number" 
                                                    name="scores[{{ $student->id }}]" 
                                                    min="0" 
                                                    max="100"
                                                    class="score-input w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-center py-2"
                                                    placeholder="0-100"
                                                    data-next="{{ $index < count($students) - 1 ? $index + 1 : 0 }}"
                                                    data-student-id="{{ $student->id }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

            <div class="mt-6 flex justify-end gap-2">

                 <button type="button" id="resetBtn"
                    class="inline-flex items-center px-4 py-2 border border-red-700 text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                    <span class="iconify mr-2 text-lg" data-icon="mdi:refresh"></span>
                    Reset
                </button>
                <!-- Save Button -->
                <button type="button" id="submitBtn"
                    class="inline-flex items-center px-4 py-2 border border-indigo-700 text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                    <span class="iconify mr-2 text-lg" data-icon="mdi:content-save"></span>
                    Simpan
                </button>
               
            </div>
                    </form>
                @else
                    <div class="py-8 text-center">
                        <div class="bg-yellow-50 p-4 rounded-md border border-yellow-100">
                            <p class="text-yellow-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Tidak ada siswa yang tersedia untuk input nilai.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    @if(session('show_notification_prompt'))
        if (confirm("{{ session('success') }}\n\nApakah Anda ingin mengirim notifikasi WhatsApp ke orang tua sekarang?")) {
            const subjectId = "{{ session('notification_subject_id') }}";
            const taskName  = "{{ session('notification_task_name') }}";
            window.location.href = `{{ route('notifications.index') }}?subject_id=${subjectId}&task_name=${encodeURIComponent(taskName)}`;
        } else {
            window.location.href = `{{ route('grades.list') }}`;
        }
    @endif
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Throttle configuration
        const throttleDelay = 300;
        let throttleTimer;
        
        // Form elements
        const submitBtn = document.getElementById('submitBtn');
        const gradeForm = document.getElementById('gradeForm');
        const studentTableBody = document.getElementById('studentTableBody');
        const resetBtn = document.getElementById('resetBtn');

        // Event delegation for student table
        if (studentTableBody) {
            studentTableBody.addEventListener('keydown', function(event) {
                if (event.target.classList.contains('score-input') && event.key === 'Enter') {
                    event.preventDefault();
                    const nextIndex = event.target.getAttribute('data-next');
                    const nextInput = document.querySelectorAll('.score-input')[nextIndex];
                    
                    if (nextInput) {
                        nextInput.focus();
                        nextInput.select();
                    }
                }
            });

            studentTableBody.addEventListener('focusout', function(event) {
                if (event.target.classList.contains('score-input')) {
                    validateScore(event.target);
                }
            });

            studentTableBody.addEventListener('input', function(event) {
                if (event.target.classList.contains('score-input')) {
                    clearTimeout(throttleTimer);
                    throttleTimer = setTimeout(function() {
                        validateScore(event.target);
                    }, throttleDelay);
                }
            });
        }

        // Submit button handler
        submitBtn.addEventListener('click', function() {
            const subjectId = document.getElementById('subject_id').value;
            const taskName = document.getElementById('task_name').value;
            const assignmentType = document.getElementById('assignment_type').value;
            const semester = document.getElementById('semester').value;
            
            if (!subjectId || !taskName || !assignmentType) {
                showAlert('error', 'Mata pelajaran, nama tugas, dan tipe tugas harus diisi!');
                return;
            }
            
            const scores = {};
            const scoreInputs = document.querySelectorAll('.score-input');
            let hasValue = false;
            
            scoreInputs.forEach(input => {
                if (input.value) {
                    const studentId = input.dataset.studentId;
                    scores[studentId] = input.value;
                    hasValue = true;
                }
            });
            
            if (!hasValue) {
                showAlert('error', 'Minimal satu nilai siswa harus diisi!');
                return;
            }
            
            // Set form values
            document.getElementById('form_subject_id').value = subjectId;
            document.getElementById('form_task_name').value = taskName;
            document.getElementById('form_assignment_type').value = assignmentType;
            document.getElementById('form_semester').value = semester;
            document.getElementById('grade_data').value = JSON.stringify(scores);
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="iconify w-3 h-3 mr-1 animate-spin" data-icon="mdi:loading"></span>
                Menyimpan...
            `;
            
            gradeForm.submit();
        });

        // Reset button handler
        resetBtn.addEventListener('click', function() {
            // Reset form inputs
            document.getElementById('subject_id').value = '';
            document.getElementById('semester').value = 'odd';
            document.getElementById('task_type').value = '';
            document.getElementById('task_name').value = '';
            document.getElementById('assignment_type').value = '';
            document.getElementById('task_name').readOnly = false;
            
            // Reset all student scores
            document.querySelectorAll('.score-input').forEach(input => {
                input.value = '';
            });
            
            // Focus to first field
            document.getElementById('subject_id').focus();
            
            showAlert('success', 'Form telah direset');
        });
    });

function updateTaskName() {
        const taskType = document.getElementById('task_type').value;
        const taskNameInput = document.getElementById('task_name');
        const assignmentTypeSelect = document.getElementById('assignment_type');

        const taskNames = {
            'nilai_harian_1': 'Nilai Harian 1',
            'nilai_harian_2': 'Nilai Harian 2',
            'nilai_harian_3': 'Nilai Harian 3',
            'uts': 'UTS',
            'uas': 'UAS',
            'custom': ''
        };

        if (taskType && taskType !== 'custom') {
            taskNameInput.value = taskNames[taskType];
            taskNameInput.readOnly = true;

            // Jika UTS atau UAS → paksa Sumatif dan disable pilihan
            if (taskType === 'uts' || taskType === 'uas') {
                assignmentTypeSelect.value = 'sumatif';
                assignmentTypeSelect.disabled = true;
                assignmentTypeSelect.classList.add('bg-gray-100', 'cursor-not-allowed');
            } else {
                // Bukan UTS/UAS → boleh pilih tipe tugas bebas
                assignmentTypeSelect.disabled = false;
                assignmentTypeSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
        } else {
            // Custom
            taskNameInput.value = '';
            taskNameInput.readOnly = false;
            taskNameInput.focus();

            // Kembalikan ke kondisi normal
            assignmentTypeSelect.disabled = false;
            assignmentTypeSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
            assignmentTypeSelect.value = ''; // kosongkan biar user pilih
        }
    }

    // Agar saat halaman pertama kali load (misal dari session/error), tetap sinkron
    document.addEventListener('DOMContentLoaded', function () {
        // Jalankan sekali saat load agar status benar
        updateTaskName();

        // Jika ada task_type yang sudah terpilih (misal kembali dari error), langsung jalankan
        const initialTaskType = document.getElementById('task_type').value;
        if (initialTaskType) {
            updateTaskName();
        }
    });

    function validateScore(input) {
        let value = parseInt(input.value);
        if (isNaN(value)) {
            input.value = '';
        } else if (value < 0) {
            input.value = 0;
        } else if (value > 100) {
            input.value = 100;
        }
    }

    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const existingAlert = alertContainer.querySelector('.alert-message');
        
        if (existingAlert) existingAlert.remove();
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert-message mb-4 p-4 rounded-lg border-l-4 ${type === 'error' ? 'bg-red-50 border-red-500 text-red-700' : 
                              type === 'success' ? 'bg-green-50 border-green-500 text-green-700' : 
                              'bg-blue-50 border-blue-500 text-blue-700'}`;
        alertDiv.innerHTML = `
            <div class="flex items-center">
                <span class="iconify mr-2" data-icon="mdi:${type === 'error' ? 'alert-circle-outline' : 
                                      type === 'success' ? 'check-circle-outline' : 
                                      'information-outline'}"></span>
                <span>${message}</span>
            </div>
        `;
        
        alertContainer.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
    </script>

    <style>
        .score-input {
            transition: all 0.15s ease-in-out;
            /* Menghilangkan spinner untuk input number */
            -webkit-appearance: none;
            -moz-appearance: textfield;
            appearance: textfield;
        }
        .score-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            border-color: rgba(59, 130, 246, 0.8);
        }
        /* Menghilangkan spinner untuk browser WebKit */
        .score-input::-webkit-inner-spin-button,
        .score-input::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</x-app-layout>