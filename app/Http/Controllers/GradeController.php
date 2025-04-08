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
            'assignment_type' => 'required|in:written,observation,homework',
            'semester' => 'required|in:odd,even',
        ]);

        // Buat atau ambil data grade terlebih dahulu
        $grade = Grade::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'subject_id' => $validated['subject_id'],
                'semester' => $validated['semester']
            ],
            ['score' => $validated['score']] // Nilai awal, akan diupdate nanti
        );

        // Membuat GradeTask baru
        $gradeTask = GradeTask::create([
            'student_id' => $validated['student_id'],
            'subject_id' => $validated['subject_id'],
            'task_name' => $validated['task_name'],
            'score' => $validated['score'],
            'type' => $validated['assignment_type'],
            'grades_id' => $grade->id, // Menambahkan grades_id dari grade yang dibuat
        ]);

        // Menghitung nilai rata-rata dan menyimpan ke tabel Grade
        $this->updateAverageGrade(
            $validated['student_id'], 
            $validated['subject_id'], 
            $validated['semester']
        );

        return redirect()->route('grades.create')->with('success', 'Nilai tugas berhasil disimpan');
    }

    public function store_batch(Request $request)
    {
        // Validasi input
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'task_name' => 'required|string',
            'grade_data' => 'required|json',
            'assignment_type' => 'required|in:written,observation,homework',
            'semester' => 'required|in:odd,even',
        ]);
        
        $subjectId = $request->subject_id;
        $taskName = $request->task_name;
        $assignmentType = $request->assignment_type;
        $semester = $request->semester;
        $gradeData = json_decode($request->grade_data, true);
        
        // Simpan nilai untuk setiap siswa
        $count = 0;
        foreach ($gradeData as $studentId => $score) {
            // Validasi nilai
            if ($score < 0 || $score > 100) {
                continue;
            }
            
            // Buat atau ambil data grade terlebih dahulu
            $grade = Grade::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                    'semester' => $semester
                ],
                ['score' => $score] // Nilai awal, akan diupdate nanti
            );
            
            // Simpan nilai ke tabel grade_tasks
            GradeTask::create([
                'subject_id' => $subjectId,
                'task_name' => $taskName,
                'score' => $score,
                'student_id' => $studentId,
                'type' => $assignmentType,
                'grades_id' => $grade->id, // Menambahkan grades_id
            ]);
            
            // Update nilai rata-rata di tabel grades
            $this->updateAverageGrade($studentId, $subjectId, $semester);
            
            $count++;
        }
        
        return redirect()->back()->with('success', "Berhasil menyimpan nilai untuk {$count} siswa.");
    }

    public function update(Request $request, $id)
    {
        try {
            // Find the correct record based on what's displayed in your view
            $grade = GradeTask::findOrFail($id);
            
            // Validate the score and type
            $validated = $request->validate([
                'score' => 'required|numeric|min:0|max:100',
                'assignment_type' => 'sometimes|in:written,observation,homework',
                'semester' => 'sometimes|in:odd,even',
            ]);

            // Update fields
            $updateData = ['score' => $validated['score']];
            
            if (isset($validated['assignment_type'])) {
                $updateData['type'] = $validated['assignment_type'];
            }
            
            $grade->update($updateData);

            // Get semester from request or use existing one
            $semester = $request->semester ?? 'odd'; // Default to odd if not provided
            
            // Update the average in grades table
            $this->updateAverageGrade($grade->student_id, $grade->subject_id, $semester);

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
            $grade = GradeTask::findOrFail($id);
            
            // Store references for average recalculation
            $studentId = $grade->student_id;
            $subjectId = $grade->subject_id;
            
            // Delete the record
            $grade->delete();
            
            // Get all grades for this student and subject
            $semester = request('semester', 'odd'); // Default to odd if not specified
            
            // Update the average in grades table
            $this->updateAverageGrade($studentId, $subjectId, $semester);
            
            return redirect()->back()->with('success', 'Nilai berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus nilai: '.$e->getMessage());
        }
    }

    /**
     * Helper method untuk menghitung rata-rata nilai dan update tabel grades
     */
    private function updateAverageGrade($studentId, $subjectId, $semester)
    {
        // Hitung nilai rata-rata dari semua tugas untuk siswa dan mata pelajaran ini
        $averageScore = GradeTask::where('student_id', $studentId)
                            ->where('subject_id', $subjectId)
                            ->avg('score');
        
        if ($averageScore) {
            Grade::updateOrCreate(
                [
                    'student_id' => $studentId, 
                    'subject_id' => $subjectId,
                    'semester' => $semester
                ],
                ['score' => $averageScore]
            );
        } else {
            // If no tasks left, delete the grade record
            Grade::where('student_id', $studentId)
                ->where('subject_id', $subjectId)
                ->where('semester', $semester)
                ->delete();
        }
    }
}