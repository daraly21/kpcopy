<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Buat Role
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $waliKelasRole = Role::firstOrCreate(['name' => 'Wali Kelas']);

        // Buat Permission
        $permissions = [
            'kelola pengguna', 
            'kelola kelas',
            'kelola nilai',
            'kirim notifikasi orang tua',
            'export nilai',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin memiliki SEMUA izin
        $adminRole->syncPermissions(Permission::all());

        // Wali Kelas hanya memiliki izin tertentu
        $waliKelasRole->givePermissionTo([
            'kelola nilai',
            'kirim notifikasi orang tua',
            'export nilai',
        ]);
    }
}
