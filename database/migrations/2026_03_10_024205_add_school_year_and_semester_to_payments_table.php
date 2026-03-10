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
        $table->unsignedBigInteger('school_year_id')->nullable()->after('status');
        $table->unsignedBigInteger('semester_id')->nullable()->after('school_year_id');
    });
}

public function down()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->dropColumn(['school_year_id', 'semester_id']);
    });
}
};
