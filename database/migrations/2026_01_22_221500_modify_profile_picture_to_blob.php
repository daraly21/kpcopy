<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the old profile_picture column (was string for file path)
            $table->dropColumn('profile_picture');
        });

        Schema::table('users', function (Blueprint $table) {
            // Add new column for storing image as data URI (includes mime type)
            // Format: data:image/jpeg;base64,/9j/4AAQ...
            $table->longText('profile_picture')->nullable()->after('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_picture');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_picture')->nullable()->after('subject_id');
        });
    }
};
