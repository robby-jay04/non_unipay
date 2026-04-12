<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('fees', function (Blueprint $table) {
        if (!Schema::hasColumn('fees', 'course')) {
            if (Schema::hasColumn('fees', 'school_year_id')) {
                $table->string('course')->nullable()->after('school_year_id');
            } else {
                $table->string('course')->nullable();
            }
        }
    });
}

    public function down()
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropColumn('course');
        });
    }
};