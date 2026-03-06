<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_add_profile_picture_to_students_table.php
public function up()
{
    Schema::table('students', function (Blueprint $table) {
        $table->string('profile_picture')->nullable()->after('contact');
    });
}

public function down()
{
    Schema::table('students', function (Blueprint $table) {
        $table->dropColumn('profile_picture');
    });
}
};
