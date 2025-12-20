<!DOCTYPE html>
<html>

<head>
    <title>Daftar Nilai</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }

        .header {
            width: 100%;
            margin-bottom: 15px;
        }

        .left-header {
            width: 33%;
            float: left;
        }

        .center-header {
            width: 33%;
            float: left;
            text-align: center;
        }

        .right-header {
            width: 33%;
            float: right;
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }

        th,
        td {
            border: 1px solid black;
            padding: 3px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .student-name {
            text-align: left;
            padding-left: 5px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .bg-gray-50 {
            background-color: #f7fafc;
        }
    </style>
</head>

<body>
    <div class="header clearfix">
        <div class="left-header">
            <p><strong>Mapel:</strong> {{ $subject->name }}</p>
            <p><strong>Kelas:</strong> {{ $class->name }}</p>
        </div>
        <div class="center-header">
            <p><strong>DAFTAR NILAI {{ strtoupper($subject->name) }} TH.
                    {{ $year }}/{{ $year + 1 }}</strong></p>
        </div>
        <div class="right-header">
            <p><strong>SEMESTER:</strong> {{ $semester == 'Odd' ? '1 (GANJIL)' : '2 (GENAP)' }}</p>
        </div>
    </div>

    <table>
        <!-- Table header -->
        <thead>
            <tr>
                <th rowspan="3">No</th>
                <th rowspan="3">NIS</th>
                <th rowspan="3">Nama Siswa</th>
                <th colspan="12">FORMATIF</th>
                <th colspan="2" rowspan="2">SUMATIF</th>
                <th rowspan="3">Nilai Akhir</th>
            </tr>
            <tr>
                <th colspan="6">TERTULIS (A)</th>
                <th colspan="6">NON TERTULIS (B)</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= 5; $i++)
                    <th>{{ $i }}</th>
                @endfor
                <th>RT2</th>
                @for ($i = 1; $i <= 5; $i++)
                    <th>{{ $i }}</th>
                @endfor
                <th>RT2</th>
                <th>UTS</th>
                <th>UAS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $student)
                <tr class="{{ $index % 2 === 0 ? '' : 'bg-gray-50' }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student['student_number'] }}</td>
                    <td class="student-name">{{ $student['name'] }}</td>
                    <!-- Written scores (Tertulis) -->
                    @for ($i = 0; $i < 5; $i++)
                        <td>{{ $student['written'][$i] }}</td>
                    @endfor
                    <td>{{ $student['average_written'] ? rtrim(rtrim(number_format($student['average_written'], 2, '.', ''), '0'), '.') : '-' }}
                    </td>
                    <!-- Observation scores (Non Tertulis) -->
                    @for ($i = 0; $i < 5; $i++)
                        <td>{{ $student['observation'][$i] }}</td>
                    @endfor
                    <td>{{ $student['average_observation'] ? rtrim(rtrim(number_format($student['average_observation'], 2, '.', ''), '0'), '.') : '-' }}
                    </td>
                    <!-- Sumatif scores -->
                    <td>{{ $student['midterm_score'] ?? '-' }}</td>
                    <td>{{ $student['final_exam_score'] ?? '-' }}</td>
                    <!-- Final scores -->
                    <td>{{ $student['final_score'] ? rtrim(rtrim(number_format($student['final_score'], 2, '.', ''), '0'), '.') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="18">Tidak ada data nilai untuk filter yang dipilih</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
