<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wompi_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_id')->nullable();
            $table->string('wompi_transaction_id')->nullable()->unique();
            $table->string('wompi_reference')->nullable();
            $table->bigInteger('amount_in_cents');
            $table->string('currency', 3)->default('USD');
            $table->string('status')->default('PENDING');
            $table->string('payment_method')->nullable();
            $table->string('customer_email');
            $table->json('response_data')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index(['status', 'created_at']);
            $table->index('wompi_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wompi_transactions');
    }
};
