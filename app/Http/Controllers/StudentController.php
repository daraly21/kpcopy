<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentClass;
use App\Models\ClassModel;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Halaman awal: grid kelas dengan filter tahun ajaran
     */
    public function pilihKelas(Request $request)
    {
        // Ambil semua tahun ajaran
        $academicYears = AcademicYear::orderBy('name', 'asc')->get();

        // Tentukan tahun ajaran yang dipilih
        if ($request->has('academic_year_id')) {
            $selectedYear = AcademicYear::find($request->academic_year_id);
        } else {
            $selectedYear = AcademicYear::where('is_active', 1)->first();
        }

        // Jika tidak ada tahun ajaran
        if (!$selectedYear) {
            return redirect()
                ->route('admin.academic-years.index')
                ->with('error', 'Silakan tambah tahun ajaran terlebih dahulu.');
        }

        // Ambil semua kelas dengan jumlah siswa per tahun ajaran
        $classes = ClassModel::withCount(['studentClasses' => function ($query) use ($selectedYear) {
            $query->where('academic_year_id', $selectedYear->id);
        }])->get();

        return view('students.index', compact('classes', 'academicYears', 'selectedYear'));
    }

    /**
     * List: daftar siswa per kelas dan tahun ajaran
     */
    public function list($classId, Request $request)
    {
        $class = ClassModel::findOrFail($classId);

        // Tentukan tahun ajaran yang dipilih (dari URL atau default ke aktif)
        if ($request->has('academic_year_id')) {
            $selectedYear = AcademicYear::find($request->academic_year_id);
        } else {
            $selectedYear = AcademicYear::where('is_active', 1)->first();
        }

        if (!$selectedYear) {
            return redirect()
                ->route('admin.siswa.kelas')
                ->with('error', 'Silakan tambah tahun ajaran terlebih dahulu.');
        }

        // Ambil siswa berdasarkan kelas dan tahun ajaran
        $students = Student::byClassAndYear($classId, $selectedYear->id)
            ->with(['studentClasses' => function ($query) use ($selectedYear) {
                $query->where('academic_year_id', $selectedYear->id);
            }])
            ->orderBy('nis', 'asc')
            ->get();

        // Ambil semua kelas untuk dropdown pindah kelas
        $classes = ClassModel::orderBy('name')->get();

        return view('students.list', compact('class', 'students', 'selectedYear', 'classes'));
    }

    /**
     * Simpan siswa baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'nis' => 'required|numeric|digits_between:1,10|unique:students,nis',
            'gender' => 'required|in:L,P',
            'parent_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'parent_phone' => 'required|numeric|starts_with:62|digits_between:10,13',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id'
        ], [
            'nis.unique' => 'NIS sudah terdaftar. Silakan gunakan NIS yang berbeda.',
            'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'parent_name.regex' => 'Nama orang tua hanya boleh berisi huruf dan spasi.',
            'parent_phone.starts_with' => 'Nomor HP harus diawali dengan 62.',
            'parent_phone.digits_between' => 'Nomor HP harus antara 10-13 digit.',
        ]);

        DB::beginTransaction();
        try {
            // Buat data siswa
            $student = Student::create([
                'name' => $request->name,
                'nis' => $request->nis,
                'gender' => $request->gender,
                'parent_name' => $request->parent_name,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'parent_phone' => $request->parent_phone,
            ]);

            // Assign ke kelas pada tahun ajaran tertentu
            StudentClass::create([
                'student_id' => $student->id,
                'class_id' => $request->class_id,
                'academic_year_id' => $request->academic_year_id
            ]);

            DB::commit();

            return redirect()
                ->route('admin.siswa.list', ['class' => $request->class_id, 'academic_year_id' => $request->academic_year_id])
                ->with('success', 'Siswa berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan siswa: ' . $e->getMessage());
        }
    }

    /**
     * Update data siswa
     */
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

        $currentClass = $student->getCurrentClass();
        $classId = $currentClass ? $currentClass->class_id : null;

        return redirect()
            ->route('admin.siswa.list', $classId)
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Pindahkan siswa ke kelas lain (untuk tahun ajaran yang sama atau berbeda)
     */


    /**
     * Naikkan kelas otomatis untuk seluruh siswa
     */
    /**
     * Halaman Naik Kelas (per kelas)
     */
    public function promotion(ClassModel $class, Request $request) {
        $academicYears = AcademicYear::orderBy('id', 'desc')->get();
        // default active year
        $activeYear = AcademicYear::where('is_active', 1)->first();
        
        // current year context (from URL or active)
        $currentYearId = $request->input('academic_year_id', $activeYear ? $activeYear->id : null);
        $currentYear = AcademicYear::find($currentYearId);

        if (!$currentYear) {
            return redirect()->back()->with('error', 'Tentukan tahun ajaran asal terlebih dahulu.');
        }

        // Get students in this class for the current year
        $students = Student::whereHas('studentClasses', function($q) use ($class, $currentYear) {
            $q->where('class_id', $class->id)
              ->where('academic_year_id', $currentYear->id);
        })->orderBy('nis', 'asc')->get();

        $classes = ClassModel::orderBy('name')->get();

        return view('students.promotion', compact('class', 'students', 'academicYears', 'currentYear', 'classes'));
    }

    /**
     * Proses Naik Kelas
     */
    public function processPromotion(Request $request)
    {
        $request->validate([
            'from_year_id' => 'required|exists:academic_years,id',
            'to_year_id' => 'required|exists:academic_years,id',
            'target_class_id' => 'required', // can be 'graduated' or class id
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        DB::beginTransaction();
        try {
            $toYearId = $request->to_year_id;
            $targetClassId = $request->target_class_id;
            $studentIds = $request->student_ids;

            $promotedCount = 0;
            $graduatedCount = 0;

            foreach ($studentIds as $studentId) {
                // Check if already exists in target year
                $exists = StudentClass::where('student_id', $studentId)
                    ->where('academic_year_id', $toYearId)
                    ->exists();

                if (!$exists) {
                    if ($targetClassId === 'graduated') {
                         // Logic for graduation (maybe just don't add to next year, or add a special status?)
                         // For now, let's assume if "graduated", we don't create a StudentClass record for the next year
                         // Or create one with a null class if your system supports it?
                         // The user said "Old logic: Class 6 -> Lulus". 
                         // Usually "Lulus" means they are alumni.
                         // Let's just NOT add them to the new year class, effectively making them inactive.
                         // Optionally: Update student status to 'alumni' if you extended the table, but let's stick to simple "no record" for now
                         // Update: if we want to track them as alumni, maybe we need a flag. 
                         // But for "Naik Kelas", simply not enrolling them in the next active year is "Graduating" them from the active roster.
                         // But to be safe, maybe we should just count them.
                         $graduatedCount++;
                    } else {
                        StudentClass::create([
                            'student_id' => $studentId,
                            'class_id' => $targetClassId,
                            'academic_year_id' => $toYearId
                        ]);
                        $promotedCount++;
                    }
                }
            }

            DB::commit();

            $message = "Berhasil memproses: {$promotedCount} siswa naik kelas";
            if ($graduatedCount > 0) {
                $message .= " dan {$graduatedCount} siswa lulus.";
            }

            return redirect()
                ->route('admin.siswa.list', ['class' => $request->from_class_id, 'academic_year_id' => $request->from_year_id])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses naik kelas: ' . $e->getMessage());
        }
    }

    /**
     * Hapus siswa
     */
    public function destroy(Student $student)
    {
        $currentClass = $student->getCurrentClass();
        $classId = $currentClass ? $currentClass->class_id : null;

        $student->delete();

        return redirect()
            ->route('admin.siswa.list', $classId)
            ->with('success', 'Siswa berhasil dihapus.');
    }
    /**
     * Naikkan Semua Kelas (Global Promotion)
     */
    public function promoteAllClasses(Request $request)
    {
        $request->validate([
            'from_year_id' => 'required|exists:academic_years,id',
        ]);

        DB::beginTransaction();
        try {
            $fromYearId = $request->from_year_id;
            $fromYear = AcademicYear::find($fromYearId);
            if (!$fromYear) {
                 return back()->with('error', 'Tahun ajaran asal tidak ditemukan.');
            }

            // Find next academic year by NAME (alphanumeric compare works for YYYY/YYYY)
            $nextYear = AcademicYear::where('name', '>', $fromYear->name)
                        ->orderBy('name', 'asc')
                        ->first();

            if (!$nextYear) {
                return back()->with('error', 'Tidak ditemukan Tahun Ajaran berikutnya. Silakan buat tahun ajaran baru terlebih dahulu.');
            }

            $classes = ClassModel::all();
            $promotedTotal = 0;
            $graduatedTotal = 0;

            // Mapping: Class 1 -> 2, 2 -> 3, ... 6 -> Graduated
            // We assume names "Kelas 1", "Kelas 2" or IDs are sequential.
            // Let's rely on ID if they are 1-6.
             // Or safer: Extract number from name? "Kelas 1" -> 1.
             // Let's use ID mapping assuming seeded data 1-6.
            
            // First, get all students in fromYear
            $studentClasses = StudentClass::where('academic_year_id', $fromYearId)->get();
            
            foreach ($studentClasses as $sc) {
                 $currentClass = $classes->find($sc->class_id);
                 if (!$currentClass) continue;

                 // Logic: If Class 1, promote to Class 2.
                 // We need to find "Class 2".
                 // Let's parse the name or use ID logic.
                 // User said: "kelas 6 lulus, kelas 1 kosong".
                 // Assuming IDs 1-6 match names Kelas 1 - Kelas 6.
                 
                 // Fallback if IDs are weird: Regex match number.
                 if (preg_match('/(\d+)/', $currentClass->name, $matches)) {
                     $level = intval($matches[1]);
                     $nextLevel = $level + 1;
                     
                     if ($nextLevel > 6) {
                         // Graduated
                         $graduatedTotal++;
                         continue; 
                     }
                     
                     // Find class with name "Kelas $nextLevel"
                     $nextClass = ClassModel::where('name', 'LIKE', "%$nextLevel%")->first();
                     
                     if ($nextClass) {
                         // Promote
                         // Check existence first
                         $exists = StudentClass::where('student_id', $sc->student_id)
                                    ->where('academic_year_id', $nextYear->id)
                                    ->exists();
                                    
                         if (!$exists) {
                             StudentClass::create([
                                 'student_id' => $sc->student_id,
                                 'class_id' => $nextClass->id,
                                 'academic_year_id' => $nextYear->id
                             ]);
                             $promotedTotal++;
                         }
                     }
                 }
            }

            DB::commit();

            return back()->with('success', "Berhasil menaikkan {$promotedTotal} siswa ke Tahun Ajaran {$nextYear->name} (dan {$graduatedTotal} lulus).");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses kenaikan massal: ' . $e->getMessage());
        }
    }
}