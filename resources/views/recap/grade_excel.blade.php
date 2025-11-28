<table border="1" style="border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="3" style="border: 1px solid black;">Mapel: {{ $subject->name }}</th>
            <th colspan="12" style="border: 1px solid black;">DAFTAR NILAI {{ strtoupper($subject->name) }} TH. {{ $year }}/{{ $year+1 }}</th>
            <th colspan="3" style="border: 1px solid black;">SEMESTER: {{ $semester == 'Odd' ? '1 (GANJIL)' : '2 (GENAP)' }}</th>
        </tr>
        <tr>
            <th colspan="3" style="border: 1px solid black;">Kelas: {{ $class->name }}</th>
            <th colspan="15" style="border: 1px solid black;"></th>
        </tr>
        <tr></tr>
        <tr>
            <th rowspan="3" style="border: 1px solid black;">No</th>
            <th rowspan="3" style="border: 1px solid black;">NIS</th>
            <th rowspan="3" style="border: 1px solid black;">Nama Siswa</th>
            <th colspan="12" style="border: 1px solid black;">FORMATIF</th>
            <th colspan="2" rowspan="2" style="border: 1px solid black;">SUMATIF</th>
            <th rowspan="3" style="border: 1px solid black;">Nilai Akhir</th>
        </tr>
        <tr>
            <th colspan="6" style="border: 1px solid black;">TERTULIS (A)</th>
            <th colspan="6" style="border: 1px solid black;">NON TERTULIS (B)</th>
        </tr>
        <tr>
            <!-- Tertulis -->
            <th style="border: 1px solid black;">1</th>
            <th style="border: 1px solid black;">2</th>
            <th style="border: 1px solid black;">3</th>
            <th style="border: 1px solid black;">4</th>
            <th style="border: 1px solid black;">5</th>
            <th style="border: 1px solid black;">RT2</th>
            <!-- Non Tertulis -->
            <th style="border: 1px solid black;">1</th>
            <th style="border: 1px solid black;">2</th>
            <th style="border: 1px solid black;">3</th>
            <th style="border: 1px solid black;">4</th>
            <th style="border: 1px solid black;">5</th>
            <th style="border: 1px solid black;">RT2</th>
            <!-- Sumatif -->
            <th style="border: 1px solid black;">UTS</th>
            <th style="border: 1px solid black;">UAS</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $student)
        <tr>
            <td style="border: 1px solid black;">{{ $index + 1 }}</td>
            <td style="border: 1px solid black;">{{ $student['student_number'] }}</td>
            <td style="border: 1px solid black;">{{ $student['name'] }}</td>
            
            <!-- Written scores (Tertulis) -->
            @for ($i = 0; $i < 5; $i++)
                <td style="border: 1px solid black;">{{ $student['written'][$i] }}</td>
            @endfor
            <td style="border: 1px solid black;">{{ $student['average_written'] ?? '-' }}</td>
            
            <!-- Observation scores (Non Tertulis) -->
            @for ($i = 0; $i < 5; $i++)
                <td style="border: 1px solid black;">{{ $student['observation'][$i] }}</td>
            @endfor
            <td style="border: 1px solid black;">{{ $student['average_observation'] ?? '-' }}</td>
            
            <!-- Sumatif scores -->
            <td style="border: 1px solid black;">{{ $student['midterm_score'] ?? '-' }}</td>
            <td style="border: 1px solid black;">{{ $student['final_exam_score'] ?? '-' }}</td>
            
            <!-- Final score -->
            <td style="border: 1px solid black;">{{ $student['final_score'] ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>