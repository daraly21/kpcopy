<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\GradeTask;
use App\Models\Subject;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherGradeController extends Controller
{
    /**
     * Menampilkan halaman pemilihan kelas
     */
    public function selectClass()
    {
        $user = Auth::user();
        
        // Cek otorisasi guru
        if ($user->role_id != 3 || !$user->subject_id) {
            Log::error('Unauthorized access to class selection', [
                'user_id' => $user->id,
                'role_id' => $user->role_id,
                'subject_id' => $user->subject_id
            ]);
            abort(403, 'Akses ditolak. Anda bukan guru mata pelajaran.');
        }

        $subject = Subject::findOrFail($user->subject_id);
        // Ambil semua kelas dengan statistik
        $classes = ClassModel::withCount('students')->get();
        // Hitung statistik per kelas untuk mata pelajaran ini
        foreach ($classes as $class) {
            $totalTasks = GradeTask::where('subject_id', $user->subject_id)
                ->whereHas('student', function ($query) use ($class) {
                    $query->where('class_id', $class->id);
                })->count();
            $class->total_tasks = $totalTasks;
            $class->completion_percentage = $class->students_count > 0 ? 
                min(100, ($totalTasks / ($class->students_count * 5)) * 100) : 0; // Asumsi 5 tugas per siswa
        }

        $totalStudents = Student::count();
        $totalGrades = GradeTask::where('subject_id', $user->subject_id)->count();

        return view('grades.teacher.select-class', [
            'subject' => $subject,
            'classes' => $classes,
            'totalStudents' => $totalStudents,
            'totalGrades' => $totalGrades
        ]);
    }

    /**
     * Menampilkan halaman manajemen nilai untuk kelas tertentu
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Cek otorisasi guru
        if ($user->role_id != 3 || !$user->subject_id) {
            Log::error('Unauthorized access to grades index', [
                'user_id' => $user->id,
                'role_id' => $user->role_id,
                'subject_id' => $user->subject_id
            ]);
            abort(403, 'Akses ditolak. Anda bukan guru mata pelajaran.');
        }

        $subject = Subject::findOrFail($user->subject_id);
        $selectedClass = $request->input('class_id');
        
        // Validasi class_id wajib ada
        if (!$selectedClass) {
            return redirect()->route('teacher.grades.select-class')
                ->with('error', 'Silakan pilih kelas terlebih dahulu.');
        }

        // Validasi kelas exists
        $class = ClassModel::find($selectedClass);
        if (!$class) {
            return redirect()->route('teacher.grades.select-class')
                ->with('error', 'Kelas tidak ditemukan.');
        }

        Log::info('Teacher accessing grades for specific class', [
            'user_id' => $user->id,
            'subject' => $subject->name,
            'subject_id' => $subject->id,
            'class_id' => $selectedClass,
            'class_name' => $class->name
        ]);

        $selectedTaskType = $request->input('task_name');

        // Ambil data siswa
        $students = $this->getStudents($selectedClass);
        if ($students->isEmpty()) {
            Log::warning('No students found for selected class', [
                'class_id' => $selectedClass,
                'class_name' => $class->name
            ]);
        }

        $taskTypes = $this->getTaskTypes($user->subject_id, $selectedClass);
        $grades = $this->getGrades($user->subject_id, $selectedClass, $selectedTaskType);
        
        // Data untuk view
        $data = [
            'subject' => $subject,
            'selectedClass' => $selectedClass,
            'selectedTaskType' => $selectedTaskType,
            'className' => $class->name,
            'students' => $students,
            'grades' => $grades,
            'task_types' => $taskTypes,
            'error' => $students->isEmpty() ? 'Tidak ada siswa di kelas ini. Silakan hubungi admin untuk menambahkan siswa.' : null
        ];

        Log::info('Grades index data prepared', [
            'selectedClass' => $selectedClass,
            'className' => $class->name,
            'students_count' => $students->count(),
            'grades_count' => $grades->count(),
            'task_types_count' => $taskTypes->count()
        ]);

        return view('grades.teacher.index', $data);
    }

    private function getStudents($classId)
    {
        if (!$classId) {
            Log::warning('No class_id provided for getStudents');
            return collect();
        }
        
        $students = Student::where('class_id', $classId)
            ->orderBy('name')
            ->get(['id', 'name', 'class_id']);
            
        Log::info('Students retrieved for class', [
            'class_id' => $classId,
            'students_count' => $students->count(),
            'student_names' => $students->pluck('name')->toArray()
        ]);
        
        return $students;
    }

  public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role_id != 3 || !$user->subject_id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        if (method_exists($user, 'hasPermissionTo') && !$user->hasPermissionTo('kelola nilai')) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk mengelola nilai'], 403);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'task_name'  => 'required|string|max:255',
            'score'      => 'required|numeric|min:0|max:100',
            'type'       => 'required|in:written,observation,sumatif',
            'semester'   => 'required|in:Odd,Even',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        if ($validated['subject_id'] != $user->subject_id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak berhak mengelola nilai untuk mata pelajaran ini'], 403);
        }

        try {
            DB::beginTransaction();

            $grade = Grade::firstOrCreate(
                [
                    'student_id' => $validated['student_id'],
                    'subject_id' => $validated['subject_id'],
                    'semester'   => $validated['semester']
                ],
                ['score' => $validated['score']]
            );

            $gradeTask = GradeTask::create([
                'student_id' => $validated['student_id'],
                'subject_id' => $validated['subject_id'],
                'task_name'  => $validated['task_name'],
                'score'      => $validated['score'],
                'type'       => $validated['type'],
                'grades_id'  => $grade->id,
            ]);

            DB::commit();

            $student = Student::find($validated['student_id']);

            return response()->json([
                'success' => true,
                'message' => "Nilai {$validated['task_name']} untuk {$student->name} berhasil disimpan!",
                'data'    => $gradeTask->load('student')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan nilai: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role_id != 3 || !$user->subject_id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'task_name'       => 'required|string|max:255',
            'score'           => 'required|numeric|min:0|max:100',
            'assignment_type' => 'required|in:written,observation,sumatif',
        ]);

        try {
            $gradeTask = GradeTask::where('subject_id', $user->subject_id)->findOrFail($id);

            DB::beginTransaction();

            $gradeTask->update([
                'task_name' => $validated['task_name'],
                'score'     => $validated['score'],
                'type'      => $validated['assignment_type']
            ]);

            if ($gradeTask->grade) {
                $gradeTask->grade->update(['score' => $validated['score']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Nilai {$validated['task_name']} berhasil diperbarui menjadi {$validated['score']}",
                'data'    => $gradeTask->fresh()->load(['student', 'subject'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui nilai: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();

        if ($user->role_id != 3 || !$user->subject_id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        try {
            $gradeTask = GradeTask::where('subject_id', $user->subject_id)->findOrFail($id);

            $taskName = $gradeTask->task_name;
            $studentName = $gradeTask->student?->name ?? 'Siswa';

            DB::beginTransaction();
            $gradeTask->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Nilai \"{$taskName}\" untuk {$studentName} berhasil dihapus"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus nilai: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeBatch(Request $request)
    {
        $user = Auth::user();

        if ($user->role_id != 3 || !$user->subject_id) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'class_id'        => 'required|exists:classes,id',
            'task_name'       => 'required|string|max:255',
            'assignment_type' => 'required|in:written,observation,sumatif',
            'semester'        => 'required|in:Odd,Even',
            'scores'          => 'required|array',
            'scores.*'        => 'nullable|numeric|min:0|max:100'
        ]);

        try {
            DB::beginTransaction();

            $savedCount = 0;
            foreach ($validated['scores'] as $studentId => $score) {
                if ($score === null || $score === '') continue;

                $student = Student::where('id', $studentId)
                    ->where('class_id', $validated['class_id'])
                    ->first();

                if (!$student) continue;

                $grade = Grade::firstOrCreate(
                    [
                        'student_id' => $studentId,
                        'subject_id' => $user->subject_id,
                        'semester'   => $validated['semester']
                    ],
                    ['score' => $score]
                );

                GradeTask::create([
                    'student_id' => $studentId,
                    'subject_id' => $user->subject_id,
                    'task_name'  => $validated['task_name'],
                    'score'      => $score,
                    'type'       => $validated['assignment_type'],
                    'grades_id'  => $grade->id,
                ]);

                $savedCount++;
            }

            DB::commit();

            if ($savedCount > 0) {
                return redirect()->back()
                    ->with('success', "Berhasil menyimpan nilai \"{$validated['task_name']}\" untuk {$savedCount} siswa");
            }

            return redirect()->back()
                ->with('info', 'Tidak ada nilai yang disimpan. Pastikan Anda mengisi setidaknya satu nilai yang valid.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menyimpan nilai batch: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function create(Request $request, int $subjectId)
    {
        $user = Auth::user();
        
        // Cek otorisasi guru
        if ($user->role_id != 3 || !$user->subject_id) {
            Log::error('Unauthorized access to create grades', [
                'user_id' => $user->id,
                'role_id' => $user->role_id,
                'subject_id' => $user->subject_id
            ]);
            abort(403, 'Akses ditolak. Anda bukan guru mata pelajaran.');
        }

        // Validasi bahwa subject_id sesuai dengan guru yang login
        if ($subjectId != $user->subject_id) {
            Log::error('Subject ID mismatch in create', [
                'user_subject_id' => $user->subject_id,
                'requested_subject_id' => $subjectId
            ]);
            abort(403, 'Anda tidak berhak mengelola nilai untuk mata pelajaran ini.');
        }

        // Ambil subject
        $subject = Subject::findOrFail($subjectId);

        // Ambil class_id dan class_name dari query parameter
        $classId = $request->query('class_id');
        $className = $request->query('class_name');

        // Validasi class_id wajib ada
        if (!$classId) {
            return redirect()->route('teacher.grades.select-class')
                ->with('error', 'Silakan pilih kelas terlebih dahulu.');
        }

        // Validasi kelas exists
        $class = ClassModel::find($classId);
        if (!$class) {
            return redirect()->route('teacher.grades.select-class')
                ->with('error', 'Kelas tidak ditemukan.');
        }

        // Ambil semua siswa pada kelas ini
        $students = Student::where('class_id', $classId)
            ->orderBy('name')
            ->get(['id', 'name', 'nis']);

        Log::info('Teacher accessing create grades page', [
            'user_id' => $user->id,
            'subject_id' => $subjectId,
            'class_id' => $classId,
            'class_name' => $className,
            'students_count' => $students->count()
        ]);

        return view('grades.teacher.create', [
            'subject'   => $subject,
            'classId'   => $classId,
            'className' => $className,
            'students'  => $students,
        ]);
    }

    /**
     * Mengambil riwayat nilai siswa untuk modal detail
     */
    public function getStudentGrades($studentId)
    {
        $user = Auth::user();
        
        if ($user->role_id != 3 || !$user->subject_id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        try {
            $grades = GradeTask::with(['student', 'subject'])
                ->where('student_id', $studentId)
                ->where('subject_id', $user->subject_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $grades
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get student grades', [
                'error' => $e->getMessage(),
                'student_id' => $studentId,
                'subject_id' => $user->subject_id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data nilai siswa'
            ], 500);
        }
    }

    private function getTaskTypes($subjectId, $classId = null)
    {
        $query = GradeTask::where('subject_id', $subjectId);
        
        if ($classId) {
            $query->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }
        
        $taskTypes = $query->distinct()->pluck('task_name')->filter()->values();
        
        Log::info('Task types retrieved', [
            'subject_id' => $subjectId,
            'class_id' => $classId,
            'task_types_count' => $taskTypes->count(),
            'task_types' => $taskTypes->toArray()
        ]);
        
        return $taskTypes;
    }

  // Di dalam method getGrades() di TeacherGradeController.php

// File: app/Http/Controllers/TeacherGradeController.php
// Di dalam method getGrades()

private function getGrades($subjectId, $classId, $taskType = null)
{
    if (!$classId) {
        Log::warning('No class_id provided for getGrades');
        return collect();
    }
    
    $query = GradeTask::with(['student', 'subject', 'grade']) // TAMBAHKAN 'grade'
        ->where('subject_id', $subjectId)
        ->whereHas('student', function ($q) use ($classId) {
            $q->where('class_id', $classId);
        });
        
    if ($taskType) {
        $query->where('task_name', $taskType);
    }
    
    $grades = $query->latest()->get()->groupBy('student_id');
    
    return $grades;
}
}
