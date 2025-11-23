<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Matikan cek foreign key dulu
        Schema::disableForeignKeyConstraints();

        // Truncate tabel anak dulu, baru parent
        DB::table('grade_tasks')->truncate();
        DB::table('students')->truncate();

        // Nyalakan lagi
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Biasanya kosong, karena truncate itu operasi yang nggak bisa di-rollback
        // Tapi kalau mau, bisa diisi seed data default lagi di sini.
    }
};
