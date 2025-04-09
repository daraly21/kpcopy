<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto_increment
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->enum('semester', ['Odd', 'Even'])->default('Odd');
            
            // Nilai formatif
            $table->decimal('average_written', 5, 2)->nullable();
            $table->decimal('average_observation', 5, 2)->nullable();
            $table->decimal('average_homework', 5, 2)->nullable();

            // Nilai sumatif
            $table->decimal('midterm_score', 5, 2)->nullable(); // UTS
            $table->decimal('final_exam_score', 5, 2)->nullable(); // UAS

            // Nilai akhir
            $table->decimal('final_score', 8, 2)->default(0.00);
            $table->string('grade_letter')->nullable(); // Predikat (A, B, C, dll)

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
}

