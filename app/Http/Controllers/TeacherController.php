<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Subject;
use App\Models\ClassModel; // ganti sesuai nama model kelas kamu bila berbeda
use App\Models\User;        // <-- ditambahkan
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    /* UTIL: ambil ID subject yang diizinkan (Agama & PJOK) */
    private function allowedSubjectIds(): array
    {
        return Subject::query()
            ->where(function ($q) {
                $q->where('name', 'like', '%Agama%')
                  ->orWhere('name', 'like', '%PJOK%');
            })
            ->pluck('id')
            ->all();
    }

    public function index(Request $request)
    {
        // --- AUTO SYNC dari users ke teachers (tanpa tombol) ---
        $this->autoSyncFromUsers();

        $query = Teacher::with(['user', 'class', 'subject']);

        // Filter berdasarkan status kerja
        if ($request->filled('status_kerja') && $request->status_kerja !== 'all') {
            $query->where('status_kerja', $request->status_kerja);
        }

        $teachers = $query->latest('id')->paginate(15);

        // data untuk modal (dropdown)
        $classes = ClassModel::orderBy('name')->get();
        $allowedSubjects = Subject::query()
            ->where('name', 'like', '%Agama%')
            ->orWhere('name', 'like', '%PJOK%')
            ->orderBy('name')
            ->get();

        // Ambil users yang belum punya teacher untuk dropdown
        $availableUsers = User::whereDoesntHave('teacher')->orderBy('name')->get(); // untuk Tambah
        $allUsers = User::orderBy('name')->get(); // untuk Edit
        return view('teachers.index', compact('teachers','classes','allowedSubjects','availableUsers','allUsers'));
    }

    public function store(Request $request)
    {
        $allowedSubjectIds = $this->allowedSubjectIds();

        $data = $request->validate([
            'user_id' => ['nullable','exists:users,id', Rule::unique('teachers','user_id')],
            'nip'           => ['nullable', 'max:50', 'unique:teachers,nip'],
            'nuptk'         => ['nullable', 'max:50', 'unique:teachers,nuptk'],
            'nama_lengkap'  => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tempat_lahir'  => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'contact_email' => ['nullable', 'email'],

            // wali kelas (opsional) → jaga 1 wali/kelas
            'class_id'      => [
                'nullable',
                'exists:classes,id',
                Rule::unique('teachers', 'class_id')->where(function ($q) {
                    return $q->whereNotNull('class_id');
                }),
            ],

            // guru mapel (opsional) → hanya Agama/PJOK
            'subject_id'    => [
                'nullable',
                Rule::in($allowedSubjectIds),
            ],
        ]);

        // Logic untuk status kerja berdasarkan NIP/NUPTK
        if (!empty($data['nip'])) {
            $data['status_kerja'] = 'PPPK';
        } elseif (!empty($data['nuptk'])) {
            $data['status_kerja'] = 'PPPK';
        } else {
            $data['status_kerja'] = 'Honorer';
        }

        Teacher::create($data);

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function update(Request $request, Teacher $teacher)
    {
        $allowedSubjectIds = $this->allowedSubjectIds();

        $data = $request->validate([
            'user_id' => ['nullable','exists:users,id', Rule::unique('teachers','user_id')->ignore($teacher->id)],
            'nip'           => ['nullable', 'max:50', Rule::unique('teachers', 'nip')->ignore($teacher->id)],
            'nuptk'         => ['nullable', 'max:50', Rule::unique('teachers', 'nuptk')->ignore($teacher->id)],
            'nama_lengkap'  => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tempat_lahir'  => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'contact_email' => ['nullable', 'email'],

            // wali kelas: unik per kelas (kecuali milik dirinya sendiri)
            'class_id'      => [
                'nullable',
                'exists:classes,id',
                Rule::unique('teachers', 'class_id')
                    ->where(fn($q) => $q->whereNotNull('class_id'))
                    ->ignore($teacher->id),
            ],

            // guru mapel: hanya Agama/PJOK
            'subject_id'    => [
                'nullable',
                Rule::in($allowedSubjectIds),
            ],
        ]);

        // Logic untuk status kerja berdasarkan NIP/NUPTK
        if (!empty($data['nip'])) {
            $data['status_kerja'] = 'PPPK';
        } elseif (!empty($data['nuptk'])) {
            $data['status_kerja'] = 'PPPK';
        } else {
            $data['status_kerja'] = 'Honorer';
        }

        $teacher->update($data);

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil dihapus.');
    }

/**
 * Auto sync dari users -> teachers (idempotent, tanpa bergantung nama role).
 * Logika:
 * - Ambil users yang BELUM punya teacher.
 * - Jika users.subject_id adalah Agama/PJOK => set subject_id (guru mapel).
 * - Jika users.class_id terisi dan belum dipakai wali => set class_id (wali).
 * - class_id boleh null (untuk guru mapel tanpa wali).
 */
private function autoSyncFromUsers(): void
{
    // Cari ID mapel yang diperbolehkan: Agama/PJOK (nama longgar)
    $allowedSubjects = Subject::query()
        ->where(function ($q) {
            $q->where('name', 'like', '%Agama%')
              ->orWhere('name', 'like', '%PJOK%');
        })
        ->get();
    $allowedIds = $allowedSubjects->pluck('id')->all();

    // Ambil users yang belum punya relasi teacher
    $users = \App\Models\User::query()
        ->whereDoesntHave('teacher')
        // hanya yang berpotensi relevan: punya subject_id ATAU class_id
        ->where(function ($q) {
            $q->whereNotNull('subject_id')
              ->orWhereNotNull('class_id');
        })
        ->get();

    foreach ($users as $u) {
        $classId   = $u->class_id ?? null;
        $subjectId = $u->subject_id ?? null;

        // Validasi subject hanya Agama/PJOK
        if ($subjectId && !in_array($subjectId, $allowedIds)) {
            $subjectId = null; // bukan guru mapel khusus SD
        }

        // Jaga aturan 1 wali per kelas (kalau kelas sudah punya wali, kosongkan)
        if ($classId && Teacher::where('class_id', $classId)->exists()) {
            $classId = null;
        }

        // Tentukan status kerja default
        $statusKerja = 'Honorer'; // default

        // Buat entri teacher, class_id boleh null (guru mapel tanpa wali OK)
        Teacher::create([
            'user_id'       => $u->id,
            'nama_lengkap'  => $u->name,
            'contact_email' => $u->email,
            'status_kerja'  => $statusKerja,
            'class_id'      => $classId,
            'subject_id'    => $subjectId,
            // kolom lain dibiarkan null (nip/nuptk/dsb)
        ]);
    }
}

}