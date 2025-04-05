<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Uploadcare\Api;

class UserController extends Controller
{
    /**
     * Display a listing of the users with CRUD capabilities.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with(['roles', 'class'])->get();
        $roles = Role::all();
        $classes = ClassModel::all();
        
        return view('users.index', compact('users', 'roles', 'classes'));
    }
    
    public function create()
    {
        $roles = Role::all();
        $classes = ClassModel::all();
    
        return view('users.create', compact('roles', 'classes'));
    }
    
    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json([
            'user' => $user->load(['roles', 'class']),
        ]);
    }

// In your store method, replace the profile picture handling with:
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|exists:roles,id',
        'class_id' => 'nullable|exists:classes,id',
        'profile_picture' => 'nullable|string', // Change to string to accept ucarecdn URL/ID
    ]);

    // Handle ucarecdn profile picture
    $profilePictureUrl = null;
    if ($request->filled('profile_picture')) {
        $profilePictureUrl = $request->profile_picture; // Store the ucarecdn URL directly
    }

    // Rest of your code remains the same
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role_id' => $request->role,
        'class_id' => $request->role == 2 ? $request->class_id : null,
        'profile_picture' => $profilePictureUrl,
    ]);

    // Assign role using Spatie
    $role = Role::findById($request->role);
    $user->assignRole($role);

    return redirect()->route('admin.users.index')
        ->with('success', 'User berhasil ditambahkan.');
}

public function edit(User $user)
{
    $roles = Role::all();
    $classes = ClassModel::all();

    return view('users.edit', compact('user', 'roles', 'classes'));
}

// Similarly update the update method:
public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:8|confirmed',
        'role' => 'required|exists:roles,id',
        'class_id' => 'nullable|exists:classes,id',
        'profile_picture' => 'nullable|string', // Change to string for ucarecdn URL/ID
    ]);

    // Handle ucarecdn profile picture
    if ($request->filled('profile_picture') && $request->profile_picture != $user->profile_picture) {
        // No need to delete the old file as it's managed by ucarecdn
        $user->profile_picture = $request->profile_picture;
    }

    // Rest of your update code
    $user->name = $request->name;
    $user->email = $request->email;
    
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }
    
    $user->role_id = $request->role;
    $user->class_id = $request->role == 2 ? $request->class_id : null;
    
    $user->save();

    // Update role using Spatie
    $user->syncRoles([]);
    $role = Role::findById($request->role);
    $user->assignRole($role);

    return redirect()->route('admin.users.index')
        ->with('success', 'Data user berhasil diperbarui.');
}

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
    
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
    
}