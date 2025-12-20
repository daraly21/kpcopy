<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        // =============================
        // 1️⃣ Ambil semua tahun ajaran
        // =============================
        $academicYears = AcademicYear::orderBy('name', 'asc')->get();

        // =============================
        // 2️⃣ Tentukan tahun ajaran terpilih
        // =============================
        if ($request->has('academic_year_id')) {
            $selectedYear = AcademicYear::find($request->academic_year_id);
        } else {
            // default: gunakan tahun ajaran aktif
            $selectedYear = AcademicYear::where('is_active', 1)->first();
        }

        // Jika tidak ada tahun ajaran sama sekali
        if (!$selectedYear) {
            return redirect()
                ->route('admin.academic-years.index')
                ->with('error', 'Silakan tambah tahun ajaran terlebih dahulu.');
        }

        // =============================
        // 3️⃣ Ambil data kelas & wali kelas
        // =============================
        // =============================
        // 3️⃣ Ambil data kelas & wali kelas
        // =============================
        $classes = ClassModel::with(['waliKelas' => function ($query) {
            $query->where('role_id', 2);
        }])
        ->withCount(['studentClasses' => function ($query) use ($selectedYear) {
            $query->where('academic_year_id', $selectedYear->id);
        }])
        ->get();

        // =============================
        // 4️⃣ Kirim data ke Blade
        // =============================
        return view('classes.index', [
            'classes'       => $classes,
            'academicYears' => $academicYears,
            'selectedYear'  => $selectedYear,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:classes,name',
        ]);

        ClassModel::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:classes,name,' . $id,
        ]);

        $kelas = ClassModel::findOrFail($id);
        $kelas->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $kelas = ClassModel::findOrFail($id);
        $kelas->delete();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
