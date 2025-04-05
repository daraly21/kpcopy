<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('task_name');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            
            // Index untuk pencarian lebih cepat
            $table->index(['student_id', 'subject_id', 'task_name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};