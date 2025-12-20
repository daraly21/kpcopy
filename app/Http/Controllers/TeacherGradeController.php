<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\GradeTask;
use App\Models\Subject;
use App\Models\Student;
use App\Models\StudentClass;
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

        // Ambil tahun ajaran aktif
        $activeYear = AcademicYear::where('is_active', 1)->first();
        if (!$activeYear) {
            return redirect()->route('dashboard')
                ->with('error', 'Tahun ajaran aktif belum diset. Silakan hubungi admin.');
        }

        $subject = Subject::findOrFail($user->subject_id);
        
        // Ambil semua kelas dengan statistik
        $classes = ClassModel::all();
        
        // Hitung statistik per kelas untuk mata pelajaran ini (hanya tahun ajaran aktif)
        foreach ($classes as $class) {
            // Ambil siswa di kelas pada tahun ajaran aktif
            $studentIds = StudentClass::where('class_id', $class->id)
                ->where('academic_year_id', $activeYear->id)
                ->pluck('student_id');
            
            $class->students_count = $studentIds->count();
            
            $totalTasks = GradeTask::where('subject_id', $user->subject_id)
                ->whereIn('student_id', $studentIds)
                ->count();
                
            $class->total_tasks = $totalTasks;
            $class->completion_percentage = $class->students_count > 0 ? 
                min(100, ($totalTasks / ($class->students_count * 5)) * 100) : 0; // Asumsi 5 tugas per siswa
        }

        // Total siswa dan nilai untuk tahun ajaran aktif
        $totalStudentIds = StudentClass::where('academic_year_id', $activeYear->id)
            ->pluck('student_id');
        $totalStudents = $totalStudentIds->count();
        $totalGrades = GradeTask::where('subject_id', $user->subject_id)
            ->whereIn('student_id', $totalStudentIds)
            ->count();

        return view('grades.teacher.select-class', [
            'subject' => $subject,
            'classes' => $classes,
            'totalStudents' => $totalStudents,
            'totalGrades' => $totalGrades,
            'activeYear' => $activeYear
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

        // Ambil tahun ajaran aktif
        $activeYear = AcademicYear::where('is_active', 1)->first();
        if (!$activeYear) {
            return redirect()->route('teacher.grades.select-class')
                ->with('error', 'Tahun ajaran aktif belum diset. Silakan hubungi admin.');
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
            'class_name' => $class->name,
            'academic_year_id' => $activeYear->id
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
            'activeYear' => $activeYear,
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
        
        // Ambil tahun ajaran aktif
        $activeYear = AcademicYear::where('is_active', 1)->first();
        if (!$activeYear) {
            Log::warning('No active academic year set');
            return collect();
        }
        
        // Ambil siswa yang terdaftar di kelas pada tahun ajaran aktif
        $studentIds = StudentClass::where('class_id', $classId)
            ->where('academic_year_id', $activeYear->id)
            ->pluck('student_id')
            ->toArray();
        
        $students = Student::whereIn('id', $studentIds)
            ->orderBy('name')
            ->get(['id', 'name']);
            
        Log::info('Students retrieved for class', [
            'class_id' => $classId,
            'academic_year_id' => $activeYear->id,
            'students_count' => $students->count(),
            'student_names' => $students->pluck('name')->toArray()
        ]);
        
        return $students;
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

    /**
     * Simpan nilai untuk beberapa siswa sekaligus (batch)
     */
    public function store(Request $request)
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

            // Ambil tahun ajaran aktif
            $activeYear = AcademicYear::where('is_active', 1)->first();
            if (!$activeYear) {
                return redirect()->back()->with('error', 'Tahun ajaran aktif belum diset.');
            }

            $savedCount = 0;
            foreach ($validated['scores'] as $studentId => $score) {
                if ($score === null || $score === '') continue;

            // Verifikasi siswa terdaftar di kelas pada tahun ajaran aktif
            $studentInClass = StudentClass::where('student_id', $studentId)
                ->where('class_id', $validated['class_id'])
                ->where('academic_year_id', $activeYear->id)
                ->exists();

            if (!$studentInClass) continue;

                $grade = Grade::updateOrCreate(
                    [
                        'student_id'       => $studentId,
                        'subject_id'       => $user->subject_id,
                        'semester'         => $validated['semester'],
                        'academic_year_id' => $activeYear->id
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

        // Ambil tahun ajaran aktif
        $activeYear = AcademicYear::where('is_active', 1)->first();
        if (!$activeYear) {
            return redirect()->route('teacher.grades.select-class')
                ->with('error', 'Tahun ajaran aktif belum diset.');
        }

        // Ambil siswa yang terdaftar di kelas pada tahun ajaran aktif
        $studentIds = StudentClass::where('class_id', $classId)
            ->where('academic_year_id', $activeYear->id)
            ->pluck('student_id')
            ->toArray();

        $students = Student::whereIn('id', $studentIds)
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
            // Ambil tahun ajaran aktif
            $activeYear = AcademicYear::where('is_active', 1)->first();
            if ($activeYear) {
                // Ambil student IDs dari StudentClass
                $studentIds = StudentClass::where('class_id', $classId)
                    ->where('academic_year_id', $activeYear->id)
                    ->pluck('student_id')
                    ->toArray();
                
                $query->whereIn('student_id', $studentIds);
            }
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
    
    // Ambil tahun ajaran aktif
    $activeYear = AcademicYear::where('is_active', 1)->first();
    if (!$activeYear) {
        Log::warning('No active academic year set for getGrades');
        return collect();
    }
    
    // Ambil student IDs dari StudentClass
    $studentIds = StudentClass::where('class_id', $classId)
        ->where('academic_year_id', $activeYear->id)
        ->pluck('student_id')
        ->toArray();
    
    Log::info('Student IDs for getGrades', [
        'class_id' => $classId,
        'student_ids' => $studentIds,
        'count' => count($studentIds)
    ]);
    
    $query = GradeTask::with(['student', 'subject', 'grade'])
        ->where('subject_id', $subjectId)
        ->whereIn('student_id', $studentIds)
        ->whereHas('grade', function($q) use ($activeYear) {
            $q->where('academic_year_id', $activeYear->id);
        });
        
    if ($taskType) {
        $query->where('task_name', $taskType);
    }
    
    $grades = $query->latest()->get()->groupBy('student_id');
    
    Log::info('Grades retrieved with academic year filter', [
        'subject_id' => $subjectId,
        'class_id' => $classId,
        'academic_year_id' => $activeYear->id,
        'grades_count' => $grades->count(),
        'total_grade_tasks' => $query->count()
    ]);
    
    return $grades;
}
}
