<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClassModel;           
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * List pengguna + data pendukung untuk modal CRUD di index.
     */
public function index()
{
    $users = User::with(['roles', 'class', 'subject'])->get();

    // Urutkan sesuai keinginan
    $users = $users->sortBy(function ($user) {
        $roleName = $user->roles->first()->name ?? '';

        // Prioritas role
        $roleOrder = [
            'Admin'       => 1,
            'Wali Kelas'  => 2,
            'Guru Mapel'  => 3,
        ];

        $priority = $roleOrder[$roleName] ?? 99;

        // Untuk Wali Kelas, urutkan berdasarkan angka kelas
        if ($roleName === 'Wali Kelas' && $user->class) {
            preg_match('/\d+/', $user->class->name, $matches);
            $classNumber = $matches[0] ?? 99;
        } else {
            $classNumber = 999;
        }

        return [$priority, $classNumber, $user->name];
    });

    $roles    = Role::orderBy('id')->get();
    $classes  = ClassModel::orderBy('name')->get();
    $subjects = Subject::orderBy('name')->get();

    return view('users.index', compact('users', 'roles', 'classes', 'subjects'));
}

    /**
     * Simpan user baru.
     * - Role 2 (Wali Kelas) butuh class_id
     * - Role 3 (Guru Mapel)  butuh subject_id
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|string|email|max:255|unique:users,email',
            'password'           => 'required|string|min:8|confirmed',
            'role'               => 'required|exists:roles,id',
            'class_id'           => 'nullable|exists:classes,id',
            'subject_id'         => 'nullable|exists:subjects,id',
            'profile_picture'    => 'nullable|string', // simpan URL (mis. ucarecdn)
        ]);

        // Tentukan field tergantung role
        $roleId   = (int) $request->role;
        $classId  = $roleId === 2 ? $request->class_id   : null; // Wali Kelas
        $subjectId= $roleId === 3 ? $request->subject_id : null; // Guru Mapel

        $user = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'role_id'         => $roleId,         // jika kamu memakai kolom role_id di tabel users
            'class_id'        => $classId,
            'subject_id'      => $subjectId,
            'profile_picture' => $request->profile_picture,
        ]);

        // Sinkronkan role Spatie (jika kamu pakai spatie/permission)
        if ($role = Role::find($roleId)) {
            $user->syncRoles([$role->name]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Update user.
     * AUTO-SYNC: Jika user ini punya relasi teacher, update nama dan email di teacher
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password'           => 'nullable|string|min:8|confirmed',
            'role'               => 'required|exists:roles,id',
            'class_id'           => 'nullable|exists:classes,id',
            'subject_id'         => 'nullable|exists:subjects,id',
            'profile_picture'    => 'nullable|string',
        ]);

        $roleId    = (int) $request->role;
        $classId   = $roleId === 2 ? $request->class_id   : null; // Wali Kelas
        $subjectId = $roleId === 3 ? $request->subject_id : null; // Guru Mapel

        $user->name            = $request->name;
        $user->email           = $request->email;
        $user->role_id         = $roleId;
        $user->class_id        = $classId;
        $user->subject_id      = $subjectId;
        if ($request->filled('profile_picture')) {
            $user->profile_picture = $request->profile_picture;
        }
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // 🔥 AUTO-SYNC: Jika user ini punya relasi teacher, update nama dan email
        if ($user->teacher) {
            $user->teacher->update([
                'nama_lengkap'  => $user->name,
                'contact_email' => $user->email,
            ]);
        }

        // Sinkronkan role Spatie (jika digunakan)
        if ($role = Role::find($roleId)) {
            $user->syncRoles([$role->name]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Hapus user.
     * 🔥 AUTO-SYNC: Jika user ini punya teacher, putuskan relasi (set user_id = null)
     */
    public function destroy(User $user)
    {
        // Putuskan relasi dengan teacher jika ada
        if ($user->teacher) {
            $user->teacher->update(['user_id' => null]);
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}