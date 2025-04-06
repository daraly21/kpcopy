<?php
// app/Http/Controllers/StudentController.php
namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Kelas;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('class')->get();
        return view('admin.siswa.index', compact('students'));
    }

    public function create()
    {
        $classes = ClassModel::all();
        return view('admin.siswa.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'nis' => 'nullable',
            'gender' => 'required|in:L,P',
            'parent_name' => 'required',
            'parent_phone' => 'required',
            'birth_place' => 'required',
            'birth_date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
        ]);

        Student::create($request->all());
        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan');
    }

    public function show(Student $siswa)
    {
        return view('admin.siswa.show', compact('siswa'));
    }

    public function edit(Student $siswa)
    {
        $classes = ClassModel::all();
        return view('admin.siswa.edit', compact('siswa', 'classes'));
    }

    public function update(Request $request, Student $siswa)
    {
        $request->validate([
            'name' => 'required',
            'nis' => 'nullable',
            'gender' => 'required|in:L,P',
            'parent_name' => 'required',
            'parent_phone' => 'required',
            'birth_place' => 'required',
            'birth_date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
        ]);

        $siswa->update($request->all());
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa diperbarui');
    }

    public function destroy(Student $siswa)
    {
        $siswa->delete();
        return back()->with('success', 'Siswa dihapus');
    }
}
