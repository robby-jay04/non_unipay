<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateClearanceStatusEnumInStudentsTable extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE students MODIFY COLUMN clearance_status ENUM('cleared', 'pending', 'not_cleared') NOT NULL DEFAULT 'not_cleared'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE students MODIFY COLUMN clearance_status ENUM('cleared', 'pending') NOT NULL DEFAULT 'pending'");
    }
}