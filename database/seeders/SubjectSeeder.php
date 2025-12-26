<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            'Matematika',
            'Bahasa Indonesia',
            'IPA',
            'IPS',
            'Bahasa Daerah',
            'Pendidikan Pancasila',
            'Bahasa Inggris',
            'Keterampilan',
            'PJOK',
            'Agama'
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                ['name' => $subject],
                ['name' => $subject]
            );
        }
    }
}
