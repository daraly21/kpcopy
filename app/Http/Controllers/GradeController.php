<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\GradeTask;
use App\Models\Subject;
use App\Models\Student;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    // Menampilkan form input nilai tugas
    public function create()
    {
        // Ambil semua data mata pelajaran dan siswa untuk dropdown
        $subjects = Subject::all();
        $students = Student::all();

        return view('grades.create', compact('subjects', 'students'));
    }

    // Menyimpan data nilai tugas yang baru
    public function store(Request $request)
    {
        // Validasi input dari form
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'task_name' => 'required|string',
            'score' => 'required|numeric',
        ]);

        // Membuat GradeTask baru
        GradeTask::create([
            'student_id' => $validated['student_id'],
            'subject_id' => $validated['subject_id'],
            'task_name' => $validated['task_name'],
            'score' => $validated['score'],
        ]);

        return redirect()->route('grades.create')->with('success', 'Nilai tugas berhasil disimpan');
    }

    public function store_batch(Request $request)
{
    // Validasi input
    $request->validate([
        'subject_id' => 'required|exists:subjects,id',
        'task_name' => 'required|string',
        'grade_data' => 'required|json',
    ]);
    
    $subjectId = $request->subject_id;
    $taskName = $request->task_name;
    $gradeData = json_decode($request->grade_data, true);
    
    // Simpan nilai untuk setiap siswa
    $count = 0;
    foreach ($gradeData as $studentId => $score) {
        // Validasi nilai
        if ($score < 0 || $score > 100) {
            continue;
        }
        
        // Simpan nilai ke tabel grade_tasks
        GradeTask::create([
            'subject_id' => $subjectId,
            'task_name' => $taskName,
            'score' => $score,
            'student_id' => $studentId,
        ]);
        
        // Hitung nilai rata-rata dari semua tugas untuk siswa dan mata pelajaran ini
        $averageScore = GradeTask::where('student_id', $studentId)
                              ->where('subject_id', $subjectId)
                              ->avg('score');
        
        // Simpan nilai rata-rata ke tabel grades
        Grade::updateOrCreate(
            ['student_id' => $studentId, 'subject_id' => $subjectId],
            ['score' => $averageScore]
        );
        
        $count++;
    }
    
    return redirect()->back()->with('success', "Berhasil menyimpan nilai untuk {$count} siswa.");
}
public function update(Request $request, $id)
{
    try {
        // Find the correct record based on what's displayed in your view
        // If your view is showing GradeTask, use this:
        $grade = GradeTask::findOrFail($id);
        
        // Validate the score
        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:100'
        ]);

        // Update the score
        $grade->update([
            'score' => $validated['score']
        ]);

        // Additionally, update the average in the grades table
        // Only if your system maintains both tables
        $averageScore = GradeTask::where('student_id', $grade->student_id)
                            ->where('subject_id', $grade->subject_id)
                            ->avg('score');
        
        Grade::updateOrCreate(
            ['student_id' => $grade->student_id, 'subject_id' => $grade->subject_id],
            ['score' => $averageScore]
        );

        return response()->json([
            'success' => true,
            'message' => 'Nilai berhasil diperbarui',
            'data' => $grade
        ]);
        
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
        // Find the correct record based on what's displayed in your view
        // If your view is showing GradeTask, use this:
        $grade = GradeTask::findOrFail($id);
        
        // Store references for average recalculation
        $studentId = $grade->student_id;
        $subjectId = $grade->subject_id;
        
        // Delete the record
        $grade->delete();
        
        // Recalculate the average in the grades table
        $averageScore = GradeTask::where('student_id', $studentId)
                            ->where('subject_id', $subjectId)
                            ->avg('score');
        
        if ($averageScore) {
            Grade::updateOrCreate(
                ['student_id' => $studentId, 'subject_id' => $subjectId],
                ['score' => $averageScore]
            );
        } else {
            // If no tasks left, delete the grade record
            Grade::where('student_id', $studentId)
                 ->where('subject_id', $subjectId)
                 ->delete();
        }
        
        return redirect()->back()->with('success', 'Nilai berhasil dihapus');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menghapus nilai: '.$e->getMessage());
    }
}


}
