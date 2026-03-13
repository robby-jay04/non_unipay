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
    Schema::create('exam_periods', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // e.g., "Prelim", "Midterm", "Semi-Final", "Finals"
        $table->foreignId('semester_id')->constrained()->onDelete('cascade');
        $table->boolean('is_current')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_periods');
    }
};
