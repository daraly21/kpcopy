<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('nis')->after('name');
            $table->enum('gender', ['L', 'P'])->after('nis');
            $table->string('parent_name')->nullable()->after('parent_phone');
            $table->string('birth_place')->nullable()->after('parent_name');
            $table->date('birth_date')->nullable()->after('birth_place');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['nis', 'gender', 'parent_name', 'birth_place', 'birth_date']);
        });
    }
};
