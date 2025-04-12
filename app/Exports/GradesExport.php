<?php
// app/Exports/GradesExport.php

namespace App\Exports;

use App\Models\Grade;
use App\Models\GradeTask;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassModel;
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

    public function __construct($subject_id, $semester, $class_id)
    {
        $this->subject_id = $subject_id;
        $this->semester = $semester;
        $this->class_id = $class_id;
        $this->subject = Subject::find($subject_id);
        $this->class = ClassModel::find($class_id);
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
            'DAFTAR NILAI MI.......................... TH. ' . $this->year . '/' . ($this->year + 1),
            null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null,
            'SEMESTER: ' . ($this->semester == 'Odd' ? '1 (GASAL)' : '2 (GENAP)'),
            null, null, null, null
        ]);

        // Baris 2: Kelas
        $rows->push([
            'Kelas: ' . $this->class->name,
            null,
            null,
            null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null
        ]);

        // Baris 3: Kosong
        $rows->push(array_fill(0, 25, null));

        // Baris 4-6: Header tabel
        $rows->push([
            'No', 'NIS', 'Nama Siswa',
            'FORMATIF', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null,
            'SUMATIF', null,
            'NILAI AKHIR', 'PREDIKAT', 'DESKRIPSI'
        ]);

        $rows->push([
            null, null, null,
            'TERTULIS (A)', null, null, null, null, null,
            'PENGAMATAN (B)', null, null, null, null, null,
            'TUGAS (P)', null, null, null, null, null,
            null, null,
            null, null, null
        ]);

        $rows->push([
            null, null, null,
            '1', '2', '3', '4', '5', 'RT2',
            '1', '2', '3', '4', '5', 'RT2',
            '1', '2', '3', '4', '5', 'RT2',
            'UTS', 'UAS',
            null, null, null
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
                $student['average_written'] ?? '-',
                // Pengamatan
                $student['observation'][0],
                $student['observation'][1],
                $student['observation'][2],
                $student['observation'][3],
                $student['observation'][4],
                $student['average_observation'] ?? '-',
                // Tugas
                $student['homework'][0],
                $student['homework'][1],
                $student['homework'][2],
                $student['homework'][3],
                $student['homework'][4],
                $student['average_homework'] ?? '-',
                // Sumatif
                $student['midterm_score'] ?? '-',
                $student['final_exam_score'] ?? '-',
                // Akhir
                $student['final_score'] ?? '-',
                $student['grade_letter'] ?? '-',
                $this->getDescription($student['grade_letter'] ?? '-')
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
            'B' => 10,  // NIS
            'C' => 20,  // Nama Siswa
            'D' => 8,   // Tertulis 1
            'E' => 8,   // Tertulis 2
            'F' => 8,   // Tertulis 3
            'G' => 8,   // Tertulis 4
            'H' => 8,   // Tertulis 5
            'I' => 8,   // RT2 Tertulis
            'J' => 8,   // Pengamatan 1
            'K' => 8,   // Pengamatan 2
            'L' => 8,   // Pengamatan 3
            'M' => 8,   // Pengamatan 4
            'N' => 8,   // Pengamatan 5
            'O' => 8,   // RT2 Pengamatan
            'P' => 8,   // Tugas 1
            'Q' => 8,   // Tugas 2
            'R' => 8,   // Tugas 3
            'S' => 8,   // Tugas 4
            'T' => 8,   // Tugas 5
            'U' => 8,   // RT2 Tugas
            'V' => 8,   // UTS
            'W' => 8,   // UAS
            'X' => 10,  // Nilai Akhir
            'Y' => 10,  // Predikat
            'Z' => 20,  // Deskripsi
        ];
    }

    /**
     * Mengatur styling (termasuk border)
     */
    public function styles(Worksheet $sheet)
    {
        // Merge cells untuk header
        $sheet->mergeCells('A1:C1'); // Mapel
        $sheet->mergeCells('D1:U1'); // Judul
        $sheet->mergeCells('V1:Y1'); // Semester
        $sheet->mergeCells('A2:C2'); // Kelas
        $sheet->mergeCells('D2:Y2'); // Kosong

        $sheet->mergeCells('D4:U4'); // FORMATIF
        $sheet->mergeCells('V4:W4'); // SUMATIF
        $sheet->mergeCells('D5:I5'); // TERTULIS (A)
        $sheet->mergeCells('J5:O5'); // PENGAMATAN (B)
        $sheet->mergeCells('P5:U5'); // TUGAS (P)

        // Styling header
        $sheet->getStyle('A1:Y1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getStyle('D1:U1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('V1:Y1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A2:C2')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
        ]);

        $sheet->getStyle('A4:Z4')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF0F0F0'],
            ],
        ]);
        $sheet->getStyle('A5:Z5')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF0F0F0'],
            ],
        ]);
        $sheet->getStyle('A6:Z6')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF0F0F0'],
            ],
        ]);

        // Border untuk semua sel
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:Z' . $highestRow)->applyFromArray([
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
                $sheet->getStyle('A' . $row . ':Z' . $row)->applyFromArray([
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
        $students = Student::where('class_id', $this->class_id)
            ->select('id', 'nis', 'name')
            ->get()
            ->keyBy('id');
        
        $grades = Grade::where('subject_id', $this->subject_id)
            ->where('semester', $this->semester)
            ->whereIn('student_id', $students->pluck('id'))
            ->select('id', 'student_id', 'midterm_score', 'final_exam_score', 'average_written', 'average_observation', 'average_homework', 'final_score', 'grade_letter')
            ->get()
            ->keyBy('student_id');
        
        $gradeTasks = GradeTask::whereIn('grades_id', $grades->pluck('id'))
            ->select('grades_id', 'type', 'score')
            ->orderBy('created_at')
            ->get()
            ->groupBy('grades_id');
        
        $result = [];
        
        foreach ($students as $student) {
            $studentData = [
                'student_id' => $student->id,
                'student_number' => $student->nis,
                'name' => $student->name,
                'written' => array_fill(0, 5, '-'),
                'observation' => array_fill(0, 5, '-'),
                'homework' => array_fill(0, 5, '-'),
                'average_written' => null,
                'average_observation' => null,
                'average_homework' => null,
                'midterm_score' => null,
                'final_exam_score' => null,
                'final_score' => null,
                'grade_letter' => '-'
            ];
            
            if (isset($grades[$student->id])) {
                $grade = $grades[$student->id];
                
                $writtenCounter = 0;
                $observationCounter = 0;
                $homeworkCounter = 0;
                
                if (isset($gradeTasks[$grade->id])) {
                    foreach ($gradeTasks[$grade->id] as $task) {
                        if ($task->type === 'written' && $writtenCounter < 5) {
                            $studentData['written'][$writtenCounter] = $task->score;
                            $writtenCounter++;
                        } elseif ($task->type === 'observation' && $observationCounter < 5) {
                            $studentData['observation'][$observationCounter] = $task->score;
                            $observationCounter++;
                        } elseif ($task->type === 'homework' && $homeworkCounter < 5) {
                            $studentData['homework'][$homeworkCounter] = $task->score;
                            $homeworkCounter++;
                        }
                    }
                }
                
                $studentData['average_written'] = $grade->average_written;
                $studentData['average_observation'] = $grade->average_observation;
                $studentData['average_homework'] = $grade->average_homework;
                $studentData['midterm_score'] = $grade->midterm_score;
                $studentData['final_exam_score'] = $grade->final_exam_score;
                $studentData['final_score'] = $grade->final_score;
                $studentData['grade_letter'] = $grade->grade_letter;
            }
            
            $result[] = $studentData;
        }
        
        return $result;
    }

    /**
     * Mendapatkan deskripsi berdasarkan grade_letter
     */
    private function getDescription($grade)
    {
        switch ($grade) {
            case 'A':
                return 'Sangat Baik';
            case 'B':
                return 'Baik';
            case 'C':
                return 'Cukup';
            case 'D':
                return 'Perlu Bimbingan';
            default:
                return '';
        }
    }
}
