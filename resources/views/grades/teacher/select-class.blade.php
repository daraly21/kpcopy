<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 flex items-center">
            <span class="iconify text-indigo-600 text-2xl mr-2" data-icon="mdi:school"></span>
            Manajemen Nilai - Pilih Kelas
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Alert Messages --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-6">
                    <div class="flex items-center">
                        <span class="iconify text-green-600 text-xl mr-2" data-icon="mdi:check-circle"></span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">
                    <div class="flex items-center">
                        <span class="iconify text-red-600 text-xl mr-2" data-icon="mdi:alert-circle"></span>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{-- Subject Info Card
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg mb-8 text-white">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="iconify text-4xl mr-4" data-icon="mdi:book-open-variant"></span>
                            <div>
                                <h2 class="text-2xl font-bold">{{ $subject->name }}</h2>
                                <p class="text-indigo-100 mt-1">Semester {{ now()->month >= 7 && now()->month <= 12 ? 'Ganjil' : 'Genap' }} {{ now()->year }}/{{ now()->year + 1 }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-indigo-100 text-sm">Guru Pengampu</p>
                            <p class="text-lg font-semibold">{{ Auth::user()->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Overview --}}
            {{-- <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <span class="iconify text-blue-600 text-2xl" data-icon="mdi:account-group"></span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Kelas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $classes->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-lg">
                            <span class="iconify text-green-600 text-2xl" data-icon="mdi:account-multiple"></span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Siswa</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalStudents }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-3 rounded-lg">
                            <span class="iconify text-yellow-600 text-2xl" data-icon="mdi:clipboard-check"></span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Nilai Tersimpan</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalGrades }}</p>
                        </div>
                    </div>
                </div>
            </div> --}}

            {{-- Class Selection Grid --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="iconify text-indigo-600 text-xl mr-2" data-icon="mdi:view-grid"></span>
                        Pilih Kelas untuk Mengelola Nilai
                    </h3>
                    <p class="text-gray-600 text-sm mt-1">Klik pada kelas untuk mulai mengelola nilai siswa</p>
                </div>

                <div class="p-6">
                    @if($classes->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($classes as $class)
                                <div class="group border border-gray-200 rounded-lg p-4 hover:border-indigo-300 hover:shadow-md transition-all duration-200 cursor-pointer"
                                     onclick="selectClass('{{ $class->name }}', {{ $class->id }})">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center">
                                            <div class="bg-{{ ['blue', 'green', 'purple', 'red', 'yellow', 'indigo'][$loop->index % 6] }}-100 p-2 rounded-lg group-hover:bg-indigo-100 transition-colors">
                                                <span class="iconify text-{{ ['blue', 'green', 'purple', 'red', 'yellow', 'indigo'][$loop->index % 6] }}-600 text-xl group-hover:text-indigo-600" data-icon="mdi:account-group"></span>
                                            </div>
                                            <div class="ml-3">
                                                <h4 class="font-semibold text-gray-900">{{ $class->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $class->students_count }} siswa</p>
                                            </div>
                                        </div>
                                        <span class="iconify text-gray-400 group-hover:text-indigo-600" data-icon="mdi:chevron-right"></span>
                                    </div>
                                    
                                    <div class="flex justify-between text-xs text-gray-500 mb-2">
                                        <span>{{ $class->total_tasks }} tugas</span>
                                        <span>{{ number_format($class->completion_percentage, 0) }}% selesai</span>
                                    </div>
                                    
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        @php
                                            $percentage = $class->completion_percentage;
                                            $colorClass = $percentage >= 80 ? 'bg-green-500' : 
                                                         ($percentage >= 60 ? 'bg-yellow-500' : 'bg-red-500');
                                        @endphp
                                        <div class="{{ $colorClass }} h-1.5 rounded-full transition-all" 
                                             style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <span class="iconify text-gray-400 text-6xl mb-4" data-icon="mdi:school-off"></span>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Kelas</h3>
                            <p class="text-gray-500">Belum ada kelas yang tersedia. Silakan hubungi admin untuk menambahkan kelas.</p>
                        </div>
                    @endif
                </div>
            </div>

          
        </div>
    </div>

    <script>
        function selectClass(className, classId) {
            // Menampilkan loading state
            const selectedCard = event.currentTarget;
            selectedCard.style.backgroundColor = '#e0f2fe';
            selectedCard.style.borderColor = '#0284c7';
            
            // Add loading indicator
            const chevronIcon = selectedCard.querySelector('[data-icon="mdi:chevron-right"]');
            chevronIcon.setAttribute('data-icon', 'mdi:loading');
            chevronIcon.style.animation = 'spin 1s linear infinite';
            
            // Navigate to grades management page
           const url = `{{ route('teacher.grades.index', ['subjectId' => $subject->id]) }}?class_id=${classId}&class_name=${encodeURIComponent(className)}`;
            window.location.href = url;
        }

      
        // Add CSS animation for loading spin
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    </script>
</x-app-layout>