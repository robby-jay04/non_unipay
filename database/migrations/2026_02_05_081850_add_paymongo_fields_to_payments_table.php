<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Remove the 'after' clause to just add the columns at the end
            $table->string('paymongo_source_id')->nullable();
            $table->string('paymongo_payment_intent_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['paymongo_source_id', 'paymongo_payment_intent_id']);
        });
    }
};