<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class RapotController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $class = null;
        $subjects = Cache::remember('subjects', 60*24, function () {
            return Subject::select('id', 'name')->get();
        });
        $studentsData = [];
        $selectedSemester = null;

        // Pastikan wali kelas memiliki kelas
        if ($user->class_id) {
            $class = ClassModel::find($user->class_id);
        }

        // Ambil data jika filter semester diterapkan
        if (request()->has('semester')) {
            $selectedSemester = request('semester');

            if ($class) {
                // Ambil Active Year
                $activeYear = \App\Models\AcademicYear::where('is_active', 1)->first();

                // Ambil Student IDs
                $studentIds = [];
                if ($activeYear) {
                    $studentIds = \App\Models\StudentClass::where('class_id', $class->id)
                                    ->where('academic_year_id', $activeYear->id)
                                    ->pluck('student_id')
                                    ->toArray();
                }

                // Ambil semua siswa di kelas
                $students = Student::whereIn('id', $studentIds)
                                  ->select('id', 'nis', 'name')
                                  ->get();

                // Ambil semua nilai siswa untuk semester yang dipilih
                $studentIds = $students->pluck('id')->toArray();
                $grades = Grade::whereIn('student_id', $studentIds)
                               ->where('semester', $selectedSemester)
                               ->select('student_id', 'subject_id', 'final_score')
                               ->get()
                               ->groupBy('student_id');

                // Siapkan data untuk tabel
                foreach ($students as $student) {
                    $studentGrades = $grades->get($student->id, collect([]));
                    $studentData = [
                        'student_number' => $student->nis,
                        'name' => $student->name,
                        'scores' => [],
                        'total' => 0,
                        'average' => 0,
                        'rank' => 0,
                    ];

                    // Inisialisasi skor untuk setiap mata pelajaran
                    foreach ($subjects as $subject) {
                        $grade = $studentGrades->firstWhere('subject_id', $subject->id);
                        $score = $grade ? $grade->final_score : 0;
                        $studentData['scores'][$subject->id] = $score;
                    }

                    // Hitung total dan rata-rata
                    $studentData['total'] = array_sum($studentData['scores']);
                    $studentData['average'] = count($subjects) > 0 ? $studentData['total'] / count($subjects) : 0;
                    $studentsData[] = $studentData;
                }

                // Urutkan siswa berdasarkan rata-rata untuk peringkat
                $studentsData = collect($studentsData)->sortByDesc('average')->values()->toArray();
                foreach ($studentsData as $index => $studentData) {
                    $studentsData[$index]['rank'] = $index + 1;
                }
            }
        }

        return view('rapor.index', compact('class', 'subjects', 'studentsData', 'selectedSemester'));
    }
}