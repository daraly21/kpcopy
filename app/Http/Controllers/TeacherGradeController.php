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
        
        // Cek otorisasi dasar
        if ($user->role_id != 3 || !$user->subject_id) {
            Log::error('Unauthorized store attempt (role/subject check)', ['user_id' => $user->id]);
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        // Cek permission tambahan jika ada
        if (method_exists($user, 'hasPermissionTo') && !$user->hasPermissionTo('kelola nilai')) {
            Log::error('Unauthorized store attempt (permission denied)', [
                'user_id' => $user->id,
                'role_id' => $user->role_id,
                'subject_id' => $user->subject_id
            ]);
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk mengelola nilai'], 403);
        }

        Log::info('Received grade store request', $request->all());

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'task_name' => 'required|string|max:255',
            'score' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:written,observation,sumatif',
            'semester' => 'required|in:Odd,Even',
            'subject_id' => 'required|exists:subjects,id',
        ], [
            'student_id.required' => 'Harap pilih siswa.',
            'task_name.required' => 'Nama tugas wajib diisi.',
            'score.required' => 'Nilai wajib diisi.',
            'score.numeric' => 'Nilai harus berupa angka.',
            'score.min' => 'Nilai minimal adalah 0.',
            'score.max' => 'Nilai maksimal adalah 100.',
            'type.required' => 'Tipe tugas wajib dipilih.',
            'semester.required' => 'Semester wajib dipilih.',
            'subject_id.required' => 'Subject ID wajib ada.',
        ]);

        // Validasi bahwa subject_id sesuai dengan guru yang login
        if ($validated['subject_id'] != $user->subject_id) {
            Log::error('Subject ID mismatch', [
                'user_subject_id' => $user->subject_id,
                'requested_subject_id' => $validated['subject_id']
            ]);
            return response()->json(['success' => false, 'message' => 'Anda tidak berhak mengelola nilai untuk mata pelajaran ini'], 403);
        }

        try {
            DB::beginTransaction();
            
            // Cari atau buat Grade utama
            $grade = Grade::firstOrCreate(
                [
                    'student_id' => $validated['student_id'],
                    'subject_id' => $validated['subject_id'],
                    'semester' => $validated['semester']
                ],
                [
                    'score' => $validated['score'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            
            // Buat GradeTask
            $gradeTask = GradeTask::create([
                'student_id' => $validated['student_id'],
                'subject_id' => $validated['subject_id'],
                'task_name' => $validated['task_name'],
                'score' => $validated['score'],
                'type' => $validated['type'],
                'grades_id' => $grade->id,
            ]);
            
            DB::commit();
            
            Log::info('Grade stored successfully', [
                'grade_task_id' => $gradeTask->id,
                'student_id' => $validated['student_id'],
                'task_name' => $validated['task_name'],
                'score' => $validated['score']
            ]);
            
            return response()->json(['success' => true, 'message' => 'Nilai berhasil disimpan'], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to store grade', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan nilai: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if ($user->role_id != 3 || !$user->subject_id) {
            Log::error('Unauthorized update attempt', ['user_id' => $user->id, 'grade_task_id' => $id]);
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'task_name' => 'required|string|max:255',
            'score' => 'required|numeric|min:0|max:100',
            'assignment_type' => 'required|in:written,observation,sumatif',
        ], [
            'task_name.required' => 'Nama tugas wajib diisi.',
            'score.required' => 'Nilai wajib diisi.',
            'score.numeric' => 'Nilai harus berupa angka.',
            'score.min' => 'Nilai minimal adalah 0.',
            'score.max' => 'Nilai maksimal adalah 100.',
            'assignment_type.required' => 'Tipe tugas wajib dipilih.',
        ]);

        try {
            $gradeTask = GradeTask::where('subject_id', $user->subject_id)->findOrFail($id);
            
            DB::beginTransaction();

            $gradeTask->update([
                'task_name' => $validated['task_name'],
                'score' => $validated['score'],
                'type' => $validated['assignment_type']
            ]);

            // Update Grade utama jika ada
            if ($gradeTask->grade) {
                $gradeTask->grade->update(['score' => $validated['score']]);
            }

            DB::commit();

            Log::info('Grade updated successfully', [
                'grade_task_id' => $gradeTask->id,
                'student_id' => $gradeTask->student_id,
                'subject_id' => $user->subject_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nilai berhasil diperbarui',
                'data' => $gradeTask->load(['student', 'subject'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update grade', [
                'error' => $e->getMessage(),
                'grade_task_id' => $id,
                'subject_id' => $user->subject_id
            ]);
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
            Log::error('Unauthorized delete attempt', ['user_id' => $user->id, 'grade_task_id' => $id]);
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        try {
            $gradeTask = GradeTask::where('subject_id', $user->subject_id)->findOrFail($id);
            
            DB::beginTransaction();
            $gradeTask->delete();
            DB::commit();

            Log::info('Grade deleted successfully', [
                'grade_task_id' => $id,
                'subject_id' => $user->subject_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nilai berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete grade', [
                'error' => $e->getMessage(),
                'grade_task_id' => $id,
                'subject_id' => $user->subject_id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus nilai: ' . $e->getMessage()
            ], 500);
        }
    }

        public function storeBatch(Request $request)
    {
        $user = Auth::user();
        
        // Log untuk debugging
        Log::info('StoreBatch called with data:', $request->all());
        
        if ($user->role_id != 3 || !$user->subject_id) {
            Log::error('Unauthorized batch store attempt', ['user_id' => $user->id]);
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        // Validasi input - sesuaikan dengan nama field dari form
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'task_name' => 'required|string|max:255',
            'assignment_type' => 'required|in:written,observation,sumatif', // ubah dari 'type' ke 'assignment_type'
            'semester' => 'required|in:Odd,Even',
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:100'
        ], [
            'class_id.required' => 'Kelas wajib dipilih.',
            'class_id.exists' => 'Kelas tidak valid.',
            'task_name.required' => 'Nama tugas wajib diisi.',
            'assignment_type.required' => 'Tipe tugas wajib dipilih.',
            'assignment_type.in' => 'Tipe tugas tidak valid.',
            'semester.required' => 'Semester wajib dipilih.',
            'semester.in' => 'Semester tidak valid.',
            'scores.required' => 'Nilai wajib diisi untuk setidaknya satu siswa.',
            'scores.array' => 'Format nilai tidak valid.',
            'scores.*.numeric' => 'Nilai harus berupa angka.',
            'scores.*.min' => 'Nilai minimal adalah 0.',
            'scores.*.max' => 'Nilai maksimal adalah 100.',
        ]);

        Log::info('Validation passed for storeBatch', [
            'class_id' => $validated['class_id'],
            'task_name' => $validated['task_name'],
            'assignment_type' => $validated['assignment_type'],
            'semester' => $validated['semester'],
            'scores_count' => count($validated['scores']),
            'user_subject_id' => $user->subject_id
        ]);

        try {
            DB::beginTransaction();
            
            $savedCount = 0;
            foreach ($validated['scores'] as $studentId => $score) {
                if (is_null($score) || $score === '') continue;
                
                // Verifikasi siswa ada di kelas
                $student = Student::where('id', $studentId)->where('class_id', $validated['class_id'])->first();
                if (!$student) {
                    Log::warning('Student not found in class', [
                        'student_id' => $studentId,
                        'class_id' => $validated['class_id']
                    ]);
                    continue;
                }
                
                Log::info('Processing student score', [
                    'student_id' => $studentId,
                    'student_name' => $student->name,
                    'score' => $score,
                    'subject_id' => $user->subject_id
                ]);
                
                // Cari atau buat Grade
                $grade = Grade::firstOrCreate(
                    [
                        'student_id' => $studentId,
                        'subject_id' => $user->subject_id, // gunakan subject_id dari user yang login
                        'semester' => $validated['semester']
                    ],
                    [
                        'score' => $score,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

                // Buat GradeTask
                $gradeTask = GradeTask::create([
                    'student_id' => $studentId,
                    'subject_id' => $user->subject_id, // gunakan subject_id dari user yang login
                    'task_name' => $validated['task_name'],
                    'score' => $score,
                    'type' => $validated['assignment_type'], // ubah ke assignment_type
                    'grades_id' => $grade->id,
                ]);
                
                Log::info('GradeTask created successfully', [
                    'grade_task_id' => $gradeTask->id,
                    'student_id' => $studentId,
                    'subject_id' => $user->subject_id
                ]);
                
                $savedCount++;
            }
            
            DB::commit();

            Log::info('Batch grades stored successfully', [
                'saved_count' => $savedCount,
                'subject_id' => $user->subject_id,
                'class_id' => $validated['class_id']
            ]);

            if ($savedCount > 0) {
                return redirect()->back()->with('success', "Berhasil menyimpan nilai untuk {$savedCount} siswa");
            } else {
                return redirect()->back()->with('warning', 'Tidak ada nilai yang tersimpan. Pastikan Anda mengisi setidaknya satu nilai.');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to store batch grades', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'class_id' => $request->input('class_id'),
                'user_subject_id' => $user->subject_id,
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage())->withInput();
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

    private function getGrades($subjectId, $classId, $taskType = null)
    {
        if (!$classId) {
            Log::warning('No class_id provided for getGrades');
            return collect();
        }
        
        $query = GradeTask::with(['student', 'subject'])
            ->where('subject_id', $subjectId)
            ->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
            
        if ($taskType) {
            $query->where('task_name', $taskType);
        }
        
        $grades = $query->latest()->get()->groupBy('student_id');
        
        Log::info('Grades retrieved', [
            'subject_id' => $subjectId,
            'class_id' => $classId,
            'task_type' => $taskType,
            'total_grade_records' => $query->count(),
            'students_with_grades' => $grades->count()
        ]);
        
        return $grades;
    }
}
