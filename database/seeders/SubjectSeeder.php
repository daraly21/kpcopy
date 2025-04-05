<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        // Data mata pelajaran SD
        $subjects = [
            'Matematika',
            'Bahasa Indonesia',
            'IPA',
            'IPS',
            'PPKN',
            'Bahasa Inggris',
            'Seni Budaya dan Prakarya',
            'Olahraga',
            'Agama'
        ];

        // Insert ke dalam tabel subjects
        foreach ($subjects as $subject) {
            DB::table('subjects')->insert([
                'name' => $subject,
            ]);
        }
    }
}
