<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        // Create Active Year
        AcademicYear::firstOrCreate(
            ['name' => '2024/2025'],
            [
                'is_active' => true
            ]
        );

        // Create Future Year (Inactive)
        AcademicYear::firstOrCreate(
            ['name' => '2025/2026'],
            [
                'is_active' => false
            ]
        );
    }
}
