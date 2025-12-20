<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

            // Mencegah siswa masuk kelas yang sama dua kali di tahun ajaran yang sama
            $table->unique(['student_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_classes');
    }
};
