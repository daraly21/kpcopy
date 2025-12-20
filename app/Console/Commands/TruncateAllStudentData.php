<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TruncateAllStudentData extends Command
{
    protected $signature = 'db:reset-students';
    protected $description = 'Truncate tabel students, grades, dan grade_tasks dengan aman';

    public function handle()
    {
        if (! $this->confirm('YAKIN mau hapus SEMUA data students, grades, dan grade_tasks? Tidak bisa dikembalikan!')) {
            return;
        }

        $this->info('Memulai truncate...');

        // Matikan foreign key sementara (hanya MySQL)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Urutan penting: anak dulu, parent terakhir
        DB::table('grade_tasks')->truncate();
        DB::table('grades')->truncate();
        DB::table('student_classes')->truncate();  // TAMBAHAN: hapus relasi siswa-kelas
        DB::table('students')->truncate();

        // Nyalakan lagi
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('Berhasil! Semua data students, grades, grade_tasks, dan student_classes sudah dibersihkan + auto increment di-reset.');
    }
}

//php artisan db:reset-students