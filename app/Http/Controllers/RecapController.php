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

class RecapController extends Controller
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
        
        return view('recap.index', compact('class', 'subjects', 'studentsData', 'selectedSubject', 'selectedSemester'));
    }

    public function exportPDF(Request $request)
    {
        $subject_id = $request->subject_id;
        $semester = $request->semester;
        $class_id = $request->class_id;
        
        $subject = Subject::find($subject_id);
        $class = ClassModel::find($class_id);
        $data = $this->getGradeData($subject_id, $semester, $class_id);
        
        $pdf = PDF::loadView('recap.grade_pdf', [
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
        $user = Auth::user();
        
        if (!$user->class_id) {
            return [];
        }
    
        if ($user->class_id != $class_id) {
            return [];
        }
    
        $result = [];
        $updates = [];
    
        // Get Active Year
        $activeYear = \App\Models\AcademicYear::where('is_active', 1)->first();
           
        // Get Student IDs for this class in active year
        $studentIds = [];
        if ($activeYear) {
            $studentIds = \App\Models\StudentClass::where('class_id', $user->class_id)
                            ->where('academic_year_id', $activeYear->id)
                            ->pluck('student_id')
                            ->toArray();
        }

        Student::whereIn('id', $studentIds)
               ->select('id', 'nis', 'name')
               ->chunk(10, function ($students) use (&$result, &$updates, $subject_id, $semester, $activeYear) {
                   Log::info('Processing chunk of students', [
                       'students_count' => $students->count(),
                       'subject_id' => $subject_id,
                       'semester' => $semester,
                       'active_year_id' => $activeYear ? $activeYear->id : null
                   ]);
                                      $studentIds = $students->pluck('id')->toArray();
                    
                    $grades = Grade::whereIn('student_id', $studentIds)
                                   ->where('subject_id', $subject_id)
                                   ->where('semester', $semester)
                                   ->where('academic_year_id', $activeYear->id)
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
                           // DEBUG: Log untuk melihat data
                           \Log::info('Processing grade for student', [
                               'student_id' => $student->id,
                               'student_name' => $student->name,
                               'grade_id' => $grade->id,
                               'subject_id' => $grade->subject_id,
                               'semester' => $grade->semester,
                               'academic_year_id' => $grade->academic_year_id
                           ]);
                           
                           // Gunakan relasi yang sudah di-eager load dan urutkan
                           $tasks = $grade->gradeTasks->sortBy('created_at');
                           
                           \Log::info('Grade tasks retrieved', [
                               'student_id' => $student->id,
                               'tasks_count' => $tasks->count(),
                               'tasks' => $tasks->map(function($t) {
                                   return [
                                       'id' => $t->id,
                                       'task_name' => $t->task_name,
                                       'type' => $t->type,
                                       'score' => $t->score
                                   ];
                               })
                           ]);
                           
                           $writtenCounter = 0;
                           $observationCounter = 0;
                           $sumatifCounter = 0;
                           
                           $writtenScores = [];
                           $observationScores = [];
                           $sumatifScores = []; // Untuk UTS dan UAS
                           
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
                                   // Sumatif pertama = UTS, Sumatif kedua = UAS
                                   $sumatifScores[] = $task->score;
                                   $sumatifCounter++;
                               }
                           }
                           
                           $averageWritten = !empty($writtenScores) ? array_sum($writtenScores) / count($writtenScores) : null;
                           $averageObservation = !empty($observationScores) ? array_sum($observationScores) / count($observationScores) : null;
                           
                           // Ambil UTS dan UAS dari sumatif tasks
                           $midtermScore = isset($sumatifScores[0]) ? $sumatifScores[0] : null;
                           $finalExamScore = isset($sumatifScores[1]) ? $sumatifScores[1] : null;
                           
                           $components = array_filter([
                               $averageWritten,
                               $averageObservation,
                               $midtermScore,
                               $finalExamScore
                           ], fn($value) => !is_null($value));
                           
                           $finalScore = !empty($components) ? array_sum($components) / count($components) : 0;
                           
                           $updates[] = [
                               'id' => $grade->id,
                               'average_written' => $averageWritten,
                               'average_observation' => $averageObservation,
                               'midterm_score' => $midtermScore,
                               'final_exam_score' => $finalExamScore,
                               'final_score' => $finalScore,
                           ];
                           
                           $studentData['average_written'] = $averageWritten;
                           $studentData['average_observation'] = $averageObservation;
                           $studentData['midterm_score'] = $midtermScore;
                           $studentData['final_exam_score'] = $finalExamScore;
                           $studentData['final_score'] = $finalScore;
                       }
                       
                       $result[] = $studentData;
                   }
               });
    
        foreach ($updates as $update) {
            Grade::where('id', $update['id'])->update([
                'average_written' => $update['average_written'],
                'average_observation' => $update['average_observation'],
                'midterm_score' => $update['midterm_score'],
                'final_exam_score' => $update['final_exam_score'],
                'final_score' => $update['final_score'],
            ]);
        }
    
        return $result;
    }
}