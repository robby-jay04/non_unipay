<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeePaymentTable extends Migration
{
    public function up()
    {
        Schema::create('fee_payment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('fee_id');
            $table->decimal('amount', 10, 2); // amount paid for this fee (usually full fee amount)
            $table->timestamps();

            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreign('fee_id')->references('id')->on('fees')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fee_payment');
    }
}