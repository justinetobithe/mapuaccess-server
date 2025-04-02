<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $semesters = [
            [
                'school_year' => '2024-2025',
                'semester' => '1st',
                'start_date' => '2024-08-01',
                'end_date' => '2024-12-31',
                'is_active' => false,
            ],
            [
                'school_year' => '2024-2025',
                'semester' => '2nd',
                'start_date' => '2025-01-01',
                'end_date' => '2025-05-31',
                'is_active' => true,
            ],
            [
                'school_year' => '2025-2026',
                'semester' => '1st',
                'start_date' => '2025-08-01',
                'end_date' => '2025-12-31',
                'is_active' => false,
            ],
            [
                'school_year' => '2025-2026',
                'semester' => '2nd',
                'start_date' => '2026-01-01',
                'end_date' => '2026-05-31',
                'is_active' => false,
            ],
        ];

        DB::table('semesters')->insert($semesters);
    }
}
