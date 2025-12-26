<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Dapodik Sistem',
            'email' => 'dapodik@example.com',
            'password' => '$2y$12$GKDnnb39ZKyHJ/ExgFCfTuLy46py46TxvIKp9nc/9csVIIMqZGnRO',
            'nip' => null,
            'nuptk' => 1234567890,
            'profile_picture' => 'profile_pictures/1766230608_69468a503a084.jpeg',
            'role_id' => 1,
            'class_id' => null,
            'subject_id' => null,
        ]);
        $user->assignRole('Admin');

        $user = User::create([
            'name' => 'Kim Yerim',
            'email' => 'walikelas@example.com',
            'password' => '$2y$12$YI0H/cKtORF1UJDI5WSElO27H/D3Vn/DDX0SEaphH972I6uriW5Iu',
            'nip' => null,
            'nuptk' => '0987654321',
            'profile_picture' => 'profile_pictures/1766233464_69469578b7126.jpg',
            'role_id' => 2,
            'class_id' => 3,
            'subject_id' => null,
        ]);
        $user->assignRole('Wali Kelas');

        $user = User::create([
            'name' => 'Guru Agama',
            'email' => 'guruagama@example.com',
            'password' => '$2y$12$HrbwEMhBPrbXQnJk3yhJlOUhZ5DlI6u1wyXhSJx0dOnU9.9UU15T.',
            'nip' => null,
            'nuptk' => 1122334455,
            'profile_picture' => 'profile_pictures/1766237300_6946a474ae57f.png',
            'role_id' => 3,
            'class_id' => null,
            'subject_id' => 7,
        ]);
        $user->assignRole('Guru Mata Pelajaran');

        $user = User::create([
            'name' => 'Guru PJOK',
            'email' => 'gurupjok@example.com',
            'password' => '$2y$12$AwG/6YiRrQ4b886G/4/Zxeib58ctBFVP4OLkg4Vr/9r0AhWKvH0ri',
            'nip' => null,
            'nuptk' => 2233445566,
            'profile_picture' => 'https://ucarecdn.com/example-guru.jpg',
            'role_id' => 3,
            'class_id' => null,
            'subject_id' => 9,
        ]);
        $user->assignRole('Guru Mata Pelajaran');

        $user = User::create([
            'name' => 'Muhammad Fadil',
            'email' => 'walikelas2@example.com',
            'password' => '$2y$12$m9AWM6NIHOAI4AiFu9xHgeOk58wzgDeziDjCnRnR12S6sw6tdjDdO',
            'nip' => null,
            'nuptk' => null,
            'profile_picture' => null,
            'role_id' => 2,
            'class_id' => 2,
            'subject_id' => null,
        ]);
        $user->assignRole('Wali Kelas');

    }
}
