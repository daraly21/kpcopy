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

$availableUsers = User::whereDoesntHave('teacher')
    ->when($request->isMethod('get') && $request->route()->getName() === 'admin.teachers.index', function ($q) {
        // Saat buka modal edit, izinkan user saat ini tetap muncul (jika sedang edit guru yang sudah pakai user itu)
        $currentTeacherId = request()->route('teacher'); // jika edit, ambil ID dari route
        if ($currentTeacherId) {
            $currentUserId = Teacher::find($currentTeacherId)?->user_id;
            if ($currentUserId) {
                $q->orWhere('id', $currentUserId);
            }
        }
    })
    ->orderBy('name')
    ->get();
        return view('teachers.index', compact('teachers','classes','allowedSubjects','availableUsers'));
    }

    public function store(Request $request)
    {
        $allowedSubjectIds = $this->allowedSubjectIds();

        // Custom error messages
        $messages = [
            'user_id.unique' => ' User ini sudah terhubung dengan guru lain. Satu user hanya boleh terhubung ke satu guru.',
            'nama_lengkap.regex' => 'Nama lengkap tidak boleh mengandung angka atau karakter khusus (kecuali spasi, titik, tanda petik, dan strip).',
            'nama_lengkap.required_without' => ' Nama lengkap wajib diisi jika tidak menghubungkan dengan user.',
            'nip.unique' => ' NIP sudah digunakan oleh guru lain. NIP harus unik dan tidak boleh duplikat.',
            'nuptk.unique' => ' NUPTK sudah digunakan oleh guru lain. NUPTK harus unik dan tidak boleh duplikat.',
            'contact_email.email' => ' Format email tidak valid. Contoh format yang benar: nama@example.com',
            'contact_email.unique' => ' Email sudah digunakan oleh guru lain. Email harus unik untuk setiap guru.',
            'class_id.unique' => ' Kelas ini sudah memiliki wali kelas. Satu kelas hanya boleh memiliki satu wali kelas.',
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
            // Nama lengkap wajib jika tidak ada user_id
            'nama_lengkap'  => [
                'required_without:user_id',
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\'\-\.]+$/u'
            ],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tempat_lahir'  => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today', 'after:1900-01-01', 'before:' . now()->subYears(20)->format('Y-m-d')],
            'contact_email' => [
                'nullable',
                'email',
                Rule::unique('teachers','contact_email')
            ],

            // wali kelas (opsional) â†’ jaga 1 wali/kelas
            'class_id' => [
                'nullable',
                'exists:classes,id',
                Rule::unique('teachers', 'class_id')
                    ->where(function ($q) {
                        return $q->whereNotNull('class_id');
                    }),
            ],

            // guru mapel (opsional) â†’ hanya Agama/PJOK
            'subject_id' => [
                'nullable',
                Rule::in($allowedSubjectIds),
            ],
        ], $messages);

       // AUTO-SYNC: Jika ada user_id, ambil nama, email, dan class dari User
    if (!empty($data['user_id'])) {
        $user = User::find($data['user_id']);
        if ($user) {
            $data['nama_lengkap'] = $user->name;
            $data['contact_email'] = $user->email;
            $data['class_id'] = $user->class_id; // ← TAMBAHKAN INI
        }
    }

        // Logic untuk status kerja berdasarkan NIP/NUPTK
// Logika status kerja
// Logic untuk status kerja berdasarkan NIP/NUPTK
if (!empty($data['nip'])) {
    // Jika ada NIP → PPPK
    $data['status_kerja'] = 'PPPK';
} else {
    // Kalau tidak ada NIP (meskipun ada NUPTK) → Honorer
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
            'nama_lengkap.required_without' => ' Nama lengkap wajib diisi jika tidak menghubungkan dengan user.',
            'nip.unique' => ' NIP sudah digunakan oleh guru lain. NIP harus unik dan tidak boleh duplikat.',
            'nuptk.unique' => ' NUPTK sudah digunakan oleh guru lain. NUPTK harus unik dan tidak boleh duplikat.',
            'contact_email.email' => ' Format email tidak valid. Contoh format yang benar: nama@example.com',
            'contact_email.unique' => ' Email sudah digunakan oleh guru lain. Email harus unik untuk setiap guru.',
            'class_id.unique' => ' Kelas ini sudah memiliki wali kelas lain. Satu kelas hanya boleh memiliki satu wali kelas.',
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
            // Nama lengkap wajib jika tidak ada user_id
            'nama_lengkap'  => [
                'required_without:user_id',
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\'\-\.]+$/u'
            ],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tempat_lahir'  => ['nullable', 'string', 'max:100'],
       'tanggal_lahir' => ['nullable', 'date', 'before:today', 'after:1900-01-01', 'before:' . now()->subYears(20)->format('Y-m-d')],
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

       
        // AUTO-SYNC: Jika ada user_id, ambil nama, email, dan class dari User
if (!empty($data['user_id'])) {
    $user = User::find($data['user_id']);
    if ($user) {
        $data['nama_lengkap'] = $user->name;
        $data['contact_email'] = $user->email;
        $data['class_id'] = $user->class_id; // ← TAMBAHKAN INI
    }
}

        // Logic untuk status kerja berdasarkan NIP/NUPTK
      // Logika status kerja
if (!empty($data['nip'])) {
    // Jika ada NIP → PPPK
    $data['status_kerja'] = 'PPPK';
} else {
    // Kalau tidak ada NIP (meskipun ada NUPTK) → Honorer
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
}