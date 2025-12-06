<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Matikan sementara foreign key check
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Hapus foreign key lama di grades
        Schema::table('grades', function ($table) {
            $table->dropForeign(['student_id']);
            $table->dropForeign(['subject_id']);
        });

        // 2. Hapus foreign key lama di grade_tasks
        Schema::table('grade_tasks', function ($table) {
            $table->dropForeign(['student_id']);
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['grades_id']);
        });

        // 3. Tambah ulang dengan cascade
        Schema::table('grades', function ($table) {
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });

        Schema::table('grade_tasks', function ($table) {
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('grades_id')->references('id')->on('grades')->onDelete('cascade');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down()
    {
        // Optional: balikin ke restrict kalau rollback
    }
};