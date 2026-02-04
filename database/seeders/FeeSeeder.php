<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fee;

class FeeSeeder extends Seeder
{
    public function run()
    {
        $fees = [
            [
                'name' => 'Tuition Fee',
                'amount' => 25000.00,
                'type' => 'tuition',
                'semester' => '1st Semester',
                'school_year' => '2024-2025',
            ],
            [
                'name' => 'Laboratory Fee',
                'amount' => 3000.00,
                'type' => 'miscellaneous',
                'semester' => '1st Semester',
                'school_year' => '2024-2025',
            ],
            [
                'name' => 'Library Fee',
                'amount' => 500.00,
                'type' => 'miscellaneous',
                'semester' => '1st Semester',
                'school_year' => '2024-2025',
            ],
            [
                'name' => 'Athletic Fee',
                'amount' => 300.00,
                'type' => 'miscellaneous',
                'semester' => '1st Semester',
                'school_year' => '2024-2025',
            ],
            [
                'name' => 'Exam Fee',
                'amount' => 200.00,
                'type' => 'exam',
                'semester' => '1st Semester',
                'school_year' => '2024-2025',
            ],
        ];

        foreach ($fees as $fee) {
            Fee::create($fee);
        }
    }
}