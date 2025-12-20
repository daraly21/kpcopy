<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    /**
     * Tampilkan daftar tahun ajaran
     */
    public function index()
    {
        $academicYears = AcademicYear::orderBy('name', 'asc')->get();
        return view('academic_years.index', compact('academicYears'));
    }

    /**
     * Simpan tahun ajaran baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20|unique:academic_years,name|regex:/^\d{4}\/\d{4}$/',
        ], [
            'name.required' => 'Nama tahun ajaran harus diisi.',
            'name.unique' => 'Tahun ajaran sudah ada.',
            'name.regex' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2024/2025).'
        ]);

        // Validasi tahun ajaran: tahun kedua harus lebih besar 1 dari tahun pertama
        $years = explode('/', $request->name);
        if (count($years) == 2) {
            $year1 = (int) $years[0];
            $year2 = (int) $years[1];
            
            if ($year2 != $year1 + 1) {
                return back()->with('error', 'Tahun kedua harus lebih besar 1 dari tahun pertama (contoh: 2024/2025).');
            }
        }

        AcademicYear::create([
            'name' => $request->name,
            'is_active' => false
        ]);

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    /**
     * Set tahun ajaran sebagai aktif
     */
    public function setActive($id)
    {
        DB::beginTransaction();
        try {
            // Non-aktifkan semua tahun ajaran
            AcademicYear::where('is_active', 1)->update(['is_active' => 0]);

            // Aktifkan tahun ajaran yang dipilih
            $academicYear = AcademicYear::findOrFail($id);
            $academicYear->update(['is_active' => 1]);

            DB::commit();

            return redirect()->route('admin.academic-years.index')
                ->with('success', "Tahun ajaran {$academicYear->name} berhasil diaktifkan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengaktifkan tahun ajaran: ' . $e->getMessage());
        }
    }
    /**
     * Hapus tahun ajaran
     */
    public function destroy($id)
    {
        $academicYear = AcademicYear::findOrFail($id);

        // 1. Cek apakah tahun ajaran aktif
        if ($academicYear->is_active) {
            return back()->with('error', 'Tidak dapat menghapus tahun ajaran yang sedang aktif.');
        }

        // 2. Cek apakah ada data siswa yang terkait (cegah hapus jika ada data)
        // Kita perlu cek model StudentClass (pivot table)
        // Pastikan import model StudentsClass atau gunakan DB/relation check
        $hasStudents = \App\Models\StudentClass::where('academic_year_id', $id)->exists();
        
        if ($hasStudents) {
             return back()->with('error', 'Tidak dapat menghapus tahun ajaran ini karena masih memiliki data siswa yang terkait. Silakan hapus data siswa terlebih dahulu atau arsipkan.');
        }

        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}