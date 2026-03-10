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
    Schema::table('students', function (Blueprint $table) {
        $table->timestamp('last_picture_update')->nullable()->after('last_profile_update');
    });
}

public function down()
{
    Schema::table('students', function (Blueprint $table) {
        $table->dropColumn('last_picture_update');
    });
}
};
