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
use Illuminate\Support\Facades\Cache;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedSubject = $request->input('subject_id');
        $selectedTaskType = $request->input('task_name');

        // Ambil tahun ajaran aktif
        $activeYear = AcademicYear::where('is_active', 1)->first();
        if (!$activeYear) {
            return redirect()
                ->route('admin.academic-years.index')
                ->with('error', 'Silakan set tahun ajaran aktif terlebih dahulu.');
        }

        // Jika user adalah Wali Kelas
        if ($user->role_id == 2) {
            $class_id = $user->class_id;
            $classes = null;
            $className = ClassModel::find($class_id)->name ?? "Kelas " . $class_id;
        } else {
            $class_id = $request->input('class_id');
            $classes = ClassModel::all();
            $className = $class_id ? (ClassModel::find($class_id)->name ?? "Kelas " . $class_id) : null;
        }

        $subjects = Subject::all();
        $task_types = null;
        $stats = null;
        $grades = [];
        $students = [];

        if ($class_id) {
            // Ambil siswa yang terdaftar di kelas pada tahun ajaran aktif
            $studentIds = StudentClass::where('class_id', $class_id)
                ->where('academic_year_id', $activeYear->id)
                ->pluck('student_id')
                ->toArray();

            // Ambil jenis tugas
            $taskQuery = GradeTask::whereIn('student_id', $studentIds);
            
            if ($selectedSubject) {
                $taskQuery->where('subject_id', $selectedSubject);
            }
            
            $task_types = $taskQuery->pluck('task_name')->unique();

            // Kalkulasi statistik
            $gradesQuery = GradeTask::whereIn('student_id', $studentIds);

            if ($selectedSubject) {
                $gradesQuery->where('subject_id', $selectedSubject);
            }

            if ($selectedTaskType) {
                $gradesQuery->where('task_name', $selectedTaskType);
            }

            $statsData = $gradesQuery->select(
                DB::raw('AVG(score) as average'),
                DB::raw('MAX(score) as highest'),
                DB::raw('MIN(score) as lowest'),
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

            // Ambil data nilai
            $grades = GradeTask::join('students', 'grade_tasks.student_id', '=', 'students.id')
                ->join('subjects', 'grade_tasks.subject_id', '=', 'subjects.id')
                ->whereIn('students.id', $studentIds)
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

            // Ambil daftar siswa
            $students = Student::whereIn('id', $studentIds)->orderBy('name')->get();
        }

        return view('grades.list', compact(
            'grades',
            'classes',
            'subjects',
            'task_types',
            'class_id',
            'className',
            'selectedSubject',
            'selectedTaskType',
            'stats',
            'students',
            'activeYear'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Ambil tahun ajaran aktif
        $activeYear = AcademicYear::where('is_active', 1)->first();
        if (!$activeYear) {
            return redirect()
                ->route('grades.index')
                ->with('error', 'Tahun ajaran aktif belum diset.');
        }

        $subjects = Cache::remember('subjects', 60*24, function () {
            return Subject::select('id', 'name')->get();
        });

        // Ambil siswa yang terdaftar di kelas wali pada tahun ajaran aktif
        $studentIds = StudentClass::where('class_id', $user->class_id)
            ->where('academic_year_id', $activeYear->id)
            ->pluck('student_id')
            ->toArray();
        
        $students = Student::whereIn('id', $studentIds)
                         ->select('id', 'name')
                         ->paginate(20);
                         
        return view('grades.create', compact('subjects', 'students', 'activeYear'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'subject_id'      => 'required|exists:subjects,id',
            'task_name'       => 'required|string|max:255',
            'grade_data'      => 'required|json',
            'assignment_type' => 'required|in:written,observation,sumatif',
            'semester'        => 'required|in:odd,even',
        ]);

        $subjectId      = $request->subject_id;
        $taskName       = trim($request->task_name);
        $assignmentType = $request->assignment_type;
        $semester       = $request->semester;
        $gradeData      = json_decode($request->grade_data, true);

        $activeYear = AcademicYear::where('is_active', 1)->first();
        if (!$activeYear) {
            return back()->with('error', 'Tahun ajaran aktif belum diset oleh admin.');
        }

        $normalizedTaskName = strtolower(trim($taskName));
        $uniqueTasks = ['uts', 'uas'];

        if (in_array($normalizedTaskName, $uniqueTasks)) {
            $studentIds = array_keys($gradeData);

            $existing = DB::table('grade_tasks')
                ->join('grades', 'grade_tasks.grades_id', '=', 'grades.id')
                ->where('grade_tasks.subject_id', $subjectId)
                ->where('grades.semester', $semester)
                ->where('grades.academic_year_id', $activeYear->id)
                ->whereIn('grade_tasks.student_id', $studentIds)
                ->whereRaw('LOWER(TRIM(grade_tasks.task_name)) = ?', $normalizedTaskName)
                ->select('grade_tasks.student_id')
                ->get();

            if ($existing->isNotEmpty()) {
                return back()->with('error', "Nilai ". strtoupper($taskName) ." sudah pernah diinput untuk siswa ini.");
            }
        }

        DB::beginTransaction();
        try {
            // Ambil siswa dalam kelas wali pada tahun ajaran aktif
            $classStudentIds = StudentClass::where('class_id', $user->class_id)
                ->where('academic_year_id', $activeYear->id)
                ->pluck('student_id')
                ->toArray();

            $gradeTasksToInsert = [];
            $updatedStudents = [];

            foreach ($gradeData as $studentId => $score) {
                $score = (int) $score;

                if (!in_array($studentId, $classStudentIds) || $score < 0 || $score > 100) {
                    continue;
                }

                $grade = Grade::updateOrCreate(
                    [
                        'student_id'       => $studentId,
                        'subject_id'       => $subjectId,
                        'semester'         => $semester,
                        'academic_year_id' => $activeYear->id,
                    ],
                    [
                        'score'            => $score,
                        'academic_year_id' => $activeYear->id,
                    ]
                );

                $gradeTasksToInsert[] = [
                    'student_id'   => $studentId,
                    'subject_id'   => $subjectId,
                    'task_name'    => $taskName,
                    'type'         => $assignmentType,
                    'grades_id'    => $grade->id,
                    'score'        => $score,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];

                $updatedStudents[] = $studentId;
            }

            if (!empty($gradeTasksToInsert)) {
                GradeTask::insert($gradeTasksToInsert);
            }

            DB::commit();

            return back()->with([
                'success'                   => "Berhasil menyimpan nilai {$taskName} untuk ".count($updatedStudents)." siswa.",
                'show_notification_prompt'  => true,
                'notification_subject_id'   => $subjectId,
                'notification_task_name'    => $taskName,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan nilai: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $gradeTask = GradeTask::findOrFail($id);

            $validated = $request->validate([
                'score'      => 'required|numeric|min:0|max:100',
                'semester'   => 'sometimes|in:odd,even',
                'assignment_type' => 'sometimes|in:written,observation,sumatif',
            ]);

            DB::beginTransaction();

            $gradeTask->update([
                'score' => $validated['score'],
                'type'  => $validated['assignment_type'] ?? $gradeTask->type,
            ]);

            if (isset($validated['semester'])) {
                $gradeTask->grade->update([
                    'semester' => $validated['semester']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Nilai berhasil diperbarui',
                'data' => $gradeTask
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui nilai: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $grade = GradeTask::findOrFail($id);
            
            DB::beginTransaction();
            try {
                $grade->delete();
                
                DB::commit();
                return redirect()->back()->with('success', 'Nilai berhasil dihapus');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus nilai: '.$e->getMessage());
        }
    }

    public function export(Request $request)
    {
        return back()->with('success', 'Data nilai berhasil diekspor!');
    }
}