<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $students = [
            [
                'name' => 'Maria Santos',
                'email' => 'maria.santos@student.com',
                'student_no' => '2024-00002',
                'course' => 'Bachelor of Science in Computer Science',
                'year_level' => 2,
                'contact' => '09987654321',
            ],
            [
                'name' => 'Pedro Reyes',
                'email' => 'pedro.reyes@student.com',
                'student_no' => '2024-00003',
                'course' => 'Bachelor of Science in Business Administration',
                'year_level' => 4,
                'contact' => '09876543210',
            ],
        ];

        foreach ($students as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password123'),
                'role' => 'student',
            ]);

            $user->student()->create([
                'student_no' => $data['student_no'],
                'course' => $data['course'],
                'year_level' => $data['year_level'],
                'contact' => $data['contact'],
            ]);
        }
    }
}
