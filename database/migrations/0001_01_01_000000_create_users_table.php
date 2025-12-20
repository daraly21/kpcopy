<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nip')->nullable(); // NIP untuk Guru
            $table->string('nuptk')->nullable(); // Nuptk untuk Guru
            $table->string('email')->unique();
            $table->string('password');
            $table->string('profile_picture')->nullable(); // Foto profil (local storage)
            $table->unsignedBigInteger('role_id'); // Role (Admin atau Wali Kelas)
            $table->unsignedBigInteger('class_id')->nullable(); // Hanya untuk Wali Kelas
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
    
            // // Foreign Key
            // $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            // $table->foreign('class_id')->references('id')->on('classes')->onDelete('set null');
        });
    

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
