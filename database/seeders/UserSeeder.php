<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $waliKelasRole = Role::where('name', 'Wali Kelas')->first();

        // Admin
        $admin = User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'profile_picture' => 'https://ucarecdn.com/example-admin.jpg',
            'role_id' => $adminRole->id,
            'class_id' => null, // Admin tidak punya kelas
        ]);
        $admin->assignRole($adminRole);

        // Wali Kelas
        // $waliKelas = User::create([
        //     'name' => 'Kim Yerim',
        //     'email' => 'earlysme@gmail.com',
        //     'password' => Hash::make('password'),
        //     'profile_picture' => 'https://ucarecdn.com/example-walikelas.jpg',
        //     'role_id' => $waliKelasRole->id,
        //     'class_id' => 3, // ID Kelas 3
        // ]);
        // $waliKelas->assignRole($waliKelasRole);
    }
}
