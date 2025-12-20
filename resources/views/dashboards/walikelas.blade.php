<x-app-layout title="Dashboard Guru">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard Wali
                <span class="text-blue-600">{{ $className }}</span>
            </h2>

        </div>
    </x-slot>

    <div class="py-6">


        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Statistik Per Mata Pelajaran --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-700 border-b pb-2 mb-4">Statistik Per Mata Pelajaran</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mata Pelajaran</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rata-rata</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tertinggi</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Terendah</th>
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


            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-700 border-b pb-2 mb-4">Aktivitas Input Nilai Terbaru</h3>
                <div class="space-y-4">
                    @forelse($recentActivities as $activity)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $activity->student_name }}
                                </p>
                                <div class="flex items-center">
                                    <span class="text-xs text-gray-500">{{ $activity->subject_name }} -
                                        {{ $activity->task_name }}</span>
                                    <span
                                        class="ml-2 px-2 py-0.5 text-xs rounded-full {{ $activity->score >= 75 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
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
