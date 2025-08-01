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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('address_id')->constrained('user_addresses');
            $table->string('phone')->nullable();
            $table->text('note')->nullable();
            $table->enum('payment_method', ['cod', 'vnpay', 'banking'])->default('cod');
            $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'failed', 'refunded'])->default('unpaid');
            $table->string('shipping_method')->nullable();
            $table->decimal('total_before_discount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_after_discount', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'shipping', 'delivered', 'cancelled', 'returned'])->default('pending');
            $table->string('sale_channel')->default('website');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};