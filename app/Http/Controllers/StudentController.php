<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    //  Halaman awal: grid kelas
    public function pilihKelas()
    {
        $classes = ClassModel::all();
        return view('students.index', compact('classes'));
    }

    //  Index: daftar siswa per kelas
    public function index($classId)
    {
        $class = ClassModel::findOrFail($classId);
        $students = Student::where('class_id', $classId)->get();

        return view('students.index', compact('class', 'students'));
    }

    //  List: daftar siswa per kelas (untuk tampilan tabel)
    public function list($classId)
    {
        $class = ClassModel::findOrFail($classId);
        $students = Student::where('class_id', $classId)->get();

        return view('students.list', compact('class', 'students'));
    }

    //  Tampilkan form tambah siswa
    public function create(Request $request)
    {
    }

    //  Simpan siswa
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'nis' => 'required|numeric|digits_between:1,10|unique:students,nis',
            'gender' => 'required|in:L,P',
            'parent_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'parent_phone' => 'required|numeric|starts_with:62|digits_between:10,13',
        ], [
            'nis.unique' => 'NIS sudah terdaftar. Silakan gunakan NIS yang berbeda.',
            'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'parent_name.regex' => 'Nama orang tua hanya boleh berisi huruf dan spasi.',
            'parent_phone.starts_with' => 'Nomor HP harus diawali dengan 62.',
            'parent_phone.digits_between' => 'Nomor HP harus antara 10-13 digit.',
        ]);

        Student::create($request->all());

        return redirect()->route('admin.siswa.list', $request->class_id)
                         ->with('success', 'Siswa berhasil ditambahkan.');
    }

    //  Show detail siswa
    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }

    //  Tampilkan form edit
    public function edit(Student $student)
    {

    }

    //  Simpan perubahan siswa
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'nis' => 'required|numeric|digits_between:1,10|unique:students,nis,' . $student->id,
            'gender' => 'required|in:L,P',
            'parent_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'parent_phone' => 'required|numeric|starts_with:62|digits_between:10,13',
        ], [
            'nis.unique' => 'NIS sudah terdaftar. Silakan gunakan NIS yang berbeda.',
            'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'parent_name.regex' => 'Nama orang tua hanya boleh berisi huruf dan spasi.',
            'parent_phone.starts_with' => 'Nomor HP harus diawali dengan 62.',
            'parent_phone.digits_between' => 'Nomor HP harus antara 10-13 digit.',
        ]);

        $student->update($request->all());

        return redirect()->route('admin.siswa.list', $student->class_id)
                         ->with('success', 'Data siswa berhasil diperbarui.');
    }

    //  Hapus siswa
    public function destroy(Student $student)
    {
        $classId = $student->class_id;
        $student->delete();

        return redirect()->route('admin.siswa.list', $classId)
                         ->with('success', 'Siswa berhasil dihapus.');
    }
}