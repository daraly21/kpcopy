<x-app-layout title="Input Nilai">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Input Nilai') }}
            </h2>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6 max-w-4xl">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-100">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                    <span class="iconify mr-2" data-icon="mdi:file-document-edit-outline" style="color: #4f46e5;"></span>
                    Form Input Nilai
                </h4>
            </div>

            <!-- Main Form Content -->
            <div class="p-6">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded flex items-center">
                        <span class="iconify mr-2" data-icon="mdi:check-circle-outline"></span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                
                <!-- Subject and Task Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Subject Selection -->
                    <div>
                        <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran *</label>
                        <select id="subject_id" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 h-10 pl-3 pr-8"
                            required>
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Task Type Selection -->
                    <div>
                        <label for="task_type" class="block text-sm font-medium text-gray-700 mb-1">Jenis Tugas *</label>
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
                
                <!-- Task Name Input -->
                <div class="mb-6">
                    <label for="task_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Tugas *</label>
                    <input type="text" id="task_name" 
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 h-10 px-3"
                        required>
                </div>
                
                <!-- Students List -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h5 class="font-medium text-gray-700">Daftar Siswa</h5>
                        <span class="text-sm text-gray-500">{{ count($students) }} siswa</span>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">No</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="studentTableBody">
                                    @foreach($students as $index => $student)
                                    <tr class="hover:bg-gray-50 transition" data-student-id="{{ $student->id }}">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <input type="number" 
                                                name="scores[{{ $student->id }}]" 
                                                min="0" 
                                                max="100"
                                                class="score-input w-full h-8 px-2 text-center rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-150"
                                                placeholder="0-100"
                                                data-next="{{ $index < count($students) - 1 ? $index + 1 : 0 }}"
                                                onkeydown="handleEnterKey(event, this)"
                                                oninput="validateScore(this)">
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
                    
                    <div class="mt-6">
                        <button type="button" id="submitBtn"
                            class="w-full py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 flex items-center justify-center">
                            <span class="iconify mr-2" data-icon="mdi:content-save"></span>
                            Simpan Semua Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Submit form handler
        const submitBtn = document.getElementById('submitBtn');
        const gradeForm = document.getElementById('gradeForm');
        
        submitBtn.addEventListener('click', function() {
            const subjectId = document.getElementById('subject_id').value;
            const taskName = document.getElementById('task_name').value;
            
            if (!subjectId || !taskName) {
                showAlert('error', 'Mata pelajaran dan nama tugas harus diisi!');
                return;
            }
            
            const scores = {};
            const scoreInputs = document.querySelectorAll('.score-input');
            let hasValue = false;
            
            scoreInputs.forEach(input => {
                const studentId = input.name.match(/\[(\d+)\]/)[1];
                if (input.value) {
                    scores[studentId] = input.value;
                    hasValue = true;
                }
            });
            
            if (!hasValue) {
                showAlert('error', 'Minimal satu nilai siswa harus diisi!');
                return;
            }
            
            document.getElementById('form_subject_id').value = subjectId;
            document.getElementById('form_task_name').value = taskName;
            document.getElementById('grade_data').value = JSON.stringify(scores);
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="iconify mr-2 animate-spin" data-icon="mdi:loading"></span>
                Menyimpan...
            `;
            
            gradeForm.submit();
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

    function handleEnterKey(event, input) {
        if (event.key === 'Enter') {
            event.preventDefault();
            const nextIndex = input.getAttribute('data-next');
            const nextInput = document.querySelectorAll('.score-input')[nextIndex];
            
            if (nextInput) {
                nextInput.focus();
                nextInput.select();
            }
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
        // Remove existing alerts first
        const existingAlert = document.querySelector('.alert-message');
        if (existingAlert) existingAlert.remove();
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert-message mb-4 p-4 rounded-lg border-l-4 ${type === 'error' ? 'bg-red-50 border-red-500 text-red-700' : 'bg-green-50 border-green-500 text-green-700'}`;
        alertDiv.innerHTML = `
            <div class="flex items-center">
                <span class="iconify mr-2" data-icon="mdi:${type === 'error' ? 'alert-circle-outline' : 'check-circle-outline'}"></span>
                <span>${message}</span>
            </div>
        `;
        
        const form = document.querySelector('.p-6');
        form.insertBefore(alertDiv, form.firstChild);
        
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