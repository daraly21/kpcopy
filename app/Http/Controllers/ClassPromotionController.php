<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\StudentClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassPromotionController extends Controller
{
    /**
     * Halaman naik kelas
     */
    public function index()
    {
        // Ambil tahun ajaran
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        
        // Ambil tahun ajaran aktif
        $activeYear = AcademicYear::where('is_active', 1)->first();
        
        // Ambil semua kelas
        $classes = ClassModel::orderBy('name')->get();
        
        return view('promotions.index', compact('academicYears', 'activeYear', 'classes'));
    }

    /**
     * Preview siswa yang akan naik kelas
     */
    public function preview(Request $request)
    {
        $request->validate([
            'from_year_id' => 'required|exists:academic_years,id',
            'to_year_id' => 'required|exists:academic_years,id',
            'class_mapping' => 'required|array',
        ]);

        $fromYear = AcademicYear::findOrFail($request->from_year_id);
        $toYear = AcademicYear::findOrFail($request->to_year_id);
        $classMapping = $request->class_mapping;

        // Ambil data siswa yang akan naik kelas
        $promotionData = [];
        $totalStudents = 0;
        $graduatingStudents = 0;

        foreach ($classMapping as $oldClassId => $newClassId) {
            if ($newClassId == 'lulus') {
                // Siswa kelas 6 yang lulus
                $students = Student::byClassAndYear($oldClassId, $fromYear->id)
                    ->with('studentClasses')
                    ->get();
                
                $graduatingStudents += $students->count();
                
                $promotionData[] = [
                    'old_class' => ClassModel::find($oldClassId),
                    'new_class' => null,
                    'students' => $students,
                    'status' => 'lulus'
                ];
            } else {
                // Siswa yang naik kelas
                $students = Student::byClassAndYear($oldClassId, $fromYear->id)
                    ->with('studentClasses')
                    ->get();
                
                $totalStudents += $students->count();
                
                $promotionData[] = [
                    'old_class' => ClassModel::find($oldClassId),
                    'new_class' => ClassModel::find($newClassId),
                    'students' => $students,
                    'status' => 'naik'
                ];
            }
        }

        return view('promotions.preview', compact(
            'fromYear',
            'toYear',
            'promotionData',
            'totalStudents',
            'graduatingStudents',
            'classMapping'
        ));
    }

    /**
     * Proses naik kelas
     */
    public function promote(Request $request)
    {
        $request->validate([
            'from_year_id' => 'required|exists:academic_years,id',
            'to_year_id' => 'required|exists:academic_years,id',
            'class_mapping' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $fromYearId = $request->from_year_id;
            $toYearId = $request->to_year_id;
            $classMapping = $request->class_mapping;

            $promoted = 0;
            $graduated = 0;
            $skipped = 0;

            foreach ($classMapping as $oldClassId => $newClassId) {
                // Ambil siswa di kelas lama pada tahun ajaran lama
                $studentClasses = StudentClass::where('class_id', $oldClassId)
                    ->where('academic_year_id', $fromYearId)
                    ->get();

                foreach ($studentClasses as $sc) {
                    if ($newClassId == 'lulus') {
                        // Siswa lulus, tidak dibuat record baru
                        $graduated++;
                    } else {
                        // Cek apakah siswa sudah ada di tahun ajaran baru
                        $exists = StudentClass::where('student_id', $sc->student_id)
                            ->where('academic_year_id', $toYearId)
                            ->exists();

                        if (!$exists) {
                            // Buat record baru untuk tahun ajaran baru
                            StudentClass::create([
                                'student_id' => $sc->student_id,
                                'class_id' => $newClassId,
                                'academic_year_id' => $toYearId
                            ]);
                            $promoted++;
                        } else {
                            $skipped++;
                        }
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.promotions.index')
                ->with('success', "Berhasil menaikkan {$promoted} siswa, {$graduated} siswa lulus" . 
                    ($skipped > 0 ? ", {$skipped} siswa dilewati (sudah ada di tahun ajaran baru)" : ""));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menaikkan kelas: ' . $e->getMessage());
        }
    }

    /**
     * Ambil statistik siswa per kelas untuk tahun ajaran tertentu
     */
    public function getStatistics(Request $request)
    {
        $yearId = $request->academic_year_id;
        
        $statistics = ClassModel::withCount(['studentClasses' => function ($query) use ($yearId) {
            $query->where('academic_year_id', $yearId);
        }])->get()->map(function ($class) {
            return [
                'class_id' => $class->id,
                'class_name' => $class->name,
                'student_count' => $class->student_classes_count
            ];
        });

        return response()->json($statistics);
    }
}