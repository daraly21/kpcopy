<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class WaliKelasStudentController extends Controller
{
    
    // Halaman daftar siswa
    // Halaman daftar siswa
    public function index($classId)
    {
        $class = ClassModel::findOrFail($classId);

         // Get Active Year
         $activeYear = \App\Models\AcademicYear::where('is_active', 1)->first();
            
         // Get Student IDs for this class in active year
         $studentIds = [];
         if ($activeYear) {
             $studentIds = \App\Models\StudentClass::where('class_id', $classId)
                             ->where('academic_year_id', $activeYear->id)
                             ->pluck('student_id')
                             ->toArray();
         }

        $students = Student::whereIn('id', $studentIds)->get();

        return view('phone.index', compact('class', 'students'));
    }

    // Update nomor telepon orang tua
    public function updateParentPhone(Request $request, Student $student)
    {
        $request->validate([
            'parent_phone' => 'required|string|starts_with:62',
        ]);

        $student->update([
            'parent_phone' => $request->parent_phone
        ]);

        return redirect()->back()->with('success', 'Nomor HP orang tua berhasil diperbarui.');
    }
}