<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="p-6">

 {{-- Chart Section --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Chart 1: Jumlah Utama (pake Doughnut biar adil) --}}
    <div class="bg-white shadow-xl rounded-2xl p-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Statistik Sekolah</h3>
        <canvas id="mainChart" height="300"></canvas>
    </div>

    {{-- Chart 2: Perbandingan Detail (pake Horizontal Bar biar keliatan semua) --}}
    <div class="bg-white shadow-xl rounded-2xl p-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Perbandingan Jumlah</h3>
        <canvas id="compareChart" height="300"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Pastikan data aman (null → 0)
const waliKelas = {{ $totalWaliKelas ?? 0 }};
const siswa     = {{ $totalSiswa ?? 0 }};
const kelas     = {{ $totalKelas ?? 0 }};
const mapel     = {{ $totalMapel ?? 0 }};

// Chart 1: Doughnut (cantik + semua keliatan meski beda skala jauh)
new Chart(document.getElementById('mainChart'), {
    type: 'doughnut',
    data: {
        labels: ['Wali Kelas', 'Siswa', 'Kelas', 'Mata Pelajaran'],
        datasets: [{
            data: [waliKelas, siswa, kelas, mapel],
            backgroundColor: [
                '#3B82F6', // blue-500
                '#10B981', // green-500
                '#8B5CF6', // purple-500
                '#F59E0B'  // amber-500
            ],
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 20
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { padding: 20, font: { size: 14 } } },
            tooltip: { callbacks: { label: ctx => `${ctx.label}: ${ctx.raw} orang/entri` } }
        }
    }
});

// Chart 2: Horizontal Bar (paling adil buat bandingin angka yang beda jauh)
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
        indexAxis: 'y', // ini yang bikin HORIZONTAL
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => `Jumlah: ${ctx.raw}` } }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: { stepSize: siswa > 500 ? 100 : 10 }
            }
        }
    }
});
</script>
</x-app-layout>
