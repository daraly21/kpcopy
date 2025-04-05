<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\GradeTask;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
public function index()
{
    $user = auth()->user();

    if ($user->hasRole('Wali Kelas')) {
        // --- Logika Dashboard Wali Kelas (pindahin dari DashboardGuruController) ---
        $class_id = $user->class_id;
        $className = ClassModel::find($class_id)->name ?? "Kelas " . $class_id;

        $totalStudents = Student::where('class_id', $class_id)->count();
        $subjects = Subject::all();

        $subjectStats = [];
        foreach ($subjects as $subject) {
            $gradesQuery = GradeTask::join('students', 'grade_tasks.student_id', '=', 'students.id')
                ->where('students.class_id', $class_id)
                ->where('grade_tasks.subject_id', $subject->id);

            $statsData = $gradesQuery->select(
                DB::raw('AVG(grade_tasks.score) as average'),
                DB::raw('MAX(grade_tasks.score) as highest'),
                DB::raw('MIN(grade_tasks.score) as lowest'),
                DB::raw('COUNT(*) as count')
            )->first();

            if ($statsData && $statsData->count > 0) {
                $subjectStats[$subject->id] = [
                    'name' => $subject->name,
                    'average' => round($statsData->average, 1),
                    'highest' => $statsData->highest,
                    'lowest' => $statsData->lowest,
                    'count' => $statsData->count
                ];
            }
        }

        $overallStats = GradeTask::join('students', 'grade_tasks.student_id', '=', 'students.id')
            ->where('students.class_id', $class_id)
            ->select(
                DB::raw('AVG(grade_tasks.score) as average'),
                DB::raw('MAX(grade_tasks.score) as highest'),
                DB::raw('MIN(grade_tasks.score) as lowest'),
                DB::raw('COUNT(*) as count'),
                DB::raw('COUNT(DISTINCT grade_tasks.student_id) as student_count')
            )->first();

        $gradeDistribution = [
            'sangat_baik' => GradeTask::join('students', 'grade_tasks.student_id', '=', 'students.id')
                ->where('students.class_id', $class_id)
                ->where('grade_tasks.score', '>=', 90)
                ->count(),
            'baik' => GradeTask::join('students', 'grade_tasks.student_id', '=', 'students.id')
                ->where('students.class_id', $class_id)
                ->whereBetween('grade_tasks.score', [75, 89.99])
                ->count(),
            'cukup' => GradeTask::join('students', 'grade_tasks.student_id', '=', 'students.id')
                ->where('students.class_id', $class_id)
                ->whereBetween('grade_tasks.score', [60, 74.99])
                ->count(),
            'perlu_perbaikan' => GradeTask::join('students', 'grade_tasks.student_id', '=', 'students.id')
                ->where('students.class_id', $class_id)
                ->where('grade_tasks.score', '<', 60)
                ->count(),
        ];

        $topStudents = GradeTask::join('students', 'grade_tasks.student_id', '=', 'students.id')
            ->join('subjects', 'grade_tasks.subject_id', '=', 'subjects.id')
            ->where('students.class_id', $class_id)
            ->select(
                'students.id',
                'students.name',
                DB::raw('AVG(grade_tasks.score) as average_score')
            )
            ->groupBy('students.id', 'students.name')
            ->orderByDesc('average_score')
            ->limit(5)
            ->get();

        $lowStudents = GradeTask::join('students', 'grade_tasks.student_id', '=', 'students.id')
            ->join('subjects', 'grade_tasks.subject_id', '=', 'subjects.id')
            ->where('students.class_id', $class_id)
            ->select(
                'students.id',
                'students.name',
                DB::raw('AVG(grade_tasks.score) as average_score')
            )
            ->groupBy('students.id', 'students.name')
            ->orderBy('average_score')
            ->limit(5)
            ->get();

        $recentActivities = GradeTask::join('students', 'grade_tasks.student_id', '=', 'students.id')
            ->join('subjects', 'grade_tasks.subject_id', '=', 'subjects.id')
            ->where('students.class_id', $class_id)
            ->select(
                'grade_tasks.*',
                'students.name as student_name',
                'subjects.name as subject_name'
            )
            ->orderByDesc('grade_tasks.created_at')
            ->limit(10)
            ->get();

        $studentsWithoutGrades = Student::whereNotIn('id', function($query) {
                $query->select('student_id')->from('grade_tasks')->distinct();
            })
            ->where('class_id', $class_id)
            ->count();

        return view('dashboards.guru', compact(
            'className',
            'totalStudents',
            'overallStats',
            'subjectStats',
            'gradeDistribution',
            'topStudents',
            'lowStudents',
            'recentActivities',
            'studentsWithoutGrades'
        ));
    }

    // --- Logika Dashboard Admin ---
    return view('dashboards.admin'); // Ganti sesuai tampilan Admin kamu
}


}
