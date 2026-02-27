<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolYear;

class SchoolYearSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing current flag (safety)
        SchoolYear::query()->update(['is_current' => false]);

        SchoolYear::create([
            'name' => '2025-2026',
            'is_current' => true,
        ]);
    }
}
