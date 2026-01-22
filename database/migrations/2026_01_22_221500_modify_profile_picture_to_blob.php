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
            // Add new columns for storing image data in database
            $table->longText('profile_picture')->nullable()->after('subject_id'); // Base64 encoded image
            $table->string('profile_picture_mime', 50)->nullable()->after('profile_picture'); // MIME type (e.g., image/jpeg)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_picture', 'profile_picture_mime']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_picture')->nullable()->after('subject_id');
        });
    }
};
