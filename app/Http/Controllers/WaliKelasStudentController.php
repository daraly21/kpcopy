<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class WaliKelasStudentController extends Controller
{
    // Tanpa constructor karena middleware sudah ditangani di routes

    // Halaman daftar siswa
    public function index($classId)
    {
        $class = ClassModel::findOrFail($classId);
        $students = Student::where('class_id', $classId)->get();

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