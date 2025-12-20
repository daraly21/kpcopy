<?php

namespace Database\Seeders;

use App\Models\GradeTask;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            AcademicYearSeeder::class, // Must be first for relationships
            RolePermissionSeeder::class,
            ClassSeeder::class,
            SubjectSeeder::class,
            UserSeeder::class,
            StudentSeeder::class,
            GradeSeeder::class,
            // GradeTaskSeeder::class, // Handled in GradeSeeder
        ]);

    }
}
