<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSchoolYearIdToFeesTable extends Migration
{
    public function up(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            if (!Schema::hasColumn('fees', 'school_year_id')) {
                $table->unsignedBigInteger('school_year_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropColumn('school_year_id');
        });
    }
}