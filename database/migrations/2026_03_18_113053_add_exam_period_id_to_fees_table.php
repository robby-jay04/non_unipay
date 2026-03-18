<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->string('exam_period')->nullable()->after('semester');
            $table->foreignId('exam_period_id')
                  ->nullable()
                  ->constrained('exam_periods')
                  ->nullOnDelete()
                  ->after('semester_id');
        });
    }

    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropForeign(['exam_period_id']);
            $table->dropColumn(['exam_period_id', 'exam_period']);
        });
    }
};