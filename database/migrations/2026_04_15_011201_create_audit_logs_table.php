<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action_type', 60);   // e.g. fee.update, payment.reverse
            $table->string('module', 60);         // e.g. LoanFees, Payments
            $table->string('entity_type')->nullable(); // e.g. App\Models\Loan
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->string('description');
            $table->enum('severity', ['low', 'medium', 'high'])->default('low');
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('session_id', 100)->nullable();
            $table->string('url')->nullable();
            $table->string('http_method', 10)->nullable();
            $table->timestamp('created_at')->useCurrent();
            // No updated_at — audit logs are append-only

            $table->index(['admin_user_id', 'created_at']);
            $table->index(['action_type', 'created_at']);
            $table->index(['severity', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};