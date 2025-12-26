<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GradeTask;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = null;
        $data = [];

        // Dashboard Admin
        if ($user->hasRole('Admin')) {
            $role = 'admin';
            
            $activeYear = \App\Models\AcademicYear::where('is_active', 1)->first();
            $studentsThisYear = 0;
            if ($activeYear) {
                $studentsThisYear = \App\Models\StudentClass::where('academic_year_id', $activeYear->id)
                    ->distinct('student_id')->count();
            }
            
            $data = [
                'totalWaliKelas' => User::where('role_id', 2)->count(),
                'totalSiswa' => Student::count(),
                'totalKelas' => ClassModel::count(),
                'totalMapel' => Subject::count(),
                'activeYear' => $activeYear,
                'studentsThisYear' => $studentsThisYear,
            ];
        }

        // Dashboard Wali Kelas
        elseif ($user->hasRole('Wali Kelas')) {
            $role = 'walikelas';
            $class_id = $user->class_id;
            $class = ClassModel::find($class_id);
            if (!$class) {
                abort(404, 'Kelas tidak ditemukan.');
            }

            // Get Active Year
            $activeYear = \App\Models\AcademicYear::where('is_active', 1)->first();
            
            // Get Student IDs for this class in active year
            $studentIds = [];
            if ($activeYear) {
                $studentIds = \App\Models\StudentClass::where('class_id', $class_id)
                                ->where('academic_year_id', $activeYear->id)
                                ->pluck('student_id')
                                ->toArray();
            }

            // Subject Stats
            $subjects = Subject::all();
            $subjectStats = [];
            
            foreach ($subjects as $subject) {
                $statsData = GradeTask::join('grades', 'grade_tasks.grades_id', '=', 'grades.id')
                    ->where('grade_tasks.subject_id', $subject->id)
                    ->where('grades.academic_year_id', $activeYear->id)
                    ->whereIn('grade_tasks.student_id', $studentIds)
                    ->selectRaw('AVG(grade_tasks.score) as average, MAX(grade_tasks.score) as highest, MIN(grade_tasks.score) as lowest, COUNT(*) as count')
                    ->first();

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

            $overallStats = GradeTask::join('grades', 'grade_tasks.grades_id', '=', 'grades.id')
                ->where('grades.academic_year_id', $activeYear->id)
                ->whereIn('grade_tasks.student_id', $studentIds)
                ->selectRaw('AVG(grade_tasks.score) as average, MAX(grade_tasks.score) as highest, MIN(grade_tasks.score) as lowest, COUNT(*) as count, COUNT(DISTINCT grade_tasks.student_id) as student_count')
                ->first();

            $gradeDistribution = [
                'sangat_baik' => GradeTask::join('grades', 'grade_tasks.grades_id', '=', 'grades.id')
                    ->where('grades.academic_year_id', $activeYear->id)
                    ->whereIn('grade_tasks.student_id', $studentIds)
                    ->where('grade_tasks.score', '>=', 90)->count(),
                'baik' => GradeTask::join('grades', 'grade_tasks.grades_id', '=', 'grades.id')
                    ->where('grades.academic_year_id', $activeYear->id)
                    ->whereIn('grade_tasks.student_id', $studentIds)
                    ->whereBetween('grade_tasks.score', [75, 89.99])->count(),
                'cukup' => GradeTask::join('grades', 'grade_tasks.grades_id', '=', 'grades.id')
                    ->where('grades.academic_year_id', $activeYear->id)
                    ->whereIn('grade_tasks.student_id', $studentIds)
                    ->whereBetween('grade_tasks.score', [60, 74.99])->count(),
                'perlu_perbaikan' => GradeTask::join('grades', 'grade_tasks.grades_id', '=', 'grades.id')
                    ->where('grades.academic_year_id', $activeYear->id)
                    ->whereIn('grade_tasks.student_id', $studentIds)
                    ->where('grade_tasks.score', '<', 60)->count(),
            ];

            $topStudents = GradeTask::join('grades', 'grade_tasks.grades_id', '=', 'grades.id')
                ->join('students', 'grade_tasks.student_id', '=', 'students.id')
                ->where('grades.academic_year_id', $activeYear->id)
                ->whereIn('grade_tasks.student_id', $studentIds)
                ->select('students.id', 'students.name', DB::raw('AVG(grade_tasks.score) as average_score'))
                ->groupBy('students.id', 'students.name')
                ->orderByDesc('average_score')
                ->limit(5)
                ->get();

            $lowStudents = GradeTask::join('grades', 'grade_tasks.grades_id', '=', 'grades.id')
                ->join('students', 'grade_tasks.student_id', '=', 'students.id')
                ->where('grades.academic_year_id', $activeYear->id)
                ->whereIn('grade_tasks.student_id', $studentIds)
                ->select('students.id', 'students.name', DB::raw('AVG(grade_tasks.score) as average_score'))
                ->groupBy('students.id', 'students.name')
                ->orderBy('average_score')
                ->limit(5)
                ->get();

            $recentActivities = GradeTask::join('grades', 'grade_tasks.grades_id', '=', 'grades.id')
                ->join('students', 'grade_tasks.student_id', '=', 'students.id')
                ->join('subjects', 'grade_tasks.subject_id', '=', 'subjects.id')
                ->where('grades.academic_year_id', $activeYear->id)
                ->whereIn('grade_tasks.student_id', $studentIds)
                ->select(
                    'grade_tasks.id',
                    'grade_tasks.task_name',
                    'grade_tasks.score',
                    'grade_tasks.created_at',
                    'students.name as student_name',
                    'subjects.name as subject_name'
                )
                ->orderByDesc('grade_tasks.created_at')
                ->limit(10)
                ->get();

            $studentsWithoutGrades = Student::whereIn('id', $studentIds)
                ->whereNotIn('id', function ($query) {
                    $query->select('student_id')->from('grade_tasks')->distinct();
                })
                ->count();

            // Class Average
            $classAverage = 0;
            if ($overallStats && $overallStats->average) {
                $classAverage = round($overallStats->average, 1);
            }

            // Students Need Attention (<60)
            $studentsNeedAttention = GradeTask::join('grades', 'grade_tasks.grades_id', '=', 'grades.id')
                ->where('grades.academic_year_id', $activeYear->id)
                ->whereIn('grade_tasks.student_id', $studentIds)
                ->select('grade_tasks.student_id', DB::raw('AVG(grade_tasks.score) as avg'))
                ->groupBy('grade_tasks.student_id')
                ->having('avg', '<', 60)
                ->count();

            $data = [
                'className' => $class->name,
                'totalStudents' => count($studentIds),
                'classAverage' => $classAverage,
                'studentsNeedAttention' => $studentsNeedAttention,
                'overallStats' => $overallStats,
                'subjectStats' => $subjectStats,
                'gradeDistribution' => $gradeDistribution,
                'topStudents' => $topStudents,
                'lowStudents' => $lowStudents,
                'recentActivities' => $recentActivities,
                'studentsWithoutGrades' => $studentsWithoutGrades,
            ];
        }

        // Dashboard Guru Mata Pelajaran
        elseif ($user->hasRole('Guru Mata Pelajaran')) {
            $role = 'guru';
            $subject_id = $user->subject_id;
            $subject = Subject::find($subject_id);
            if (!$subject) {
                abort(404, 'Mata pelajaran tidak ditemukan.');
            }

            $totalStudents = GradeTask::where('subject_id', $subject_id)
                ->distinct('student_id')
                ->count('student_id');

            $overallStats = GradeTask::where('subject_id', $subject_id)
                ->selectRaw('AVG(score) as average, MAX(score) as highest, MIN(score) as lowest, COUNT(*) as count')
                ->first();

            $gradeDistribution = [
                'sangat_baik' => GradeTask::where('subject_id', $subject_id)->where('score', '>=', 90)->count(),
                'baik' => GradeTask::where('subject_id', $subject_id)->whereBetween('score', [75, 89.99])->count(),
                'cukup' => GradeTask::where('subject_id', $subject_id)->whereBetween('score', [60, 74.99])->count(),
                'perlu_perbaikan' => GradeTask::where('subject_id', $subject_id)->where('score', '<', 60)->count(),
            ];

            $recentActivities = GradeTask::where('subject_id', $subject_id)
                ->join('students', 'grade_tasks.student_id', '=', 'students.id')
                ->select(
                    'grade_tasks.id',
                    'grade_tasks.task_name',
                    'grade_tasks.score',
                    'grade_tasks.created_at',
                    'students.name as student_name'
                )
                ->orderByDesc('grade_tasks.created_at')
                ->limit(10)
                ->get();

            $studentsWithoutGrades = 0;

            // Students Need Remedial (<60)
            $studentsNeedRemedial = GradeTask::where('subject_id', $subject_id)
                ->select('student_id', DB::raw('AVG(score) as avg'))
                ->groupBy('student_id')
                ->having('avg', '<', 60)
                ->count();

            $taskStats = GradeTask::where('subject_id', $subject_id)
                ->groupBy('task_name')
                ->selectRaw('task_name as name, AVG(score) as average')
                ->get();

            $data = [
                'subjectName' => $subject->name,
                'totalStudents' => $totalStudents,
                'studentsNeedRemedial' => $studentsNeedRemedial,
                'overallStats' => $overallStats,
                'gradeDistribution' => $gradeDistribution,
                'recentActivities' => $recentActivities,
                'studentsWithoutGrades' => $studentsWithoutGrades,
                'taskStats' => $taskStats,
            ];
        }

        else {
            abort(403, 'Unauthorized action.');
        }

        return view('dashboards.index', array_merge(['role' => $role], $data));
    }
}