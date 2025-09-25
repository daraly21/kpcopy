<x-app-layout title="Kelola Nilai - {{ $subject->name ?? 'Subjek' }}">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Kelola Nilai - <span class="text-blue-600">{{ $subject->name }}</span>
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
                    <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Filter Data Nilai</h3>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <!-- Class Filter -->
                        <div>
                            <label for="class_filter" class="block text-sm font-medium text-gray-700">Filter Kelas</label>
                            <select id="class_filter" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Semua Kelas</option>
                                @if(isset($classes))
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Task Filter -->
                        <div>
                            <label for="task_filter" class="block text-sm font-medium text-gray-700">Filter Tugas</label>
                            <select id="task_filter" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Semua Tugas</option>
                                @if(isset($task_types))
                                    @foreach($task_types as $task)
                                        <option value="{{ $task }}">{{ $task }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Type Filter -->
                        <div>
                            <label for="type_filter" class="block text-sm font-medium text-gray-700">Filter Tipe</label>
                            <select id="type_filter" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Semua Tipe</option>
                                <option value="written">Tertulis</option>
                                <option value="observation">Pengamatan</option>
                                <option value="sumatif">Sumatif</option>
                            </select>
                        </div>
                        
                        <!-- Semester Filter -->
                        <div>
                            <label for="semester_filter" class="block text-sm font-medium text-gray-700">Filter Semester</label>
                            <select id="semester_filter" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Semua Semester</option>
                                <option value="odd">Gasal</option>
                                <option value="even">Genap</option>
                            </select>
                        </div>

                        <!-- Search Input -->
                        <div>
                            <label for="search_input" class="block text-sm font-medium text-gray-700">Cari Nama Siswa</label>
                            <input type="text" id="search_input" 
                                placeholder="Ketik nama siswa..."
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200">
                        <div class="flex gap-2">
                            <button id="resetFilterBtn"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                                <span class="iconify mr-2" data-icon="mdi:filter-off"></span>
                                Reset Filter
                            </button>
                        </div>
                        
                        <button onclick="redirectToCreatePage()" 
                            class="inline-flex items-center px-4 py-2 border border-green-700 text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                            <span class="iconify mr-2 text-lg" data-icon="mdi:plus"></span>
                            Tambah Nilai
                        </button>
                    </div>
                </div>

                <!-- Grades Table Section -->
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-700 border-b pb-2">Daftar Nilai Siswa</h3>
                        <div class="text-sm text-gray-500">
                            Total: <span id="totalRecords">0</span> data
                        </div>
                    </div>
                    
                    @if($grades->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="gradesTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Tugas</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Nilai</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="gradesTableBody">
                                    @php $rowIndex = 0; @endphp
                                    @foreach($grades as $studentId => $studentGrades)
                                        @foreach($studentGrades as $grade)
                                            <tr class="grade-row {{ $rowIndex % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}"
                                                data-student-name="{{ strtolower($grade->student->name ?? '') }}"
                                                data-class-id="{{ $grade->student->class_id ?? '' }}"
                                                data-task-name="{{ $grade->task_name }}"
                                                data-task-type="{{ $grade->type }}"
                                                data-semester="{{ $grade->grades->semester ?? '' }}"
                                                data-grade-id="{{ $grade->id }}">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $rowIndex + 1 }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $grade->student->name ?? 'Tidak Ditemukan' }}</div>
                                                    <div class="text-sm text-gray-500">{{ $grade->student->class->name ?? 'Kelas Tidak Ditemukan' }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900 task-name-display">{{ $grade->task_name }}</div>
                                                    <input type="text" 
                                                        class="task-name-input hidden w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        value="{{ $grade->task_name }}">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900 type-display">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                            {{ $grade->type === 'written' ? 'bg-blue-100 text-blue-800' : 
                                                               ($grade->type === 'observation' ? 'bg-green-100 text-green-800' : 
                                                               'bg-yellow-100 text-yellow-800') }}">
                                                            {{ $grade->type === 'written' ? 'Tertulis' : 
                                                               ($grade->type === 'observation' ? 'Pengamatan' : 'PR') }}
                                                        </span>
                                                    </div>
                                                    <select class="type-input hidden w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <option value="written" {{ $grade->type === 'written' ? 'selected' : '' }}>Tertulis</option>
                                                        <option value="observation" {{ $grade->type === 'observation' ? 'selected' : '' }}>Pengamatan</option>
                                                        <option value="sumatif" {{ $grade->type === 'sumatif' ? 'selected' : '' }}>PR</option>
                                                    </select>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                            {{ ($grade->grades->semester ?? '') === 'odd' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800' }}">
                                                            {{ ($grade->grades->semester ?? '') === 'odd' ? 'Gasal' : 'Genap' }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span class="score-display inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                                        {{ $grade->score >= 75 ? 'bg-green-100 text-green-800' : 
                                                           ($grade->score >= 60 ? 'bg-yellow-100 text-yellow-800' : 
                                                           'bg-red-100 text-red-800') }}">
                                                        {{ $grade->score }}
                                                    </span>
                                                    <input type="number" 
                                                        class="score-input hidden w-20 px-2 py-1 text-sm text-center border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        value="{{ $grade->score }}" min="0" max="100">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <div class="flex gap-1">
                                                        <button onclick="toggleEditMode(this)" 
                                                            class="edit-btn text-blue-600 hover:text-blue-900 transition-colors"
                                                            title="Edit"
                                                            data-grade-id="{{ $grade->id }}">
                                                            <span class="iconify" data-icon="mdi:pencil"></span>
                                                        </button>
                                                        <button onclick="saveGrade(this)" 
                                                            class="save-btn hidden text-green-600 hover:text-green-900 transition-colors"
                                                            title="Simpan"
                                                            data-grade-id="{{ $grade->id }}">
                                                            <span class="iconify" data-icon="mdi:content-save"></span>
                                                        </button>
                                                        <button onclick="cancelEdit(this)" 
                                                            class="cancel-btn hidden text-gray-600 hover:text-gray-900 transition-colors"
                                                            title="Batal">
                                                            <span class="iconify" data-icon="mdi:close"></span>
                                                        </button>
                                                        <button onclick="deleteGrade({{ $grade->id }})" 
                                                            class="delete-btn text-red-600 hover:text-red-900 transition-colors"
                                                            title="Hapus">
                                                            <span class="iconify" data-icon="mdi:delete"></span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @php $rowIndex++; @endphp
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="py-8 text-center">
                            <div class="bg-yellow-50 p-4 rounded-md border border-yellow-100">
                                <p class="text-yellow-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Tidak ada data nilai yang tersedia.
                                </p>
                            </div>
                            <div class="mt-4">
                                <button onclick="redirectToCreatePage()" 
                                    class="inline-flex items-center px-4 py-2 border border-green-700 text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                    <span class="iconify mr-2 text-lg" data-icon="mdi:plus"></span>
                                    Tambah Nilai
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- No Results Message (Hidden by default) -->
                    <div id="noResultsMessage" class="py-8 text-center hidden">
                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                            <p class="text-gray-600">
                                <span class="iconify mr-2" data-icon="mdi:magnify"></span>
                                Tidak ada data yang sesuai dengan filter yang diterapkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize filter functionality
            initializeFilters();
            updateRecordCount();
        });

        function redirectToCreatePage() {
            // Get current URL parameters to maintain class context
            const urlParams = new URLSearchParams(window.location.search);
            const classId = urlParams.get('class_id');
            const className = urlParams.get('class_name');
            
            // Build the create page URL
            let createUrl = `{{ route('teacher.grades.create', ['subjectId' => $subject->id]) }}`;
            
            // Add query parameters if they exist
            if (classId && className) {
                createUrl += `?class_id=${classId}&class_name=${encodeURIComponent(className)}`;
            }
            
            window.location.href = createUrl;
        }

        function initializeFilters() {
            const classFilter = document.getElementById('class_filter');
            const taskFilter = document.getElementById('task_filter');
            const typeFilter = document.getElementById('type_filter');
            const semesterFilter = document.getElementById('semester_filter');
            const searchInput = document.getElementById('search_input');
            const resetBtn = document.getElementById('resetFilterBtn');

            // Add event listeners for filters
            [classFilter, taskFilter, typeFilter, semesterFilter, searchInput].forEach(element => {
                if (element) {
                    element.addEventListener('change', applyFilters);
                    if (element.type === 'text') {
                        element.addEventListener('input', debounce(applyFilters, 300));
                    }
                }
            });

            // Reset filter functionality
            resetBtn.addEventListener('click', function() {
                classFilter.value = '';
                taskFilter.value = '';
                typeFilter.value = '';
                semesterFilter.value = '';
                searchInput.value = '';
                applyFilters();
                showAlert('success', 'Filter telah direset');
            });
        }

        function applyFilters() {
            const classFilter = document.getElementById('class_filter').value;
            const taskFilter = document.getElementById('task_filter').value;
            const typeFilter = document.getElementById('type_filter').value;
            const semesterFilter = document.getElementById('semester_filter').value;
            const searchValue = document.getElementById('search_input').value.toLowerCase();

            const rows = document.querySelectorAll('.grade-row');
            let visibleCount = 0;

            rows.forEach((row, index) => {
                const studentName = row.getAttribute('data-student-name');
                const classId = row.getAttribute('data-class-id');
                const taskName = row.getAttribute('data-task-name');
                const taskType = row.getAttribute('data-task-type');
                const semester = row.getAttribute('data-semester');

                let shouldShow = true;

                // Apply class filter
                if (classFilter && classId !== classFilter) {
                    shouldShow = false;
                }

                // Apply task filter
                if (taskFilter && taskName !== taskFilter) {
                    shouldShow = false;
                }

                // Apply type filter
                if (typeFilter && taskType !== typeFilter) {
                    shouldShow = false;
                }

                // Apply semester filter
                if (semesterFilter && semester !== semesterFilter) {
                    shouldShow = false;
                }

                // Apply search filter
                if (searchValue && !studentName.includes(searchValue)) {
                    shouldShow = false;
                }

                if (shouldShow) {
                    row.style.display = '';
                    row.classList.remove('bg-white', 'bg-gray-50');
                    row.classList.add(visibleCount % 2 === 0 ? 'bg-white' : 'bg-gray-50');
                    // Update row number
                    row.querySelector('td:first-child').textContent = visibleCount + 1;
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide no results message
            const noResultsMessage = document.getElementById('noResultsMessage');
            const gradesTable = document.getElementById('gradesTable');
            
            if (visibleCount === 0 && rows.length > 0) {
                gradesTable.style.display = 'none';
                noResultsMessage.classList.remove('hidden');
            } else {
                gradesTable.style.display = '';
                noResultsMessage.classList.add('hidden');
            }

            updateRecordCount(visibleCount);
        }

        function updateRecordCount(count = null) {
            const totalRecords = document.getElementById('totalRecords');
            if (count !== null) {
                totalRecords.textContent = count;
            } else {
                const rows = document.querySelectorAll('.grade-row');
                totalRecords.textContent = rows.length;
            }
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function toggleEditMode(button) {
            const row = button.closest('tr');
            const gradeId = button.getAttribute('data-grade-id');
            
            // Get display and input elements
            const taskNameDisplay = row.querySelector('.task-name-display');
            const taskNameInput = row.querySelector('.task-name-input');
            const typeDisplay = row.querySelector('.type-display');
            const typeInput = row.querySelector('.type-input');
            const scoreDisplay = row.querySelector('.score-display');
            const scoreInput = row.querySelector('.score-input');
            
            // Get action buttons
            const editBtn = row.querySelector('.edit-btn');
            const saveBtn = row.querySelector('.save-btn');
            const cancelBtn = row.querySelector('.cancel-btn');
            const deleteBtn = row.querySelector('.delete-btn');
            
            // Store original values for cancel functionality
            taskNameInput.setAttribute('data-original', taskNameDisplay.textContent);
            typeInput.setAttribute('data-original', typeInput.value);
            scoreInput.setAttribute('data-original', scoreDisplay.textContent);
            
            // Toggle display/edit mode
            taskNameDisplay.classList.add('hidden');
            taskNameInput.classList.remove('hidden');
            typeDisplay.classList.add('hidden');
            typeInput.classList.remove('hidden');
            scoreDisplay.classList.add('hidden');
            scoreInput.classList.remove('hidden');
            
            // Toggle buttons
            editBtn.classList.add('hidden');
            deleteBtn.classList.add('hidden');
            saveBtn.classList.remove('hidden');
            cancelBtn.classList.remove('hidden');
            
            // Focus on first input
            taskNameInput.focus();
            taskNameInput.select();
            
            // Add row highlight
            row.classList.add('ring-2', 'ring-blue-300', 'ring-opacity-50');
        }

        function cancelEdit(button) {
            const row = button.closest('tr');
            
            // Get display and input elements
            const taskNameDisplay = row.querySelector('.task-name-display');
            const taskNameInput = row.querySelector('.task-name-input');
            const typeDisplay = row.querySelector('.type-display');
            const typeInput = row.querySelector('.type-input');
            const scoreDisplay = row.querySelector('.score-display');
            const scoreInput = row.querySelector('.score-input');
            
            // Get action buttons
            const editBtn = row.querySelector('.edit-btn');
            const saveBtn = row.querySelector('.save-btn');
            const cancelBtn = row.querySelector('.cancel-btn');
            const deleteBtn = row.querySelector('.delete-btn');
            
            // Restore original values
            taskNameInput.value = taskNameInput.getAttribute('data-original');
            typeInput.value = typeInput.getAttribute('data-original');
            scoreInput.value = scoreInput.getAttribute('data-original');
            
            // Toggle back to display mode
            taskNameDisplay.classList.remove('hidden');
            taskNameInput.classList.add('hidden');
            typeDisplay.classList.remove('hidden');
            typeInput.classList.add('hidden');
            scoreDisplay.classList.remove('hidden');
            scoreInput.classList.add('hidden');
            
            // Toggle buttons back
            editBtn.classList.remove('hidden');
            deleteBtn.classList.remove('hidden');
            saveBtn.classList.add('hidden');
            cancelBtn.classList.add('hidden');
            
            // Remove row highlight
            row.classList.remove('ring-2', 'ring-blue-300', 'ring-opacity-50');
        }

        function saveGrade(button) {
            const row = button.closest('tr');
            const gradeId = button.getAttribute('data-grade-id');
            
            // Get input values
            const taskNameInput = row.querySelector('.task-name-input');
            const typeInput = row.querySelector('.type-input');
            const scoreInput = row.querySelector('.score-input');
            const newTaskName = taskNameInput.value.trim();
            const newType = typeInput.value;
            const newScore = parseInt(scoreInput.value);
            
            // Validation
            if (!newTaskName) {
                showAlert('error', 'Nama tugas tidak boleh kosong!');
                taskNameInput.focus();
                return;
            }
            
            if (!newType) {
                showAlert('error', 'Tipe tugas wajib dipilih!');
                typeInput.focus();
                return;
            }
            
            if (isNaN(newScore) || newScore < 0 || newScore > 100) {
                showAlert('error', 'Nilai harus berupa angka antara 0-100!');
                scoreInput.focus();
                return;
            }
            
            // Show loading state
            const originalHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="iconify animate-spin" data-icon="mdi:loading"></span>';
            
            // Prepare data for AJAX request
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('_method', 'PUT');
            formData.append('task_name', newTaskName);
            formData.append('score', newScore);
            formData.append('assignment_type', newType);
            
            // Send AJAX request
            fetch(`/teacher/grades/${gradeId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw { status: response.status, errors: errorData.errors || { message: errorData.message } };
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update display elements
                    const taskNameDisplay = row.querySelector('.task-name-display');
                    const typeDisplay = row.querySelector('.type-display');
                    const scoreDisplay = row.querySelector('.score-display');
                    
                    taskNameDisplay.textContent = newTaskName;
                    scoreDisplay.textContent = newScore;
                    
                    // Update type display
                    const typeText = newType === 'written' ? 'Tertulis' : 
                                   (newType === 'observation' ? 'Pengamatan' : 'PR');
                    const typeColorClass = newType === 'written' ? 'bg-blue-100 text-blue-800' : 
                                          (newType === 'observation' ? 'bg-green-100 text-green-800' : 
                                          'bg-yellow-100 text-yellow-800');
                    
                    typeDisplay.innerHTML = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${typeColorClass}">${typeText}</span>`;
                    
                    // Update score color based on new value
                    scoreDisplay.className = `score-display inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${
                        newScore >= 75 ? 'bg-green-100 text-green-800' : 
                        newScore >= 60 ? 'bg-yellow-100 text-yellow-800' : 
                        'bg-red-100 text-red-800'
                    }`;
                    
                    // Update data attributes for filtering
                    row.setAttribute('data-task-name', newTaskName);
                    row.setAttribute('data-task-type', newType);
                    
                    // Exit edit mode
                    cancelEdit(button);
                    
                    showAlert('success', data.message || 'Nilai berhasil diperbarui!');
                } else {
                    showAlert('error', data.message || 'Gagal memperbarui nilai!');
                }
            })
            .catch(error => {
                console.error('Update error:', error);
                let errorMsg = '';
                
                if (error.status === 422) {
                    errorMsg = 'Validasi gagal: ';
                    for (let field in error.errors) {
                        errorMsg += error.errors[field][0] + ' ';
                    }
                } else if (error.status === 404) {
                    errorMsg = 'Data nilai tidak ditemukan!';
                } else {
                    errorMsg = `Error: ${error.status} - ${error.errors?.message || 'Terjadi kesalahan saat memperbarui nilai.'}`;
                }
                
                showAlert('error', errorMsg);
            })
            .finally(() => {
                // Reset button state
                button.disabled = false;
                button.innerHTML = originalHtml;
            });
        }

        function editGrade(gradeId) {
            // This function is now replaced by toggleEditMode
            showAlert('info', `Edit grade dengan ID: ${gradeId} (Fitur dalam pengembangan)`);
        }

        function deleteGrade(gradeId) {
            if (confirm('Apakah Anda yakin ingin menghapus nilai ini?')) {
                // Show loading for all delete buttons with same ID
                const deleteButtons = document.querySelectorAll(`[onclick="deleteGrade(${gradeId})"]`);
                const originalHtml = deleteButtons[0].innerHTML;
                
                deleteButtons.forEach(btn => {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="iconify animate-spin" data-icon="mdi:loading"></span>';
                });
                
                // Prepare delete request
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('_method', 'DELETE');
                
                fetch(`/teacher/grades/${gradeId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw { status: response.status, message: errorData.message || 'Gagal menghapus nilai' };
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Remove the row from table
                        const row = document.querySelector(`tr[data-grade-id="${gradeId}"]`);
                        if (row) {
                            row.style.transition = 'all 0.3s ease';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(-100%)';
                            
                            setTimeout(() => {
                                row.remove();
                                // Re-number the rows
                                updateRowNumbers();
                                updateRecordCount();
                            }, 300);
                        }
                        
                        showAlert('success', data.message || 'Nilai berhasil dihapus!');
                    } else {
                        showAlert('error', data.message || 'Gagal menghapus nilai!');
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    showAlert('error', error.message || 'Terjadi kesalahan saat menghapus nilai.');
                })
                .finally(() => {
                    // Reset button state
                    deleteButtons.forEach(btn => {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    });
                });
            }
        }

        function updateRowNumbers() {
            const visibleRows = document.querySelectorAll('.grade-row:not([style*="display: none"])');
            visibleRows.forEach((row, index) => {
                const numberCell = row.querySelector('td:first-child');
                if (numberCell) {
                    numberCell.textContent = index + 1;
                }
                // Update alternating colors
                row.classList.remove('bg-white', 'bg-gray-50');
                row.classList.add(index % 2 === 0 ? 'bg-white' : 'bg-gray-50');
            });
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
        /* Inline edit styles */
        .task-name-input,
        .score-input,
        .type-input {
            transition: all 0.15s ease-in-out;
        }
        
        .task-name-input:focus,
        .score-input:focus,
        .type-input:focus {
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
            border-color: rgba(59, 130, 246, 0.8);
        }
        
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

        /* Row edit highlight */
        .grade-row {
            transition: all 0.2s ease;
        }
        
        /* Action buttons hover effects */
        .edit-btn:hover,
        .save-btn:hover,
        .cancel-btn:hover,
        .delete-btn:hover {
            transform: scale(1.1);
        }

        /* Filter transition effects */
        .grade-row {
            transition: all 0.3s ease;
        }

        /* Custom scrollbar for table */
        .overflow-x-auto::-webkit-scrollbar {
            height: 6px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</x-app-layout>