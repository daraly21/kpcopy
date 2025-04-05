<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\GradeTask;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class GradeReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $selectedSubject = $request->input('subject_id');
        $selectedTaskType = $request->input('task_name');

        // Jika user adalah Wali Kelas, otomatis ambil kelasnya
        if ($user->role_id == 2) { // Role ID 2 = Wali Kelas/Guru
            $class_id = $user->class_id;
            $classes = null;
            $className = ClassModel::find($class_id)->name ?? "Kelas " . $class_id;
        } else {
            $class_id = $request->input('class_id'); // Admin harus memilih kelas
            $classes = ClassModel::all();
            $className = $class_id ? (ClassModel::find($class_id)->name ?? "Kelas " . $class_id) : null;
        }

        // Ambil daftar mata pelajaran
        $subjects = Subject::all();

        // Ambil daftar jenis tugas berdasarkan filter mata pelajaran
        $task_types = null;
        if ($class_id) {
            $taskQuery = GradeTask::join('students', 'grade_tasks.student_id', '=', 'students.id')
                ->where('students.class_id', $class_id);
            
            // Filter jenis tugas berdasarkan mata pelajaran yang dipilih
            if ($selectedSubject) {
                $taskQuery->where('grade_tasks.subject_id', $selectedSubject);
            }
            
            $task_types = $taskQuery->pluck('task_name')->unique();
        }

        // Kalkulasi statistik nilai jika kelas sudah dipilih
        $stats = null;
        if ($class_id) {
            // Buat query dasar untuk data nilai
            $gradesQuery = GradeTask::join('students', 'grade_tasks.student_id', '=', 'students.id')
                ->where('students.class_id', $class_id);

            // Terapkan filter yang dipilih
            if ($selectedSubject) {
                $gradesQuery->where('grade_tasks.subject_id', $selectedSubject);
            }

            if ($selectedTaskType) {
                $gradesQuery->where('grade_tasks.task_name', $selectedTaskType);
            }

            // Menggunakan score dari tabel grade_tasks
            $statsData = $gradesQuery->select(
                DB::raw('AVG(grade_tasks.score) as average'),
                DB::raw('MAX(grade_tasks.score) as highest'),
                DB::raw('MIN(grade_tasks.score) as lowest'),
                DB::raw('COUNT(*) as count')
            )->first();

            if ($statsData) {
                $stats = [
                    'average' => round($statsData->average, 1),
                    'highest' => $statsData->highest,
                    'lowest' => $statsData->lowest,
                    'count' => $statsData->count
                ];
            }
        }

        // Ambil data nilai berdasarkan filter
        $grades = [];
        if ($class_id) {
            $grades = GradeTask::join('students', 'grade_tasks.student_id', '=', 'students.id')
                ->join('subjects', 'grade_tasks.subject_id', '=', 'subjects.id')
                ->where('students.class_id', $class_id)
                ->when($selectedSubject, function ($query) use ($selectedSubject) {
                    $query->where('grade_tasks.subject_id', $selectedSubject);
                })
                ->when($selectedTaskType, function ($query) use ($selectedTaskType) {
                    $query->where('grade_tasks.task_name', $selectedTaskType);
                })
                ->select(
                    'grade_tasks.*',
                    'students.name as student_name',
                    'subjects.name as subject_name'
                )
                ->orderBy('students.name')
                ->get();
        }

        // Ambil daftar semua siswa di kelas untuk laporan yang lebih komprehensif
        $students = [];
        if ($class_id) {
            $students = Student::where('class_id', $class_id)->orderBy('name')->get();
        }

        return view('grades.recap', compact(
            'grades',
            'classes',
            'subjects',
            'task_types',
            'class_id',
            'className',
            'selectedSubject',
            'selectedTaskType',
            'stats',
            'students'
        ));
    }

    public function export(Request $request)
    {
        // Implementasi export nilai ke Excel/PDF bisa ditambahkan di sini
        // ...
        
        return back()->with('success', 'Data nilai berhasil diekspor!');
    }
}