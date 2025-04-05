<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Pastikan kolom sudah ada, atau tambahkan jika belum ada
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id'); // Tambahkan role_id jika belum ada
            }
            if (!Schema::hasColumn('users', 'class_id')) {
                $table->unsignedBigInteger('class_id')->nullable(); // class_id boleh null
            }

            // Tambahkan foreign key
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['class_id']);
        });
    }
};
