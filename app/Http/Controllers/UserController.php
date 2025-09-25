<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClassModel;           // gunakan ClassModel
use App\Models\Subject;             // untuk dropdown mapel (Guru Mapel)
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
        // Ambil users beserta role, class, subject
        $users = User::with(['roles', 'class', 'subject'])->latest()->get();

        // Dropdown role, kelas, mapel
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

        // Sinkronkan role Spatie (jika digunakan)
        if ($role = Role::find($roleId)) {
            $user->syncRoles([$role->name]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Hapus user.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
