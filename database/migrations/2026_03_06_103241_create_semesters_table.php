<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSemestersTable extends Migration
{
    public function up()
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_year_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "1st Semester", "2nd Semester"
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('semesters');
    }
}