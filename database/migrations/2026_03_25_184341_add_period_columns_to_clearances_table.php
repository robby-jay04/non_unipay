<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('clearances', function (Blueprint $table) {
        $table->unsignedBigInteger('semester_id')->nullable();
        $table->unsignedBigInteger('school_year_id')->nullable();
        $table->unsignedBigInteger('exam_period_id')->nullable();
        
        // optional: foreign keys
        $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
        $table->foreign('school_year_id')->references('id')->on('school_years')->onDelete('cascade');
        $table->foreign('exam_period_id')->references('id')->on('exam_periods')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clearances', function (Blueprint $table) {
            //
        });
    }
};
