<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto_increment
            $table->string('name');
            $table->string('nis');
            $table->enum('gender', ['L', 'P']);
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->string('parent_phone');
            $table->string('parent_name')->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }

};
