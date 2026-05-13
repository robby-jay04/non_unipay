<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->enum('priority', ['normal', 'important', 'urgent'])->default('normal');
            // audience: 'all' | 'course' | 'year_level'
            $table->enum('audience', ['all', 'course', 'year_level'])->default('all');
            $table->string('audience_value')->nullable(); // e.g. 'BSIT' or '2'
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};