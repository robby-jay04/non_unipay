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
    Schema::table('payments', function (Blueprint $table) {
        $table->unsignedBigInteger('semester_id')->nullable()->after('student_id');
        $table->unsignedBigInteger('school_year_id')->nullable()->after('semester_id');

        $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('set null');
        $table->foreign('school_year_id')->references('id')->on('school_years')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->dropForeign(['semester_id']);
        $table->dropForeign(['school_year_id']);
        $table->dropColumn(['semester_id', 'school_year_id']);
    });
}
};
