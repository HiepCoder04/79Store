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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            $table->decimal('amount', 12, 2); // số tiền thanh toán
            $table->string('payment_method')->nullable(); // ví dụ: COD, Momo, VNPay, PayPal
            $table->string('status')->default('pending'); // trạng thái giao dịch: pending, success, failed, refunded

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
