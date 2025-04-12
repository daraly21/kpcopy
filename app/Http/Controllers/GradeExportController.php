<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\GradeTask;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Exports\GradesExport;
use App\Models\ClassModel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GradeExportController extends Controller
{
    public function index()
    {
        $class = null;
        if (Auth::user()->class_id) {
            $class = ClassModel::find(Auth::user()->class_id);
        }
        
        $subjects = Subject::all();
        $studentsData = [];
        $selectedSubject = null;
        $selectedSemester = null;
        
        if (request()->has('subject_id') && request()->has('semester')) {
            $selectedSubject = Subject::find(request('subject_id'));
            $selectedSemester = request('semester');
            
            if ($class && $selectedSubject) {
                $studentsData = $this->getGradeData($selectedSubject->id, $selectedSemester, $class->id);
            }
        }
        
        return view('exports.index', compact('class', 'subjects', 'studentsData', 'selectedSubject', 'selectedSemester'));
    }

    public function exportPDF(Request $request)
    {
        $subject_id = $request->subject_id;
        $semester = $request->semester;
        $class_id = $request->class_id;
        
        $subject = Subject::find($subject_id);
        $class = ClassModel::find($class_id);
        $data = $this->getGradeData($subject_id, $semester, $class_id);
        
        $pdf = PDF::loadView('exports.grade_pdf', [
            'data' => $data,
            'subject' => $subject,
            'class' => $class,
            'semester' => $semester,
            'year' => date('Y'),
        ])->setPaper('a4', 'landscape'); 
        
        return $pdf->download('daftar_nilai_' . $subject->name . '.pdf');
    }
    
    public function exportExcel(Request $request)
    {
        $subject_id = $request->subject_id;
        $semester = $request->semester;
        $class_id = $request->class_id;
        
        $subject = Subject::find($subject_id);
        $class = ClassModel::find($class_id);
        
        return Excel::download(new GradesExport($subject_id, $semester, $class_id), 'daftar_nilai_' . $subject->name . '.xlsx');
    }
    
    public function generateExport(Request $request)
    {
        if ($request->export_type == 'pdf') {
            return $this->exportPDF($request);
        } else {
            return $this->exportExcel($request);
        }
    }
    
    private function getGradeData($subject_id, $semester, $class_id)
    {
        // Ambil data pengguna yang login (wali kelas)
        $user = auth::user();
        
        // Pastikan pengguna memiliki class_id (kelas yang dikelola)
        if (!$user->class_id) {
            Log::warning("User does not have an associated class_id", ['user_id' => $user->id]);
            return [];
        }

        // Pastikan class_id yang digunakan adalah kelas yang dikelola oleh wali kelas
        if ($user->class_id != $class_id) {
            Log::warning("User attempted to access a class they do not manage", [
                'user_id' => $user->id,
                'class_id' => $class_id,
                'user_class_id' => $user->class_id
            ]);
            return [];
        }

        $result = [];
        $updates = []; // Untuk menyimpan data yang akan di-update

        // Gunakan chunk untuk memproses siswa dalam batch kecil
        Student::where('class_id', $user->class_id)
               ->select('id', 'nis', 'name')
               ->chunk(10, function ($students) use (&$result, &$updates, $subject_id, $semester) {
                   // Ambil semua grade untuk siswa di batch ini
                   $studentIds = $students->pluck('id')->toArray();
                   $grades = Grade::whereIn('student_id', $studentIds)
                                  ->where('subject_id', $subject_id)
                                  ->where('semester', $semester)
                                  ->select('id', 'student_id', 'subject_id', 'semester', 'midterm_score', 'final_exam_score', 'final_score', 'grade_letter')
                                  ->with(['gradeTasks' => function ($query) {
                                      $query->select('id', 'grades_id', 'type', 'score', 'created_at')
                                            ->orderBy('created_at')
                                            ->take(15);
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
                           'homework' => array_fill(0, 5, '-'),
                           'grade_details' => [
                               'average_written' => null,
                               'average_observation' => null,
                               'average_homework' => null,
                               'midterm_score' => null,
                               'final_exam_score' => null,
                               'final_score' => null,
                               'grade_letter' => '-'
                           ]
                       ];
                       
                       $grade = $grades->get($student->id);
                       
                       if ($grade) {
                           $tasks = $grade->gradeTasks;
                           
                           $writtenCounter = 0;
                           $observationCounter = 0;
                           $homeworkCounter = 0;
                           
                           $writtenScores = [];
                           $observationScores = [];
                           $homeworkScores = [];
                           
                           foreach ($tasks as $task) {
                               if ($task->type === 'written' && $writtenCounter < 5) {
                                   $studentData['written'][$writtenCounter] = $task->score;
                                   $writtenScores[] = $task->score;
                                   $writtenCounter++;
                               } elseif ($task->type === 'observation' && $observationCounter < 5) {
                                   $studentData['observation'][$observationCounter] = $task->score;
                                   $observationScores[] = $task->score;
                                   $observationCounter++;
                               } elseif ($task->type === 'homework' && $homeworkCounter < 5) {
                                   $studentData['homework'][$homeworkCounter] = $task->score;
                                   $homeworkScores[] = $task->score;
                                   $homeworkCounter++;
                               }
                           }
                           
                           $averageWritten = !empty($writtenScores) ? array_sum($writtenScores) / count($writtenScores) : null;
                           $averageObservation = !empty($observationScores) ? array_sum($observationScores) / count($observationScores) : null;
                           $averageHomework = !empty($homeworkScores) ? array_sum($homeworkScores) / count($homeworkScores) : null;
                           
                           $components = array_filter([
                               $averageWritten,
                               $averageObservation,
                               $averageHomework,
                               $grade->midterm_score,
                               $grade->final_exam_score
                           ], fn($value) => !is_null($value));
                           
                           $finalScore = !empty($components) ? array_sum($components) / count($components) : 0;
                           
                           // Simpan data untuk update massal
                           $updates[] = [
                               'id' => $grade->id,
                               'average_written' => $averageWritten,
                               'average_observation' => $averageObservation,
                               'average_homework' => $averageHomework,
                               'final_score' => $finalScore,
                           ];
                           
                           $studentData['grade_details'] = [
                               'average_written' => $averageWritten,
                               'average_observation' => $averageObservation,
                               'average_homework' => $averageHomework,
                               'midterm_score' => $grade->midterm_score,
                               'final_exam_score' => $grade->final_exam_score,
                               'final_score' => $finalScore,
                               'grade_letter' => $grade->grade_letter
                           ];
                       }
                       
                       $result[] = $studentData;
                   }
               });

        // Lakukan update massal untuk semua grade
        foreach ($updates as $update) {
            Grade::where('id', $update['id'])->update([
                'average_written' => $update['average_written'],
                'average_observation' => $update['average_observation'],
                'average_homework' => $update['average_homework'],
                'final_score' => $update['final_score'],
            ]);
        }

        return $result;
    }
    
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
