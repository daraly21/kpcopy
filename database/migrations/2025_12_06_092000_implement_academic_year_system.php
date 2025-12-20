<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration implements the Academic Year system:
     * 1. Creates academic_years table
     * 2. Creates student_classes pivot table (many-to-many with academic year)
     * 3. Adds academic_year_id to grades table
     * 4. Removes class_id from students table (moved to student_classes)
     */
    public function up(): void
    {
        // Step 1: Create academic_years table
        if (!Schema::hasTable('academic_years')) {
            Schema::create('academic_years', function (Blueprint $table) {
                $table->id();
                $table->string('name', 20); // Example: 2024/2025
                $table->boolean('is_active')->default(false);
                $table->timestamps();
            });
        }

        // Step 2: Create student_classes pivot table
        if (!Schema::hasTable('student_classes')) {
            Schema::create('student_classes', function (Blueprint $table) {
                $table->id();

                $table->foreignId('student_id')
                    ->constrained('students')
                    ->onDelete('cascade');

                $table->foreignId('class_id')
                    ->constrained('classes')
                    ->onDelete('cascade');

                $table->foreignId('academic_year_id')
                    ->constrained('academic_years')
                    ->onDelete('cascade');

                $table->timestamps();

                // Prevent student from being in same class twice in same academic year
                $table->unique(['student_id', 'academic_year_id']);
            });
        }

        // Step 3: Add academic_year_id to grades table
        if (Schema::hasTable('grades') && !Schema::hasColumn('grades', 'academic_year_id')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->foreignId('academic_year_id')
                    ->nullable()
                    ->after('subject_id')
                    ->constrained('academic_years')
                    ->onDelete('cascade');
            });
        }

        // Step 4: Remove class_id from students table
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'class_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropConstrainedForeignId('class_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse in opposite order
        
        // Step 4 reverse: Add back class_id to students
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('class_id')
                ->nullable()
                ->after('nis')
                ->constrained('classes')
                ->onDelete('set null');
        });

        // Step 3 reverse: Remove academic_year_id from grades
        Schema::table('grades', function (Blueprint $table) {
            $table->dropConstrainedForeignId('academic_year_id');
        });

        // Step 2 reverse: Drop student_classes table
        Schema::dropIfExists('student_classes');

        // Step 1 reverse: Drop academic_years table
        Schema::dropIfExists('academic_years');
    }
};
