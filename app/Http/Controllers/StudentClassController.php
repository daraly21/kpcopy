<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassModel;
use App\Models\AcademicYear;
use App\Models\StudentClass;
use Illuminate\Http\Request;

class StudentClassController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::where('is_active', 1)->first();
        $classes = ClassModel::all();

        return view('student_classes.index', compact('activeYear', 'classes'));
    }

    public function show($classId)
    {
        $activeYear = AcademicYear::where('is_active', 1)->first();

        $students = StudentClass::with('student')
            ->where('class_id', $classId)
            ->where('academic_year_id', $activeYear->id)
            ->get();

        $class = ClassModel::findOrFail($classId);

        return view('student_classes.show', compact('class', 'students', 'activeYear'));
    }

    public function addStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        $activeYear = AcademicYear::where('is_active', 1)->first();

        StudentClass::create([
            'student_id' => $request->student_id,
            'class_id' => $request->class_id,
            'academic_year_id' => $activeYear->id,
        ]);

        return back()->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }

    public function removeStudent($id)
    {
        StudentClass::findOrFail($id)->delete();

        return back()->with('success', 'Siswa berhasil dihapus dari kelas.');
    }

    public function promoteStudents()
    {
        $current = AcademicYear::where('is_active', 1)->first();
        $next = AcademicYear::where('id', '>', $current->id)->orderBy('id')->first();

        if (!$next) {
            return back()->with('error','Tahun ajaran selanjutnya belum dibuat.');
        }

        $rombels = StudentClass::where('academic_year_id', $current->id)->get();

        foreach ($rombels as $rombel) {
            StudentClass::create([
                'student_id' => $rombel->student_id,
                'class_id' => $rombel->class_id + 1, // naik kelas
                'academic_year_id' => $next->id,
            ]);
        }

        return back()->with('success', 'Semua siswa berhasil dinaikkan kelas.');
    }
}
