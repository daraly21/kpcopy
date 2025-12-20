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
        // Ambil role dari database
        $adminRole = Role::where('name', 'Admin')->first();
        $waliKelasRole = Role::where('name', 'Wali Kelas')->first();
        $guruMapelRole = Role::where('name', 'Guru Mata Pelajaran')->first();

        // Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Sistem',
                'nuptk' => '1234567890',
                'password' => Hash::make('password'),
                'profile_picture' => null,
                'role_id' => 1,
                'class_id' => null,
                'subject_id' => null,
            ]
        );
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }

        // Wali Kelas
        $waliKelas = User::updateOrCreate(
            ['email' => 'walikelas@example.com'],
            [
                'name' => 'Kim Yerim',
                'nuptk' => '0987654321',
                'password' => Hash::make('password'),
                'profile_picture' => null,
                'role_id' => 2,
                'class_id' => 3,
                'subject_id' => null,
            ]
        );
        if ($waliKelasRole) {
            $waliKelas->assignRole($waliKelasRole);
        }

        // Guru Mata Pelajaran (Agama)
        $guruAgama = User::updateOrCreate(
            ['email' => 'guruagama@example.com'],
            [
                'name' => 'Guru Agama',
                'nuptk' => '1122334455',
                'password' => Hash::make('password'),
                'profile_picture' => null,
                'role_id' => 3,
                'class_id' => null,
                'subject_id' => 10,
            ]
        );
        if ($guruMapelRole) {
            $guruAgama->assignRole($guruMapelRole);
        }

        // Guru Mata Pelajaran (Olahraga)
        $guruPjok = User::updateOrCreate(
            ['email' => 'gurupjok@example.com'],
            [
                'name' => 'Guru PJOK',
                'nuptk' => '2233445566',
                'password' => Hash::make('password'),
                'profile_picture' => null,
                'role_id' => 3,
                'class_id' => null,
                'subject_id' => 9,
            ]
        );
        if ($guruMapelRole) {
            $guruPjok->assignRole($guruMapelRole);
        }
    }
}