<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('address_id')->nullable()->constrained('user_addresses')->onDelete('set null');

            $table->decimal('total_price', 12, 2)->default(0); // tổng tiền đơn hàng
            $table->decimal('shipping_fee', 12, 2)->default(0); // phí vận chuyển
            $table->decimal('discount', 12, 2)->default(0); // khuyến mãi nếu có

            $table->string('order_status')->default('pending'); // pending, confirmed, shipped, delivered, canceled
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, refunded
            $table->string('payment_method')->nullable(); // cod, momo, bank_transfer...
            $table->string('shipping_method')->nullable(); // giao hàng nhanh, tiết kiệm...

            $table->json('point_method')->nullable(); // điểm thưởng nếu có
            $table->text('note')->nullable(); // ghi chú của khách

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
