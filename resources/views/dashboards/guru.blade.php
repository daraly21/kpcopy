<x-app-layout title="Dashboard Guru">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Guru') }}
                <span class="text-blue-600">{{ $className }}</span>
            </h2>
            
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Statistik Utama --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Siswa</p>
                            <p class="text-2xl font-semibold text-gray-800">{{ $totalStudents }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Rata-rata Nilai</p>
                            <p class="text-2xl font-semibold text-gray-800">{{ $overallStats->average ? round($overallStats->average, 1) : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Nilai Tertinggi</p>
                            <p class="text-2xl font-semibold text-gray-800">{{ $overallStats->highest ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Nilai Terendah</p>
                            <p class="text-2xl font-semibold text-gray-800">{{ $overallStats->lowest ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Notifikasi Siswa Tanpa Nilai --}}
            @if($studentsWithoutGrades > 0)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Ada <span class="font-medium">{{ $studentsWithoutGrades }}</span> siswa yang belum memiliki catatan nilai.
                            <a href="#" class="font-medium underline text-yellow-700 hover:text-yellow-600">
                                Lihat daftar siswa
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Distribusi Nilai --}}
                <div class="col-span-1 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-700 border-b pb-2 mb-4">Distribusi Nilai</h3>
                    <div class="relative h-80">
                        <canvas id="gradeDistributionChart"></canvas>
                    </div>
                </div>
                
                {{-- Top 5 Siswa --}}
                <div class="col-span-1 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-700 border-b pb-2 mb-4">5 Siswa Nilai Tertinggi</h3>
                    <div class="space-y-4">
                        @foreach($topStudents as $student)
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-500 font-bold">
                                    {{ $loop->iteration }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                                </div>
                            </div>
                            <div>
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                    {{ $student->average_score >= 90 ? 'bg-green-100 text-green-800' : 
                                       ($student->average_score >= 75 ? 'bg-blue-100 text-blue-800' : 
                                       ($student->average_score >= 60 ? 'bg-yellow-100 text-yellow-800' : 
                                       'bg-red-100 text-red-800')) }}">
                                    {{ round($student->average_score, 1) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                        
                        @if(count($topStudents) == 0)
                        <div class="text-center py-4 text-gray-500">
                            Belum ada data nilai
                        </div>
                        @endif
                    </div>
                </div>
                
                {{-- Siswa yang Membutuhkan Perhatian --}}
                <div class="col-span-1 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-700 border-b pb-2 mb-4">Siswa yang Membutuhkan Perhatian</h3>
                    <div class="space-y-4">
                        @foreach($lowStudents as $student)
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-500 font-bold">
                                    {{ $loop->iteration }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                                </div>
                            </div>
                            <div>
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                    {{ $student->average_score >= 90 ? 'bg-green-100 text-green-800' : 
                                       ($student->average_score >= 75 ? 'bg-blue-100 text-blue-800' : 
                                       ($student->average_score >= 60 ? 'bg-yellow-100 text-yellow-800' : 
                                       'bg-red-100 text-red-800')) }}">
                                    {{ round($student->average_score, 1) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                        
                        @if(count($lowStudents) == 0)
                        <div class="text-center py-4 text-gray-500">
                            Belum ada data nilai
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Statistik Per Mata Pelajaran --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-700 border-b pb-2 mb-4">Statistik Per Mata Pelajaran</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tertinggi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terendah</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($subjectStats as $stat)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $stat['name'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $stat['average'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $stat['highest'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $stat['lowest'] }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada data nilai mata pelajaran
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- Aktivitas Terbaru --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-700 border-b pb-2 mb-4">Aktivitas Input Nilai Terbaru</h3>
                    <div class="space-y-4">
                        @forelse($recentActivities as $activity)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $activity->student_name }}
                                </p>
                                <div class="flex items-center">
                                    <span class="text-xs text-gray-500">{{ $activity->subject_name }} - {{ $activity->task_name }}</span>
                                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full {{ $activity->score >= 75 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $activity->score }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-gray-500">
                            Belum ada aktivitas input nilai
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- JavaScript untuk Chart --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart Distribusi Nilai
            const ctx = document.getElementById('gradeDistributionChart');
            
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Sangat Baik (≥90)', 'Baik (75-89)', 'Cukup (60-74)', 'Perlu Perbaikan (<60)'],
                    datasets: [{
                        data: [
                            {{ $gradeDistribution['sangat_baik'] }}, 
                            {{ $gradeDistribution['baik'] }}, 
                            {{ $gradeDistribution['cukup'] }}, 
                            {{ $gradeDistribution['perlu_perbaikan'] }}
                        ],
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.7)',
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(250, 204, 21, 0.7)',
                            'rgba(239, 68, 68, 0.7)'
                        ],
                        borderColor: [
                            'rgba(34, 197, 94, 1)',
                            'rgba(59, 130, 246, 1)',
                            'rgba(250, 204, 21, 1)',
                            'rgba(239, 68, 68, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>