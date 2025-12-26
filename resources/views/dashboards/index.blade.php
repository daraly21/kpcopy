<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                @if ($role === 'admin')
                    Dashboard Admin
                @elseif($role === 'walikelas')
                    Dashboard Wali <span class="text-blue-600">{{ $className }}</span>
                @elseif($role === 'guru')
                    Dashboard Guru Mata Pelajaran <span class="text-blue-600">{{ $subjectName }}</span>
                @endif
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        {{-- DASHBOARD ADMIN --}}
        @if ($role === 'admin')
            {{-- Overview Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                {{-- Tahun Ajaran Aktif --}}
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-xl rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Tahun Ajaran Aktif</p>
                            <p class="text-3xl font-bold">{{ $activeYear->name ?? 'Belum ada' }}</p>
                        </div>
                        <div class="text-5xl opacity-50">📅</div>
                    </div>
                </div>

                {{-- Siswa Tahun Ini --}}
                <div class="bg-white shadow-xl rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Siswa Aktif Tahun Ini</p>
                            <p class="text-3xl font-bold text-gray-800">{{ $studentsThisYear }}</p>
                            @if ($activeYear)
                                <p class="text-xs text-gray-400">Tahun {{ $activeYear->name }}</p>
                            @endif
                        </div>
                        <div class="text-5xl">👥</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Chart 1: Statistik Sekolah --}}
                <div class="bg-white shadow-xl rounded-2xl p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Statistik Sekolah</h3>
                    <canvas id="mainChart" height="300"></canvas>
                </div>

                {{-- Chart 2: Perbandingan Jumlah --}}
                <div class="bg-white shadow-xl rounded-2xl p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Perbandingan Jumlah</h3>
                    <canvas id="compareChart" height="300"></canvas>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const waliKelas = {{ $totalWaliKelas ?? 0 }};
                const siswa = {{ $totalSiswa ?? 0 }};
                const kelas = {{ $totalKelas ?? 0 }};
                const mapel = {{ $totalMapel ?? 0 }};

                // Chart 1: Doughnut
                new Chart(document.getElementById('mainChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Wali Kelas', 'Siswa', 'Kelas', 'Mata Pelajaran'],
                        datasets: [{
                            data: [waliKelas, siswa, kelas, mapel],
                            backgroundColor: ['#3B82F6', '#10B981', '#8B5CF6', '#F59E0B'],
                            borderWidth: 3,
                            borderColor: '#fff',
                            hoverOffset: 20
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    font: {
                                        size: 14
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => `${ctx.label}: ${ctx.raw} orang/entri`
                                }
                            }
                        }
                    }
                });

                // Chart 2: Horizontal Bar
                new Chart(document.getElementById('compareChart'), {
                    type: 'bar',
                    data: {
                        labels: ['Wali Kelas', 'Siswa', 'Kelas', 'Mata Pelajaran'],
                        datasets: [{
                            label: 'Jumlah',
                            data: [waliKelas, siswa, kelas, mapel],
                            backgroundColor: '#6366F1',
                            borderRadius: 10,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => `Jumlah: ${ctx.raw}`
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: siswa > 500 ? 100 : 10
                                }
                            }
                        }
                    }
                });
            </script>
        @endif

        {{-- DASHBOARD WALI KELAS --}}
        @if ($role === 'walikelas')
            {{-- Overview Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                {{-- Total Siswa --}}
                <div class="bg-white shadow-xl rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Siswa</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $totalStudents }}</p>
                        </div>
                    </div>
                </div>

                {{-- Rata-rata Kelas --}}
                <div class="bg-white shadow-xl rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Rata-rata Kelas</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $classAverage }}</p>
                        </div>
                    </div>
                </div>

                {{-- Perlu Perhatian --}}
                <div class="bg-white shadow-xl rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Perlu Perhatian</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $studentsNeedAttention }}</p>
                            <p class="text-xs text-gray-400">siswa <60< /p>
                        </div>
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mata
                                        Pelajaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Rata-rata</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Tertinggi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terendah
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($subjectStats as $stat)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $stat['name'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $stat['average'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $stat['highest'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $stat['lowest'] }}</td>
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

                {{-- Aktivitas Input Nilai Terbaru --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-700 border-b pb-2 mb-4">Aktivitas Input Nilai Terbaru</h3>
                    <div class="space-y-4">
                        @forelse($recentActivities as $activity)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $activity->student_name }}</p>
                                    <div class="flex items-center">
                                        <span class="text-xs text-gray-500">{{ $activity->subject_name }} -
                                            {{ $activity->task_name }}</span>
                                        <span
                                            class="ml-2 px-2 py-0.5 text-xs rounded-full {{ $activity->score >= 75 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $activity->score }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">{{ $activity->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-gray-500">Belum ada aktivitas input nilai</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
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
        @endif

        {{-- DASHBOARD GURU MATA PELAJARAN --}}
        @if ($role === 'guru')
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                {{-- Statistik Utama --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Rata-rata Nilai</p>
                                <p class="text-2xl font-semibold text-gray-800">
                                    {{ $overallStats->average ? round($overallStats->average, 1) : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Perlu Remedial</p>
                                <p class="text-2xl font-semibold text-gray-800">{{ $studentsNeedRemedial }}</p>
                                <p class="text-xs text-gray-400">siswa <60< /p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    {{-- Distribusi Nilai --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-700 border-b pb-2 mb-4">Distribusi Nilai</h3>
                        <div class="relative h-80">
                            <canvas id="gradeDistributionChart"></canvas>
                        </div>
                    </div>

                    {{-- Aktivitas Input Nilai Terbaru --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-700 border-b pb-2 mb-4">Aktivitas Input Nilai Terbaru
                        </h3>
                        <div class="space-y-4">
                            @forelse($recentActivities as $activity)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $activity->student_name }}</p>
                                        <div class="flex items-center">
                                            <span class="text-xs text-gray-500">{{ $activity->task_name }}</span>
                                            <span
                                                class="ml-2 px-2 py-0.5 text-xs rounded-full {{ $activity->score >= 75 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $activity->score }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $activity->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-gray-500">Belum ada aktivitas input nilai</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Statistik Nilai Per Tugas --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-700 border-b pb-2 mb-4">Statistik Nilai Per Tugas</h3>
                    <div class="relative h-80">
                        <canvas id="taskStatsChart"></canvas>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Chart Distribusi Nilai
                    const ctx1 = document.getElementById('gradeDistributionChart');
                    new Chart(ctx1, {
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

                    // Chart Statistik Nilai Per Tugas
                    const ctx2 = document.getElementById('taskStatsChart');
                    new Chart(ctx2, {
                        type: 'bar',
                        data: {
                            labels: [@foreach ($taskStats as $task)'{{ $task['name'] }}', @endforeach],
                            datasets: [{
                                label: 'Rata-rata Nilai',
                                data: [@foreach ($taskStats as $task){{ $task['average'] }}, @endforeach],
                                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                });
            </script>
        @endif
    </div>
</x-app-layout>
