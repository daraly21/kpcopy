<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="p-6">


    {{-- Info Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white shadow rounded-2xl p-4 flex items-center">
            <div class="text-blue-500 text-3xl mr-4">👨‍🏫</div>
            <div>
                <p class="text-gray-600 text-sm">Total Wali Kelas</p>
                <p class="text-xl font-bold">{{ $totalWaliKelas }}</p>
            </div>
        </div>
        <div class="bg-white shadow rounded-2xl p-4 flex items-center">
            <div class="text-green-500 text-3xl mr-4">🧑‍🎓</div>
            <div>
                <p class="text-gray-600 text-sm">Total Siswa</p>
                <p class="text-xl font-bold">{{ $totalSiswa }}</p>
            </div>
        </div>
        <div class="bg-white shadow rounded-2xl p-4 flex items-center">
            <div class="text-purple-500 text-3xl mr-4">🏫</div>
            <div>
                <p class="text-gray-600 text-sm">Total Kelas</p>
                <p class="text-xl font-bold">{{ $totalKelas }}</p>
            </div>
        </div>
        <div class="bg-white shadow rounded-2xl p-4 flex items-center">
            <div class="text-yellow-500 text-3xl mr-4">📚</div>
            <div>
                <p class="text-gray-600 text-sm">Total Mata Pelajaran</p>
                <p class="text-xl font-bold">{{ $totalMapel }}</p>
            </div>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="bg-white shadow rounded-2xl p-6">
        <h2 class="text-lg font-semibold mb-4">Statistik Data Utama</h2>
        <canvas id="dataChart" height="100"></canvas>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('dataChart').getContext('2d');
    const dataChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Wali Kelas', 'Siswa', 'Kelas', 'Mata Pelajaran'],
            datasets: [{
                label: 'Jumlah',
                data: [{{ $totalWaliKelas }}, {{ $totalSiswa }}, {{ $totalKelas }}, {{ $totalMapel }}],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)',  // Blue
                    'rgba(34, 197, 94, 0.7)',   // Green
                    'rgba(139, 92, 246, 0.7)',  // Purple
                    'rgba(250, 204, 21, 0.7)'   // Yellow
                ],
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
</x-app-layout>
