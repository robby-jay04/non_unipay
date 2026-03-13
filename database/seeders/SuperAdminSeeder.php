<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@non-unipay.com'],
            [
                'name'              => 'Super Admin',
                'role'              => 'superadmin',
                'password'          => Hash::make('SuperAdmin@123'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Super Admin created: superadmin@non-unipay.com / SuperAdmin@123');
    }
}