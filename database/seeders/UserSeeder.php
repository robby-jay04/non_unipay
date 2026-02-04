<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@nonunipay.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create Sample Student User
        $student = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'student@nonunipay.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        // Create student profile
        $student->student()->create([
            'student_no' => '2024-00001',
            'course' => 'Bachelor of Science in Information Technology',
            'year_level' => 3,
            'contact' => '09123456789',
        ]);
    }
}