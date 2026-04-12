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
    if (!Schema::hasColumn('students', 'profile_picture')) {
        Schema::table('students', function (Blueprint $table) {
            $table->string('profile_picture')->nullable()->after('is_confirmed');
        });
    }
}

public function down()
{
    Schema::table('students', function (Blueprint $table) {
        $table->dropColumn('profile_picture');
    });
}
};
