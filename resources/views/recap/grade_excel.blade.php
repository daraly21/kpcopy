<table border="1" style="border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="3" style="border: 1px solid black;">Mapel: {{ $subject->name }}</th>
            <th colspan="17" style="border: 1px solid black;">DAFTAR NILAI MI.......................... TH. {{ $year }}/{{ $year+1 }}</th>
            <th colspan="5" style="border: 1px solid black;">SEMESTER: {{ $semester == 'Odd' ? '1 (GASAL)' : '2 (GENAP)' }}</th>
        </tr>
        <tr>
            <th colspan="3" style="border: 1px solid black;">Kelas: {{ $class->name }}</th>
            <th colspan="22" style="border: 1px solid black;"></th>
        </tr>
        <tr></tr>
        <tr>
            <th rowspan="4" style="border: 1px solid black;">No</th>
            <th rowspan="4" style="border: 1px solid black;">NIS</th>
            <th rowspan="4" style="border: 1px solid black;">Nama Siswa</th>
            <th colspan="19" style="border: 1px solid black;">FORMATIF</th>
            <th colspan="2" rowspan="2" style="border: 1px solid black;">SUMATIF</th>
            <th rowspan="4" style="border: 1px solid black;">RATA-RATA</th>
            <th rowspan="4" style="border: 1px solid black;">PREDIKAT</th>
            <th rowspan="4" style="border: 1px solid black;">DESKRIPSI</th>
        </tr>
        <tr>
            <th colspan="8" style="border: 1px solid black;">TERTULIS (A)</th>
            <th colspan="5" style="border: 1px solid black;">PENGAMATAN (B)</th>
            <th colspan="6" style="border: 1px solid black;">PR</th>
        </tr>
        <tr>
            <!-- Tertulis -->
            <th style="border: 1px solid black;">1</th>
            <th style="border: 1px solid black;">2</th>
            <th style="border: 1px solid black;">3</th>
            <th style="border: 1px solid black;">4</th>
            <th style="border: 1px solid black;">5</th>
            <th style="border: 1px solid black;">6</th>
            <th style="border: 1px solid black;">7</th>
            <th style="border: 1px solid black;">RT2</th>
            <!-- Pengamatan -->
            <th style="border: 1px solid black;">1</th>
            <th style="border: 1px solid black;">2</th>
            <th style="border: 1px solid black;">3</th>
            <th style="border: 1px solid black;">4</th>
            <th style="border: 1px solid black;">RT2</th>
            <!-- PR -->
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
        <tr>
            <!-- Penomoran kolom -->
            <th style="border: 1px solid black;">(1)</th>
            <th style="border: 1px solid black;">(2)</th>
            <th style="border: 1px solid black;">(3)</th>
            <th style="border: 1px solid black;">(4)</th>
            <th style="border: 1px solid black;">(5)</th>
            <th style="border: 1px solid black;">(6)</th>
            <th style="border: 1px solid black;">(7)</th>
            <th style="border: 1px solid black;">(8)</th>
            <th style="border: 1px solid black;">(9)</th>
            <th style="border: 1px solid black;">(10)</th>
            <th style="border: 1px solid black;">(11)</th>
            <th style="border: 1px solid black;">(12)</th>
            <th style="border: 1px solid black;">(13)</th>
            <th style="border: 1px solid black;">(14)</th>
            <th style="border: 1px solid black;">(15)</th>
            <th style="border: 1px solid black;">(16)</th>
            <th style="border: 1px solid black;">(17)</th>
            <th style="border: 1px solid black;">(18)</th>
            <th style="border: 1px solid black;">(19)</th>
            <th style="border: 1px solid black;">(20)</th>
            <th style="border: 1px solid black;">(21)</th>
            <th style="border: 1px solid black;">(22)</th>
            <th style="border: 1px solid black;">(23)</th>
            <th style="border: 1px solid black;">(24)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $student)
        <tr>
            <td style="border: 1px solid black;">{{ $index + 1 }}</td>
            <td style="border: 1px solid black;">{{ $student['student_number'] }}</td>
            <td style="border: 1px solid black;">{{ $student['name'] }}</td>
            
            <!-- Tertulis -->
            @foreach($student['written'] as $written)
                <td style="border: 1px solid black;">{{ $written }}</td>
            @endforeach
            <td style="border: 1px solid black;">{{ $student['average_written'] }}</td>
            
            <!-- Pengamatan -->
            @foreach($student['observation'] as $obs)
                <td style="border: 1px solid black;">{{ $obs }}</td>
            @endforeach
            <td style="border: 1px solid black;">{{ $student['average_observation'] }}</td>
            
            <!-- PR -->
            @foreach($student['homework'] as $hw)
                <td style="border: 1px solid black;">{{ $hw }}</td>
            @endforeach
            <td style="border: 1px solid black;">{{ $student['average_homework'] }}</td>
            
            <!-- Sumatif -->
            <td style="border: 1px solid black;">{{ $student['midterm_score'] }}</td>
            <td style="border: 1px solid black;">{{ $student['final_exam_score'] }}</td>
            
            <!-- Akhir -->
            <td style="border: 1px solid black;">{{ $student['final_score'] }}</td>
            <td style="border: 1px solid black;">{{ $student['grade_letter'] }}</td>
            <td style="border: 1px solid black;">
                @if($student['grade_letter'] == 'A')
                    Sangat Baik
                @elseif($student['grade_letter'] == 'B')
                    Baik
                @elseif($student['grade_letter'] == 'C')
                    Cukup
                @elseif($student['grade_letter'] == 'D')
                    Perlu Bimbingan
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>