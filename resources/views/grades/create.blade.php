<x-app-layout title="Input Nilai">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Input Nilai') }}
            </h2>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-8 max-w-5xl">
        <!-- Notification Section -->
        <div id="alertContainer" class="mb-6"></div>
        
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded flex items-center">
                <span class="iconify mr-2" data-icon="mdi:check-circle-outline"></span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded flex items-center">
                <span class="iconify mr-2" data-icon="mdi:alert-circle-outline"></span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Card Header -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Form Input Nilai Siswa</h3>
            </div>
            
            <!-- Card Content -->
            <div class="p-6">
                <!-- Subject and Semester Selection -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Subject Selection -->
                    <div>
                        <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran *</label>
                        <select id="subject_id" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 h-10 pl-3 pr-8"
                            required>
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Semester Selection -->
                    <div>
                        <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">Semester *</label>
                        <select id="semester" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 h-10 pl-3 pr-8"
                            required>
                            <option value="odd" selected>Gasal</option>
                            <option value="even">Genap</option>
                        </select>
                    </div>
                    
                    <!-- Task Type Selection -->
                    <div>
                        <label for="task_type" class="block text-sm font-medium text-gray-700 mb-2">Jenis Tugas *</label>
                        <select id="task_type" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 h-10 pl-3 pr-8"
                            onchange="updateTaskName()">
                            <option value="">Pilih Jenis Tugas</option>
                            <option value="nilai_harian_1">Nilai Harian 1</option>
                            <option value="nilai_harian_2">Nilai Harian 2</option>
                            <option value="nilai_harian_3">Nilai Harian 3</option>
                            <option value="uts">UTS</option>
                            <option value="uas">UAS</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                </div>
                
                <!-- Task Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Task Name -->
                    <div>
                        <label for="task_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Tugas *</label>
                        <input type="text" id="task_name" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 h-10 px-3"
                            required>
                    </div>
                    
                    <!-- Assignment Type -->
                    <div>
                        <label for="assignment_type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Tugas *</label>
                        <select id="assignment_type" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 h-10 pl-3 pr-8"
                            required>
                            <option value="">Pilih Tipe Tugas</option>
                            <option value="written">Tertulis</option>
                            <option value="observation">Pengamatan</option>
                            <option value="homework">PR</option>
                        </select>
                    </div>
                </div>
                
                <!-- Students Table -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-medium text-gray-700 text-lg">Daftar Siswa</h4>
                        <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">{{ count($students) }} siswa</span>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">No</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="studentTableBody">
                                    @foreach($students as $index => $student)
                                    <tr class="hover:bg-gray-50 transition" data-student-id="{{ $student->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" 
                                                name="scores[{{ $student->id }}]" 
                                                min="0" 
                                                max="100"
                                                class="score-input w-full h-9 px-3 text-center rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-150"
                                                placeholder="0-100"
                                                data-next="{{ $index < count($students) - 1 ? $index + 1 : 0 }}"
                                                data-student-id="{{ $student->id }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Form -->
                <form action="{{ route('grades.store_batch') }}" method="POST" id="gradeForm">
                    @csrf
                    <input type="hidden" name="subject_id" id="form_subject_id">
                    <input type="hidden" name="task_name" id="form_task_name">
                    <input type="hidden" name="grade_data" id="grade_data">
                    <input type="hidden" name="assignment_type" id="form_assignment_type">
                    <input type="hidden" name="semester" id="form_semester">
                    
                    <div class="mt-8 flex flex-col sm:flex-row gap-4">
                        <!-- Save Button -->
                        <button type="button" id="submitBtn"
                            class="flex-1 py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 flex items-center justify-center">
                            <span class="iconify mr-2" data-icon="mdi:content-save"></span>
                            Simpan Nilai
                        </button>

                        <!-- Reset Button -->
                        <button type="button" id="resetBtn"
                            class="flex-1 py-2.5 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 flex items-center justify-center">
                            <span class="iconify mr-2" data-icon="mdi:refresh"></span>
                            Reset Form
                        </button>

                        <!-- Back Button -->
                        <a href="{{ url()->previous() }}"
                            class="flex-1 py-2.5 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 flex items-center justify-center text-center">
                            <span class="iconify mr-2" data-icon="mdi:arrow-left"></span>
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
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
                <span class="iconify mr-2 animate-spin" data-icon="mdi:loading"></span>
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
            
            showAlert('info', 'Form telah direset');
        });
    });

    function updateTaskName() {
        const taskType = document.getElementById('task_type').value;
        const taskNameInput = document.getElementById('task_name');
        
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
        } else {
            taskNameInput.value = '';
            taskNameInput.readOnly = false;
            taskNameInput.focus();
        }
    }

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
                              type === 'info' ? 'bg-blue-50 border-blue-500 text-blue-700' : 
                              'bg-green-50 border-green-500 text-green-700'}`;
        alertDiv.innerHTML = `
            <div class="flex items-center">
                <span class="iconify mr-2" data-icon="mdi:${type === 'error' ? 'alert-circle-outline' : 
                                      type === 'info' ? 'information-outline' : 
                                      'check-circle-outline'}"></span>
                <span>${message}</span>
            </div>
        `;
        
        alertContainer.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => alertDiv.remove(), 300);
        }, 5000);
    }
    </script>

    <style>
    .score-input {
        transition: all 0.15s ease-in-out;
    }
    .score-input:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        border-color: rgba(99, 102, 241, 0.8);
    }
    </style>
</x-app-layout>