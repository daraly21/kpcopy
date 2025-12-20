<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {

            // Tambahkan kolom academic_year_id
            $table->foreignId('academic_year_id')
                ->nullable()
                ->after('subject_id')
                ->constrained('academic_years')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropConstrainedForeignId('academic_year_id');
        });
    }
};
