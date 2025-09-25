<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 flex items-center">
            <span class="iconify text-indigo-600 text-2xl mr-2" data-icon="mdi:clipboard-plus"></span>
            Input Nilai - {{ $subject->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Alert Messages --}}
            @if($errors->any())
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
                <a href="{{ route('teacher.grades.index', ['subjectId' => $subject->id]) }}?class_id={{ $classId }}&class_name={{ urlencode($className) }}" class="hover:text-indigo-600">{{ $className }}</a>
                <span class="iconify" data-icon="mdi:chevron-right"></span>
                <span class="text-gray-900 font-medium">Input Nilai Baru</span>
            </div>

            {{-- Class Info Card --}}
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg mb-8 text-white">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="iconify text-4xl mr-4" data-icon="mdi:book-open-variant"></span>
                            <div>
                                <h2 class="text-2xl font-bold">{{ $subject->name }}</h2>
                                <p class="text-indigo-100 mt-1">Kelas: {{ $className }} | Total Siswa: {{ $students->count() }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-indigo-100 text-sm">Guru Pengampu</p>
                            <p class="text-lg font-semibold">{{ Auth::user()->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Input Nilai --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="iconify text-indigo-600 text-xl mr-2" data-icon="mdi:clipboard-edit"></span>
                        Input Nilai Siswa
                    </h3>
                    <p class="text-gray-600 text-sm mt-1">Isi nilai untuk siswa yang ingin dinilai, kosongkan jika tidak ada nilai</p>
                </div>

                {{-- Add this debug section at the top of your form for testing --}}
<div class="bg-yellow-100 border border-yellow-400 p-4 rounded-lg mb-4" style="display: none;" id="debug-info">
    <h4>Debug Info:</h4>
    <p>Subject ID: {{ $subject->id }}</p>
    <p>Class ID: {{ $classId }}</p>
    <p>Class Name: {{ $className }}</p>
    <p>User Subject ID: {{ Auth::user()->subject_id }}</p>
    <p>User Role ID: {{ Auth::user()->role_id }}</p>
</div>

<form action="{{ route('teacher.grades.store-batch') }}" method="POST" class="p-6" id="gradeForm">
    @csrf
    
    {{-- Hidden fields yang sangat penting --}}
    <input type="hidden" name="class_id" value="{{ $classId }}">
    
    {{-- Debug: tampilkan nilai hidden fields --}}
    <script>
        console.log('Hidden fields values:');
        console.log('class_id:', '{{ $classId }}');
        console.log('subject_id from user:', '{{ Auth::user()->subject_id }}');
        console.log('subject_id from param:', '{{ $subject->id }}');
    </script>

    {{-- Task Information --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tugas/Ujian <span class="text-red-500">*</span></label>
            <input type="text" name="task_name" required value="{{ old('task_name') }}"
                class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('task_name') border-red-500 @enderror"
                placeholder="Contoh: Ulangan Harian 1">
            @error('task_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Penilaian <span class="text-red-500">*</span></label>
            <select name="assignment_type" required
                class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('assignment_type') border-red-500 @enderror">
                <option value="">- Pilih Tipe -</option>
                <option value="written" {{ old('assignment_type') == 'written' ? 'selected' : '' }}>Tugas Tertulis</option>
                <option value="observation" {{ old('assignment_type') == 'observation' ? 'selected' : '' }}>Observasi</option>
                <option value="sumatif" {{ old('assignment_type') == 'sumatif' ? 'selected' : '' }}>Sumatif</option>
            </select>
            @error('assignment_type')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Semester <span class="text-red-500">*</span></label>
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

        <div class="flex items-end">
            <button type="button" onclick="fillAllScores()" 
                class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-all duration-200 flex items-center justify-center">
                <span class="iconify mr-2" data-icon="mdi:format-list-numbered"></span>
                Isi Semua
            </button>
        </div>
    </div>

                    @if($students->isNotEmpty())
                        {{-- Students Table --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Nilai (0-100)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($students as $student)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $student->nis ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <input type="number" 
                                                    name="scores[{{ $student->id }}]" 
                                                    min="0" max="100" step="0.1"
                                                    value="{{ old('scores.' . $student->id) }}"
                                                    class="w-20 border border-gray-300 rounded-md py-1 px-2 text-center focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 score-input @error('scores.' . $student->id) border-red-500 @enderror"
                                                    placeholder="0-100">
                                                @error('scores.' . $student->id)
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                           <div class="mb-4">
        <button type="button" onclick="toggleDebug()" class="px-4 py-2 bg-gray-600 text-white rounded">Show/Hide Debug Info</button>
        <button type="button" onclick="logFormData()" class="px-4 py-2 bg-blue-600 text-white rounded ml-2">Log Form Data</button>
    </div>

                        {{-- Validation Message for scores --}}
                        @error('scores')
                            <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                                <p class="text-sm">{{ $message }}</p>
                            </div>
                        @enderror

                        {{-- Action Buttons --}}
                        <div class="flex justify-between items-center pt-6 mt-6 border-t border-gray-200">
                            <a href="{{ route('teacher.grades.index', ['subjectId' => $subject->id]) }}?class_id={{ $classId }}&class_name={{ urlencode($className) }}"
                               class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-all duration-200 flex items-center">
                                <span class="iconify mr-2" data-icon="mdi:arrow-left"></span>
                                Kembali
                            </a>
                            
                            <div class="flex gap-3">
                                <button type="button" onclick="clearAllScores()"
                                    class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition-all duration-200 flex items-center">
                                    <span class="iconify mr-2" data-icon="mdi:eraser"></span>
                                    Bersihkan Semua
                                </button>
                                
                                <button type="submit" id="submitBtn"
                                    class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-all duration-200 flex items-center">
                                    <span class="iconify mr-2" data-icon="mdi:content-save"></span>
                                    Simpan Nilai
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <span class="iconify text-gray-400 text-6xl mb-4" data-icon="mdi:account-off"></span>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Siswa</h3>
                            <p class="text-gray-500">Belum ada siswa di kelas ini. Silakan hubungi admin untuk menambahkan siswa.</p>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <script>

    function toggleDebug() {
    const debugDiv = document.getElementById('debug-info');
    debugDiv.style.display = debugDiv.style.display === 'none' ? 'block' : 'none';
}

function logFormData() {
    const formData = new FormData(document.getElementById('gradeForm'));
    console.log('=== FORM DATA DEBUG ===');
    
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    // Check if scores array has values
    const scoreInputs = document.querySelectorAll('.score-input');
    let hasScores = false;
    let scoreCount = 0;
    
    scoreInputs.forEach((input, index) => {
        if (input.value && input.value !== '') {
            hasScores = true;
            scoreCount++;
            console.log(`Score ${index + 1} (Student ID: ${input.name}):`, input.value);
        }
    });
    
    console.log('Has scores:', hasScores);
    console.log('Score count:', scoreCount);
    console.log('=== END FORM DATA DEBUG ===');
}

// Enhanced form validation
document.getElementById('gradeForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default untuk debug
    
    console.log('=== FORM SUBMISSION DEBUG ===');
    
    const formData = new FormData(this);
    const taskName = formData.get('task_name');
    const assignmentType = formData.get('assignment_type');
    const semester = formData.get('semester');
    const classId = formData.get('class_id');
    
    console.log('Task Name:', taskName);
    console.log('Assignment Type:', assignmentType);
    console.log('Semester:', semester);
    console.log('Class ID:', classId);
    
    // Count valid scores
    let validScoreCount = 0;
    const scoreInputs = document.querySelectorAll('.score-input');
    
    scoreInputs.forEach(input => {
        if (input.value && input.value !== '') {
            validScoreCount++;
            console.log(`Student ${input.name}: ${input.value}`);
        }
    });
    
    console.log('Valid score count:', validScoreCount);
    
    // Basic validation
    if (!taskName || !assignmentType || !semester || !classId) {
        console.error('Missing required fields!');
        alert('Ada field yang kosong!');
        return;
    }
    
    if (validScoreCount === 0) {
        console.error('No scores provided!');
        alert('Tidak ada nilai yang diisi!');
        return;
    }
    
    console.log('=== VALIDATION PASSED ===');
    
    // Uncomment this line when ready to actually submit
    this.submit();
    
    // For now, just show success message
    alert(`Form valid! Ready to submit ${validScoreCount} scores.`);
});

//         // Form validation before submit
//       // Update the JavaScript validation in your create.blade.php
// document.getElementById('gradeForm').addEventListener('submit', function(e) {
//     const taskName = document.querySelector('input[name="task_name"]').value.trim();
//     const assignmentType = document.querySelector('select[name="assignment_type"]').value;
//     const semester = document.querySelector('select[name="semester"]').value;
//     const scoreInputs = document.querySelectorAll('.score-input');
    
//     let hasValidScore = false;
//     let hasError = false;
    
//     // Check if required fields are filled
//     if (!taskName) {
//         showAlert('error', 'Nama tugas wajib diisi!');
//         e.preventDefault();
//         return;
//     }
    
//     if (!assignmentType) {
//         showAlert('error', 'Tipe penilaian wajib dipilih!');
//         e.preventDefault();
//         return;
//     }
    
//     if (!semester) {
//         showAlert('error', 'Semester wajib dipilih!');
//         e.preventDefault();
//         return;
//     }
    
//     // Check if at least one score is filled and validate scores
//     scoreInputs.forEach(input => {
//         if (input.value !== '') {
//             hasValidScore = true;
//             const value = parseFloat(input.value);
//             if (isNaN(value) || value < 0 || value > 100) {
//                 hasError = true;
//             }
//         }
//     });
    
//     if (!hasValidScore) {
//         showAlert('error', 'Silakan isi setidaknya satu nilai siswa!');
//         e.preventDefault();
//         return;
//     }
    
//     if (hasError) {
//         showAlert('error', 'Terdapat nilai yang tidak valid. Pastikan nilai antara 0-100!');
//         e.preventDefault();
//         return;
//     }
    
//     // Show loading state
//     const submitBtn = document.getElementById('submitBtn');
//     const originalHtml = submitBtn.innerHTML;
//     submitBtn.disabled = true;
//     submitBtn.innerHTML = '<span class="iconify animate-spin mr-2" data-icon="mdi:loading"></span>Menyimpan...';
    
//     // Reset button after 5 seconds if form doesn't redirect (fallback)
//     setTimeout(() => {
//         if (submitBtn.disabled) {
//             submitBtn.disabled = false;
//             submitBtn.innerHTML = originalHtml;
//         }
//     }, 5000);
// });

        // Function to fill all scores with a specific value
        function fillAllScores() {
            const value = prompt('Masukkan nilai untuk semua siswa (0-100):');
            if (value !== null && value !== '') {
                const numValue = parseFloat(value);
                if (numValue >= 0 && numValue <= 100) {
                    document.querySelectorAll('.score-input').forEach(input => {
                        input.value = value;
                    });
                    showAlert('success', `Semua nilai berhasil diisi dengan ${value}`);
                } else {
                    showAlert('error', 'Nilai harus antara 0-100');
                }
            }
        }

        // Function to clear all scores
        function clearAllScores() {
            if (confirm('Hapus semua nilai yang sudah diisi?')) {
                document.querySelectorAll('.score-input').forEach(input => {
                    input.value = '';
                });
                showAlert('info', 'Semua nilai telah dihapus');
            }
        }

        // Validate score input on blur
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.score-input').forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value !== '') {
                        const value = parseFloat(this.value);
                        if (isNaN(value) || value < 0 || value > 100) {
                            showAlert('error', 'Nilai harus antara 0-100');
                            this.focus();
                            this.select();
                        }
                    }
                });
                
                // Auto-format decimal places
                input.addEventListener('change', function() {
                    if (this.value !== '') {
                        const value = parseFloat(this.value);
                        if (!isNaN(value) && value >= 0 && value <= 100) {
                            this.value = value.toFixed(1);
                        }
                    }
                });
            });
        });

        // Alert function
        function showAlert(type, message) {
            // Remove existing alerts
            const existingAlert = document.querySelector('.alert-message');
            if (existingAlert) existingAlert.remove();
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert-message mb-4 p-4 rounded-lg border-l-4 ${
                type === 'error' ? 'bg-red-50 border-red-500 text-red-700' : 
                type === 'success' ? 'bg-green-50 border-green-500 text-green-700' : 
                'bg-blue-50 border-blue-500 text-blue-700'
            }`;
            alertDiv.innerHTML = `
                <div class="flex items-center">
                    <span class="iconify mr-2" data-icon="mdi:${
                        type === 'error' ? 'alert-circle-outline' : 
                        type === 'success' ? 'check-circle-outline' : 
                        'information-outline'
                    }"></span>
                    <span>${message}</span>
                </div>
            `;
            
            // Insert alert at the top of the form container
            const formContainer = document.querySelector('.bg-white.rounded-lg.shadow-sm');
            formContainer.insertBefore(alertDiv, formContainer.firstChild);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
            
            // Scroll to alert
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    </script>

    <style>
        /* Loading animation */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
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