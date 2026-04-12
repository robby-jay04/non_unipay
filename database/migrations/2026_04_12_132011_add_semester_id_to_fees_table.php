<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSemesterIdToFeesTable extends Migration
{
    public function up(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            if (!Schema::hasColumn('fees', 'semester_id')) {
                $table->unsignedBigInteger('semester_id')->nullable();
            }
            if (!Schema::hasColumn('fees', 'exam_period_id')) {
                $table->unsignedBigInteger('exam_period_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropColumn(['semester_id', 'exam_period_id']);
        });
    }
}