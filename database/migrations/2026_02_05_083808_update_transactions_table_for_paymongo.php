<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Step 1: Add new columns
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->after('payment_id');
            $table->decimal('amount', 10, 2)->default(0)->after('transaction_id');
            $table->string('payment_method')->default('gcash')->after('amount');
            $table->json('metadata')->nullable()->after('payment_method');
        });
        
        // Step 2: Copy data from old columns to new ones
        DB::statement('UPDATE transactions SET transaction_id = reference_no WHERE transaction_id IS NULL');
        DB::statement('UPDATE transactions SET metadata = JSON_OBJECT("gateway_response", gateway_response) WHERE gateway_response IS NOT NULL');
        
        // Step 3: Drop old columns
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['reference_no', 'gateway_response']);
        });
        
        // Step 4: Modify status column to enum
        DB::statement("ALTER TABLE transactions MODIFY status ENUM('pending', 'completed', 'failed') DEFAULT 'pending'");
    }

    public function down()
    {
        // Step 1: Add back old columns
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('reference_no')->nullable()->after('payment_id');
            $table->text('gateway_response')->nullable();
        });
        
        // Step 2: Copy data back
        DB::statement('UPDATE transactions SET reference_no = transaction_id WHERE reference_no IS NULL');
        
        // Step 3: Drop new columns
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'amount', 'payment_method', 'metadata']);
        });
        
        // Step 4: Restore status column
        DB::statement("ALTER TABLE transactions MODIFY status VARCHAR(255)");
    }
};