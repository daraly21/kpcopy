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
use Illuminate\Support\Facades\Cache;

class GradeController extends Controller
{
       public function index(Request $request)
    {
        $user = Auth::user();
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
            'students'
        ));
    }

    // Menampilkan form input nilai tugas dengan optimasi
    public function create()
    {
        $user = Auth::user();
        
        // Cache subjects data
        $subjects = Cache::remember('subjects', 60*24, function () {
            return Subject::select('id', 'name')->get();
        });
        
        // Optimasi query dengan select kolom spesifik
        $students = Student::where('class_id', $user->class_id)
                         ->select('id', 'name')
                         ->paginate(20);
                         
        return view('grades.create', compact('subjects', 'students'));
    }

    // Menyimpan data nilai tugas yang baru
    public function store(Request $request)
    {
        // // Validasi input dari form
        // $validated = $request->validate([
        //     'student_id' => 'required|exists:students,id',
        //     'subject_id' => 'required|exists:subjects,id',
        //     'task_name' => 'required|string',
        //     'score' => 'required|numeric',
        //     'assignment_type' => 'required|in:written,observation,homework',
        //     'semester' => 'required|in:odd,even',
        // ]);

        // // Menggunakan transaksi database untuk memastikan konsistensi data
        // DB::beginTransaction();
        // try {
        //     // Buat atau ambil data grade terlebih dahulu
        //     $grade = Grade::updateOrCreate(
        //         [
        //             'student_id' => $validated['student_id'],
        //             'subject_id' => $validated['subject_id'],
        //             'semester' => $validated['semester']
        //         ],
        //         ['score' => $validated['score']] // Nilai awal, akan diupdate nanti
        //     );

        //     // Membuat GradeTask baru
        //     GradeTask::create([
        //         'student_id' => $validated['student_id'],
        //         'subject_id' => $validated['subject_id'],
        //         'task_name' => $validated['task_name'],
        //         'score' => $validated['score'],
        //         'type' => $validated['assignment_type'],
        //         'grades_id' => $grade->id,
        //     ]);
            
        //     DB::commit();
        //     return redirect()->route('grades.create')->with('success', 'Nilai tugas berhasil disimpan');
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return redirect()->route('grades.create')->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        // }
    }

public function store_batch(Request $request)
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

    // Normalisasi task_name untuk perbandingan (case insensitive + trim)
    $normalizedTaskName = strtolower(trim($taskName));

    // Tugas yang hanya boleh ada SATU KALI per siswa per mata pelajaran per semester
    $uniqueTasks = ['uts', 'uas'];

    if (in_array($normalizedTaskName, $uniqueTasks)) {

        // Ambil semua student_id yang akan diinput
        $studentIds = array_keys($gradeData);

        // Cek apakah sudah ada UTS/UAS untuk siswa-siswa ini pada:
        // - subject_id yang sama
        // - semester yang sama (dari tabel grades)
        // - task_name yang sama (di grade_tasks)
        $existing = DB::table('grade_tasks')
            ->join('grades', 'grade_tasks.grades_id', '=', 'grades.id')
            ->where('grade_tasks.subject_id', $subjectId)
            ->where('grades.semester', $semester)
            ->whereIn('grade_tasks.student_id', $studentIds)
            ->whereRaw('LOWER(TRIM(grade_tasks.task_name)) = ?', $normalizedTaskName)
            ->select('grade_tasks.student_id', 'grade_tasks.task_name', 'grades.semester')
            ->get();

        if ($existing->isNotEmpty()) {
            $studentNames = Student::whereIn('id', $existing->pluck('student_id'))
                ->pluck('name')
                ->implode(', ');

            return redirect()->back()->with('error', 
                "Nilai " . strtoupper($taskName) . " sudah pernah diinput untuk siswa ini"
            );
        }
    }

    // === PROSES PENYIMPANAN ===
    DB::beginTransaction();
    try {
        $classStudentIds = Cache::remember('class_students_'.$user->class_id, 60, function () use ($user) {
            return Student::where('class_id', $user->class_id)->pluck('id')->toArray();
        });

        $gradeTasksToInsert = [];
        $updatedStudents = [];

        foreach ($gradeData as $studentId => $score) {
            $score = (int) $score;

            if (!in_array($studentId, $classStudentIds) || $score < 0 || $score > 100) {
                continue;
            }

            // Buat atau update record di tabel grades (rapor akhir)
            $grade = Grade::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                    'semester'   => $semester
                ],
                [
                    'score' => $score // sementara, nanti bisa dihitung rata-rata otomatis
                ]
            );

            // Siapkan data untuk insert ke grade_tasks
            $gradeTasksToInsert[] = [
                'student_id'   => $studentId,
                'subject_id'   => $subjectId,
                'task_name'    => $taskName,
                'score'        => $score,
                'type'         => $assignmentType,
                'grades_id'    => $grade->id,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];

            $updatedStudents[] = $studentId;
        }

        // Insert semua sekaligus (batch)
        if (!empty($gradeTasksToInsert)) {
            GradeTask::insert($gradeTasksToInsert);
        }

        DB::commit();

        $count = count($updatedStudents);

        return redirect()->back()->with([
            'success'                   => "Berhasil menyimpan nilai {$taskName} untuk {$count} siswa.",
            'show_notification_prompt' => true,
            'notification_subject_id'  => $subjectId,
            'notification_task_name'   => $taskName,
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
    }
}

    public function update(Request $request, $id)
    {
        try {
            $grade = GradeTask::findOrFail($id);
            
            // Validate the score and type
            $validated = $request->validate([
                'score' => 'required|numeric|min:0|max:100',
                'assignment_type' => 'sometimes|in:written,observation,sumatif',
                'semester' => 'sometimes|in:odd,even',
            ]);

            $updateData = ['score' => $validated['score']];
            
            if (isset($validated['assignment_type'])) {
                $updateData['type'] = $validated['assignment_type'];
            }
            
            DB::beginTransaction();
            try {
                $grade->update($updateData);
                
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Nilai berhasil diperbarui',
                    'data' => $grade
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui nilai: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Find the correct record
            $grade = GradeTask::findOrFail($id);
            
            // Gunakan transaksi database
            DB::beginTransaction();
            try {
                // Delete the record
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