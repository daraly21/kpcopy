<?php
// app/Exports/GradesExport.php

namespace App\Exports;

use App\Models\Grade;
use App\Models\GradeTask;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassModel;
use App\Models\StudentClass;
use App\Models\AcademicYear;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class GradesExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $subject_id;
    protected $semester;
    protected $class_id;
    protected $subject;
    protected $class;
    protected $year;
    protected $data;
    protected $academicYear;

    public function __construct($subject_id, $semester, $class_id)
    {
        $this->subject_id = $subject_id;
        $this->semester = $semester;
        $this->class_id = $class_id;
        $this->subject = Subject::find($subject_id);
        $this->class = ClassModel::find($class_id);
        $this->academicYear = AcademicYear::where('is_active', 1)->first();
        $this->year = date('Y');
        $this->data = $this->getGradeData();
    }

    /**
     * Mengambil data untuk diekspor
     */
    public function collection()
    {
        $rows = new Collection();

        // Baris 1: Header (Mapel, Judul, Semester)
        $rows->push([
            'Mapel: ' . $this->subject->name,
            null,
            null,
            'DAFTAR NILAI ' . strtoupper($this->subject->name) . ' TH. ' . $this->year . '/' . ($this->year + 1),
            null, null, null, null, null, null, null, null, null, null, null,
            'SEMESTER: ' . ($this->semester == 'Odd' ? '1 (GANJIL)' : '2 (GENAP)'),
            null, null
        ]);

        // Baris 2: Kelas
        $rows->push([
            'Kelas: ' . $this->class->name,
            null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null
        ]);

        // Baris 3: Kosong
        $rows->push(array_fill(0, 18, null));

        // Baris 4-6: Header tabel
        $rows->push([
            'No', 'NIS', 'Nama Siswa',
            'FORMATIF', null, null, null, null, null, null, null, null, null, null, null,
            'SUMATIF', null,
            'Nilai Akhir'
        ]);

        $rows->push([
            null, null, null,
            'TERTULIS (A)', null, null, null, null, null,
            'NON TERTULIS (B)', null, null, null, null, null,
            null, null,
            null
        ]);

        $rows->push([
            null, null, null,
            '1', '2', '3', '4', '5', 'RT2',
            '1', '2', '3', '4', '5', 'RT2',
            'UTS', 'UAS',
            null
        ]);

        // Baris data siswa
        foreach ($this->data as $index => $student) {
            $rows->push([
                $index + 1,
                $student['student_number'],
                $student['name'],
                // Tertulis
                $student['written'][0],
                $student['written'][1],
                $student['written'][2],
                $student['written'][3],
                $student['written'][4],
                $student['average_written'] ? rtrim(rtrim(number_format($student['average_written'], 2, '.', ''), '0'), '.') : '-',
                // Non Tertulis (Observation)
                $student['observation'][0],
                $student['observation'][1],
                $student['observation'][2],
                $student['observation'][3],
                $student['observation'][4],
                $student['average_observation'] ? rtrim(rtrim(number_format($student['average_observation'], 2, '.', ''), '0'), '.') : '-',
                // Sumatif
                $student['midterm_score'] ?? '-',
                $student['final_exam_score'] ?? '-',
                // Akhir
                $student['final_score'] ? rtrim(rtrim(number_format($student['final_score'], 2, '.', ''), '0'), '.') : '-'
            ]);
        }

        return $rows;
    }

    /**
     * Mengatur header tabel
     */
    public function headings(): array
    {
        return [];
    }

    /**
     * Mengatur lebar kolom
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 12,  // NIS
            'C' => 25,  // Nama Siswa
            'D' => 8,   // Tertulis 1
            'E' => 8,   // Tertulis 2
            'F' => 8,   // Tertulis 3
            'G' => 8,   // Tertulis 4
            'H' => 8,   // Tertulis 5
            'I' => 8,   // RT2 Tertulis
            'J' => 8,   // Non Tertulis 1
            'K' => 8,   // Non Tertulis 2
            'L' => 8,   // Non Tertulis 3
            'M' => 8,   // Non Tertulis 4
            'N' => 8,   // Non Tertulis 5
            'O' => 8,   // RT2 Non Tertulis
            'P' => 8,   // UTS
            'Q' => 8,   // UAS
            'R' => 10,  // Nilai Akhir
        ];
    }

    /**
     * Mengatur styling (termasuk border)
     */
    public function styles(Worksheet $sheet)
    {
        // Merge cells untuk header
        $sheet->mergeCells('A1:C1'); // Mapel
        $sheet->mergeCells('D1:O1'); // Judul
        $sheet->mergeCells('P1:R1'); // Semester
        $sheet->mergeCells('A2:C2'); // Kelas
        $sheet->mergeCells('D2:R2'); // Kosong

        $sheet->mergeCells('D4:O4'); // FORMATIF
        $sheet->mergeCells('P4:Q4'); // SUMATIF
        $sheet->mergeCells('D5:I5'); // TERTULIS (A)
        $sheet->mergeCells('J5:O5'); // NON TERTULIS (B)

        // Styling header
        $sheet->getStyle('A1:R1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getStyle('D1:O1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('P1:R1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A2:C2')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
        ]);

        $sheet->getStyle('A4:R4')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF0F0F0'],
            ],
        ]);
        $sheet->getStyle('A5:R5')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF0F0F0'],
            ],
        ]);
        $sheet->getStyle('A6:R6')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF0F0F0'],
            ],
        ]);

        // Border untuk semua sel
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:R' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Styling untuk kolom Nama Siswa (rata kiri)
        $sheet->getStyle('C7:C' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        // Styling untuk baris data (zebra effect)
        for ($row = 7; $row <= $highestRow; $row++) {
            if (($row - 7) % 2 == 1) {
                $sheet->getStyle('A' . $row . ':R' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF7FAFC'],
                    ],
                ]);
            }
        }

        return [];
    }

    /**
     * Mengambil data untuk diekspor
     */
    private function getGradeData()
    {
        $result = [];
        $updates = [];

        // Return empty if no active academic year
        if (!$this->academicYear) {
            return $result;
        }

        // Get Student IDs from StudentClass pivot table for this class and active academic year
        $studentIds = StudentClass::where('class_id', $this->class_id)
                        ->where('academic_year_id', $this->academicYear->id)
                        ->pluck('student_id')
                        ->toArray();

        // Return empty if no students found
        if (empty($studentIds)) {
            return $result;
        }

        Student::whereIn('id', $studentIds)
               ->select('id', 'nis', 'name')
               ->chunk(10, function ($students) use (&$result, &$updates) {
                   $studentIds = $students->pluck('id')->toArray();
                   $grades = Grade::whereIn('student_id', $studentIds)
                                  ->where('subject_id', $this->subject_id)
                                  ->where('semester', $this->semester)
                                  ->where('academic_year_id', $this->academicYear->id)
                                  ->with(['gradeTasks' => function($query) {
                                      $query->orderBy('created_at', 'asc');
                                  }])
                                  ->get()
                                  ->keyBy('student_id');

                   foreach ($students as $student) {
                       $studentData = [
                           'student_id' => $student->id,
                           'student_number' => $student->nis,
                           'name' => $student->name,
                           'written' => array_fill(0, 5, '-'),
                           'observation' => array_fill(0, 5, '-'),
                           'average_written' => null,
                           'average_observation' => null,
                           'midterm_score' => null,
                           'final_exam_score' => null,
                           'final_score' => null
                       ];
                       
                       $grade = $grades->get($student->id);
                       
                       if ($grade) {
                           $tasks = $grade->gradeTasks->sortBy('created_at');
                           
                           $writtenCounter = 0;
                           $observationCounter = 0;
                           $sumatifCounter = 0;
                           
                           $writtenScores = [];
                           $observationScores = [];
                           $sumatifScores = [];
                           
                           foreach ($tasks as $task) {
                               if ($task->type === 'written' && $writtenCounter < 5) {
                                   $studentData['written'][$writtenCounter] = $task->score;
                                   $writtenScores[] = $task->score;
                                   $writtenCounter++;
                               } elseif ($task->type === 'observation' && $observationCounter < 5) {
                                   $studentData['observation'][$observationCounter] = $task->score;
                                   $observationScores[] = $task->score;
                                   $observationCounter++;
                               } elseif ($task->type === 'sumatif' && $sumatifCounter < 2) {
                                   $sumatifScores[] = $task->score;
                                   $sumatifCounter++;
                               }
                           }
                           
                           $averageWritten = !empty($writtenScores) ? array_sum($writtenScores) / count($writtenScores) : null;
                           $averageObservation = !empty($observationScores) ? array_sum($observationScores) / count($observationScores) : null;
                           
                           $midtermScore = isset($sumatifScores[0]) ? $sumatifScores[0] : null;
                           $finalExamScore = isset($sumatifScores[1]) ? $sumatifScores[1] : null;
                           
                           $components = array_filter([
                               $averageWritten,
                               $averageObservation,
                               $midtermScore,
                               $finalExamScore
                           ], fn($value) => !is_null($value));
                           
                           $finalScore = !empty($components) ? array_sum($components) / count($components) : 0;
                           
                           $studentData['average_written'] = $averageWritten;
                           $studentData['average_observation'] = $averageObservation;
                           $studentData['midterm_score'] = $midtermScore;
                           $studentData['final_exam_score'] = $finalExamScore;
                           $studentData['final_score'] = $finalScore;
                       }
                       
                       $result[] = $studentData;
                   }
               });

        return $result;
    }
}