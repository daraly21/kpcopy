<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Subject;
use App\Models\ClassModel;
use App\Models\User;
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
        // $this->autoSyncFromUsers();

        $query = Teacher::with(['user', 'class', 'subject']);

        // Filter berdasarkan status kerja
        if ($request->filled('status_kerja') && $request->status_kerja !== 'all') {
            $query->where('status_kerja', $request->status_kerja);
        }

        // Urutkan berdasarkan kelas (ascending), guru tanpa kelas di akhir
        $teachers = $query
            ->leftJoin('classes', 'teachers.class_id', '=', 'classes.id')
            ->select('teachers.*')
            ->orderByRaw('CASE WHEN teachers.class_id IS NULL THEN 1 ELSE 0 END')
            ->orderBy('classes.name', 'asc')
            ->orderBy('teachers.nama_lengkap', 'asc')
            ->paginate(15);

        // data untuk modal (dropdown)
        $classes = ClassModel::orderBy('name')->get();
        $allowedSubjects = Subject::query()
            ->where('name', 'like', '%Agama%')
            ->orWhere('name', 'like', '%PJOK%')
            ->orderBy('name')
            ->get();

        // Ambil users yang belum punya teacher untuk dropdown
        $availableUsers = User::whereDoesntHave('teacher')->orderBy('name')->get();
        
        return view('teachers.index', compact('teachers','classes','allowedSubjects','availableUsers'));
    }

    public function store(Request $request)
    {
        $allowedSubjectIds = $this->allowedSubjectIds();

        // Custom error messages
        $messages = [
            'user_id.unique' => ' User ini sudah terhubung dengan guru lain. Satu user hanya boleh terhubung ke satu guru.',
            'nama_lengkap.regex' => 'Nama lengkap tidak boleh mengandung angka atau karakter khusus (kecuali spasi, titik, tanda petik, dan strip).',
            'nip.unique' => ' NIP sudah digunakan oleh guru lain. NIP harus unik dan tidak boleh duplikat.',
            'nuptk.unique' => ' NUPTK sudah digunakan oleh guru lain. NUPTK harus unik dan tidak boleh duplikat.',
            'contact_email.email' => ' Format email tidak valid. Contoh format yang benar: nama@example.com',
            'contact_email.unique' => ' Email sudah digunakan oleh guru lain. Email harus unik untuk setiap guru.',
            'class_id.unique' => ' Kelas ini sudah memiliki wali kelas. Satu kelas hanya boleh memiliki satu wali kelas.',
            'nama_lengkap.required' => ' Nama lengkap guru wajib diisi.',
            'jenis_kelamin.required' => ' Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => ' Jenis kelamin harus L (Laki-laki) atau P (Perempuan).',
            'tanggal_lahir.before' => ' Tanggal lahir harus sebelum hari ini.',
        ];

        $data = $request->validate([
            'user_id' => [
                'nullable',
                'exists:users,id',
                Rule::unique('teachers','user_id')
            ],
            'nip' => [
                'nullable',
                'max:50',
                Rule::unique('teachers','nip')
            ],
            'nuptk' => [
                'nullable',
                'max:50',
                Rule::unique('teachers','nuptk')
            ],
            'nama_lengkap'  => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'\-\.]+$/u'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tempat_lahir'  => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'contact_email' => [
                'nullable',
                'email',
                Rule::unique('teachers','contact_email')
            ],

            // wali kelas (opsional) → jaga 1 wali/kelas
            'class_id' => [
                'nullable',
                'exists:classes,id',
                Rule::unique('teachers', 'class_id')
                    ->where(function ($q) {
                        return $q->whereNotNull('class_id');
                    }),
            ],

            // guru mapel (opsional) → hanya Agama/PJOK
            'subject_id' => [
                'nullable',
                Rule::in($allowedSubjectIds),
            ],
        ], $messages);

        // Logic untuk status kerja berdasarkan NIP/NUPTK
        if (!empty($data['nip'])) {
            $data['status_kerja'] = 'PPPK';
        } elseif (!empty($data['nuptk'])) {
            $data['status_kerja'] = 'PPPK';
        } else {
            $data['status_kerja'] = 'Honorer';
        }

        Teacher::create($data);

        return redirect()->route('admin.teachers.index')
            ->with('success', ' Guru berhasil ditambahkan!');
    }

    public function update(Request $request, Teacher $teacher)
    {
        $allowedSubjectIds = $this->allowedSubjectIds();

        // Custom error messages
        $messages = [
            'user_id.unique' => ' User ini sudah terhubung dengan guru lain. Satu user hanya boleh terhubung ke satu guru.',
            'nama_lengkap.regex' => 'Nama lengkap tidak boleh mengandung angka atau karakter khusus (kecuali spasi, titik, tanda petik, dan strip).',
            'nip.unique' => ' NIP sudah digunakan oleh guru lain. NIP harus unik dan tidak boleh duplikat.',
            'nuptk.unique' => ' NUPTK sudah digunakan oleh guru lain. NUPTK harus unik dan tidak boleh duplikat.',
            'contact_email.email' => ' Format email tidak valid. Contoh format yang benar: nama@example.com',
            'contact_email.unique' => ' Email sudah digunakan oleh guru lain. Email harus unik untuk setiap guru.',
            'class_id.unique' => ' Kelas ini sudah memiliki wali kelas lain. Satu kelas hanya boleh memiliki satu wali kelas.',
            'nama_lengkap.required' => ' Nama lengkap guru wajib diisi.',
            'jenis_kelamin.required' => ' Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => ' Jenis kelamin harus L (Laki-laki) atau P (Perempuan).',
            'tanggal_lahir.before' => ' Tanggal lahir harus sebelum hari ini.',
        ];

        $data = $request->validate([
            'user_id' => [
                'nullable',
                'exists:users,id',
                Rule::unique('teachers','user_id')
                    ->ignore($teacher->id)
            ],
            'nip' => [
                'nullable',
                'max:50',
                Rule::unique('teachers', 'nip')
                    ->ignore($teacher->id)
            ],
            'nuptk' => [
                'nullable',
                'max:50',
                Rule::unique('teachers', 'nuptk')
                    ->ignore($teacher->id)
            ],
            'nama_lengkap'  => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'\-\.]+$/u'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tempat_lahir'  => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'contact_email' => [
                'nullable',
                'email',
                Rule::unique('teachers','contact_email')
                    ->ignore($teacher->id)
            ],

            // wali kelas: unik per kelas (kecuali milik dirinya sendiri)
            'class_id' => [
                'nullable',
                'exists:classes,id',
                Rule::unique('teachers', 'class_id')
                    ->where(fn($q) => $q->whereNotNull('class_id'))
                    ->ignore($teacher->id),
            ],

            // guru mapel: hanya Agama/PJOK
            'subject_id' => [
                'nullable',
                Rule::in($allowedSubjectIds),
            ],
        ], $messages);

        // Logic untuk status kerja berdasarkan NIP/NUPTK
        if (!empty($data['nip'])) {
            $data['status_kerja'] = 'PPPK';
        } elseif (!empty($data['nuptk'])) {
            $data['status_kerja'] = 'PPPK';
        } else {
            $data['status_kerja'] = 'Honorer';
        }

        $teacher->update($data);

        return redirect()->route('admin.teachers.index')
            ->with('success', ' Data guru berhasil diperbarui!');
    }

    public function destroy(Teacher $teacher)
    {
        $namaGuru = $teacher->nama_lengkap;
        
        try {
            $teacher->delete();
            return redirect()->route('admin.teachers.index')
                ->with('success', " Guru '{$namaGuru}' berhasil dihapus.");
        } catch (\Exception $e) {
            return redirect()->route('admin.teachers.index')
                ->with('error', " Gagal menghapus guru. Error: " . $e->getMessage());
        }
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
        $users = User::query()
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